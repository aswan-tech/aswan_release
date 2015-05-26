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
 * @category    FCM
 * @package     FCM_Shippingmethod
 */


/**
 * Shippingmethod Backend Validate model
 *
 * @category   FCM
 * @package    FCM_Shippingmethod
 * @author     FCM Team
 */
class FCM_Shippingmethod_Model_System_Config_Backend_Validate extends Mage_Core_Model_Config_Data
{
	/** 
	* Function to be called before saving configuration setting for custom shipping methods
	**/
    protected function _beforeSave()
    {
		$value = $this->getValue();
		
        if (!is_numeric($value)) {
			
            Mage::throwException(Mage::helper('core')->__('The %s you entered is invalid. Please make sure that it is a numeric value.', $this->getFieldConfig()->label)); 
			
		}
        $this->setValue($value);
		
        return $this;
    }
}
