<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Icons extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amstockstatus/icons.phtml');
        $this->_doUpload();
    }
    
    protected function _doUpload()
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'amstockstatus' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
                                                    
        /**
        * Deleting
        */
        $toDelete = Mage::app()->getRequest()->getPost('amstockstatus_icon_delete');
        if ($toDelete)
        {
            foreach ($toDelete as $optionId => $del)
            {
                if ($del)
                {
                    @unlink($uploadDir . $optionId . '.jpg');
                }
            }
        }
        
        /**
        * Uploading files
        */
        if (isset($_FILES['amstockstatus_icon']) && isset($_FILES['amstockstatus_icon']['error']))
        {
            foreach ($_FILES['amstockstatus_icon']['error'] as $optionId => $errorCode)
            {
                if (UPLOAD_ERR_OK == $errorCode)
                {
                    move_uploaded_file($_FILES['amstockstatus_icon']['tmp_name'][$optionId], $uploadDir . $optionId . '.jpg');
                }
            }
        }
    }
    
    public function getOptionsCollection()
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
                ->setPositionOrder('desc', true)
                ->load();
        return $optionCollection;
    }
    
    public function getIcon($option)
    {
        return Mage::helper('amstockstatus')->getStatusIconUrl($option->getId());
    }
    
    public function getSubmitUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'])
        {
            $url = str_replace('http:', 'https:', $url);
        }
        return $url;
    }
}
