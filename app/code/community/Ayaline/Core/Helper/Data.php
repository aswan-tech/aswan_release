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

class Ayaline_Core_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * Check code (equivalent validate-xml-identifier)
	 *
	 * @param string $code
	 * @return bool
	 */
	public function isValidCode($code) {
		return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $code);
	}

	/**
	 * Format string for url_key
	 *
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str) {
		Mage::helper('core/string')->cleanString($str);
		$weirdChars = array('’', '–');	//	chars from Ms Word
		$str = str_replace($weirdChars, '-', $str);
		$str = Mage::helper('core')->removeAccents($str);
		$urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, '-');
		return $urlKey;
	}

	/**
	 * Add custom notification message
	 *
	 * @param Varien_Object $notificationObject (fields : title, date, url, description)
	 * @param int $severity
	 */
	public function addCustomAdminNotification($notificationObject, $severity = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR){
		$notification = Mage::getModel('adminnotification/inbox');
		$notification->setseverity($severity);
		$notification->setTitle($notificationObject->getTitle());
		$notification->setDateAdded($notificationObject->getDate());
		$notification->setUrl($notificationObject->getUrl());
		$notification->setDescription($notificationObject->getDescription());
		$notification->save();
	}
	

	/**
	 * Upload a file on server
	 *
	 * @param string $field (field in $_FILES)
	 * @param string $path
	 * @param array $allowedExt
	 * @return string
	 * @throws Exception
	 */
	public function upload($field, $path, $allowedExt = array('jpg','jpeg','gif','png')){
		try {
			$uploader = new Varien_File_Uploader($field);
			$uploader->setAllowedExtensions($allowedExt);
			$uploader->setAllowRenameFiles(true);
			$uploader->setFilesDispersion(true);
			$uploader->setAllowCreateFolders(true);
				
			$uplResult = $uploader->save($path);
			return $uplResult['file'];
		} catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}
	}
	
	
}
