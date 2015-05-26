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

class MLogix_Gallery_Block_Week extends Mage_Core_Block_Template {

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
            $breadcrumbsBlock->addCrumb('week', array(
                'label' => Mage::helper('catalog')->__('Look of the Week'),
                'title' => Mage::helper('catalog')->__('Look of the Week'),
                'link' => ''
            ));
        }
        return parent::_prepareLayout();
    }

    public function getWeek($itemTitle=0) {
        $model = $this->getCurrentWeek();
        if (!$model)
            return array();

        $itemTitle = $model->getItemTitle();

        if ($itemTitle) {
            return $model->getLookByItemTitle($itemTitle);
        } else {

            $weekArr = Mage::helper('gallery')->rangeWeek(date('Y-m-d'));
            $title = $weekArr['start'] . " ~ " . $weekArr['end'];

            return $model->getLookByItemTitle($title);
        }
    }

    public function getImageUrl($itemId) {
        $model = Mage::getModel('gallery/week')->load($itemId);
        return $model->getImageUrl();
    }
	
	public function getViewUrl($itemId) {
        return $this->getUrl("*/*/index/", array('id'=>$itemId));
    }

    public function getCurrentWeek() {
        if (!Mage::registry('current_week'))
            Mage::register('current_week', Mage::getModel('gallery/week'));

        return Mage::registry('current_week');
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

    public function getHighlights($week_end_date) {

        /*
         * SELECT *, SUBSTRING(item_title,14) as D1
         * from galleryweek
         * where parent_id = 0 and SUBSTRING(item_title,14) < '2013-07-07'
         * order by D1 desc
         * limit 2;
         */

        $weekArr = Mage::helper('gallery')->rangeWeek(date('Y-m-d'));
        $pagesize = Mage::getStoreConfig('gallery/lookoftheweek/highlightsperweek');

        $highlights = Mage::getModel('gallery/week')->getCollection()
                        ->addFieldToFilter('parent_id', array('eq' => '0'))
                        ->addFieldToFilter('SUBSTRING(item_title,14)', array('lt' => $week_end_date))
                        ->addFieldToFilter("status", '1')
                        ->setOrder('SUBSTRING(item_title,14)', 'desc')
                        ->setPageSize($pagesize);

        return $highlights;
    }

    public function getLoginRequired() {
        return Mage::getStoreConfig('gallery/weekcomments/login');
    }

    public function getCommentsEnabled() {
        return Mage::getStoreConfig('gallery/weekcomments/enabled');
    }

    public function getComment($post) {
        $_curPage = Mage::app()->getRequest()->getParam('p') ? Mage::app()->getRequest()->getParam('p') : 1;
        $_perPage = Mage::helper('gallery')->getCommentsPerPage();
        $collection = Mage::getModel('gallery/comment')->getCollection()
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