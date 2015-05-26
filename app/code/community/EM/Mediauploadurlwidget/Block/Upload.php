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
class EM_Mediauploadurlwidget_Block_Upload extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

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
        $flashvars = trim($this->getData('flashvars'));
        $wmode = trim($this->getData('wmode'));

        if (substr($media, -4) == ".flv") {
            $this->setTemplate('mediauploadurlwidget/flv.phtml');
        }
        if (substr($media, -4) == ".wmv") {
            $this->setTemplate('mediauploadurlwidget/wmv.phtml');
        }
        if (substr($media, -4) == ".avi") {
            $this->setTemplate('mediauploadurlwidget/avi.phtml');
        }
        
        // Youtube
        if (substr($media, 0, 22) == "http://www.youtube.com") {
            $this->setTemplate('mediauploadurlwidget/embed_youtube.phtml');
            if (stripos($media, "&") > 0)
                $media = substr($media, stripos($media, "v=") + 2, stripos($media, "&") - stripos($media, "v=") - 2);
            else
                $media = substr($media, stripos($media, "v=") + 2);

            if ($width == "") {
                $width = "480";
            }

            if ($height == "") {
                $height = "385";
            }
        }
        // Dailymotion
        if (substr($media, 0, 26) == "http://www.dailymotion.com") {
            $this->setTemplate('mediauploadurlwidget/embed_dailymotion.phtml');
            if (stripos($media, "&") > 0)
                $media = substr($media, stripos($media, "video/") + 6, stripos($media, "&") - stripos($media, "video/") - 6);
            else
                $media = substr($media, stripos($media, "video/") + 6);

            if ($width == "") {
                $width = "480";
            }

            if ($height == "") {
                $height = "270";
            }
        }
        // mp3
        if (substr($media, -4) == ".mp3") {
            $this->setTemplate('mediauploadurlwidget/mp3.phtml');
        }
        // swf
        if (substr($media, -4) == ".swf") {
            $this->setTemplate('mediauploadurlwidget/swf.phtml');
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
