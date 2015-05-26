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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Navigation extends Mage_Core_Block_Template {

    protected $_categoryInstance = null;
    /**
     * Current category key
     *
     * @var string
     */
    protected $_currentCategoryKey;
    /**
     * Array of level position counters
     *
     * @var array
     */
    protected $_itemLevelPositions = array();

    protected function _construct() {
        $this->addData(array(
            'cache_lifetime' => false,
            'cache_tags' => array(Mage_Catalog_Model_Category::CACHE_TAG, Mage_Core_Model_Store_Group::CACHE_TAG),
        ));
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo() {
        $shortCacheId = array(
            'CATALOG_NAVIGATION',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrenCategoryKey()
        );
        $cacheId = $shortCacheId;

        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['category_path'] = $this->getCurrenCategoryKey();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    /**
     * Get current category key
     *
     * @return mixed
     */
    public function getCurrenCategoryKey() {
        if (!$this->_currentCategoryKey) {
            $category = Mage::registry('current_category');
            if ($category) {
                $this->_currentCategoryKey = $category->getPath();
            } else {
                $this->_currentCategoryKey = Mage::app()->getStore()->getRootCategoryId();
            }
        }

        return $this->_currentCategoryKey;
    }

    /**
     * Get catagories of current store
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getStoreCategories() {
        $helper = Mage::helper('catalog/category');
        return $helper->getStoreCategories();
    }

    /**
     * Retrieve child categories of current category
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getCurrentChildCategories() {
        $layer = Mage::getSingleton('catalog/layer');

        if (strtolower($this->getCurrentCategory()->getName()) == "new arrivals" || strtolower(
                        $this->getCurrentCategory()->getCategoryCode()) == "new arrivals") {

            $catid = $this->getCurrentCategory()->getParentId();
            if ($catid) {
                $category = Mage::getModel('catalog/category')->load($catid);
            } else {
                $category = $layer->getCurrentCategory();
            }
        } else {
            $category = $layer->getCurrentCategory();
        }

        /* if($this->getRequest()->getParam('id')) {
          $layer = Mage::getSingleton('catalog/layer');
          $catid = $this->getRequest()->getParam('id');
          $category = Mage::getModel('catalog/category')->load($catid);
          } else {
          $layer = Mage::getSingleton('catalog/layer');
          $category  = $layer->getCurrentCategory();
          } */

        /* @var $category Mage_Catalog_Model_Category */

        $categories = $category->getChildrenCategories();
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($categories);
        return $categories;
    }

	public function getCurrentChildCategoriesCustom()
    {
        $layer = Mage::getSingleton('catalog/layer');

        if (strtolower($this->getCurrentCategory()->getName()) == "new arrivals" || strtolower(
                        $this->getCurrentCategory()->getCategoryCode()) == "new arrivals") {

            $catid = $this->getCurrentCategory()->getParentId();
            if ($catid) {
                $category = Mage::getModel('catalog/category')->load($catid);
            } else {
                $category = $layer->getCurrentCategory();
            }
        } else {
            $category = $layer->getCurrentCategory();
        }

        /* @var $category Mage_Catalog_Model_Category */

        $categories = $category->getChildrenCategories();
        return $categories;
    }
	
    /**
     * Retrieve child categories of parent category
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getParentChildCategories($param_id='0') {
        $layer = Mage::getSingleton('catalog/layer');
        $category = $category = Mage::getModel('catalog/category')->load($param_id);

        /* if($this->getRequest()->getParam('id')) {
          $layer = Mage::getSingleton('catalog/layer');
          $catid = $this->getRequest()->getParam('id');
          $category = Mage::getModel('catalog/category')->load($catid);
          } else {
          $layer = Mage::getSingleton('catalog/layer');
          $category  = $layer->getCurrentCategory();
          } */

        /* @var $category Mage_Catalog_Model_Category */

        $categories = $category->getChildrenCategories();
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($categories);
        return $categories;
    }
	
	public function getParentChildCategoriesCustom($param_id='0') {
        $layer = Mage::getSingleton('catalog/layer');
        $category = $category = Mage::getModel('catalog/category')->load($param_id);

        /* if($this->getRequest()->getParam('id')) {
          $layer = Mage::getSingleton('catalog/layer');
          $catid = $this->getRequest()->getParam('id');
          $category = Mage::getModel('catalog/category')->load($catid);
          } else {
          $layer = Mage::getSingleton('catalog/layer');
          $category  = $layer->getCurrentCategory();
          } */

        /* @var $category Mage_Catalog_Model_Category */

        $categories = $category->getChildrenCategories();
        //$productCollection = Mage::getResourceModel('catalog/product_collection');
        //$layer->prepareProductCollection($productCollection);
        //$productCollection->addCountToCategories($categories);
        return $categories;
    }

    /**
     * Checkin activity of category
     *
     * @param   Varien_Object $category
     * @return  bool
     */
    public function isCategoryActive($category) {
        if ($this->getCurrentCategory()) {
            return in_array($category->getId(), $this->getCurrentCategory()->getPathIds());
        }
        return false;
    }

    protected function _getCategoryInstance() {
        if (is_null($this->_categoryInstance)) {
            $this->_categoryInstance = Mage::getModel('catalog/category');
        }
        return $this->_categoryInstance;
    }

    /**
     * Get url for category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category) {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $url = $category->getUrl();
        } else {
            $url = $this->_getCategoryInstance()
                            ->setData($category->getData())
                            ->getUrl();
        }

        return $url;
    }

    /**
     * Return item position representation in menu tree
     *
     * @param int $level
     * @return string
     */
    protected function _getItemPosition($level) {
        if ($level == 0) {
            $zeroLevelPosition = isset($this->_itemLevelPositions[$level]) ? $this->_itemLevelPositions[$level] + 1 : 1;
            $this->_itemLevelPositions = array();
            $this->_itemLevelPositions[$level] = $zeroLevelPosition;
        } elseif (isset($this->_itemLevelPositions[$level])) {
            $this->_itemLevelPositions[$level]++;
        } else {
            $this->_itemLevelPositions[$level] = 1;
        }

        $position = array();
        for ($i = 0; $i <= $level; $i++) {
            if (isset($this->_itemLevelPositions[$i])) {
                $position[] = $this->_itemLevelPositions[$i];
            }
        }
        return implode('-', $position);
    }

    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false, $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false) {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array) $category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'level' . $level;
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="' . $outermostItemClass . '"';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
            $attributes['onmouseover'] = 'toggleMenu(this,1)';
            $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;

        $html[] = '<a href="' . $this->getCategoryUrl($category) . '"' . $linkClass . '>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';

        // render children
        $htmlChildren = '';
        $j = 0;

        $sub_categories = Mage::getModel('catalog/category')->load($category->getEntityId())->getChildrenCategories();
        $ctr = 1;

        $htmlChildren .= '<span class="groupArrow">';

        foreach ($sub_categories as $child) {



            $htmlChildren.= '<li>';
            $htmlChildren.= '<a href="' . $this->getCategoryUrl($child) . '" >';
            $htmlChildren.= '<span>' . $this->escapeHtml($child->getName()) . '</span>';
            $htmlChildren.= '</a>';
            $htmlChildren.= '</li>';

            if ($ctr % 7 == 0) {
                echo $ctr . "<br>";
                $htmlChildren.='</span>';
                $htmlChildren.= '<span class="groupArrow">';
            }
            $ctr++;
        }

        /* $htmlChildren.= '<li>';
          $htmlChildren.= '<a href="'.$this->getBaseUrl().'arrivals/?id='.urlencode($category->getEntityId()).'" >';
          $htmlChildren.= '<span>New Arrivals</span>';
          $htmlChildren.= '</a>';
          $htmlChildren.= '</li>'; */

        $htmlChildren.= '</span>';

        /* foreach ($activeChildren as $child) {
          $htmlChildren .= $this->_renderCategoryMenuItemHtml(
          $child,
          ($level + 1),
          ($j == $activeChildrenCount - 1),
          ($j == 0),
          false,
          $outermostItemClass,
          $childrenWrapClass,
          $noEventAttributes
          );
          $j++;
          } */



        if (!empty($htmlChildren)) {
            if ($childrenWrapClass) {
                $html[] = '<div class="' . $childrenWrapClass . '">';
            }

            $Category = Mage::getModel("catalog/category")->load($category->getEntityId());

            if ($Category->getImageUrl()) {
                $imageUrl = '<img src=' . $Category->getImageUrl() . ' class="nav_image">';
            }

            if ($Category->getThumbnail()) {
                $imageUrl = '<img src=' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/category/' . $Category->
                                getThumbnail() . ' class="nav_image">';
            } else {
                $imageUrl = '<img src=' . $this->getSkinUrl('images/noimage.jpg') . ' class="nav_image">';
            }

            $html[] = '<ul class="level' . $level . '">';
            $html[] = $htmlChildren;
            $html[] = '<span class="groupArrow2">';
            $html[] = '<li class="nav_img">' . $imageUrl . '</li>';
            $html[] = '</span>';
            $html[] = '</ul>';
            if ($childrenWrapClass) {
                $html[] = '</div>';
            }
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }

    /**
     * Render category to html
     *
     * @deprecated deprecated after 1.4
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @return string
     */
    public function drawItem($category, $level = 0, $last = false) {
        return $this->_renderCategoryMenuItemHtml($category, $level, $last);
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory() {
        if (Mage::getSingleton('catalog/layer')) {
            return Mage::getSingleton('catalog/layer')->getCurrentCategory();
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getCurrentCategoryPath() {
        if ($this->getCurrentCategory()) {
            return explode(',', $this->getCurrentCategory()->getPathInStore());
        }
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function drawOpenCategoryItem($category) {
        $html = '';
        if (!$category->getIsActive()) {
            return $html;
        }

        $html.= '<li';

        if ($this->isCategoryActive($category)) {
            $html.= ' class="active"';
        }

        $html.= '>' . "\n";
        $html.= '<a href="' . $this->getCategoryUrl($category) . '"><span>' . $this->htmlEscape($category->getName()) . '</span></a>' . "\n";

        if (in_array($category->getId(), $this->getCurrentCategoryPath())) {
            $children = $category->getChildren();
            $hasChildren = $children && $children->count();

            if ($hasChildren) {
                $htmlChildren = '';
                foreach ($children as $child) {
                    $htmlChildren.= $this->drawOpenCategoryItem($child);
                }

                if (!empty($htmlChildren)) {
                    $html.= '<ul>' . "\n"
                            . $htmlChildren
                            . '</ul>';
                }
            }
        }
        $html.= '</li>' . "\n";
        return $html;
    }

    /**
     * Render categories menu in HTML
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '', $ilevel=0) {
        $activeCategories = array();
        $i = 0;
        foreach ($this->getStoreCategories() as $child) {
            if ($i < $ilevel) {
                if ($child->getIsActive()) {
                    $activeCategories[] = $child;
                }
                $i++;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryMenuItemHtml(
                            $category,
                            $level,
                            ($j == $activeCategoriesCount - 1),
                            ($j == 0),
                            true,
                            $outermostItemClass,
                            $childrenWrapClass,
                            true
            );
            $j++;
        }
        // $html.= $this->createGalleryMenu($j);
        return $html;
    }

    public function createGalleryMenu($j='0') {

        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '">
					<span>SALE</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';
        if (strstr($_SERVER['REQUEST_URI'], 'gallery')) {
            $class_gallery = "active";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'productsale')) {
            $class_sale = 'active';
        } else {
            $class_sale = '';
        }
        $menu = array();
        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_gallery . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'gallery">
					<span>TRENDS</span></a>';
        $menu[] = '<ul class="level0">
			<li class="level1 nav-' . $j . '-1 first">
			<a href="' . $this->getBaseURL() . 'gallery" class="">
			<span>Top 25 Trend</span></a></li>
			<li class="level1 nav-' . $j . '-2 first">
			<a href="' . $this->getBaseURL() . 'gallery/day" class="">
			<span>Look of the Day</span></a></li>
			<li class="level1 nav-' . $j . '-3 parent">
			<a href="' . $this->getBaseURL() . 'gallery/month" class="">
			<span>Look of the Week</span>
			</a></ul></li>';

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_blog . '">';
        $menu[] = '<a class="level-top" href="' . $this->getBaseURL() . 'blog">
					<span>BLOG</span></a></li>';

        $url = Mage::getModel('core/url');
        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */
        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        $menu[] = '<li class="level0 nav-' . ++$j . ' level-top last parent ' . $class_sale . '">';
        $menu[] = '<a class="level-top" href="' . $saleUrl . '" title="Coming Soon">
					<span>Clearance</span></a></li>';

        $menu = implode("\n", $menu);
        return $menu;
    }

    /*
     * This function is made by atul to fetch Top Menu categories
     */

    public function getTopMenuCategoryHtml() {
        $menuHtml = '';
        $activeCategories = array();
        $activeSubCategories = array();
        $sub_ctr = 1;
        $main_cat = 1;
        $class = '';

        $url = Mage::getModel('core/url');

        /*
         * url encoding will be done in Url.php http_build_query
         * so no need to explicitly called urlencode for the text
         */

        $url->setQueryParam('q', '439ed537979d8e831561964dbbbd7413');
        $saleUrl = $url->getUrl('catalogsearch/result');

        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }

        if (count($activeCategories) < 1) {
            return '';
        }

        $menuHtml .= '<div class="topnavigation" style="position:relative; *position:inherit;"><ul>';

        // $menuHtml .='<li id="home"><a class="topcat_space" href="'.$this->getUrl().'"></a></li>';

        $clearanceCategory = "";
		
		$cat_array = array();
		
		foreach ($activeCategories as $category) {
            if (strtolower($category->getName()) != 'clearance') {
				$cat_array[] = $category;
                $menuHtml .= '<li id="' . str_replace(" ", "", strtolower($this->escapeHtml($category->getName()))) . '">';
                if ($this->isCategoryActive($category)) {
                    $class = 'class="active_menu"';
                } else {
                    $class = 'class=""';
                }
                $menuHtml .= '<a ' . $class . ' id="megaanchor' . $main_cat . '" href="' . $this->getCategoryUrl($category) . '">' . $this->escapeHtml($category->getName()) . '</a></li>';
                $main_cat++;
            } else {
				$clearanceCategory .= '<li id="' . str_replace(" ", "", strtolower($this->escapeHtml($category->getName()))) . '">';
                if ($this->isCategoryActive($category)) {
                    $class = 'class="active_menu"';
                } else {
                    $class = 'class=""';
                }

                $clearanceCategory .= '<a ' . $class . ' id="" href="' . $saleUrl . '"  title="Clearance">' . $this->escapeHtml($category->getName()) . '</a></li>';
			}
			
            $class = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'album')) {
            $class_gallery = "active_menu";
        } else {
            $class_gallery = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'blog')) {
            $class_blog = 'active_menu';
        } else {
            $class_blog = '';
        }

        if (strstr($_SERVER['REQUEST_URI'], 'result')) {
            $class_sale = '';
        } else {
            $class_sale = '';
        }


        $menuHtml .= '<li>';
        $menuHtml .= '<a class="' . $class_gallery . '" href="' . $this->getBaseURL() . 'gallery"><span>TRENDS</span></a></li>';

        $menuHtml .= '<li>';
        $menuHtml .= '<a class="' . $class_blog . '" href="' . $this->getBaseURL() . 'blog"><span>BlOG</span></a></li>';

        
		if (!empty($clearanceCategory)) {
			$menuHtml .= $clearanceCategory;
		}
		
        $menuHtml .= '</ul></div><div class="clear"></div>';

        foreach ($cat_array as $category_obj) {
			$_entity_id = $category_obj->getEntityId();
			
            $category_obj = Mage::getModel("catalog/category")->load($_entity_id);

            if ($category_obj->getThumbnail()) {
                $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/category/' . $category_obj->getThumbnail();
            } else {
                $imageUrl = $this->getSkinUrl('images/noimage.jpg');
            }

			$menuHtml .= '<div id="megamenu' . $sub_ctr . '" class="megamenu">';


            $cat_url = $category_obj->getUrlKey();
            $cat_name = $category_obj->getName();

            if (!empty($cat_url)) {
                $catUrl = $cat_url;
            } else {
                $catUrl = $cat_name;
            }

            if ($catUrl == 'clearance') {
				//Clearance Menu
				$menuHtml .= $this->getClearanceMenu($category_obj);
            } else {
				//Sub Categories Menu
                $menuHtml .='<div class="cat_img"><img class="lazy" src="' .$this->getSkinUrl('images/grey.gif').'" data-original="'.$imageUrl . '" alt="' . $category_obj->getName() . '" width="230" height="360"></div>';
                $menuHtml .='<div class="shCat"><div id="cat_top">';

                $catNewUrl = strtolower($catUrl) . '/new-arrivals.html';

                //$sub_categories = Mage::getModel('catalog/category')->load($v)->getChildrenCategories();
				//Get Child Categories
				$sub_categories = $category_obj->getChildrenCategories();
				
				//Get the look & New Arrivals link begins
				
				$cond = false;
				$cond2 = false;
				
				foreach ($sub_categories as $_category) {
					if (strtolower($_category->getName()) == 'get the look' && $_category->getIsActive()) {
						$cond = true;
					}
					
					if (strtolower($_category->getName()) == 'new arrivals' && $_category->getIsActive()) {
						$cond2 = true;
					}
				}
				
				if (strtolower($category_obj->getName()) == 'men' || strtolower($category_obj->getName()) == 'women') {
                    if ($cond) {
                        $catLookUrl = strtolower($catUrl) . '/get-the-look.html';
                        $menuHtml .='<span class="menu_head"><a href="' . $this->getBaseUrl() . $catLookUrl . '">Get the Look</a></span>';
                    }
				}
				
				if ($cond2) {
					$menuHtml .='<span class="menu_head"><a href="' . $this->getBaseUrl() . $catNewUrl . '">New Arrivals</a></span>';
				}
                
                $menuHtml .='</div>';

				//Get the look link ends
				
                //$menuHtml .='<div class="nav_menu"><span class="menu_head_blue">Shop</span>';
                $menuHtml .='<div class="nav_menu"><div>';

				$activeSubCategories = array();
                foreach ($sub_categories as $subchild) {
                    if ($subchild->getIsActive()) {
                        $activeSubCategories[] = $subchild;
                    }
                }

                if (count($activeSubCategories) < 1) {
                    return '';
                }

                $menuHtml .='<ul id="cat2">';
                $ctr_loop = 0;
                $count_total = count($sub_categories);
				$show_more = false;
                foreach ($activeSubCategories as $_category) {
                    $inner_cat = 1;
                    if (strtolower($_category->getName()) != 'new arrivals') {
                        if (strtolower($_category->getName()) != 'get the look') {
							if($ctr_loop < 16){
								$catName = $_category->getName();
								if(strlen($catName) > 40){
									$catName = substr($catName, 0, 40).'...'; 
								}
								$menuHtml .='<li><a title="'.$_category->getName().'" href="' . $this->getCategoryUrl($_category) . '">' . $this->escapeHtml($catName) . '</a></li>';
								$ctr_loop++;
							}else{
								$show_more = true;
								break;
							}
                        }
                    }
                    $menuHtml .='</ul><ul id="cat2">';
                }
				/* More link + recommended products */
				$menuHtml .='</div>';
				if($show_more){
					$menuHtml .='<div class="more_link"><a href="'.$this->getCategoryUrl($category_obj).'">+ More</a></div>';
				}
				
				if($ctr_loop >= 1){
					try{
						$menuHtml .= $this->getRecommendedCategoryProducts($category_obj);
					}catch(Exception $e){
						//die silently
					}
				}
                $menuHtml .='</div></div><div class="clear"></div></div>';
            }
			$sub_ctr++;
        }
		
        return $menuHtml;
    }
	
	private function getClearanceMenu($category_object) 
	{
		$menuHtml = "";
		$menuHtml .='<div class="cat_img clearance-cat-image" style="width:490px;"><img class="lazy" src="' .$this->getSkinUrl('images/grey.gif').'" data-original="'.$imageUrl . '" alt="' . $category_object->getName() . '" width="442" height="356"></div>';
		$menuHtml .='<div class="shCat clearance-cat-top" style="width:490px;"><div id="cat_top" style="min-height:44px;width:271px;"></div>';

		//$menuHtml .='<div class="nav_menu"><span class="menu_head_blue">Shop</span>';
		$menuHtml .='<div class="nav_menu">';

		$Table = Mage::getSingleton('core/resource')->getTableName('saledepartments');

		$select = "SELECT department_id,department_url FROM $Table";
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$result = $read->fetchAll($select);
		if (count($result) > 0) {
			foreach ($result as $subchild) {
				if (!empty($subchild['department_id']) && !empty($subchild['department_url'])) {
					$salearray[$subchild['department_id']] = $subchild['department_url'];
				}
			}
			if (count($salearray) < 1) {
				return '';
			}

			$menuHtml .='<ul id="cat2">';
			$ctr_loop = 1;
			$count_total = count($salearray);
			foreach ($salearray as $key => $value) {
				$_category = Mage::getModel('catalog/category')->load($key);
				$inner_cat = 1;

				$menuHtml .='<li><a href="' . $value . '">' . $this->escapeHtml($_category->getName()) . '</a></li>';

				if ($ctr_loop % ceil($count_total / 2) == 0) {
					$menuHtml .='</ul><ul id="cat2">';
				}
				$ctr_loop++;
			}
		}
		//$sub_ctr++;
		$menuHtml .='</div></div><div class="clear"></div></div>';
		
		return $menuHtml;
	}
	
	private function getSubCategoryMenu($Category) 
	{
	
	}

	private function getRecommendedCategoryProducts($category_object) 
	{
		$menuHtml = "";
		
		$pCollection = Mage::getModel('catalog/product')->getCollection();
		$pCollection->addCategoryFilter($category_object); 
		$pCollection->addAttributeToSelect(array('name', 'url_path', 'url_key',  'price', 'image', 'special_from_date', 'special_to_date', 'special_price', 'thumbnail', 'small_image')); 
		$pCollection->addFieldToFilter('status', 1)->addFieldToFilter('visibility', 4)->addAttributeToFilter('show_in_megamenu',1);
		$pCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
		$pCollection->setPage(1, 4)->load();
		
		if(count($pCollection->getData()) > 0){
			$menuHtml .='<div class="recomended_band">We Recommend</div>';
			$menuHtml .='<div class="recomendedProduct"> <ul>';
			
			foreach($pCollection as $p) {
				$product = Mage::getModel('catalog/product')->load($p->getId());
				/* $url = (!is_null( $p->getUrlPath($Category))) ?  Mage::getBaseUrl() . $p->getUrlPath($Category) : $p->getProductUrl(); */
				
				$url = $p->getProductUrl();
				
				//echo $url = Mage::getBaseUrl() . $p->getUrlPath($Category);
				$name = $this->htmlEscape($p->getName());
				if(strlen($name) > 15){
					$name = substr($name, 0, 15).'...'; 
				}

				$defPrice = $p->getPrice();
				$specialprice = 0;
				$_taxHelper  = Mage::helper('tax');
				$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
				$currentDate = date("Y-m-d h:m:s", $currentDate);

				$specialToDate = $p->getSpecialToDate();
				$specialFromDate = $p->getSpecialFromDate();

				if ( ($currentDate >= $specialFromDate && $currentDate < $specialToDate || $specialToDate == "") && $p->getSpecialPrice() != 0 ){
					$price = $p->getFinalPrice();
					$specialprice = $p->getSpecialPrice();
				} else {
					$price = $p->getFinalPrice();
				}
				
				$price = $_taxHelper->getPrice($p, $price, true);
				if ($specialprice != 0 && (int)$specialprice <= (int)$price) { 
					$specialprice = $_taxHelper->getPrice($p, $p->getSpecialPrice(), true);
					$price = $specialprice;
				}
				
				$menuHtml .='<li>';
				//$menuHtml .='<a href="'.$url .'"><img src="'.$p->getImageUrl().'" width="112" height="117"></a>';
				
				$menuHtml .='<a href="'.$url .'">
				<img data-original="'.Mage::helper('catalog/image')->init($product, 'image',
				$p->getFile())->resize(112,112)->keepFrame(false).'" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/grey.gif" width="112" 
				height="112" class="lazy"></a>';
				
				$menuHtml .='<p><a href="'.$url .'">'.$name.'</a><br>';
				
				if(!((int)$price < (int)$defPrice)){
					//$menuHtml .='<span class="new_price"><span class="WebRupee">`</span>'. number_format($price,2) .'</span>';
					$menuHtml .='<span class="new_price">'. Mage::helper('common')->currency($price) .'</span>';
				}else{
					//$menuHtml .='<span class="strike"><span class="WebRupee">`</span>'. number_format($defPrice,2). '</span>';
					//$menuHtml .='&nbsp;<span class="new_price"><span class="WebRupee">`</span>'.number_format($price,2) .'</span>';
					$menuHtml .='<span class="strike">'. Mage::helper('common')->currency($defPrice). '</span>';
					$menuHtml .='&nbsp;<span class="new_price">'.Mage::helper('common')->currency($price) .'</span>';
				}
				
				$menuHtml .='</p>';
				$menuHtml .='</li>';
			}
		
			$menuHtml .='</ul></div>';
		/* More link + recommended products */
		}
		
		return $menuHtml;
	}
}