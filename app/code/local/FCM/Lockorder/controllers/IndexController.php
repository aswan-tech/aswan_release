<?php
class FCM_Lockorder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/lockorder?id=15 
    	 *  or
    	 * http://site.com/lockorder/id/15 	
    	 */
    	/* 
		$lockorder_id = $this->getRequest()->getParam('id');

  		if($lockorder_id != null && $lockorder_id != '')	{
			$lockorder = Mage::getModel('lockorder/lockorder')->load($lockorder_id)->getData();
		} else {
			$lockorder = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($lockorder == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$lockorderTable = $resource->getTableName('lockorder');
			
			$select = $read->select()
			   ->from($lockorderTable,array('lockorder_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$lockorder = $read->fetchRow($select);
		}
		Mage::register('lockorder', $lockorder);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}