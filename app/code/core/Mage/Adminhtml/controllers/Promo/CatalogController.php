<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Backend Catalog Price Rules controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Promo_CatalogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Dirty rules notice message
     *
     * @var string
     */
    protected $_dirtyRulesNoticeMessage;

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('promo/catalog')
            ->_addBreadcrumb(
                Mage::helper('catalogrule')->__('Promotions'),
                Mage::helper('catalogrule')->__('Promotions')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Catalog Price Rules'));

        $dirtyRules = Mage::getModel('catalogrule/flag')->loadSelf();
        if ($dirtyRules->getState()) {
            Mage::getSingleton('adminhtml/session')->addNotice($this->getDirtyRulesNoticeMessage());
        }

        $this->_initAction()
            ->_addBreadcrumb(
                Mage::helper('catalogrule')->__('Catalog'),
                Mage::helper('catalogrule')->__('Catalog')
            )
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Catalog Price Rules'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalogrule/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalogrule')->__('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('current_promo_catalog_rule', $model);

        $this->_initAction()->getLayout()->getBlock('promo_catalog_edit')
             ->setData('action', $this->getUrl('*/promo_catalog/save'));

        $breadcrumb = $id
            ? Mage::helper('catalogrule')->__('Edit Rule')
            : Mage::helper('catalogrule')->__('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();

    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('catalogrule/rule');
                Mage::dispatchEvent(
                    'adminhtml_controller_catalogrule_prepare_save',
                    array('request' => $this->getRequest())
                );
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                if ($id = $this->getRequest()->getParam('rule_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('catalogrule')->__('Wrong rule specified.'));
                    }
                }

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                $autoApply = false;
                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                }

                $model->loadPost($data);

                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalogrule')->__('The rule has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($autoApply) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    Mage::getModel('catalogrule/flag')->loadSelf()
                        ->setState(1)
                        ->save();
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('catalogrule/rule');
                $model->load($id);
                $model->delete();
                Mage::getModel('catalogrule/flag')->loadSelf()
                    ->setState(1)
                    ->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalogrule')->__('The rule has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while deleting the rule. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('catalogrule')->__('Unable to find a rule to delete.')
        );
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function chooserAction()
    {
        switch ($this->getRequest()->getParam('attribute')) {
            case 'sku':
                $type = 'adminhtml/promo_widget_chooser_sku';
                break;

            case 'categories':
                $type = 'adminhtml/promo_widget_chooser_categories';
                break;
        }
        if (!empty($type)) {
            $block = $this->getLayout()->createBlock($type);
            if ($block) {
                $this->getResponse()->setBody($block->toHtml());
            }
        }
    }

    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix('actions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Action_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Apply all active catalog price rules
     */
    public function applyRulesAction()
    {
        $applyDiscountTagging = $this->getRequest()->getParams('applyDiscountTagging');
        if(is_array($applyDiscountTagging) && isset($applyDiscountTagging['applyDiscountTagging'])) {
            $applyDiscountTagging = $applyDiscountTagging['applyDiscountTagging'];
            $applyDiscountTagging = $applyDiscountTagging == 'true' ? TRUE : FALSE;
        } else {
            $applyDiscountTagging = FALSE;
        }
        $success = FALSE;
        $errorMessage = Mage::helper('catalogrule')->__('Unable to apply rules.');
        try {
            Mage::getModel('catalogrule/rule')->applyAll();
            Mage::getModel('catalogrule/flag')->loadSelf()
                ->setState(0)
                ->save();
            $this->_getSession()->addSuccess(Mage::helper('catalogrule')->__('The rules have been applied.'));
            $success = TRUE;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($errorMessage);
        }
        if ($success && $applyDiscountTagging) {
            $this->_redirect('*/*/applyDiscountTagging');
        } else {
            $this->_redirect('*/*');
        }
    }
    
    public function applyDiscountTaggingAction() {
        $log_file = 'apply_discount_tag.log';
        Mage::log("Started script to apply discount tagging.", null, $log_file);
        
        $discount_options = array();
		$discount_options[1003] = 0;
		$discount_options[1004] = 1;
		$discount_options[1005] = 2;
		$discount_options[1006] = 3;
		$discount_options[1007] = 4;
		$discount_options[1008] = 5;
		$discount_options[1009] = 6;
		$discount_options[1010] = 7;
		$discount_options[1011] = 8;
		$discount_options[1012] = 9;
		$discount_options[1013] = 10;
		$discount_options[1014] = 11;
		$discount_options[1015] = 12;
		$discount_options[1016] = 13;
		$discount_options[1017] = 14;
		$discount_options[1018] = 15;
		$discount_options[1019] = 16;
		$discount_options[1020] = 17;
		$discount_options[1021] = 18;
		$discount_options[1022] = 19;
		$discount_options[1023] = 20;
		$discount_options[1024] = 21;
		$discount_options[1025] = 22;
		$discount_options[1026] = 23;
		$discount_options[1027] = 24;
		$discount_options[1028] = 25;
		$discount_options[1029] = 26;
		$discount_options[1030] = 27;
		$discount_options[1031] = 28;
		$discount_options[1032] = 29;
		$discount_options[1033] = 30;
		$discount_options[1034] = 31;
		$discount_options[1035] = 32;
		$discount_options[1036] = 33;
		$discount_options[1037] = 34;
		$discount_options[1038] = 35;
		$discount_options[1039] = 36;
		$discount_options[1040] = 37;
		$discount_options[1041] = 38;
		$discount_options[1042] = 39;
		$discount_options[1043] = 40;
		$discount_options[1044] = 41;
		$discount_options[1045] = 42;
		$discount_options[1046] = 43;
		$discount_options[1047] = 44;
		$discount_options[1048] = 45;
		$discount_options[1049] = 46;
		$discount_options[1050] = 47;
		$discount_options[1051] = 48;
		$discount_options[1052] = 49;
		$discount_options[1053] = 50;
		$discount_options[1054] = 51;
		$discount_options[1055] = 52;
		$discount_options[1056] = 53;
		$discount_options[1057] = 54;
		$discount_options[1058] = 55;
		$discount_options[1059] = 56;
		$discount_options[1060] = 57;
		$discount_options[1061] = 58;
		$discount_options[1062] = 59;
		$discount_options[1063] = 60;
		$discount_options[1064] = 61;
		$discount_options[1065] = 62;
		$discount_options[1066] = 63;
		$discount_options[1067] = 64;
		$discount_options[1068] = 65;
		$discount_options[1069] = 66;
		$discount_options[1070] = 67;
		$discount_options[1071] = 68;
		$discount_options[1072] = 69;
		$discount_options[1073] = 70;
		$discount_options[706] = 71;
		$discount_options[507] = 72;
		$discount_options[508] = 73;
		$discount_options[509] = 74;
		$discount_options[510] = 75;
		$discount_options[511] = 76;
		$discount_options[512] = 77;
		$discount_options[513] = 78;
		$discount_options[514] = 79;
		$discount_options[515] = 80;
		$discount_options[652] = 81;
		$discount_options[516] = 82;
		$discount_options[517] = 83;
		$discount_options[518] = 84;
		$discount_options[519] = 85;
		$discount_options[800] = 86;
		$discount_options[505] = 87;
		$discount_options[801] = 88;
		$discount_options[506] = 89;
		$discount_options[817] = 90;
		$discount_options[993] = 91;
		$discount_options[994] = 92;
		$discount_options[995] = 93;
		$discount_options[996] = 94;
		$discount_options[997] = 95;
		$discount_options[998] = 96;
		$discount_options[999] = 97;
		$discount_options[1000] = 98;
		$discount_options[1001] = 99;
		$discount_options[1002] = 100;

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        
        $collection = Mage::getModel('catalogrule/rule')->getResourceCollection();
        $collection->addWebsitesToResult();
        $collection->setOrder('sort_order', 'DESC');
        #pr($collection->getData());
        
        Mage::log("Resetting all discuount attribute to \"No Discount\" (0) for all products.", null, $log_file);
        $sql = "UPDATE `catalog_product_entity_int` SET value=1003 WHERE `attribute_id`=220;";
        $write->query($sql);

        $catalog_rules = array();
        foreach($collection as $catalog_rule) {
            if ($catalog_rule->getData('is_active') != 1) {
                Mage::log("Returning as rule Id #" . $catalog_rule->getData('rule_id') . " is not active.", null, $log_file);
                continue;
            }
            if ($catalog_rule->getData('from_date') != "" && strtotime($catalog_rule->getData('from_date')) > strtotime(date('Y-m-d 23:59:59'))) {
                Mage::log("Returning as from date is a future date for rule Id #" . $catalog_rule->getData('rule_id') . ".", null, $log_file);
                continue;
            }
            if ($catalog_rule->getData('from_date') != "" && strtotime($catalog_rule->getData('to_date')) < strtotime(date('Y-m-d 23:59:59'))) {
                Mage::log("Returning as to date has been passed for rule Id #" . $catalog_rule->getData('rule_id') . ".", null, $log_file);
                continue;
            }
            if ($catalog_rule->getData('simple_action') != 'by_percent') {
                Mage::log("Returning as rule Id #" . $catalog_rule->getData('rule_id') . " is not a percent discount.", null, $log_file);
                continue;
            }
            $discount = $catalog_rule->getData('discount_amount');
            if (!in_array($discount, $discount_options)) {
                Mage::log("Returning as discount value " . $discount . " is not defined in discount options for rule Id #" . $catalog_rule->getData('rule_id') . ".", null, $log_file);
                continue;
            }
            $catalog_rules[$catalog_rule->getData('rule_id')] = $catalog_rule;
        }
        $wait = 500;

        foreach($catalog_rules as $ruleId => $catalog_rule) {
            Mage::log("Updating rule id: " . $ruleId . "", null, $log_file);
            $this->_getSession()->addSuccess("Updating rule id: " . $ruleId . "");
            $discount_id = array_search($catalog_rule->getData('discount_amount'), $discount_options);
            $productIds = $catalog_rule->getMatchingProductIds();
            $allProductIds = array();
            
            Mage::log("Fetching all products for " . count($productIds) . " products", null, $log_file);
            
            for($p = 0; true; $p++) {
                $from = $wait * $p;
                $pIds = array_slice($productIds, $from, $wait);
                if (count($pIds) <= 0) {
                    break;
                }
                foreach($pIds as $pId) {
                    $allProductIds[] = $pId;
                }
                $pIdsSql = "'" . implode("','", $pIds) . "'";
                $sql = "SELECT product_id FROM catalog_product_super_link WHERE parent_id IN (".$pIdsSql.");";
                $result = $read->fetchAll($sql);
                foreach($result as $row) {
                    $allProductIds[] = $row['product_id'];
                }
                if (count($pIds) <= 0 || count($pIds) < $wait) {
                    break;
                }
            }

            $productIds = array_unique($allProductIds);

            Mage::log("Running for " . count($productIds) . " products", null, $log_file);
            
            $wait = 1;
            for($p = 0; $p < count($productIds); $p++) {
                $pId = $productIds[$p];
                try {
                    $sql = "SELECT * FROM catalog_product_entity_int WHERE entity_id = '".$pId."' AND `attribute_id`=220;";
                    $result = $read->fetchAll($sql);
                    if (count($result) > 0) {
                        $sql = "UPDATE `catalog_product_entity_int` SET value='".$discount_id."' WHERE `attribute_id`=220 AND entity_id IN (".$pId.")";
                        $write->query($sql);
                        #Mage::log($p . " of " . count($productIds) . " : Updated product id " .$pId, null, $log_file);
                    } else {
                        $sql = "INSERT INTO `catalog_product_entity_int` (entity_type_id, attribute_id, store_id, entity_id, value) VALUES('4', '220', '0', '".$pId."', '".$discount_id."');";
                        $write->query($sql);
                        #Mage::log($p . " of " . count($productIds) . " : Inserted product id " .$pId, null, $log_file);
                    }
                } catch (Exception $ex) {
                    Mage::log($sql . "\nCaught exception: ",  $ex->getMessage(), null, $log_file);
                    $this->_getSession()->addError('Failed discount tagging for rule id #' . $ruleId . '<br />' . $ex->getMessage());
                }
            }
        }
        $this->_getSession()->addSuccess('Discount tagging completed.');
        $this->_redirect('*/*');
    }

    /**
     * @deprecated since 1.5.0.0
     */
    public function addToAlersAction()
    {
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/catalog');
    }

    /**
     * Set dirty rules notice message
     *
     * @param string $dirtyRulesNoticeMessage
     */
    public function setDirtyRulesNoticeMessage($dirtyRulesNoticeMessage)
    {
        $this->_dirtyRulesNoticeMessage = $dirtyRulesNoticeMessage;
    }

    /**
     * Get dirty rules notice message
     *
     * @return string
     */
    public function getDirtyRulesNoticeMessage()
    {
        $defaultMessage = Mage::helper('catalogrule')->__('There are rules that have been changed but were not applied. Please, click Apply Rules in order to see immediate effect in the catalog.');
        return $this->_dirtyRulesNoticeMessage ? $this->_dirtyRulesNoticeMessage : $defaultMessage;
    }
}
