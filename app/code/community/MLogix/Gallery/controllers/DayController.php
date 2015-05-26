<?php

/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_DayController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $id = $this->getRequest()->getParam('id');

        if (!$id)
            $id = 0;

        $model = Mage::getModel('gallery/day')->load($id);

        Mage::register('current_day', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _validateData($data) {
        $errors = array();

        $helper = Mage::helper('gallery');

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

    public function postCommentAction() {

        $session = Mage::getSingleton('customer/session');
        $helper = Mage::helper('gallery');

        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('gallery/daycomment');
            $model->setData($data);

            if (!Mage::getStoreConfig('gallery/daycomments/enabled')) {
                Mage::getSingleton('core/session')->addError($helper->__('Comments are not enabled.'));
                $this->_redirectReferer();
                return;
            }

            if (!$session->isLoggedIn() && Mage::getStoreConfig('gallery/daycomments/login')) {
                Mage::getSingleton('core/session')->addError($helper->__('You must be logged in to comment.'));
                $this->_redirectReferer();
            } else if ($session->isLoggedIn() && Mage::getStoreConfig('gallery/daycomments/login')) {
                $model->setUser($helper->getUserName());
                $model->setEmail($helper->getUserEmail());
            }

            try {

                $errors = $this->_validateData($model);
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        Mage::getSingleton('core/session')->addError($error);
                    }
                    $this->_redirectReferer();
                    return;
                }

                if ($session->getData('gallery_post_model')) {
                    $session->unsetData('gallery_post_model');
                }

                $model->setCreatedTime(now());
                $model->setComment(htmlspecialchars($model->getComment(), ENT_QUOTES));
                if (Mage::getStoreConfig('gallery/daycomments/approval')) {
                    $model->setStatus(2);
                    Mage::getSingleton('core/session')->addSuccess($helper->__('Your comment has been submitted.'));
                } else if ($session->isLoggedIn() && Mage::getStoreConfig('gallery/daycomments/loginauto')) {
                    $model->setStatus(2);
                    Mage::getSingleton('core/session')->addSuccess($helper->__('Your comment has been submitted.'));
                } else {
                    $model->setStatus(1);
                    Mage::getSingleton('core/session')->addSuccess($helper->__('Your comment has been submitted and is awaiting approval.'));
                }
                $model->save();

                $comment_id = $model->getCommentId();

                if (Mage::getStoreConfig('gallery/daycomments/approval')) {
                    $data = $model->load($this->getRequest()->getParam('id'));
                    $this->sendCommentApprovalEmail($comment_id, $this->getRequest()->getParam('id'));
                }

                if ($comment_id && $data['notify_comment']) {
                    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');

                    $select = 'SELECT id ' .
                            'FROM `galleryday_comment_notification` ' .
                            'WHERE post_id="' . $data['post_id'] . '" and email_id="' . $data['email'] . '" ';
                    $result = $read->fetchAll($select);

                    if (!count($result)) {
                        $sql = 'INSERT INTO `galleryday_comment_notification` (post_id, email_id) ' .
                                'VALUES ("' . $data['post_id'] . '", "' . $data['email'] . '" )';
                        $write->query($sql);
                    }
                }

                if ($data['notify_trend']) {

                    $model = Mage::getModel('newslettergroup/subscriber');
                    $existing_ids = $model->loadByEmail($data['email'])->getNewsletterGroupId();
                    $model->setSubscriberStatus('3');
                    $model->save();

                    if ($existing_ids) {
                        $array = explode(",", $existing_ids);
                        $array[] = '3';
                        $array = array_unique($array);
                        $model->groupSubscribe($data['email'], $array);
                    } else {
                        $model->groupSubscribe($data['email'], 3);
                    }
                }

                $this->_redirectReferer();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($helper->__('Some error while posting comment, try again.'));
                $this->_redirectReferer();
            }
        }
    }

    public function sendCommentApprovalEmail($commentid, $postid) {
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $blogColl = Mage::getModel('gallery/day')->getCollection()
                        ->addFieldToSelect(array('item_title'))
                        ->addFieldToFilter('main_table.gallery_id', array('eq' => $postid))
                        ->addFieldToFilter('cmnt.comment_id', array('eq' => $commentid));
        $blogColl->getSelect()->join(array('cmnt' => Mage::getSingleton('core/resource')->getTableName('gallery/daycomment')), 'main_table.gallery_id = cmnt.post_id', array('cmnt.comment', 'cmnt.email'));
        foreach ($blogColl as $blogCollec) {
            $block = new MLogix_Gallery_Block_Week();
            $postUrl = $block->getViewUrl($postid);
            $module = Mage::app()->getRequest()->getModuleName();
            $cntrlr = Mage::app()->getRequest()->getControllerName();

            $data['post_name'] = $blogCollec->getTitle();
            $data['post_url'] = $postUrl;
            $data['comment_detail'] = $blogCollec->getComment();
        }

        $select = 'SELECT email_id FROM `galleryday_comment_notification` where post_id="' . $postid . '" ';
        $result = $read->fetchAll($select);

        foreach ($result as $key => $arr) {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            try {
                $data['unsubscribe_url'] = Mage::getBaseUrl() . "gallery/day/unsubscribe/postid/" . $postid . "/email/" . $arr['email_id'];

                $postObject = new Varien_Object();
                $postObject->setData($data);

                $mailTemplate = Mage::getModel('core/email_template');

                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => Mage::app()->getStore()->getId()));

                $mailTemplate->sendTransactional(
                        Mage::getStoreConfig('gallery/daycomments/email_comment_approve_notify'),
                        Mage::getStoreConfig('gallery/daycomments/sender_email_identity'),
                        $arr['email_id'],
                        $arr['email_id'],
                        array('data' => $postObject)
                );

                $translate->setTranslateInline(true);
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
            }
        }
    }

    public function archiveAction() {
        $this->loadLayout();

        $month = $this->getRequest()->getParam('m');
        $year = $this->getRequest()->getParam('y');

        if ($month == '' && $year == '') {
            $month = date('m');
            $year = date('Y');

            $this->_redirect('*/*/archive/y/' . $year . '/m/' . $month);
        }

        $this->renderLayout();
    }

    public function searchAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function unsubscribeAction() {

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        try {
            $delSql = 'Delete FROM `galleryday_comment_notification` where post_id="' . $this->getRequest()->getParam('postid') . '" and email_id="' . $this->getRequest()->getParam('email') . '" ';
            $write->query($delSql);

            $block = new MLogix_Gallery_Block_Day();
            $postUrl = $block->getViewUrl($this->getRequest()->getParam('postid'));

            Mage::getSingleton('core/session')->addSuccess($this->__('Unsubscription successful.'));
            $this->_redirectSuccess($postUrl);
            return;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError("Unable to unsubscribe, please try later.");
            $this->_redirect('*/*/index');
        }
    }

}