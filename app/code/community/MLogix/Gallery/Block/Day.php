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

class MLogix_Gallery_Block_Day extends Mage_Core_Block_Template {

    private $_pageCount = 1;
    private $_totalCommentsCount = null;

    public function _prepareLayout() {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label' => Mage::helper('catalog')->__('Home'),
                'title' => Mage::helper('catalog')->__('Home'),
                'link' => Mage::getBaseUrl()
            ));
            $breadcrumbsBlock->addCrumb('trends', array(
                'label' => Mage::helper('catalog')->__('Trends'),
                'title' => Mage::helper('catalog')->__('Trends'),
                'link' => Mage::getBaseUrl() . 'gallery/album/view'
            ));
            $breadcrumbsBlock->addCrumb('day', array(
                'label' => Mage::helper('catalog')->__('Look of the Day'),
                'title' => Mage::helper('catalog')->__('Look of the Day'),
                'link' => ''
            ));
        }
        return parent::_prepareLayout();
    }

    public function getDay($itemTitle=0) {
        $model = $this->getCurrentDay();
        if (!$model)
            return array();

        $itemTitle = $model->getItemTitle();

        if ($itemTitle) {
            return $model->getLookByItemTitle($itemTitle);
        } else {
            $yr = date('Y');
            $mn = date('m');
            $dy = date('d');

            $title = $yr . "-" . $mn . "-" . $dy;

            return $model->getLookByItemTitle($title);
        }
    }

    public function getImageUrl($itemId) {
        $model = Mage::getModel('gallery/day')->load($itemId);
        return $model->getImageUrl();
    }

    public function getViewUrl($itemId) {
        return $this->getUrl("*/*/index/", array('id' => $itemId));
    }

    public function getCurrentDay() {
        if (!Mage::registry('current_day'))
            Mage::register('current_day', Mage::getModel('gallery/day'));

        return Mage::registry('current_day');
    }

    public function getGalleryTitle() {
        $cg = $this->getCurrentGallery();
        if ($cg && $cg->getTitle())
            return $cg->getTitle();
        else
            return "Gallery";
    }

    public function getBreadcrumbs() {
        return $this->getCurrentGallery()->getBreadcrumbPath();
    }

    public function getHighlights($day_date) {

        /*
         * SELECT *, SUBSTRING(item_title,14) as D1
         * from galleryweek
         * where parent_id = 0 and SUBSTRING(item_title,14) < '2013-07-07'
         * order by D1 desc
         * limit 2;
         */

        $pagesize = Mage::getStoreConfig('gallery/lookoftheday/highlightsperweek');

        $highlights = Mage::getModel('gallery/day')->getCollection()
                        ->addFieldToFilter('parent_id', array('eq' => '0'))
                        ->addFieldToFilter('item_title', array('lt' => $day_date))
                        ->addFieldToFilter("status", '1')
                        ->setOrder('item_title', 'desc')
                        ->setPageSize($pagesize);

        return $highlights;
    }

    public function getLoginRequired() {
        return Mage::getStoreConfig('gallery/daycomments/login');
    }

    public function getCommentsEnabled() {
        return Mage::getStoreConfig('gallery/daycomments/enabled');
    }

    public function getComment($post) {
        $_curPage = Mage::app()->getRequest()->getParam('p') ? Mage::app()->getRequest()->getParam('p') : 1;
        $_perPage = Mage::helper('gallery')->getCommentsPerPage();
        $collection = Mage::getModel('gallery/daycomment')->getCollection()
                        ->addPostFilter($post->getGalleryId())
                        ->setOrder('created_time ', 'desc')
                        ->addApproveFilter(2)
                        ->setPageSize($_perPage)
                        ->setCurPage($_curPage);
        $this->_totalCommentsCount = $collection->getSize();
        $this->_pageCount = intval(ceil($this->_totalCommentsCount / $_perPage));
        return $collection;
    }

    public function getCommentTotalString($comments) {
        $comment_count = $this->_totalCommentsCount;
        if ($comment_count == 1) {
            $comment_string = $comment_count . " " . Mage::helper('gallery')->__('Comment');
        } else {
            $comment_string = $comment_count . " " . Mage::helper('gallery')->__('Comments');
        }
        return $comment_string;
    }

    public function getPageCount() {
        return $this->_pageCount;
    }

    public function current($i) {

        if ($i == 1 && !Mage::app()->getRequest()->getParam('p')) {
            return true;
        }
        return $i == Mage::app()->getRequest()->getParam('p');
    }

    public function getPageAddress($page) {
        
        $route = Mage::helper('core/url')->getCurrentUrl();
        $route = str_replace(Mage::getUrl(), "", $route);
        $params = array(
            '_query' => array(
                'p' => $page
            ),
            '_direct' => $route
        );
        return Mage::getUrl('', $params);
    }

}