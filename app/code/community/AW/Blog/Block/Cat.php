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


class AW_Blog_Block_Cat extends AW_Blog_Block_Abstract {

    public function getPosts() {

        $cats = Mage::getSingleton('blog/cat');

        if ($cats->getCatId() === NULL) {
            return false;
        }

        $collection = parent::_prepareCollection(array('addCatFilter' => $cats->getCatId()));
		
		$pageSize = (int) Mage::getStoreConfig('blog/blog/perpage');
		
		if ($pageSize != 0) {
			$pager = $this->getLayout()
						  ->createBlock('page/html_pager', 'blog.pager');
			
			$pager->setLimit($pageSize);
			
			$pager->setTemplate('aw_blog/pager/list.phtml');		  
			$pager->setCollection($collection);		
						  
			$this->setChild('pager', $pager);
		}
		
        parent::_processCollection($collection, $categoryMode = true);

        return $collection;
    }

    public function getCat() {
        $cats = Mage::getSingleton('blog/cat');
        return $cats;
    }

    public function getPages() {

        echo parent::getPagesCollection('category', array('setCatId' => $this->getCat()->getId()));
    }

    protected function _prepareLayout() {

        $post = $this->getCat();

        $route = Mage::helper('blog')->getRoute();

        // show breadcrumbs
        if (Mage::getStoreConfig('blog/blog/blogcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))) {
            //$breadcrumbs->addCrumb('home', array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));
            ;
            //$breadcrumbs->addCrumb('blog', array('label' => Mage::getStoreConfig('blog/blog/title'), 'title' => Mage::helper('blog')->__('Return to ' . Mage::getStoreConfig('blog/blog/title')), 'link' => Mage::getUrl($route)));
			
			$breadcrumbs->addCrumb('blog',  array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Return to ' . Mage::getStoreConfig('blog/blog/title')), 'link' => Mage::getUrl($route)));
			
            $breadcrumbs->addCrumb('blog_page', array('label' => $post->getTitle(), 'title' => $post->getTitle()));
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($post->getTitle());
            $head->setKeywords($post->getMetaKeywords());
            $head->setDescription($post->getMetaDescription());
        }
    }

    protected function _toHtml() {
        return Mage::helper('blog')->filterWYS(parent::_toHtml());
    }

	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    } 
}
