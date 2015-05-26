<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_Block_Adminhtml_Gallery_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('gallery_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('gallery')->__('Item Information'));
      $this->setTemplate('widget/tabshoriz.phtml');
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('gallery')->__('Item Information'),
          'title'     => Mage::helper('gallery')->__('Item Information'),          
          'content'   => $this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}