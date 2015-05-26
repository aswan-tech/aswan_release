<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * System config file field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class CommerceExtensions_Productcrosssellupsellimportexport_Model_System_Config_Backend_File extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array("csv");
    }
	
	/**
     * Save uploaded file before saving config value
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_File
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
		
		$uploadDir = Mage::getBaseDir('var').'/import/relatedproducts/';
		
        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){
			
			/* check directory exists if not create it */
			
			if(is_dir($uploadDir)){
				$directory_exists = true;
			}else{
				$directory_exists = false;
				mkdir($uploadDir,777);
			}
			try {
				$filename = '';
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
				
				$name_of_file = $file['name'];
				
				$filename = explode(".",$name_of_file);
				
				if(is_array($filename)){
					if(sizeof($filename) == 2){
						if($filename[0] != 'import_products_relations'){
							$file['name'] = 'import_products_relations'.'.'.$filename[1];
						}
					}else{
						Mage::throwException("Please remove any '.' from filename other than extension OR provide a missing extension to file.");
					}
				}else{
					Mage::throwException("Please provide a missing extension to file.");
				}
				
				/* extra code added to remove file from directory if it exists and upload new one */
				if($directory_exists){
					$file_path = $uploadDir.'/'.trim($file['name']);
					if(is_file($file_path)){
						unlink($file_path);
					}
				}
				
				/*Extra code ends */
                $uploader = new Mage_Core_Model_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);

            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
					$file_path = $uploadDir.'/'.trim($value['value']);
					if(is_file($file_path)){
						unlink($file_path);
					}
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }
}
