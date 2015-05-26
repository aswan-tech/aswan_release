<?php
class Brandammo_Progallery_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function get_product_viewAction()
    {
      $_product = Mage::getModel('catalog/product')->load($_POST['pid']); 
      $galleryImages = $_product->getMediaGalleryImages();
      $viewImageUri = null;
      $viewImageUriBig = null;	   
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width')) {
			$main_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width');
		} else {
			$main_image_resize_width = 395;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height')) {
			$main_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height');
		} else {
			$main_image_resize_height = 413;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_width')) {
			$lightbox_image_resize_width = Mage::getStoreConfig(
			'progallery/resizeImage/lightbox_image_resize_width');
		} else {
			$lightbox_image_resize_width = 1100;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_height')) {
			$lightbox_image_resize_height = Mage::getStoreConfig(
			'progallery/resizeImage/lightbox_image_resize_height');
		} else {
			$lightbox_image_resize_height = 1150;	
	   }
	
      foreach ($galleryImages as $k => $img)
      {
         if ($k == $_POST['clicked_thumb_index'])
         {
			if(isset($_POST['gallery_thumb'])) {				
				$viewImageUri = Mage::helper('catalog/image')->init($_product, 'image', $img->getFile
				())->resize($main_image_resize_width , $main_image_resize_height)->keepFrame(false)->__toString();
				
				$viewImageUriBig = Mage::helper('catalog/image')->init($_product, 'image', $img->getFile
				())->resize($lightbox_image_resize_width, $lightbox_image_resize_height)->keepFrame(false)->__toString();
				
				break;
			} else {			
				$viewImageUri = Mage::helper('catalog/image')->init($_product, 'image', $img->getFile())->resize(
				$lightbox_image_resize_width, $lightbox_image_resize_height)->keepFrame(false)->__toString();
				
				break;		
			}
        }
      }
	  
	  if($viewImageUri) {
			if ($k == $_POST['clicked_thumb_index'])
			{
			if(isset($_POST['gallery_thumb'])) {
				echo json_encode(array('uri' => $viewImageUri, 'uribig' => $viewImageUriBig));
			}  else {
				echo json_encode(array('uri' => $viewImageUri));
			}
			
			} else {
				echo json_encode(array('uri' => $viewImageUri));
			}
	  }
    }
	
	
	public function get_product_view_zoomAction()
    {
      $_product = Mage::getModel('catalog/product')->load($_POST['pid']); 
      $galleryImages = $_product->getMediaGalleryImages();
      $viewImageUri = null;
      $viewImageUriBig = null;	   
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width')) {
			$main_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width');
		} else {
			$main_image_resize_width = 395;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height')) {
			$main_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height');
		} else {
			$main_image_resize_height = 413;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_width')) {
			$lightbox_image_resize_width = Mage::getStoreConfig(
			'progallery/resizeImage/lightbox_image_resize_width');
		} else {
			$lightbox_image_resize_width = 1100;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_height')) {
			$lightbox_image_resize_height = Mage::getStoreConfig(
			'progallery/resizeImage/lightbox_image_resize_height');
		} else {
			$lightbox_image_resize_height = 1150;	
	   }
	
      foreach ($galleryImages as $k => $img)
      {
         if ($k == $_POST['clicked_thumb_index'])
         {
			if(isset($_POST['gallery_thumb'])) {				
				$viewImageUri = Mage::helper('catalog/image')->init($_product, 'image', $img->getFile
				())->resize($main_image_resize_width , $main_image_resize_height)->keepFrame(false)->__toString();
				
				$viewImageUriBig = Mage::helper('catalog/image')->init($_product, 'image', $img->getFile
				())->resize($lightbox_image_resize_width, $lightbox_image_resize_height)->keepFrame(false)->__toString();
				
				break;
			} else {			
				$viewImageUri = Mage::helper('catalog/image')->init($_product, 'image', $img->getFile())->resize(
				$lightbox_image_resize_width, $lightbox_image_resize_height)->keepFrame(false)->__toString();
				
				break;		
			}
        }
      }
	  
	  if($viewImageUri) {
			if ($k == $_POST['clicked_thumb_index'])
			{
			if(isset($_POST['gallery_thumb'])) {
				echo json_encode(array('uri' => $viewImageUri, 'uribig' => $viewImageUriBig));
			}  else {
				$this->getResponse()->setBody($viewImageUri);
				//exit;				
			}
			
			} else {			
				$this->getResponse()->setBody($viewImageUri);
				//exit;
			}
	  }
    }
}