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
 * Manage log for cron script
 * 
 * @package Ayaline_Core
 */
abstract class Ayaline_Core_Model_Cron_Abstract extends Varien_Object {

	const AYALINE_DIR = 'ayaline';
	
	/**
	 * Path to log files
	 * 
	 * @var string
	 */
	protected $_ayalineLogPath;
	
	protected $_ayalineLogFilename;
	
	protected function _construct() {
		//	ex: {{base_dir}}/var/log/ayaline/competition
		$this->_ayalineLogPath = Mage::getBaseDir('log').DS.self::AYALINE_DIR.DS.$this->_getLogPath();
		
		$this->_ayalineLogFilename = self::AYALINE_DIR.DS.$this->_getLogPath().DS.$this->_getLogFilename();
	}
	
	/**
	 * Log message
	 *
	 * @param mixed $message
	 * @param int $level (see Zend_Log)
	 */
	protected function _log($message, $level = null) {
		$forceLog = $this->_logIsActive();
		if($forceLog) {
			if(!is_dir($this->_ayalineLogPath)) {
				mkdir($this->_ayalineLogPath, 0755, true);
			}
			Mage::log($message, $level, $this->_ayalineLogFilename, $forceLog);
		}
	}

	/**
	 * Log exception (in the same file as log)
	 *
	 * @param Exception $exception
	 */
	protected function _logException($exception) {
		$this->_log("\n".$exception->__toString(), Zend_Log::ERR);
	}

	/**
	 * Check if log is enabled
	 * 
	 * @return bool
	 */
	abstract protected function _logIsActive();

	/**
	 * Path to log files, from {{base_dir}}/var/log/ayaline
	 * 
	 * @return string
	 */
	abstract protected function _getLogPath();
	
	/**
	 * Retrieve log filename
	 * 
	 * @return string
	 */
	abstract protected function _getLogFilename();
	
}