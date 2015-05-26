<?php
/**
 * Adminhtml newsletter templates grid block action item renderer
 * 
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */

class Fsite_NewsletterGroup_Block_Adminhtml_Group_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renderer for "Action" column in Newsletter templates grid
     *
     * @var Mage_Newsletter_Model_Template $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions[] = array(
            'url'     => $this->getUrl('*/*/delete', array('id'=>$row->getId())),
            'popup'   => true,
            'caption' => Mage::helper('newsletter')->__('Delete')
        );

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
