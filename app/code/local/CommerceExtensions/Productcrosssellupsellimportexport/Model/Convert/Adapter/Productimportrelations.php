<?php
/**
 * Productimportrelations.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Product
 * @package    Productimport
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */ 

class CommerceExtensions_Productcrosssellupsellimportexport_Model_Convert_Adapter_Productimportrelations
extends Mage_Catalog_Model_Convert_Adapter_Product
{
	
	/**
	* Save product (import)
	* 
	* @param array $importData 
	* @throws Mage_Core_Exception
	* @return bool 
	*/
	public function saveRow( array $importData )
	{
		$product = $this->getProductModel()->reset();
		
		if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage::throwException($message);
            }
        }
        else {
            $store = $this->getStoreByCode($importData['store']);
        }
		
		if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage::throwException($message);
		}

		if (empty($importData['sku'])) {
				$message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'sku');
				Mage::throwException($message);
		}
		$product->setStoreId($store->getId());
		$productId = $product->getIdBySku($importData['sku']);
		
		if ($productId) {
            $product->load($productId);
        }
        else {
			$message = Mage::helper('catalog')->__('Skip import row, the "%s" does NOT exist in your current store', 'sku');
			Mage::throwException($message);
        }
		
		if ( isset( $importData['related'] ) ) {
		
			$relatedexploded = explode(':', $importData['related']);
			$link = $product->getLinkInstance()->setLinkTypeId(Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED);
			$collection = $link->getProductCollection()->setIsStrongMode()->setProduct($product);
			$linkIds = array();
			foreach ($collection as $linkedProduct) {
			   $linkIds[$linkedProduct->getId()] = array();
			   foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
				  $linkIds[$linkedProduct->getId()][$attribute['code']] = $linkedProduct->getData($attribute['code']);
				  #$linkIds[$linkedProduct->getId()][$attribute['code']] = "";
			   }
			}
			if(isset($relatedexploded[1])) {
				$linkIds = $this -> skusToIdswithPosition( $importData['related'], $product );
			} else {
				$linkIds = $this -> skusToIds( $importData['related'], $product );
			}
			if ( !empty( $linkIds ) ) {
				$product->setRelatedLinkData($linkIds);
			} 
		
		} 

		if ( isset( $importData['upsell'] ) ) {
		
			$upsellexploded = explode(':', $importData['upsell']);
			$link = $product->getLinkInstance()->setLinkTypeId(Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL);
			$collection = $link->getProductCollection()->setIsStrongMode()->setProduct($product);
			$linkIds = array();
			foreach ($collection as $linkedProduct) {
			   $linkIds[$linkedProduct->getId()] = array();
			   foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
				  $linkIds[$linkedProduct->getId()][$attribute['code']] = $linkedProduct->getData($attribute['code']);
				  #$linkIds[$linkedProduct->getId()][$attribute['code']] = "";
			   }
			}
			if(isset($upsellexploded[1])) {
				$linkIds = $this -> skusToIdswithPosition( $importData['upsell'], $product );
			} else {
				$linkIds = $this -> skusToIds( $importData['upsell'], $product );
			}
			if ( !empty( $linkIds ) ) {
				$product->setUpSellLinkData($linkIds);
			} 
		} 

		if ( isset( $importData['crosssell'] ) ) {
		
			$crosssellexploded = explode(':', $importData['crosssell']);
			$link = $product->getLinkInstance()->setLinkTypeId(Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL);
			$collection = $link->getProductCollection()->setIsStrongMode()->setProduct($product);
			$linkIds = array();
			foreach ($collection as $linkedProduct) {
			   $linkIds[$linkedProduct->getId()] = array();
			   foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
				  $linkIds[$linkedProduct->getId()][$attribute['code']] = $linkedProduct->getData($attribute['code']);
				  #$linkIds[$linkedProduct->getId()][$attribute['code']] = "";
			   }
			}
			if(isset($crosssellexploded[1])) {
				$linkIds = $this -> skusToIdswithPosition( $importData['crosssell'], $product );
			} else {
				$linkIds = $this -> skusToIds( $importData['crosssell'], $product );
			}
			if ( !empty( $linkIds ) ) {
				$product->setCrossSellLinkData($linkIds);
			} 
		} 
		
		#$product -> setIsMassupdate( true );
		#$product -> setExcludeUrlRewrite( true );
		$product -> save();
		
		return true;
	} 
	
	protected function userCSVDataAsArray( $data )
	{
		return explode( ',', trim($data));
		//return explode( ',', $data ); === fix for sku's with spaces
	} 
	
	protected function skusToIds( $userData, $product )
	{
		$productIds = array();
		foreach ( $this -> userCSVDataAsArray( $userData ) as $oneSku ) {
			if ( ( $a_sku = ( int )$product -> getIdBySku( $oneSku ) ) > 0 ) {
				parse_str( "position=", $productIds[$a_sku] );
			} 
		} 
		return $productIds;
	} 
	protected function skusToIdswithPosition( $userData, $product )
	{
		
		$productIds = array();
		foreach ( $this -> userCSVDataAsArray( $userData ) as $oneSku ) {
			
			$oneSkuexploded = explode(':', $oneSku);
			//fix for if export and import has extra , .. was coming back no array so error not definied
			if(isset($oneSkuexploded[1])) {
				if ( ( $a_sku = ( int )$product -> getIdBySku( $oneSkuexploded[1] ) ) > 0 ) {
					parse_str( "position=".$oneSkuexploded[0]."", $productIds[$a_sku] );
				} 
			}
		} 
		return $productIds;
	} 
	protected function _removeFile( $file )
	{
		if ( file_exists( $file ) ) {
		$ext = substr(strrchr($file, '.'), 1);
			if( strlen( $ext ) == 4 ) {
				if ( unlink( $file ) ) {
					return true;
				} 
			}
		} 
		return false;
	} 
	
    protected function _initProduct($productId)
    {
        $product = Mage::getModel('catalog/product');
		if($product->getIdBySku($productId)) {
        	$idBySku = $product->getIdBySku($productId);
		} else {
			Mage::throwException('product2_not_exists');
			return;
		}

        if ($idBySku) {
            $productId = $idBySku;
        }
		
        $product->load($productId);
        if (!$product->getId()) {
            Mage::throwException('product2_not_exists');
        }
        return $product;
    }
}