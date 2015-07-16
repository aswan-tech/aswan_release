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


class AW_Blog_Block_Abstract extends Mage_Core_Block_Template {

    protected function _processCollection($collection, $category = false) {

        $route = Mage::helper('blog')->getRoute();

        foreach ($collection as $item) {

            /* Escape tags */
            AW_Blog_Helper_Data::escapeSpecialChars($item);


            if ($category) {
                if (Mage::getStoreConfig('blog/blog/categories_urls')) {
                    $item->setAddress($this->getUrl($route . '/cat/' . $this->getCat()->getIdentifier() . '/post/' . $item->getIdentifier()));
                } else {
                    $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
                }
            } else {
                $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
            }

            $createdTime = Mage::getModel('core/date')->date("jS, F, Y h:i:s A", $item->getCreatedTime());			
			$item->setCreatedTime($createdTime);
			
			$updatedTime = Mage::getModel('core/date')->date("jS, F, Y h:i:s A", $item->getUpdateTime());
            $item->setUpdateTime($updatedTime);

            if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_USESHORTCONTENT) && trim($item->getShortContent())) {
                $content = trim($item->getShortContent());
                $content = $this->closetags($content);
                //$content .= ' <a href="' . $this->getUrl($route . "/" . $item->getIdentifier()) . '" >' . $this->__('Read More') . '</a>';
                $item->setPostContent($content);
            } elseif ((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE) != 0) {

                $content = $item->getPostContent();
                $strManager = new AW_Blog_Helper_Substring(array('input' => Mage::helper('blog')->filterWYS($content)));
                $content = $strManager->getHtmlSubstr((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE));

                if ($strManager->getSymbolsCount() == Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_READMORE)) {
                    //$content .= ' <a href="' . $this->getUrl($route . "/" . $item->getIdentifier()) . '" >' . $this->__('Read More') . '</a>';
                }
                $item->setPostContent($content);
            }


            $comments = Mage::getModel('blog/comment')->getCollection()
                    ->addPostFilter($item->getPostId())
                    ->addApproveFilter(2);
            $item->setCommentCount(count($comments));

            $post = Mage::getModel('blog/post')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($item->getPostId(), 'post_id');

            $item->setCats($post->getCats());
        }


        if ($category) {

            $this->setData('cat', $collection);
            return $this->getData('cat');
        }


        return $collection;
    }

    public function getBookmarkHtml($post) {
        if (Mage::getStoreConfig('blog/blog/bookmarkslist')) {
            $this->setTemplate('aw_blog/bookmark.phtml');
            $this->setPost($post);
            return $this->toHtml();
        }
        return;
    }

    public function getTagsHtml($post) {

        if (trim($post->getTags())) {
            $this->setTemplate('aw_blog/line_tags.phtml');
            $this->setPost($post);
            return $this->toHtml();
        }
        return;
    }

    public function getCommentsEnabled() {
        return Mage::getStoreConfig('blog/comments/enabled');
    }

    public function getPagesCollection($mode, $params = array()) {

        if ((int) Mage::getStoreConfig('blog/blog/perpage') != 0) {

            if ($mode == 'list') {
                $bool = false;
            } else {
                $bool = true;
            }

            $pager = Mage::getConfig()->getBlockClassName('blog/pager');
            $pager = new $pager();
            $pager->setTemplate('aw_blog/pager/list.phtml');
            $pager->setCategoryMode($bool);


            foreach ($params as $key => $param) {
                $pager->{$key}($param);
            }

            return $pager->renderView();
        }
    }

    public function addTopLink() {
        if (Mage::helper('blog')->getEnabled()) {
            $route = Mage::helper('blog')->getRoute();
            $title = Mage::getStoreConfig('blog/blog/title');
            $this->getParentBlock()->addLink($title, $route, $title, true, array(), 15, null, 'class="top-link-blog"');
        }
    }

    public function addFooterLink() {
        if (Mage::helper('blog')->getEnabled()) {
            $route = Mage::helper('blog')->getRoute();
            $title = Mage::getStoreConfig('blog/blog/title');
            $this->getParentBlock()->addLink($title, $route, $title, true);
        }
    }

    public function closetags($html) {
        return Mage::helper('blog/post')->closetags($html);
    }

    protected function _prepareCollection($customFilters = array()) {

        if (!$this->getCachedCollection()) {

            $collection = Mage::getModel('blog/blog')->getCollection()
                    ->addPresentFilter()
                    ->addStoreFilter(Mage::app()->getStore()->getId(), false)
                    ->setOrder('created_time ', 'desc');
			
            if (!empty($customFilters)) {
                foreach ($customFilters as $filter => $value) {
                    $collection->{$filter}($value);
                }
            }

            $page = $this->getRequest()->getParam('page');
            Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
            $collection->setPageSize((int) Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PERPAGE));
            $collection->setCurPage($page);

            $this->setCachedCollection($collection);
        }
		
        return $this->getCachedCollection();
    }

}
