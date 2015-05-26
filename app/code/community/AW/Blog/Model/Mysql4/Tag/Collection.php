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


class AW_Blog_Model_Mysql4_Tag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected $_previewFlag;

    protected function _construct() {
        $this->_init('blog/tag');
    }

    public function toOptionArray() {
        return $this->_toOptionArray('identifier', 'title');
    }

    public function addStoreFilter($store) {
        if (!Mage::app()->isSingleStoreMode()) {
            $id = $store->getId();
            $this->getSelect()->where('store_id=' . $id . ' OR store_id=0');
            return $this;
        }
        return $this;
    }

    public function addTagFilter($tag) {
        $this->getSelect()->where('tag=?', $tag);
        return $this;
    }

}
