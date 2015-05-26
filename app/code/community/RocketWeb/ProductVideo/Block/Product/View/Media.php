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

/**
 * Simple product data view
 *
 */
class RocketWeb_ProductVideo_Block_Product_View_Media extends Brandammo_Progallery_Block_ProductViewMedia
{
    protected function _toHtml()
	{
		$html = parent::_toHtml();
		$html .= $this->getChildHtml('media_video');
		return $html;
	}
}