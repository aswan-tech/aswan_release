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


class AW_Blog_Block_Tags extends Mage_Core_Block_Template {

    public function _construct() {
        parent::_construct();
        return $this->setTemplate('aw_blog/tags.phtml');
    }

    public function getCollection() {
        if ($this->getData('collection')) {
            return $this->getData('collection');
        }
        $coll = Mage::getModel('blog/tag')->getCollection();
        $coll->getSelect()
                ->where('tag_count > 0')
                ->columns(array('tag_final_count' => 'SUM(tag_count)'))
                ->joinLeft(array("stores" => $coll->getTable('blog/store')), 'main_table.store_id = stores.store_id', array())
                ->joinLeft(array("blogs" => $coll->getTable('blog/blog')), "stores.post_id = blogs.post_id", array())
                ->where('blogs.status = 1')
                ->where('FIND_IN_SET(main_table.tag, blogs.tags)')
                ->where('main_table.store_id = ? OR main_table.store_id = 0', Mage::app()->getStore()->getId())
                ->order(array('tag_final_count DESC',
                    'tag'))
                ->limit(Mage::getStoreConfig(AW_Blog_Helper_Config::XML_TAGCLOUD_SIZE))
                ->group('tag');

        foreach ($coll as $item) {
            if ($item->getTagFinalCount() >= $this->getMaxCount()) {
                $this->setMaxCount($item->getTagFinalCount());
            } elseif ($item->getTagFinalCount() <= $this->getMinCount() || !$this->getMinCount()) {
                $this->setMinCount($item->getTagFinalCount());
                $this->setMinTag($item);
            }
        }
        if ($coll->count()) {
            if (!$this->getMinTag()) {
                $this->setMinTag($item);
            }
            if (!$this->getMaxTag()) {
                $this->setMaxTag($item);
            }
        }

        $this->setCollection($coll);
        return $this->getCollection();
    }

    public function getTagWeight($tag, $isMin=null) {


        /* Returns tag weight from 1 to 10, starts from 1 */
        $total_results = $this->getCollection()->count();
        $min_weight = $this->getMinCount();
        $max_weight = $this->getMaxCount();

        $count = $tag->getTagFinalCount();

        if ($max_weight) {
            $k = ($count / (intval($max_weight)));
        } else {
            $k = 0.1;
        }

        if (!$isMin) {
            $weight = $this->getTagWeight($this->getMinTag(), 1);
            if ((int) $weight) {
                $k = $k / $weight;
            } else {
                $k = 0.1;
            }
        }

        return round($k * 10);
    }

    public function getTagHref($tag) {

        $route = Mage::helper('blog')->getRoute();
        return Mage::getUrl($route . "/tag/" . urlencode($tag->getTag()));
    }

}
