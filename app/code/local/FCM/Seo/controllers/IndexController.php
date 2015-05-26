<?php
class FCM_Seo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/seo?id=15 
    	 *  or
    	 * http://site.com/seo/id/15 	
    	 */
    	/* 
		$seo_id = $this->getRequest()->getParam('id');

  		if($seo_id != null && $seo_id != '')	{
			$seo = Mage::getModel('seo/seo')->load($seo_id)->getData();
		} else {
			$seo = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($seo == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$seoTable = $resource->getTableName('seo');
			
			$select = $read->select()
			   ->from($seoTable,array('seo_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$seo = $read->fetchRow($select);
		}
		Mage::register('seo', $seo);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}