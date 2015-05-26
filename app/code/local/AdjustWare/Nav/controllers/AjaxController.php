<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('b2a95e2ec290706d1cb55cff52dc0cdf'); ?><?php
class AdjustWare_Nav_AjaxController extends Mage_Core_Controller_Front_Action {
    public function categoryAction() {
         $categoryId =(int)$this->getRequest()->getQuery('cat');
         if(!$categoryId && Mage::helper('adjnav')->isCategoryCleared(true) ) {
             $categoryId = (int) $this->getRequest()->getParam('id', false);
         }

        if (!$categoryId) {
            $categoryId = (int) $this->getRequest()->getParam('id', false);
        }

        if (!$categoryId) {
            $categoryId = Mage::getSingleton('catalog/session')->getAdjnavLastCategoryId();
        }

        if (!$categoryId) {
            $this->_forward('noRoute');
            return;
        }

        $category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($categoryId);
        Mage::register('current_category', $category);

        try {
            $this->loadLayout();
        } catch (Varien_Exception $e) {
            if ((NULL !== strpos($e->getMessage(), 'addColumnCountLayoutDepend')) && version_compare(Mage::getVersion(), '1.3.2', '<')) {
            } else {
                throw $e;
            }
        }

        $update = $this->getLayout()->getUpdate();

        if (Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion('>=1.10')) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyHandle($settings->getPageLayout());
            }
        } else {
            Mage::getModel('catalog/design')->applyDesign($category, Mage_Catalog_Model_Design::APPLY_FOR_CATEGORY);
            $this->_applyCustomDesignSettings($category, $update);
            if ($category->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($category->getPageLayout());
            }
        }

        $response = array();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $cache = Mage::app()->getCache();
        $cacheKey = 'LayerBreadCrumbs_' . md5( date('Y-m-d') . 'TASLC' ) . '_' . $categoryId;

        if( $serialData = $cache->load( $cacheKey )) {
            $response['breadcrumbs'] = unserialize( $serialData );
        } else {
            if($categoryId) {
                if(isset($_GET['q'])){
                    $qString = "?q=".$_GET['q']."&order=".$_GET['order']."&no_cache=".$_GET['no_cache']."&p=".$_GET['p'];
                }else{
                    $qString = "?order=".$_GET['order']."&no_cache=".$_GET['no_cache']."&p=".$_GET['p'];
                }
                $parentName = '';
                $cat = Mage::getModel('catalog/category')->load($categoryId);

               if($cat->getLevel() > 2) {
                 $qString = $qString."&cat=".$cat->getParentId();
                 $parentCat = Mage::getModel('catalog/category')->load($cat->getParentId());
                 $parentName = $parentCat->getName();
                 $parentUrl = $parentCat->getUrl();
                 $childName = $cat->getName();
                 $childUrl = $qString."&cat=".$cat->getId();
               } else {
                 $qString = $qString."&cat=".$cat->getId();
                 $childName = $cat->getName();
                 $childUrl = $qString;
               }
                if ($breadcrumbs && $parentName !='') {
                    $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                        ->addCrumb( 'parent', array( 'label' => $parentName, 'title' => $parentName, 'link' =>  $parentUrl ) )
                        ->addCrumb( 'child', array( 'label' => $childName, 'title' => $childName ) );
                } else {
                    $breadcrumbs->addCrumb('home', array( 'label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                        ->addCrumb('child', array( 'label' => $childName, 'title' => $childName ));
                }
               $response['breadcrumbs'] = $this->getLayout()->getBlock('breadcrumbs')->toHtml();
           } else {
                $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) );
                $response['breadcrumbs'] = $this->getLayout()->getBlock('breadcrumbs')->toHtml();
            }
            $cache->save(serialize( $response['breadcrumbs'] ), $cacheKey, array("CATALOG_BREADCRUMBS"), 86400);
        }
        $response['category_name'] = $category->getName();
        $response['products'] = $this->getLayout()->getBlock('products')->toHtml();
        $response['layer']    = $this->getLayout()->getBlock('layer')->toHtml();
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    public function searchAction() {
        $this->loadLayout();
        $response = array();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if(isset($_GET['cat']) && $_GET['cat'] != 'clear') {
            $qString = "?q=".$_GET['q']."#!/q=".$_GET['q']."&order=".$_GET['order']."&no_cache=".$_GET['no_cache']."&p=".$_GET['p'];
            $parentName = '';
            $cat = Mage::getModel('catalog/category')->load($_GET['cat']);

            if($cat->getLevel() > 2) {
                $parentCat = Mage::getModel('catalog/category')->load($cat->getParentId());
                $parentName = $parentCat->getName();
                $parentUrl = $qString."&cat=".$cat->getParentId();
                $childName = $cat->getName();
                $childUrl = $qString."&cat=".$cat->getId();
            } else {
                $qString = $qString."&cat=".$cat->getId();
                $childName = $cat->getName();
                $childUrl = $qString;
            }
            if($_GET['q'] == '439ed537979d8e831561964dbbbd7413'){
                if ($breadcrumbs && $parentName !='') {
                    $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                        ->addCrumb('sale', array( 'label' => 'Clearance', 'title' => 'Clearance', 'link' => '?q=439ed537979d8e831561964dbbbd7413' ))
                        ->addCrumb('parent', array( 'label' => $parentName, 'title' => $parentName, 'link' =>  $parentUrl ))
                        ->addCrumb('child', array( 'label' => $childName, 'title' => $childName, 'link' => $childUrl ));
                }else{
                    $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                        ->addCrumb('sale', array( 'label' => 'Clearance', 'title' => 'Clearance', 'link' => '?q=439ed537979d8e831561964dbbbd7413' ))
                        ->addCrumb('child', array( 'label' => $childName, 'title' => $childName, 'link' => $childUrl ));
                }
                $response['breadcrumbs'] = $this->getLayout()->getBlock('breadcrumbs')->toHtml();
            }else{
                if ($breadcrumbs && $parentName !='') {
                    $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                        ->addCrumb( 'parent', array( 'label' => $parentName, 'title' => $parentName, 'link' =>  $parentUrl ) )
                        ->addCrumb( 'child', array( 'label' => $childName, 'title' => $childName ) );
                }else{
                    $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                        ->addCrumb('child', array( 'label' => $childName, 'title' => $childName ));
                }
                $response['breadcrumbs'] = $this->getLayout()->getBlock('breadcrumbs')->toHtml();
            }
        } else {
            if($_GET['q'] == '439ed537979d8e831561964dbbbd7413') {
                $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                    ->addCrumb('search', array( 'label' => 'Clearance', 'title' => 'Clearance' ));
            }else{
                $breadcrumbs->addCrumb( 'home', array('label' => 'Home', 'title' => 'Go to Home Page', 'link'  => Mage::getBaseUrl() ) )
                    ->addCrumb( 'search', array( 'label' => "Search results for:".$_GET['q'], 'title' => "Search results for:".$_GET['q'] ) );
            }
           $response['breadcrumbs'] = $this->getLayout()->getBlock('breadcrumbs')->toHtml();
        }
        $response['products'] = $this->getLayout()->getBlock('products')->setIsSearchMode()->toHtml();
        $response['layer']    = $this->getLayout()->getBlock('layer')->toHtml();
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    protected function _applyCustomDesignSettings($category, $update) {
        if ($category->getCustomUseParentSettings() && $category->getLevel() > 1) {
            $parentCategory = $category->getParentCategory();
            if ($parentCategory && $parentCategory->getId()) {
                return $this->_applyCustomDesignSettings($parentCategory, $update);
            }
        }
        $validityDate = $category->getCustomDesignDate();
        if (array_key_exists('from', $validityDate) && array_key_exists('to', $validityDate) && Mage::app()->getLocale()->isStoreDateInInterval(null, $validityDate['from'], $validityDate['to'])) {
            if ($category->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyHandle($category->getPageLayout());
            }
            $update->addUpdate($category->getCustomLayoutUpdate());
        }
        return $this;
    }

} }
