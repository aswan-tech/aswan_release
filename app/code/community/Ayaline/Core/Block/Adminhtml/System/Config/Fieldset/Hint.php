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

class Ayaline_Core_Block_Adminhtml_System_Config_Fieldset_Hint
	extends Mage_Adminhtml_Block_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface {

	protected $_template = 'ayaline/core/system/config/fieldset/hint.phtml';
	
	protected $_infos = null;
	
	/**
	 * Render fieldset html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		return $this->toHtml();
	}
	
	// TESTS
	public function getAyalineModules() {
		if(is_null($this->_infos)) {
			$modulesInfos = Mage::getConfig()->getNode('ayalinemodules_infos');
			$infos = array();
//			foreach($modulesInfos as $moduleInfos) {
//				foreach($moduleInfos as $key => $module) {
//					$infos[$key] = array(
//						'title'			=>	(string)$module->title,
//						'description'	=>	(string)$module->description,
//						'picture'		=>	(string)$module->picture,
//					);
//				}
//			}
			$this->_infos = $infos;
		}
		return $this->_infos;
	}
	
	public function getKeys() {
		$infos = $this->getAyalineModules();
		$infos = array_keys($infos);
		return Mage::helper('core')->jsonEncode($infos);
	}

}