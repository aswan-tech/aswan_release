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
 
class RocketWeb_ProductVideo_Block_Rewrite_AdminhtmlCatalogProductEditTabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs 
{
	protected function _prepareLayout() 
    {
		$return = parent::_prepareLayout();
		 
		 $this->addTab('rw_youtube_videos', array(
            'label'     => Mage::helper('productvideo')->__('Videos'),
            'url'       => $this->getUrl('productvideo_admin/adminhtml_videos', array('_current' => true)),
            'class'     => 'ajax',
            'after'     => 'inventory',
        ));
		
		return $return;
	}
}