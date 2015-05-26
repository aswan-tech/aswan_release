<?php
class Magestore_Categoryslider_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/categoryslider?id=15 
    	 *  or
    	 * http://site.com/categoryslider/id/15 	
    	 */
    	/* 
		$categoryslider_id = $this->getRequest()->getParam('id');

  		if($categoryslider_id != null && $categoryslider_id != '')	{
			$categoryslider = Mage::getModel('categoryslider/categoryslider')->load($categoryslider_id)->getData();
		} else {
			$categoryslider = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($categoryslider == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$categorysliderTable = $resource->getTableName('categoryslider');
			
			$select = $read->select()
			   ->from($categorysliderTable,array('categoryslider_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$categoryslider = $read->fetchRow($select);
		}
		Mage::register('categoryslider', $categoryslider);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}