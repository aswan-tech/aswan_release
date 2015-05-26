<?php
class Brandammo_Progallery_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/progallery?id=15 
    	 *  or
    	 * http://site.com/progallery/id/15 	
    	 */
    	/* 
		$progallery_id = $this->getRequest()->getParam('id');

  		if($progallery_id != null && $progallery_id != '')	{
			$progallery = Mage::getModel('progallery/progallery')->load($progallery_id)->getData();
		} else {
			$progallery = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($progallery == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$progalleryTable = $resource->getTableName('progallery');
			
			$select = $read->select()
			   ->from($progalleryTable,array('progallery_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$progallery = $read->fetchRow($select);
		}
		Mage::register('progallery', $progallery);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}