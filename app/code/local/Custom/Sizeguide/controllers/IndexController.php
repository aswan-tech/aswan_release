<?php
class Custom_Sizeguide_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/sizeguide?id=15 
    	 *  or
    	 * http://site.com/sizeguide/id/15 	
    	 */
    	/* 
		$sizeguide_id = $this->getRequest()->getParam('id');

  		if($sizeguide_id != null && $sizeguide_id != '')	{
			$sizeguide = Mage::getModel('sizeguide/sizeguide')->load($sizeguide_id)->getData();
		} else {
			$sizeguide = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($sizeguide == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$sizeguideTable = $resource->getTableName('sizeguide');
			
			$select = $read->select()
			   ->from($sizeguideTable,array('sizeguide_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$sizeguide = $read->fetchRow($select);
		}
		Mage::register('sizeguide', $sizeguide);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}