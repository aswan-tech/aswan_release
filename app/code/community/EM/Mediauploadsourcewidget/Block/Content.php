<?php
class EM_Mediauploadsourcewidget_Block_Content extends Mage_Adminhtml_Block_Media_Uploader
{
//protected $_config;

    public function __construct()
    {
        parent::__construct();
    	$this->setUseAjax(true);
        $this->setId($this->getId() . '_Uploader');
        $this->setTemplate('mediauploadsourcewidget/uploader.phtml');
        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload'));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters(array(
            'media' => array(
                'label' => Mage::helper('adminhtml')->__('Media (.avi, .flv, .swf, .mp3, .wmv)'),
                'files' => array('*.avi', '*.flv', '*.swf','*.mp3','*.wmv')
            )
        ));
    }
 public function getContentsUrl()
    {
        return $this->getUrl('*/admin_chooser/contents', array('type' => $this->getRequest()->getParam('type')));
    }
 
}
?>