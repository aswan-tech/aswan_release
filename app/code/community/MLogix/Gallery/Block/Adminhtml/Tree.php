<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php
class MLogix_Gallery_Block_Adminhtml_Tree extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{  	
		$this->_controller = 'adminhtml_gallery';
		$this->_blockGroup = 'gallery';
		$this->_headerText = Mage::helper('gallery')->__('Categories');
		
		parent::__construct();
	}
  
  
  
	protected function _prepareLayout()
	{
	    $addUrl = $this->getUrl("*/*/new", array(
	        '_current'=>true,
	        'id'=>null,
	        '_query' => false
	    ));
	    
		
	    $this->setChild('add_sub_button',
	        $this->getLayout()->createBlock('adminhtml/widget_button')
	            ->setData(array(
	                'label'     => Mage::helper('gallery')->__('Add Season Trend Item'),
	                'onclick'   => "addNew('".$addUrl."', false)",
	                'class'     => 'add'
	            ))
	    );
		
	    $this->setChild('add_root_button',
	        $this->getLayout()->createBlock('adminhtml/widget_button')
	            ->setData(array(
	                'label'     => Mage::helper('gallery')->__('Add Season Trend'),
	                'onclick'   => "addNew('".$addUrl."', true)",
	                'class'     => 'add',
	                'id'        => 'add_root_category_button'
	            ))
	    );
		
		
	    return parent::_prepareLayout();
	}

	
	public function getResetUrl()
	{
		return $this->getUrl("*/*/reset");
	}
	
	public function getEditUrl()
	{
		return $this->getUrl("*/*/edit");
	}
	
	public function getMoveUrl()
	{
		return $this->getUrl("*/*/move"); // todo
	}
	
	public function getStoreId()
	{
		return 0; // not important
	}
	
	public function getLoadTreeUrl($expanded=null)
	{
		return $this->getUrl("*/*/categoriesJson");		
	}
	
	public function getCategoryId()
	{
		return 1;
	}
	
	public function getRootName()
	{
		//$x = Mage::getModel('gallery/gallery')->load(1);
		//$x = $x->toArray();

		return 'Season Trends';
	}
	
	public function getIsWasExpanded()
	{
		return true;
	}
	
	public function getSwitchTreeUrl()
	{
		return $this->getUrl("*/*/categoriesJson"); // todo
	}
	
	
	
	public function getTreeJson($node = 0)
	{
		$cats = Mage::getModel('gallery/gallery')->getCategories($node);
		
		
		
		$json = Zend_Json::encode($cats);
		//echo $json;
		//die();

        return $json;	
	}
    
    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }    	
      
}