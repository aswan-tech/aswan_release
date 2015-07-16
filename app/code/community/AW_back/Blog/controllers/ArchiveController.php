<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AW_Blog_ArchiveController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        
		if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::getStoreConfig('blog/blog/title'));
            $head->setKeywords(Mage::getStoreConfig('blog/blog/keywords'));
            $head->setDescription(Mage::getStoreConfig('blog/blog/description'));
        }
		
		$month = $this->getRequest()->getParam('m');
        $year = $this->getRequest()->getParam('y');
		
		if($month == '' && $year == ''){
			$month = date('m');
			$year = date('Y');
			
			$this->_redirect('*/*/index/y/'.$year.'/m/'.$month);
		}
		
        $this->renderLayout();
    }

}

?>
