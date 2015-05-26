<?php

/* * *********************************************************
 * Lock Order Observers
 *
 * Module for locking order while editing or credit memo generation.
 *
 * @category    Mage
 * @package     Mage_LockOrder
 * @author	Shikha Raina
 * @company	HCL Technologies
 * @created Monday, July 3, 2012
 * @copyright	Four cross media
 * ******************************************************** */

class FCM_Seo_Model_Observer {

    /**
     *
     * @param <type> $observer
     * @return <type>
     * @desciption After saving order, Relase Lock
     */
    public function productView(Varien_Event_Observer $observer) {
        $product = $observer->getEvent()->getProduct();
        /* @var $product Mage_Catalog_Model_Product */

        if ($product) {

            $currentCategory = Mage::registry('current_category');
            if ($currentCategory) {
                $flag = $currentCategory->getLevel();
                if ($flag != 2) {
                    $parentCategory = $currentCategory->getParentCategory();
                } else {
                    $parentCategory = $currentCategory;
                }
            } else {
                foreach ($product->getCategoryIds() as $cats) {
                    $currentCategory = Mage::getModel('catalog/category')->load($cats);
                    $flag = $currentCategory->getLevel();
                    if ($flag != 2) {
                        $parentCategory = $currentCategory->getParentCategory();
                    } else {
                        $parentCategory = $currentCategory;
                    }
                }
            }

            if ($currentCategory && $parentCategory) {
                $searchArray = array("[Product Name]", "[Department]", "[Sub-Category]");
                $replaceArray = array($product->getName(), $parentCategory->getName(), $currentCategory->getName());
                $title = Mage::getStoreConfig('seo/product_setting/meta_title');
                $keywords = Mage::getStoreConfig('seo/product_setting/meta_keywords');
                $description = Mage::getStoreConfig('seo/product_setting/meta_description');

                // Add the product name
                $title = str_replace($searchArray, $replaceArray, $title);
                $keywords = str_replace($searchArray, $replaceArray, $keywords);
                $description = str_replace($searchArray, $replaceArray, $description);

                if (!$product->getMetaTitle()) {
                    $product->setMetaTitle($title);
                }
                if (!$product->getMetaKeyword()) {
                    $product->setMetaKeyword($keywords);
                }
                if (!$product->getMetaDescription()) {
                    $product->setMetaDescription($description);
                }
            }
        }
    }

    public function categoryView(Varien_Event_Observer $observer) {
        $category = $observer->getEvent()->getCategory();
        $parentCategory = $category->getParentCategory();
        $flag = $category->getLevel();

        if ($flag == 2) {
            $searchArray = array("[Department]", "[Sub-Category] - ", " - [Sub-Category]", "[Sub-Category] ", " [Sub-Category]", "[Sub-Category],", "[Sub-Category]");
            $replaceArray = array($category->getName(), "", "", "", "", "", "");
        } else {
            $searchArray = array("[Department]", "[Sub-Category]");
            $replaceArray = array($parentCategory->getName(), $category->getName());
        }

        if ($category && $parentCategory) {
            $title = Mage::getStoreConfig('seo/category_setting/meta_title');
            $keywords = Mage::getStoreConfig('seo/category_setting/meta_keywords');
            $description = Mage::getStoreConfig('seo/category_setting/meta_description');

            // Add the product name
            $title = str_replace($searchArray, $replaceArray, $title);
            $keywords = str_replace($searchArray, $replaceArray, $keywords);
            $description = str_replace($searchArray, $replaceArray, $description);

            if (!$category->getMetaTitle()) {
                $category->setMetaTitle($title);
            }
            if (!$category->getMetaKeywords()) {
                $category->setMetaKeywords($keywords);
            }
            if (!$category->getMetaDescription()) {
                $category->setMetaDescription($description);
            }
        }
    }

}
