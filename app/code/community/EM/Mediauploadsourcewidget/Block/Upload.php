<?php

/**
 * Magento
 *
 *  
 *
 * @package     EM_MediaUploadUrlWidget
 * @copyright   Copyright (c) 2009 Quick Solution LT
 * 
 */
class EM_Mediauploadsourcewidget_Block_Upload extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

    public function _construct() {
        parent::_construct();
		
        // default template location
    }
	
    protected function _toHtml() {
	
        $media = trim($this->getData('media'));
        $width = trim($this->getData('width'));
        $height = trim($this->getData('height'));
        $id = trim($this->getData('id'));
        $class = trim($this->getData('class'));
        $attrs = trim($this->getData('attrs'));
        //$flashvars = trim($this->getData('flashvars'));
        $wmode = trim($this->getData('wmode'));
        $_select = trim($this->getData('select'));
        $flashvars=Mage::getBaseUrl('media'). 'flash/';
        $media = $flashvars.$media;
        if (substr($media, -4) == ".flv") {
                $this->setTemplate('mediauploadsourcewidget/flv.phtml');
            }
            if (substr($media, -4) == ".wmv") {
                $this->setTemplate('mediauploadsourcewidget/wmv.phtml');
            }
            if (substr($media, -4) == ".avi") {
                $this->setTemplate('mediauploadsourcewidget/avi.phtml');
            }
            // mp3
            if (substr($media, -4) == ".mp3") {
                $this->setTemplate('mediauploadsourcewidget/mp3.phtml');
            }
            // swf
            if (substr($media, -4) == ".swf") {
                $this->setTemplate('mediauploadsourcewidget/swf.phtml');
            }
   
        $this->assign('media', $media);
        $this->assign('width', $width);
        $this->assign('height', $height);
        $this->assign('id', $id);
        $this->assign('class', $class);
        $this->assign('attrs', $attrs);
        $this->assign('flashvars', $flashvars);
        $this->assign('wmode', $wmode);
        
        return parent::_toHtml();
    }

}
