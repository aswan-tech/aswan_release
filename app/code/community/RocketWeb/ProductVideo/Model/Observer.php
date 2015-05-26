<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   RocketWeb
 * @package    RocketWeb_ProductVideo
 * @copyright  Copyright (c) 2011 RocketWeb (http://rocketweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     RocketWeb
 */

class RocketWeb_ProductVideo_Model_Observer
{	
	public function catalog_product_save_after($observer)
    {
        $product = $observer->getProduct();
		
		if($product->getVideos()) {
		
		foreach($product->getVideos() as $video)
		{
            $model = Mage::getModel('productvideo/videos');
			try
			{
				if(isset($video['delete']) && $video['delete'])
                {
                    $model->setId($video['id']);
                    $model->delete();
                }
                else
                {
                    if(isset($video['id']) && $video['id']) $model->setId($video['id']);
                    
                    $model->setProductId($product->getId());
                    $model->setStoreId($product->getStoreId());
                    $model->setVideoCode($video['code']);
                    $model->setVideoTitle($video['title']);
                    $model->setVideoThumbWidth($video['thumb_width']);
                    $model->setVideoThumbHeight($video['thumb_height']);
                    $model->setVideoWidth($video['width']);
                    $model->setVideoHeight($video['height']);
                    
                    $model->save();
                }
			}
			catch (Mage_Core_Exception $e) 
			{
				Mage::setSingleton('adminhtml/session')->addError($e->setMessage());
			}        
			catch (Exception $e) 
			{
				Mage::setSingleton('adminhtml/session')->addError(
					Mage::helper('productvideo')->__('An error occurred while saving the video data. Please review the log and try again.'));
					Mage::logException($e);
			}
		}
	  }
    }
}