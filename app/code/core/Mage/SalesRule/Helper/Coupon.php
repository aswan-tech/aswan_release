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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Helper for coupon codes creating and managing
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Helper_Coupon extends Mage_Core_Helper_Abstract
{
    /**
     * Constants which defines all possible coupon codes formats
     */
    const COUPON_FORMAT_ALPHANUMERIC    = 'alphanum';
    const COUPON_FORMAT_ALPHABETICAL    = 'alpha';
    const COUPON_FORMAT_NUMERIC         = 'num';

    /**
     * Defines type of Coupon
     */
    const COUPON_TYPE_SPECIFIC_AUTOGENERATED = 1;

    /**
     * XML paths to coupon codes generation options
     */
    const XML_PATH_SALES_RULE_COUPON_LENGTH        = 'promo/auto_generated_coupon_codes/length';
    const XML_PATH_SALES_RULE_COUPON_FORMAT        = 'promo/auto_generated_coupon_codes/format';
    const XML_PATH_SALES_RULE_COUPON_PREFIX        = 'promo/auto_generated_coupon_codes/prefix';
    const XML_PATH_SALES_RULE_COUPON_SUFFIX        = 'promo/auto_generated_coupon_codes/suffix';
    const XML_PATH_SALES_RULE_COUPON_DASH_INTERVAL = 'promo/auto_generated_coupon_codes/dash';

    /**
     * Config path for character set and separator
     */
    const XML_CHARSET_NODE      = 'global/salesrule/coupon/charset/%s';
    const XML_CHARSET_SEPARATOR = 'global/salesrule/coupon/separator';

    /**
     * Get all possible coupon codes formats
     *
     * @return array
     */
    public function getFormatsList()
    {
        return array(
            self::COUPON_FORMAT_ALPHANUMERIC => $this->__('Alphanumeric'),
            self::COUPON_FORMAT_ALPHABETICAL => $this->__('Alphabetical'),
            self::COUPON_FORMAT_NUMERIC      => $this->__('Numeric'),
        );
    }

    /**
     * Get default coupon code length
     *
     * @return int
     */
    public function getDefaultLength()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_SALES_RULE_COUPON_LENGTH);
    }

    /**
     * Get default coupon code format
     *
     * @return int
     */
    public function getDefaultFormat()
    {
        return Mage::getStoreConfig(self::XML_PATH_SALES_RULE_COUPON_FORMAT);
    }

    /**
     * Get default coupon code prefix
     *
     * @return string
     */
    public function getDefaultPrefix()
    {
        return Mage::getStoreConfig(self::XML_PATH_SALES_RULE_COUPON_PREFIX);
    }

    /**
     * Get default coupon code suffix
     *
     * @return string
     */
    public function getDefaultSuffix()
    {
        return Mage::getStoreConfig(self::XML_PATH_SALES_RULE_COUPON_SUFFIX);
    }

    /**
     * Get dashes occurrences frequency in coupon code
     *
     * @return int
     */
    public function getDefaultDashInterval()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_SALES_RULE_COUPON_DASH_INTERVAL);
    }

    /**
     * Get Coupon's alphabet as array of chars
     *
     * @param string $format
     * @return array|bool
     */
    public function getCharset($format)
    {
        return str_split((string) Mage::app()->getConfig()->getNode(sprintf(self::XML_CHARSET_NODE, $format)));
    }

    /**
     * Retrieve Separator from config
     *
     * @return string
     */
    public function getCodeSeparator()
    {
        return (string) Mage::app()->getConfig()->getNode(Mage_SalesRule_Helper_Coupon::XML_CHARSET_SEPARATOR);
    }

    public function getVoucherTypes() {
        if ($this->_voucherTypes === null) {
            $this->_voucherTypes = array(
            //    'DIS01' => 'Customer Acquisition',
            //    'DIS02' => 'Customer Retention',
            //    'DIS03' => 'Marketing Discount',
            //    'DIS00' => 'Trade Discount',
		'CAC' => 'Customer Acquisition Cost',
                'DISCOUNT' => 'Discount',
            );
        }
        return $this->_voucherTypes;
    }

}
