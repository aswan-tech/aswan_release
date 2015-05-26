<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MLogix_Gallery_ArchiveController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        /*if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::getStoreConfig('blog/blog/title'));
            $head->setKeywords(Mage::getStoreConfig('blog/blog/keywords'));
            $head->setDescription(Mage::getStoreConfig('blog/blog/description'));
        }*/
        $this->renderLayout();
    }
	
	public function weekAction() {
        $this->loadLayout();
        /*if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::getStoreConfig('blog/blog/title'));
            $head->setKeywords(Mage::getStoreConfig('blog/blog/keywords'));
            $head->setDescription(Mage::getStoreConfig('blog/blog/description'));
        }*/
        $this->renderLayout();
    }
}

?>
