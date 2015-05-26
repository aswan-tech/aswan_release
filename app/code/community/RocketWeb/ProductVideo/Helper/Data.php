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

class RocketWeb_ProductVideo_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_DEFAULT_THUMB_WIDTH     = 'rocketweb_productvideo/settings/default_thumb_width';
	const XML_PATH_DEFAULT_THUMB_HEIGHT    = 'rocketweb_productvideo/settings/default_thumb_height';
	const XML_PATH_DEFAULT_WIDTH           = 'rocketweb_productvideo/settings/default_width';
	const XML_PATH_DEFAULT_HEIGHT          = 'rocketweb_productvideo/settings/default_height';
	const XML_PATH_PADDING                 = 'rocketweb_productvideo/settings/padding';
	const XML_PATH_MARGIN                  = 'rocketweb_productvideo/settings/margin';
	const XML_PATH_OPACITY                 = 'rocketweb_productvideo/settings/opacity';
	const XML_PATH_MODAL                   = 'rocketweb_productvideo/settings/modal';
	const XML_PATH_CYCLIC                  = 'rocketweb_productvideo/settings/cyclic';
	const XML_PATH_SCROLLING               = 'rocketweb_productvideo/settings/scrolling';
	const XML_PATH_AUTO_SCALE              = 'rocketweb_productvideo/settings/auto_scale';
	const XML_PATH_AUTO_DIMENSIONS         = 'rocketweb_productvideo/settings/auto_dimensions';
	const XML_PATH_CENTER_ON_SCROLL        = 'rocketweb_productvideo/settings/center_on_scroll';
	const XML_PATH_HIDE_ON_OVERLAY_CLICK   = 'rocketweb_productvideo/settings/hide_on_overlay_click';
	const XML_PATH_HIDE_ON_CONTENT_CLICK   = 'rocketweb_productvideo/settings/hide_on_content_click';
	const XML_PATH_OVERLAY_SHOW            = 'rocketweb_productvideo/settings/overlay_show';
	const XML_PATH_OVERLAY_OPACITY         = 'rocketweb_productvideo/settings/overlay_opacity';
	const XML_PATH_OVERLAY_COLOR           = 'rocketweb_productvideo/settings/overlay_color';
	const XML_PATH_TITLE_SHOW              = 'rocketweb_productvideo/settings/title_show';
	const XML_PATH_TITLE_POSITION          = 'rocketweb_productvideo/settings/title_position';
	const XML_PATH_TITLE_FORMAT            = 'rocketweb_productvideo/settings/title_format';
	const XML_PATH_TRANSITION_IN           = 'rocketweb_productvideo/settings/transition_in';
	const XML_PATH_TRANSITION_OUT          = 'rocketweb_productvideo/settings/transition_out';
	const XML_PATH_SPEED_IN                = 'rocketweb_productvideo/settings/speed_in';
	const XML_PATH_SPEED_OUT               = 'rocketweb_productvideo/settings/speed_out';
	const XML_PATH_CHANGE_SPEED            = 'rocketweb_productvideo/settings/change_speed';
	const XML_PATH_CHANGE_FADE             = 'rocketweb_productvideo/settings/change_fade';
	CONST XML_PATH_EASING_IN               = 'rocketweb_productvideo/settings/easing_in';
	CONST XML_PATH_EASING_OUT              = 'rocketweb_productvideo/settings/easing_out';
	CONST XML_PATH_SHOW_CLOSE_BUTTON       = 'rocketweb_productvideo/settings/show_close_button';
	CONST XML_PATH_SHOW_NAV_ARROWS         = 'rocketweb_productvideo/settings/show_nav_arrows';
	CONST XML_PATH_ENABLE_ESCAPE_BUTTON    = 'rocketweb_productvideo/settings/enable_escape_button';
	CONST XML_PATH_ALLOW_FULLSCREEN        = 'rocketweb_productvideo/settings/allow_fullscreen';
	
	public function getDefaultThumbWidth()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_THUMB_WIDTH);
	}
	
	public function getDefaultThumbHeight()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_THUMB_HEIGHT);
	}
	
	public function getDefaultWidth()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_WIDTH);
	}
	
	public function getDefaultHeight()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_HEIGHT);
	}
	
	public function getPadding()
	{
		return Mage::getStoreConfig(self::XML_PATH_PADDING);
	}
	
	public function getMargin()
	{
		return Mage::getStoreConfig(self::XML_PATH_MARGIN);
	}
	
	public function getOpacity()
	{
		return Mage::getStoreConfig(self::XML_PATH_OPACITY);
	}
	
	public function getModal()
	{
		return Mage::getStoreConfig(self::XML_PATH_MODAL);
	}
	
	public function getCyclic()
	{
		return Mage::getStoreConfig(self::XML_PATH_CYCLIC);
	}
	
	public function getScrolling()
	{
		return Mage::getStoreConfig(self::XML_PATH_SCROLLING);
	}
	
	public function getAutoScale()
	{
		return Mage::getStoreConfig(self::XML_PATH_AUTO_SCALE);
	}
	
	public function getAutoDimensions()
	{
		return Mage::getStoreConfig(self::XML_PATH_AUTO_DIMENSIONS);
	}
	
	public function getCenterOnScroll()
	{
		return Mage::getStoreConfig(self::XML_PATH_CENTER_ON_SCROLL);
	}
	
	public function getHideOnOverlayClick()
	{
		return Mage::getStoreConfig(self::XML_PATH_HIDE_ON_OVERLAY_CLICK);
	}
	
	public function getHideOnContentClick()
	{
		return Mage::getStoreConfig(self::XML_PATH_HIDE_ON_CONTENT_CLICK);
	}
	
	public function getOverlayShow()
	{
		return Mage::getStoreConfig(self::XML_PATH_OVERLAY_SHOW);
	}
	
	public function getOverlayOpacity()
	{
		return Mage::getStoreConfig(self::XML_PATH_OVERLAY_OPACITY);
	}
	
	public function getOverlayColor()
	{
		return Mage::getStoreConfig(self::XML_PATH_OVERLAY_COLOR);
	}
	
	public function getTitleShow()
	{
		return Mage::getStoreConfig(self::XML_PATH_TITLE_SHOW);
	}
	
	public function getTitlePosition()
	{
		return Mage::getStoreConfig(self::XML_PATH_TITLE_POSITION);
	}
	
	public function getTitleFormat()
	{
		return Mage::getStoreConfig(self::XML_PATH_TITLE_FORMAT);
	}
	
	public function getTransitionIn()
	{
		return Mage::getStoreConfig(self::XML_PATH_TRANSITION_IN);
	}
	
	public function getTransitionOut()
	{
		return Mage::getStoreConfig(self::XML_PATH_TRANSITION_OUT);
	}
	
	public function getSpeedIn()
	{
		return Mage::getStoreConfig(self::XML_PATH_SPEED_IN);
	}
	
	public function getSpeedOut()
	{
		return Mage::getStoreConfig(self::XML_PATH_SPEED_OUT);
	}
	
	public function getChangeSpeed()
	{
		return Mage::getStoreConfig(self::XML_PATH_CHANGE_SPEED);
	}
	
	public function getChangeFade()
	{
		return Mage::getStoreConfig(self::XML_PATH_CHANGE_FADE);
	}
	
	public function getEasingIn()
	{
		return Mage::getStoreConfig(self::XML_PATH_EASING_IN);
	}
	
	public function getEasingOut()
	{
		return Mage::getStoreConfig(self::XML_PATH_EASING_OUT);
	}
	
	public function getShowCloseButton()
	{
		return Mage::getStoreConfig(self::XML_PATH_SHOW_CLOSE_BUTTON);
	}
	
	public function getShowNavArrows()
	{
		return Mage::getStoreConfig(self::XML_PATH_SHOW_NAV_ARROWS);
	}
	
	public function getEnableEscapeButton()
	{
		return Mage::getStoreConfig(self::XML_PATH_ENABLE_ESCAPE_BUTTON);
	}
	
	public function getAllowFullScreen()
	{
		return Mage::getStoreConfig(self::XML_PATH_ALLOW_FULLSCREEN);
	}
	
	
	
	public function importVideoData($headers, $row, $product, $cnt) {
		$assocArr = array();
		$merged = array();
		$error = array();
		$col = 0;
		
		/** Checks to apply
			1. any row is empty
			2. sku is null
			3. video_code is null
			4. both, sku ad video_code, are null
			5. video_width is not numeric
			6. video_height is not numeric
		*/
		
		foreach($headers as $k=>$header) {
			if(trim($header) == 'sku'){
				if($row[$k] == ''){//sku is empty
					$error[] = trim($header).' is Empty';
					$col++;
				}else{
					//check if SKU exists
					$productId = $product->getIdBySku($row[$k]);
					if ($productId) {
						$merged['product_id'] = $productId;
					}else{
						$error[] = trim($header).' does not exist';
						$col++;
					}
				}
				$merged[trim($header)] = $row[$k];
			}
			
			if(trim($header) == 'video_code'){
				if($row[$k] == ''){//video_code is Empty
					$error[] = trim($header).' is empty';
					$col++;
				}
				$merged[trim($header)] = $row[$k];
			}
			
			if(trim($header) == 'video_title'){
				if($row[$k] == ''){//video_title is Empty
					$col++;
				}
				$merged[trim($header)] = $row[$k];
			}
			
			if(trim($header) == 'video_width'){
				if($row[$k] != '' && (int)($row[$k]) == 0){//video_width is provided and is NOT Numeric
					$error[] = trim($header).' is NOT Numeric';
				}elseif($row[$k] == ''){
					$col++;
				}
				$merged[trim($header)] = (int)($row[$k]) == 0 ? 720: (int)($row[$k]);
			}
			
			if(trim($header) == 'video_height'){
				if($row[$k] != '' && (int)($row[$k]) == 0){//video_height is provided and is NOT Numeric
					$error[] = trim($header).' is NOT Numeric';
				}elseif($row[$k] == ''){
					$col++;
				}
				$merged[trim($header)] = (int)($row[$k]) == 0 ? 580: (int)($row[$k]);
			}
		}
		
		if(count($error)>0){
			if($col == 5)
				$assocArr['error'] = 'Error at Row : '.$cnt.' -> Blank Row';
			else
				$assocArr['error'] = 'Error at Row : '.$cnt.' -> '.implode(', ', $error);
		}
		
		$assocArr['success'] = $merged;
		
		
		return $assocArr;
	}
}