<?php
class FCM_Productsale_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/productsale?id=15 
    	 *  or
    	 * http://site.com/productsale/id/15 	
    	 */
    	/* 
		$productsale_id = $this->getRequest()->getParam('id');

  		if($productsale_id != null && $productsale_id != '')	{
			$productsale = Mage::getModel('productsale/productsale')->load($productsale_id)->getData();
		} else {
			$productsale = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($productsale == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$productsaleTable = $resource->getTableName('productsale');
			
			$select = $read->select()
			   ->from($productsaleTable,array('productsale_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$productsale = $read->fetchRow($select);
		}
		Mage::register('productsale', $productsale);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}