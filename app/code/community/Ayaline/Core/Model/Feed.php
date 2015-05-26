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
class Ayaline_Core_Model_Feed extends Mage_AdminNotification_Model_Feed {

	const XML_USE_HTTPS_PATH = 'ayalinecore/adminnotification/use_https';
	const XML_FEED_URL_BASE_PATH = 'ayalinecore/adminnotification/feed_url';
	const XML_FEED_EN_PATH = 'ayalinecore/adminnotification/feed_en';
	const XML_FEED_FR_PATH = 'ayalinecore/adminnotification/feed_fr';
	const XML_FREQUENCY_PATH = 'ayalinecore/adminnotification/frequency';

	const AYALINE_TYPE_GENERAL = 'general';
	const AYALINE_TYPE_MODULE = 'module';

	/**
	 * Contient la liste des modules Ayaline de la communauté installées sur ce site
	 *
	 * @var array
	 */
	protected $_allAyalineModules = array();

	/**
	 * Récupère l'url du flux RSS
	 *
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/AdminNotification/Model/Mage_AdminNotification_Model_Feed::getFeedUrl()
	 */
	public function getFeedUrl() {
		if(is_null($this->_feedUrl)) {
			$path = self::XML_FEED_EN_PATH;
			$localeCode = Mage::app()->getLocale()->getLocaleCode();
			if(preg_match('#fr_#', $localeCode)) {
				$path = self::XML_FEED_FR_PATH;
			}
			$this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://') . Mage::getStoreConfig(self::XML_FEED_URL_BASE_PATH) . Mage::getStoreConfig($path);
		}
		return $this->_feedUrl;
	}

	/**
	 * Lit et traite le flux RSS
	 *
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/AdminNotification/Model/Mage_AdminNotification_Model_Feed::checkUpdate()
	 */
	public function checkUpdate() {
		if(($this->getFrequency() + $this->getLastUpdate()) > time()) {
			return $this;
		}

		$feedData = array();

		$feedXml = $this->getFeedData();

		if($feedXml && $feedXml->channel && $feedXml->channel->item) {
			$this->_getAllAyalineModules();
			foreach($feedXml->channel->item as $item) {	//	Notification général, pour tous le monde
				if((string)$item->ayaline_type == self::AYALINE_TYPE_GENERAL) {
					$feedData[] = array(
	                    'severity'		=>	(int)$item->severity,
	                    'date_added'	=>	$this->getDate((string)$item->pubDate),
	                    'title'			=>	(string)$item->title,
	                    'description'	=>	(string)$item->description,
	                    'url'			=>	(string)$item->link,
					);
				} elseif((string)$item->ayaline_type == self::AYALINE_TYPE_MODULE){	//	Notification ciblée sur un module
					if(in_array((string)$item->ayaline_module, $this->_allAyalineModules)) {
						$feedData[] = array(
		                    'severity'		=>	(int)$item->severity,
		                    'date_added'	=>	$this->getDate((string)$item->pubDate),
		                    'title'			=>	(string)$item->title,
		                    'description'	=>	(string)$item->description,
		                    'url'			=>	(string)$item->link,
						);
					}
				}
			}
				
			if($feedData) {
				Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
			}
		}
		$this->setLastUpdate();

		return $this;
	}

	/**
	 * Récupération des modules Ayaline, de la communauté, installées sur ce site
	 */
	protected function _getAllAyalineModules() {
		$modules = Mage::getConfig()->getNode('modules');
		foreach($modules->asArray() as $_moduleName => $_moduleInfos) {
			if(preg_match('#Ayaline_#', $_moduleName) && $_moduleInfos['codePool'] == 'community') {
				$this->_allAyalineModules[] = $_moduleName;
			}
		}
	}


}