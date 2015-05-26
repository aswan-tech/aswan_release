<?php
/**
 * Adminhtml newsletter templates grid block
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Newsletter_Template_Grid
{

    protected function _prepareColumns()
    {
        $this->addColumn('template_code',
            array('header'=>Mage::helper('newsletter')->__('ID'), 'align'=>'center', 'index'=>'template_id'));
        $this->addColumn('code',
            array(
                'header'=>Mage::helper('newsletter')->__('Template Name'),
                   'index'=>'template_code'
        ));

        $this->addColumn('added_at',
            array(
                'header'=>Mage::helper('newsletter')->__('Date Added'),
                'index'=>'added_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('modified_at',
            array(
                'header'=>Mage::helper('newsletter')->__('Date Updated'),
                'index'=>'modified_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('subject',
            array(
                'header'=>Mage::helper('newsletter')->__('Subject'),
                'index'=>'template_subject'
        ));

        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('newsletter')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender'
        ));

        $this->addColumn('type',
            array(
                'header'=>Mage::helper('newsletter')->__('Template Type'),
                'index'=>'template_type',
                'type' => 'options',
                'options' => array(
                    Mage_Newsletter_Model_Template::TYPE_HTML   => 'html',
                    Mage_Newsletter_Model_Template::TYPE_TEXT 	=> 'text'
                ),
        ));

        // RY edit
        $this->addColumn('newsletter_group', array(
            'header'    => Mage::helper('newsletter')->__('Group'),
            'index'     => 'newsletter_group_id',
            'type'      => 'options',
            'options'   => $this->_getNewsletterGroupOptions()
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('newsletter')->__('Action'),
                'index'     =>'template_id',
                'sortable' =>false,
                'filter'   => false,
                'no_link' => true,
                'width'	   => '170px',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
        ));

        return $this;
    }

    protected function _getNewsletterGroupOptions()
    {
        $collection = Mage::getResourceModel('newslettergroup/group_collection')->load();
        $options = array();
        foreach ($collection as $group) {
            $options[ $group->getId() ] = $group->getGroupName();
        }
        return $options;

    }

}

