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


class AW_Blog_Manage_CommentController extends Mage_Adminhtml_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/blog/comment');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('blog/comment')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Comment Manager'), Mage::helper('adminhtml')->__('Comment Manager'));
        $this->displayTitle('Comments');

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('blog/comment');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Comment was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function approveAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('blog/comment');
				
				
				######################	My Code	##############################
				$data = $model->load($this->getRequest()->getParam('id'));
				$this->sendCommentApprovalEmail($this->getRequest()->getParam('id'), $data->getPostId());
				######################	My Code	##############################
				
				
                $model->setId($this->getRequest()->getParam('id'))
                        ->setStatus(2)
                        ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Comment was approved'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
	public function sendCommentApprovalEmail($commentid, $postid) {
		$read	=	Mage::getSingleton('core/resource')->getConnection('core_read');
		$write	=	Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$cats = Mage::getModel('blog/cat')->getCollection()
                ->addPostFilter($postid)
                ->addStoreFilter(Mage::app()->getStore()->getId(), false);
        foreach ($cats as $cat) {
            $catUrl	=	$route . "cat/" . $cat->getIdentifier();
			break;//Force break after first category
        }
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
		Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
		
		$route 	= 	Mage::getStoreConfig('blog/blog/route');
		if ($route == "") {
			$route = "blog";
		}
		$route 		= 	Mage::getUrl($route);
		
		$blogColl = Mage::getModel('blog/blog')->getCollection()
					->addFieldToSelect(array('title','identifier'))
					->addFieldToFilter('main_table.post_id', array('eq' => $postid))
					->addFieldToFilter('cmnt.comment_id', array('eq' => $commentid))
                    ->addStoreFilter(Mage::app()->getStore()->getId(), false);
		$blogColl->getSelect()->join( array('cmnt'=>Mage::getSingleton('core/resource')->getTableName('blog/comment')), 'main_table.post_id = cmnt.post_id', array('cmnt.comment','cmnt.email'));
		
		/* Setting store back to admin for this product */
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);	
		Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMIN, Mage_Core_Model_App_Area::PART_EVENTS);
		
		
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
	
    public function unapproveAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('blog/comment');

                $model->setId($this->getRequest()->getParam('id'))
                        ->setStatus(1)
                        ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Comment was unapproved'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {


        $blogIds = $this->getRequest()->getParam('comment');
        if (!is_array($blogIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select comment(s)'));
        } else {
            try {
                foreach ($blogIds as $blogId) {
                    $blog = Mage::getModel('blog/comment')->load($blogId);
                    $blog->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d comments(s) were successfully deleted', count($blogIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function massApproveAction() {
        $blogIds = $this->getRequest()->getParam('comment');
		
        if (!is_array($blogIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select comment(s)'));
        } else {
            try {
                foreach ($blogIds as $blogId) {
                    $blog = Mage::getSingleton('blog/comment')
							->load($blogId);
					
					######################	My Code	##############################
					$sendMail = $this->sendCommentApprovalEmail($blogId, $blog->getPostId());//$blogId is the comment id
					######################	My Code	##############################
					
					$blog->setStatus(2)
							->setIsMassupdate(true)
							->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d comment(s) were successfully approved', count($blogIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function massUnapproveAction() {
        $blogIds = $this->getRequest()->getParam('comment');

        if (!is_array($blogIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select comment(s)'));
        } else {
            try {
                foreach ($blogIds as $blogId) {
                    $blog = Mage::getSingleton('blog/comment')
                            ->load($blogId)
                            ->setStatus(1)
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d comment(s) were successfully unapproved', count($blogIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blog/comment')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('blog_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('blog/posts');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('blog/manage_comment_edit'));
            $this->displayTitle('Edit comment');

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Post does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blog/comment');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blog')->__('Comment was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find comment to save'));
        $this->_redirect('*/*/');
    }

    protected function displayTitle($data = null, $root = 'Blog') {

        if (!Mage::helper('blog')->magentoLess14()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Blog'))->_title($root);
            }
        }
        return $this;
    }

}