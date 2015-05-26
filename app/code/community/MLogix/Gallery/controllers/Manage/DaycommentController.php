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
class MLogix_Gallery_Manage_DaycommentController extends Mage_Adminhtml_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/gallery/daycomment');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('gallery/daycomment')
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
                $model = Mage::getModel('gallery/daycomment');

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
                $model = Mage::getModel('gallery/daycomment');
                $data = $model->load($this->getRequest()->getParam('id'));
                //$this->sendCommentApprovalEmail($this->getRequest()->getParam('id'), $data->getPostId());
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

    public function unapproveAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('gallery/daycomment');

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
                    $blog = Mage::getModel('gallery/daycomment')->load($blogId);
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

                    $blog = Mage::getSingleton('gallerydaycomment')
                                    ->load($blogId);

                    //$sendMail = $this->sendCommentApprovalEmail($blogId, $blog->getPostId());

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
                    $blog = Mage::getSingleton('gallery/daycomment')
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
        $model = Mage::getModel('gallery/daycomment')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('gallery_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('gallery/day');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('gallery/manage_daycomment_edit'));
            $this->displayTitle('Edit comment');

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Post does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('gallery/daycomment');
            $model->setData($data)->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Comment was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Unable to find comment to save'));
        $this->_redirect('*/*/');
    }

    protected function displayTitle($data = null, $root = 'Look of Day') {

        if (!Mage::helper('gallery')->magentoLess14()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('gallery'))->_title($root);
            }
        }
        return $this;
    }

}