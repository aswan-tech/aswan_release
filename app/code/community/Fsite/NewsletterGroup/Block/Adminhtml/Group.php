<?php
/**
 * Adminhtml newsletter templates page content block
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Block_Adminhtml_Group extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('newsletter/group/list.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('newslettergroup/adminhtml_group_grid', 'newsletter.group.grid'));
        return parent::_prepareLayout();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    public function getHeaderText()
    {
        return Mage::helper('newsletter')->__('Newsletter Groups');
    }
}
