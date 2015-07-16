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
class AW_Blog_Model_Blog extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('blog/blog');
    }

    public function getShortContent() {
        $content = $this->getData('short_content');
        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getPostContent() {
        $content = $this->getData('post_content');
        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function _beforeSave() {
        if (is_array($this->getData('tags'))) {
            $this->setData('tags', implode(",", $this->getData('tags')));
        }
        return parent::_beforeSave();
    }

    public function getTodaysPost() {

        $collection = $this->getCollection();
        $collection->addFieldToFilter('identifier', array('nin' => array('header-image')));

        $gttimestamp = mktime('00', '00', '00', date("m"), date("d"), date("Y"));
        $lttimestamp = mktime('23', '59', '59', date("m"), date("d"), date("Y"));
        $dateTo = date("Y-m-d H:i:s", $gttimestamp);
        $dateFrom = date("Y-m-d H:i:s", $lttimestamp);

        $collection->addFieldToFilter('created_time', array('gteq' => array($dateTo)));
        $collection->addFieldToFilter('created_time', array('lteq' => array($dateFrom)));

        $collection->addFieldToSelect('short_content_img');
        $collection->addFieldToSelect('title');
        $collection->addFieldToSelect('short_content');

        return $collection;
    }

}
