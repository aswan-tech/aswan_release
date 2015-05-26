<?php
/**
 * Adminhtml newsletter templates grid block
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Block_Adminhtml_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setEmptyText(Mage::helper('newsletter')->__('No Groups Found'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newslettergroup/group_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('group_id',
            array('header'=>Mage::helper('newsletter')->__('ID'),
                'align'=>'center',
                'index'=>'id'
        ));

        $this->addColumn('group_name',
            array(
                'header'=>Mage::helper('newsletter')->__('Group Name'),
                'index'=>'group_name'
        ));

        $this->addColumn('visible_in_frontend',
            array(
                'header'=>Mage::helper('newsletter')->__('Visible In Frontend'),
                'index'=>'visible_in_frontend',
                'type' => 'options',
                'options' => array(
                    0   => 'No',
                    1 	=> 'Yes'
                ),
        ));

        $this->addColumn( 'parent_group_id',
            array( 'header' => Mage::helper( 'newslettergroup' )->__( 'Parent Group ID' ),
                'align' => 'center',
                'index' => 'parent_group_id'
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

