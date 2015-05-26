<?php
class FCM_Categorycode_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/categorycode?id=15 
    	 *  or
    	 * http://site.com/categorycode/id/15 	
    	 */
    	/* 
		$categorycode_id = $this->getRequest()->getParam('id');

  		if($categorycode_id != null && $categorycode_id != '')	{
			$categorycode = Mage::getModel('categorycode/categorycode')->load($categorycode_id)->getData();
		} else {
			$categorycode = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($categorycode == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$categorycodeTable = $resource->getTableName('categorycode');
			
			$select = $read->select()
			   ->from($categorycodeTable,array('categorycode_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$categorycode = $read->fetchRow($select);
		}
		Mage::register('categorycode', $categorycode);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}