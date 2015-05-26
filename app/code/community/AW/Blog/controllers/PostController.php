<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */


require_once 'recaptcha/recaptchalib-aw.php';

class AW_Blog_PostController extends Mage_Core_Controller_Front_Action {

    public function preDispatch() {

        parent::preDispatch();

        if (!Mage::helper('blog')->getEnabled()) {
            $this->_redirectUrl(Mage::helper('core/url')->getHomeUrl());
        }
    }

    protected function _validateData($data) {
        $errors = array();

        $helper = Mage::helper('blog');

        if (!Zend_Validate::is($data->getUser(), 'NotEmpty')) {
            $errors[] = $helper->__('Name can\'t be empty');
        }

        if (!Zend_Validate::is($data->getComment(), 'NotEmpty')) {
            $errors[] = $helper->__('Comment can\'t be empty');
        }

        if (!Zend_Validate::is($data->getPostId(), 'NotEmpty')) {
            $errors[] = $helper->__('post_id can\'t be empty');
        }

        $validator = new Zend_Validate_EmailAddress();
        if (!$validator->isValid($data->getEmail())) {
            $errors[] = $helper->__('"%s" is not a valid email address.', $data->getEmail());
        }

        return $errors;
    }

    public function viewAction() {

        $identifier = $this->getRequest()->getParam('identifier', $this->getRequest()->getParam('id', false));

        $helper = Mage::helper('blog');
        $session = Mage::getSingleton('customer/session');
		
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blog/comment');
            $data['user'] = strip_tags($data['user']);
            $model->setData($data);

            if (!Mage::getStoreConfig('blog/comments/enabled')) {
                $session->addError($helper->__('Comments are not enabled.'));
                if (!Mage::helper('blog/post')->renderPage($this, $identifier)) {
                    $this->_forward('NoRoute');
                }
                return;
            }


            if (!$session->isLoggedIn() && Mage::getStoreConfig('blog/comments/login')) {
                $session->addError($helper->__('You must be logged in to comment.'));
                if (!Mage::helper('blog/post')->renderPage($this, $identifier)) {
                    $this->_forward('NoRoute');
                }
                return;
            } else if ($session->isLoggedIn() && Mage::getStoreConfig('blog/comments/login')) {
                $model->setUser($helper->getUserName());
                $model->setEmail($helper->getUserEmail());
            }

            try {

                if (Mage::getStoreConfig('blog/recaptcha/enabled') && !$session->isLoggedIn()) {
                    $publickey = Mage::getStoreConfig('blog/recaptcha/publickey');
                    $privatekey = Mage::getStoreConfig('blog/recaptcha/privatekey');

                    $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $data["recaptcha_challenge_field"], $data["recaptcha_response_field"]);

                    if (!$resp->is_valid) {
                        if ($resp->error == "incorrect-captcha-sol") {
                            $session->addError($helper->__('Your Recaptcha solution was incorrect, please try again'));
                        } else {
                            $session->addError($helper->__('An error occured. Please try again'));
                        }
                        // Redirect back with error message
                        $session->setBlogPostModel($model);
                        $this->_redirectReferer();
                        return;
                    }
                }


                $errors = $this->_validateData($model);
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $session->addError($error);
                    }
                    $this->_redirectReferer();
                    return;
                }

                if ($session->getData('blog_post_model')) {
                    $session->unsetData('blog_post_model');
                }
                $model->setCreatedTime(now());
                $model->setComment(htmlspecialchars($model->getComment(), ENT_QUOTES));
                if (Mage::getStoreConfig('blog/comments/approval')) {
                    $model->setStatus(2);
                    $session->addSuccess($helper->__('Your comment has been submitted.'));
                } else if ($session->isLoggedIn() && Mage::getStoreConfig('blog/comments/loginauto')) {
                    $model->setStatus(2);
                    $session->addSuccess($helper->__('Your comment has been submitted.'));
                } else {
                    $model->setStatus(1);
                    $session->addSuccess($helper->__('Your comment has been submitted and is awaiting approval.'));
                }
                $model->save();

                $comment_id = $model->getCommentId();
				
				if (Mage::getStoreConfig('blog/comments/approval')) {
					######################	My Code	##############################
					$data = $model->load($this->getRequest()->getParam('id'));
					$this->sendCommentApprovalEmail($comment_id, $this->getRequest()->getParam('id'));
					######################	My Code	##############################
				}
				
				if($comment_id && $data['notify_comment']){
					$read	=	Mage::getSingleton('core/resource')->getConnection('core_read');
					$write	=	Mage::getSingleton('core/resource')->getConnection('core_write');
					
					$select = 
						' SELECT id '.
						' FROM `aw_blog_comment_notification` where post_id="'.$data['post_id'].'" and email_id="'.$data['email'].'" and type="blog"';
					$result = $read->fetchAll($select);
					//pr($result);
					
					if(!count($result)){
						$sql = 
							' INSERT INTO `aw_blog_comment_notification` (post_id, email_id, type) '.
							' VALUES ("'.$data['post_id'].'", "'.$data['email'].'", "blog")';
						$write->query($sql);
					}
				}
				
				if($data['notify_blog']){
					$subscribeToBlog = Mage::getModel('newslettergroup/subscriber')->groupSubscribe($data['email'], 'Daily Blogs');
				}
            } catch (Exception $e) {
                if (!Mage::helper('blog/post')->renderPage($this, $identifier)) {
                    $this->_forward('NoRoute');
                }
            }
			
            if (Mage::getStoreConfig('blog/comments/recipient_email') != null && $model->getStatus() == 1 && isset($comment_id)) {
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                try {
                    $data["url"] = Mage::getUrl('blog/manage_comment/edit/id/' . $comment_id);
                    $postObject = new Varien_Object();
                    $postObject->setData($data);
                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                            ->sendTransactional(
                                    Mage::getStoreConfig('blog/comments/email_template'), Mage::getStoreConfig('blog/comments/sender_email_identity'), Mage::getStoreConfig('blog/comments/recipient_email'), null, array('data' => $postObject)
                    );
                    $translate->setTranslateInline(true);
                } catch (Exception $e) {
                    $translate->setTranslateInline(true);
                }
            }
            $this->_redirectReferer();
            return;
            if (!Mage::helper('blog/post')->renderPage($this, $identifier)) {
                $this->_forward('NoRoute');
            }
        } else {
            /* GET request */
            if (!Mage::helper('blog/post')->renderPage($this, $identifier)) {
                $session->addNotice($helper->__('The requested page could not be found'));
                $this->_redirect($helper->getRoute());
                return false;
            }
        }
    }

    public function noRouteAction($coreRoute = null) {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $pageId = Mage::getStoreConfig('web/default/cms_no_route');
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }
	
	public function unsubscribeAction() {
		//$read	=	Mage::getSingleton('core/resource')->getConnection('core_read');
		$write	=	Mage::getSingleton('core/resource')->getConnection('core_write');
		
		try {
			$delSql = 'Delete FROM `aw_blog_comment_notification` where post_id="'.$this->getRequest()->getParam('postid').'" and email_id="'.$this->getRequest()->getParam('email').'" and type="blog"';
			$write->query($delSql);
			
			$route 	= 	Mage::getStoreConfig('blog/blog/route');
			if ($route == "") {
				$route = "blog";
			}
			$route 		= 	Mage::getUrl($route);
			
			$blogColl	= Mage::getModel('blog/blog')->load($this->getRequest()->getParam('postid'));
			$cats = Mage::getModel('blog/cat')->getCollection()
					->addPostFilter($this->getRequest()->getParam('postid'))
					->addStoreFilter(Mage::app()->getStore()->getId(), false);
			foreach ($cats as $cat) {
				$catUrl	=	$route . "cat/" . $cat->getIdentifier();
				break;//Force break after first category
			}
			
			if (Mage::getStoreConfig('blog/blog/categories_urls')) {
				$postUrl	=	$catUrl . '/post/' . $blogColl->getIdentifier();
			} else {
				$postUrl	=	$route . $blogColl->getIdentifier();
			}
			
			Mage::getSingleton('core/session')->addSuccess($this->__('Unsubscription successful.'));
			$this->_redirectSuccess($postUrl);
			return;
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError("Unable to unsubscribe, please try later.");
            $this->_redirect('*/*/index');
		}
	}
	
	public function sendCommentApprovalEmail($commentid, $postid) {
		$read	=	Mage::getSingleton('core/resource')->getConnection('core_read');
		$write	=	Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$route 	= 	Mage::getStoreConfig('blog/blog/route');
		if ($route == "") {
			$route = "blog";
		}
		$route 		= 	Mage::getUrl($route);
		
		$cats = Mage::getModel('blog/cat')->getCollection()
                ->addPostFilter($postid)
                ->addStoreFilter(Mage::app()->getStore()->getId(), false);
        foreach ($cats as $cat) {
            $catUrl	=	$route . "cat/" . $cat->getIdentifier();
			break;//Force break after first category
        }
		
		$blogColl = Mage::getModel('blog/blog')->getCollection()
					->addFieldToSelect(array('title','identifier'))
					->addFieldToFilter('main_table.post_id', array('eq' => $postid))
					->addFieldToFilter('cmnt.comment_id', array('eq' => $commentid))
                    ->addStoreFilter(Mage::app()->getStore()->getId(), false);
		$blogColl->getSelect()->join( array('cmnt'=>Mage::getSingleton('core/resource')->getTableName('blog/comment')), 'main_table.post_id = cmnt.post_id', array('cmnt.comment','cmnt.email'));
		foreach($blogColl as $blogCollec){
			if (Mage::getStoreConfig('blog/blog/categories_urls')) {
				$postUrl	=	$catUrl . '/post/' . $blogCollec->getIdentifier();
			} else {
				$postUrl	=	$route . $blogCollec->getIdentifier();
			}
			
			$module	=	Mage::app()->getRequest()->getModuleName();
			$cntrlr	=	Mage::app()->getRequest()->getControllerName();
			
			$data['post_name']			=	$blogCollec->getTitle();
			$data['post_url']			=	$postUrl;
			$data['comment_detail']		=	$blogCollec->getComment();
		}
		
		$select = 'SELECT email_id FROM `aw_blog_comment_notification` where post_id="'.$postid.'" and type="blog"';
		$result = $read->fetchAll($select);
		foreach($result as $key=>$arr){
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);
			try {
				//$encryptData	=	Mage::helper('core')->encrypt("postid/".$postid."/email/".$arr['email_id']);
				$data['unsubscribe_url']	=	$route."post/unsubscribe/postid/".$postid."/email/".$arr['email_id'];
				
				$postObject = new Varien_Object();
				$postObject->setData($data);
				$mailTemplate = Mage::getModel('core/email_template');
				/* @var $mailTemplate Mage_Core_Model_Email_Template */
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->sendTransactional(
								Mage::getStoreConfig('blog/comments/email_comment_approve_notify'), Mage::getStoreConfig('blog/comments/sender_email_identity'), $arr['email_id'], null, array('data' => $postObject)
				);
				$translate->setTranslateInline(true);
			} catch (Exception $e) {
				$translate->setTranslateInline(true);
			}
		}
	}
}
