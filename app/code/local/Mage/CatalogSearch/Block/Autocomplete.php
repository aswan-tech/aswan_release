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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Autocomplete queries list
 */
class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    protected $_suggestData = null;

    protected function _toHtml()
    {
		$html = '';
		$showSugg = true;
		$showProd = true;
		
        if (!$this->_beforeToHtml()) {
            return $html;
        }
		
		$html .= '<div id="autosuggest"><ul><li style="display:none"></li>';
		$titleSugg = "<li class='head'>Search Suggestions</li>";
		//$titleProd = "<li class='head'>Products</li>";
		
		//Check if products exist for the search text
		$results=$this->getSuggestProducts();
		if(!$results->getSize()) {
			$showProd = false;
			//If product do not exist how can the suggestion be, do not show the suggestion also
			$showSugg = false;
		}
		
        //$suggestData = $this->getSuggestData();
        if (!($count = count($suggestData))) {
			$showSugg = false;
            //return $html;
        }
		
        $count--;
		
		
		if($showSugg){
			$html .= $titleSugg;
			foreach ($suggestData as $index => $item) {
				if ($index == 0) {
					$item['row_class'] .= ' first';
				}

				if ($index == $count) {
					$item['row_class'] .= ' last';
				}
				
				$title = $this->htmlEscape($item['title']);
				
				//Get actual string (case sensitive) matched in product name
				$pos = stripos($title, $this->helper('catalogsearch')->getQueryText());
				$actualStr = substr($title, $pos, strlen($this->helper('catalogsearch')->getQueryText()));
				
				if (strlen($title ) > 48) {
					$title = substr($title, 0, 45).'...';
				}
				
				//$html .=  '<li title="'.$this->htmlEscape($item['title']).'" class="'.$item['row_class'].'">'
				  //  . '<span class="amount">'.$item['num_of_results'].'</span>'. $title .'</li>';
				
				//$html .=  '<li title="'.$item['title'].'" class="'.$item['row_class'].'">'
				  //  . '<span class="amount">'.$item['num_of_results'].'</span>'. str_ireplace($this->helper('catalogsearch')->getQueryText(), '<b>'.$actualStr.'</b>', $title) .'</li>';
				$html .=  '<li title="'.$item['title'].'" class="'.$item['row_class'].'">'
					. str_ireplace($this->helper('catalogsearch')->getQueryText(), '<b>'.$actualStr.'</b>', $title) .'</li>';
			}
		}
		
		##########		Added By Vishal		###############
		if($showProd){
			$html .= $titleProd;
			$counter = 0;
			
			//print "<pre>";
				//print_r($results->getData());
			//print "</pre>";
			
			foreach($results as $pro){
				$row_class = (++$counter)%2 ? 'even' : 'odd';
				
				if ($counter == $results->getSize()) {
					$row_class .= ' last';
				}
				
				$prod = Mage::getModel('catalog/product')->load($pro->getId());
				//print $prod->getName().'====='.$prod->getFinalPrice()."+++++++++++++++";
				
				$theProductBlock = new Mage_Catalog_Block_Product;
				
				//Get actual string (case sensitive) matched in product name
				$prodName = strlen(strip_tags($pro->getName())) > 39 ? substr(strip_tags($pro->getName()), 0, 36).'...' : strip_tags($pro->getName());
				$pos = stripos($prodName, $this->helper('catalogsearch')->getQueryText());
				$actualStr = substr($prodName, $pos, strlen($this->helper('catalogsearch')->getQueryText()));
				
				$pos = stripos($pro->getName(), $this->helper('catalogsearch')->getQueryText());
				$actualStr = substr($pro->getName(), $pos, strlen($this->helper('catalogsearch')->getQueryText()));
				
				$html .=  "<li><a href=\"{$pro->getProductUrl()}\">";
				$html .=  "<img src=\"{$this->helper('catalog/image')->init($pro, 'small_image')->resize(50,50)}\"  />";
				$html .=  str_ireplace($this->helper('catalogsearch')->getQueryText(), '<b>'.strtolower($actualStr).'</b>', ucfirst(strtolower($prodName)));
				
				$description = strlen(strip_tags($pro->getDescription()))>41 ? substr(strip_tags($pro->getDescription()),0,38).'...' : strip_tags($pro->getDescription());
				//$description=wordwrap($description, 30, '<br>', true);
				$description=ucfirst(strtolower($description));
				$html .=  "<div class=\"description\">".$description."</div>";
				$html .=  "<div class=\"description\"><span class='priceBlock'></span> ".Mage::helper('core')->currency($prod->getFinalPrice(),true,false)."</div>";
				//$html .=  "<div class=\"description\"><span class='priceBlock'>Price</span> ".$theProductBlock->getPriceHtml($prod, true)."</div>";
				$html .=  "</a></li>";
			}
		}
		##########		Added By Vishal		###############
		
		
		if(!$showProd && !$showSugg){
			$html .= '<li>Oops! No product found.</li>';
		}
		
        $html.= '</ul></div>';

        return $html;
    }

    public function getSuggestData()
    {
        if (!$this->_suggestData) {
            $collection = $this->helper('catalogsearch')->getSuggestCollection();
            $query = $this->helper('catalogsearch')->getQueryText();
            $counter = 0;
            $data = array();
            foreach ($collection as $item) {
                $_data = array(
                    //'title' => str_ireplace($query, '<b>'.$query.'</b>', $item->getQueryText()),
					'title' => $item->getQueryText(),
                    'row_class' => (++$counter)%2?'odd':'even',
                    'num_of_results' => $item->getNumResults()
                );

                if ($item->getQueryText() == $query) {
                    array_unshift($data, $_data);
                }
                else {
                    $data[] = $_data;
                }
            }
            $this->_suggestData = $data;
        }
        return $this->_suggestData;
    }
	
	##########		Added By Vishal		###############
	public function getSuggestProducts()     
	{
		############ do not delete this code ##########
		//Below code fetches data in the same way as on the search listing page (jumbled words are also matched into the products' data).
		//from the file app\code\core\Mage\CatalogSearch\Model\Layer.php		prepareProductCollection()
		//addSearchFilter	on app\code\core\Mage\CatalogSearch\Model\Resource\Fulltext\Collection.php
		
		$query = Mage::helper('catalogsearch')->getQuery();
        $query->setStoreId(Mage::app()->getStore()->getId());
		
		if ($query->getQueryText() != '') {
			if ($query->getId()) {
				$query->setPopularity($query->getPopularity()+1);
			}
			else {
				$query->setPopularity(1);
			}
			$query->prepare();
			
			if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
			
			//Need to handle with MIN and MAX length query (show errors like here app\code\core\Mage\CatalogSearch\Helper\Data.php	in checkNotes())
			
			$collection = Mage::getResourceModel('catalogsearch/fulltext_collection');
			$collection
				->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				->addSearchFilter($query->getQueryText())
				->setStore(Mage::app()->getStore())
				->addMinimalPrice()
				->addFinalPrice()
				->addTaxPercents()
				->addStoreFilter()
				->addUrlRewrite();
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);
			
			$collection->setOrder('relevance', 'desc');
			//$collection->setPageSize(5);
			
			//Use order by relevance also here.
			//print '==='.$collection->getSelect();//die;
			return $collection;
		}
		//############ do not delete this code ##########
		
		/*
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id', array('lt'=>2810));
		$collection->getSelect('*');
		
		print '==='.$collection->getSelect().'<br><br>';
		foreach($collection as $prod){
			$product = Mage::getResourceModel('catalog/product');
			print $prod->getId().'=='.$product->getAttributeRawValue($prod->getId(), 'color', 0);
			//print '=='.$prod->getProduct()->getResource()->getAttribute('color')->getFrontend()->getValue($prod->getProduct());
			print '<br>';
		}
		
		print "<pre>";
			print_r($collection->getData());
		print "</pre>";
		print __LINE__;die;
		
		
		$query = Mage::helper('catalogsearch')->getQuery();
		$query->setStoreId(Mage::app()->getStore()->getId());

		if ($query->getRedirect()){
			$query->save();
		}else{
			$query->prepare();
		}
		Mage::helper('catalogsearch')->checkNotes();
		
		$results=$query->getResultCollection();//->setPageSize(5);
		$results=Mage::getResourceModel('catalogsearch/search_collection');
		$results->addSearchFilter(Mage::app()->getRequest()->getParam('q'));
		$results->addAttributeToFilter('visibility', array('neq' => 1));
		
		//Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($results);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($results);
		$results->setPageSize(5);
		
		$results->addAttributeToSelect('description');
		$results->addAttributeToSelect('name');
		$results->addAttributeToSelect('thumbnail');
		$results->addAttributeToSelect('small_image');
		$results->addAttributeToSelect('url_key');
		
		//print '==='.$results->getSelect();
		return $results;*/
	}
	##########		Added By Vishal		###############
}