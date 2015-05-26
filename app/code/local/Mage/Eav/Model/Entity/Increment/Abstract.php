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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Enter description here...
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
 */
abstract class Mage_Eav_Model_Entity_Increment_Abstract extends Varien_Object
    implements Mage_Eav_Model_Entity_Increment_Interface
{
    public function getPadLength()
    {
        /* Commented to pick values from code
		$padLength = $this->getData('pad_length');
		*/
		$padLength = '';
        if (empty($padLength)) {
            $padLength = 5;
        }
        return $padLength;
    }

    public function getPadChar()
    {
		/* Commented to pick values from code
		$padChar = $this->getData('pad_char');
		*/    
		$padChar = '';
        if (empty($padChar)) {
            $padChar = '0';
        }
        return $padChar;
    }

    public function format($id)
    {
		//date_default_timezone_set ("Asia/Calcutta");
		$today = Mage::app()->getLocale()->date();
		$time = $today->toString('dd/MM/yy');
		
		//$time = date("d/m/y", time());
		$time = explode("/",$time);
		if(isset($time[0]) && isset($time[1]) && isset($time[2])){
			$date = $time[0].$time[1].$time[2];
		}
		/* 7 has been used as a fix number during the creation of order ID */
        $result = (string)'AS'.$date.'7';
        $result .= str_pad((string)$id, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
		
        return $result;
    }

    public function frontendFormat($id)
    {
        return $id;
    }
}
