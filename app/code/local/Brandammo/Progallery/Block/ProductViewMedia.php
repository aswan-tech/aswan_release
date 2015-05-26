<?php
class Brandammo_Progallery_Block_ProductViewMedia extends Mage_Catalog_Block_Product_View_Media
{
   private $_storeId;
   
   public function __construct()
   {
      parent::__construct();
      $this->_storeId = Mage::app()->getStore()->getStoreId();
   }
   
   public function getConfig($key)
   {
      return Mage::getStoreConfig('progallery/progalleryconfig/' . $key, $this->_storeId);
   }
   
   public function getCarouselConfig($key)
   {
      return Mage::getStoreConfig('progallery/thumbscarouselconfig/' . $key, $this->_storeId);
   }
   
   public function getResizeConfig($key)
   {
      return Mage::getStoreConfig('progallery/resizeImage/' . $key, $this->_storeId);
   }
   
   public function getLightboxConfig($key)
   {
      return Mage::getStoreConfig('progallery/lightboxconfig/' . $key, $this->_storeId);
   }
   
   public function getLightboxThumbsBarConfig($key)
   {
      return Mage::getStoreConfig('progallery/lightboxthumbsbarconfig/' . $key, $this->_storeId);
   }
   
   public function getLightboxCarouselConfig($key)
   {
      return Mage::getStoreConfig('progallery/lightboxthumbscarouselconfig/' . $key, $this->_storeId);
   }
   
   public function getImageFilename($src)
   {
      $srcArray = explode('/', $src);
      return array_pop($srcArray);
   }
   
   public function getStoreId()
   {
      return $this->_storeId;
   }
   
}