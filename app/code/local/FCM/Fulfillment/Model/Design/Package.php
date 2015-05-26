<?php
/**
 * Package model class
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
 
 /*
  * Compress css files
  *
  */
  
$libdir = Mage::getBaseDir('lib');
require_once 'jsmin' . DS . 'jsmin.php';

class FCM_Fulfillment_Model_Design_Package extends Mage_Core_Model_Design_Package
{
	/**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {
        $targetFilename = md5(implode(',', $files)) . '.js';
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeJs'), 'js')) {
            return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js/' . $targetFilename;
        }
        return '';
    }
	
	/**
     * Before merge css callback function
     *
     * @param string $file
     * @param string $contents
     * @return string
     */
    public function beforeMergeCss($file, $contents)
    {
       $this->_setCallbackFileDir($file);

       $cssImport = '/@import\\s+([\'"])(.*?)[\'"]/';
       $contents = preg_replace_callback($cssImport, array($this, '_cssMergerImportCallback'), $contents);

       $cssUrl = '/url\\(\\s*(?!data:)([^\\)\\s]+)\\s*\\)?/';
       $contents = preg_replace_callback($cssUrl, array($this, '_cssMergerUrlCallback'), $contents);
	   
	   // Remove comments
	   $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

	   // Remove space after colons
	   $contents = str_replace(': ', ':', $contents);

	   // Remove whitespace
	   //$contents = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contents);
	   $contents = str_replace(array("\r\n", "\r", "\n", "\t"), '', $contents);
	   
	   $contents = preg_replace('/\s\s+/',' ',$contents);

       return $contents;
    }
	
	/**
     * Before merge js callback function
     *
     * @param string $file
     * @param string $contents
     * @return string
     */
    public function beforeMergeJs($file, $contents)
    {
       //$this->_setCallbackFileDir($file);
	   $contents = JSMin::minify($contents);

       return $contents;
    }
}
