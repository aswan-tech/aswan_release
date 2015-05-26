<?php
/**
 * created : 12/04/2011
 * 
 * @category Ayaline
 * @package Ayaline_Core
 * @author aYaline
 * @copyright Ayaline - 2012 - http://magento-shop.ayaline.com
 * @license http://shop.ayaline.com/magento/fr/conditions-generales-de-vente.html
 */

/**
 * 
 * @package Ayaline_Core
 */
class Ayaline_Core_Model_Observer {

	/**
	 * Vérifie s'il y a des mises à jours des modules Ayaline
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function checkUpdate($observer) {
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			/* @var $feedModel Ayaline_Core_Model_Feed */
			$feedModel  = Mage::getModel('ayalinecore/feed');
			$feedModel->checkUpdate();
		}
	}

}