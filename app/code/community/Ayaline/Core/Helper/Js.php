<?php
/**
 * created : 20 avr. 2012
 *
 * @category Ayaline
 * @package Ayaline_XXXX
 * @author aYaline
 * @copyright Ayaline - 2012 - http://magento-shop.ayaline.com
 * @license http://shop.ayaline.com/magento/fr/conditions-generales-de-vente.html
 */

class Ayaline_Core_Helper_Js extends Mage_Core_Helper_Js {

	/**
	 * Array of senteces of JS translations
	 *
	 * @var array
	 */
	protected $_translateData = null;

	/**
	 * Retrieve JS translator initialization javascript
	 *
	 * @return string
	 */
	public function getTranslatorScript() {
		$script = 'var AyalineTranslator = new Translate('.$this->getTranslateJson().');';
		return $this->getScript($script);
	}

	/**
	 * Retrieve JS translation array
	 *
	 * @return array
	 */
	protected function _getTranslateData() {
		if($this->_translateData === null) {
			$_translateData = array(
				
			);
			
			$_translateData = new Varien_Object($_translateData);
			Mage::dispatchEvent('ayalinecore_helper_js_translate', array('translate_data' => $_translateData));
			$this->_translateData = $_translateData->getData();
			
			foreach($this->_translateData as $key => $value) {
				if($key == $value) {
					unset($this->_translateData[$key]);
				}
			}
		}
		return $this->_translateData;
	}

}