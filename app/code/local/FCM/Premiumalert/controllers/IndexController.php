<?php
class FCM_Premiumalert_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/premiumalert?id=15 
    	 *  or
    	 * http://site.com/premiumalert/id/15 	
    	 */
    	/* 
		$premiumalert_id = $this->getRequest()->getParam('id');

  		if($premiumalert_id != null && $premiumalert_id != '')	{
			$premiumalert = Mage::getModel('premiumalert/premiumalert')->load($premiumalert_id)->getData();
		} else {
			$premiumalert = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($premiumalert == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$premiumalertTable = $resource->getTableName('premiumalert');
			
			$select = $read->select()
			   ->from($premiumalertTable,array('premiumalert_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$premiumalert = $read->fetchRow($select);
		}
		Mage::register('premiumalert', $premiumalert);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}