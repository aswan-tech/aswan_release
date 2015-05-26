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


class AW_Blog_Block_Blog extends AW_Blog_Block_Abstract {

    public function getPosts() {

        $collection = parent::_prepareCollection();

        if ($tag = $this->getRequest()->getParam('tag')) {
            $collection->addTagFilter(urldecode($tag));
        }
		
		$pageSize = (int) Mage::getStoreConfig('blog/blog/perpage');
		
		if ($pageSize != 0) {
			if ($this->getLayout()) {
				$pager = $this->getLayout()
							  ->createBlock('page/html_pager', 'blog.pager');
				
				$pager->setLimit($pageSize);
				
				$pager->setTemplate('aw_blog/pager/list.phtml');		  
				$pager->setCollection($collection);		
							  
				$this->setChild('pager', $pager);
			}
		}

        parent::_processCollection($collection);

        return $collection;
    }

    public function getTagsHtml($post) {

        if (trim($post->getTags())) {
            $this->setTemplate('aw_blog/line_tags.phtml');
            $this->setPost($post);
            return $this->toHtml();
        }
        return;
    }

    public function getPages() {

        echo parent::getPagesCollection('list');
    }

    public function getRecent() {

        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_RECENT_SIZE) != 0) {
            $collection = Mage::getModel('blog/blog')->getCollection()
                    ->addPresentFilter()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->setOrder('created_time ', 'desc');

            $route = Mage::helper('blog')->getRoute();

            Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
            $collection->setPageSize(Mage::getStoreConfig(AW_Blog_Helper_Config::XML_RECENT_SIZE));
            $collection->setCurPage(1);
            foreach ($collection as $item) {
                $item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
            }
            return $collection;
        } else {
            return false;
        }
    }

    public function getCategories() {

        $collection = Mage::getModel('blog/cat')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId(), false)->setOrder('sort_order ', 'asc');
        $route = Mage::helper('blog')->getRoute();

        foreach ($collection as $item) {
            $item->setAddress($this->getUrl($route . "/cat/" . $item->getIdentifier()));
        }
        return $collection;
    }

    protected function _prepareLayout() {

        $route = Mage::helper('blog')->getRoute();
        $isBlogPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'blog';

        // show breadcrumbs
        if ($isBlogPage && Mage::getStoreConfig('blog/blog/blogcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))) {
            //$breadcrumbs->addCrumb('home', array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));
            ;
            if ($tag = @urldecode($this->getRequest()->getParam('tag'))) {
                //$breadcrumbs->addCrumb('blog', array('label' => Mage::getStoreConfig('blog/blog/title'), 'title' => Mage::helper('blog')->__('Return to ' . Mage::getStoreConfig('blog/blog/title')), 'link' => Mage::getUrl($route)));
				$breadcrumbs->addCrumb('blog', array('label' => Mage::helper('blog')->__('Home'), 'title' => Mage::helper('blog')->__('Return to ' . Mage::getStoreConfig('blog/blog/title')), 'link' => Mage::getUrl($route)));
                $breadcrumbs->addCrumb('blog_tag', array('label' => Mage::helper('blog')->__('Tagged with "%s"', Mage::helper('blog')->convertSlashes($tag)), 'title' => Mage::helper('blog')->__('Tagged with "%s"', $tag)));
            } else {
                $breadcrumbs->addCrumb('blog', array('label' => Mage::getStoreConfig('blog/blog/title')));
            }
        }
    }

    public function _toHtml() {
		$contentFormat = Mage::getStoreConfig('blog/blog/contentformat');

		if ($this->getHasLayoutOptions() == 1) {	
			if (!empty($contentFormat)) {
				$template = "aw_blog/". $contentFormat .".phtml";
				$this->setTemplate($template);
			}
		}
		
        if (Mage::helper('blog')->getEnabled()) {
            $isLeft = ($this->getParentBlock() === $this->getLayout()->getBlock('left'));
            $isRight = ($this->getParentBlock() === $this->getLayout()->getBlock('right'));

            $isBlogPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'blog';

            $leftAllowed = ($isBlogPage && Mage::getStoreConfig('blog/menu/left') == 2) || (Mage::getStoreConfig('blog/menu/left') == 1);
            $rightAllowed = ($isBlogPage && Mage::getStoreConfig('blog/menu/right') == 2) || (Mage::getStoreConfig('blog/menu/right') == 1);

            if (!$leftAllowed && $isLeft) {
                return '';
            }
            if (!$rightAllowed && $isRight) {
                return '';
            }
            try {
                if (Mage::getModel('widget/template_filter'))
                    $processor = Mage::getModel('widget/template_filter');
                return $processor->filter(parent::_toHtml());
            } catch (Exception $ex) {
                return parent::_toHtml();
            }
        }
    }
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    } 
}
