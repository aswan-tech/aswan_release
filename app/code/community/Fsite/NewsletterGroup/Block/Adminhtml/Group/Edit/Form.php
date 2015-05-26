<?php
/**
 * Adminhtml Newsletter Template Edit Form Block
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Block_Adminhtml_Group_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Define Form settings
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieve template object
     *
     * @return Mage_Newsletter_Model_Template
     */
    public function getModel()
    {
        return Mage::registry('_current_group');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
     */
    protected function _prepareForm()
    {
        $model  = $this->getModel();
        $identity = Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY);
        $identityName = Mage::getStoreConfig('trans_email/ident_'.$identity.'/name');
        $identityEmail = Mage::getStoreConfig('trans_email/ident_'.$identity.'/email');

        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('newsletter')->__('Group Information'),
            'class'     => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $model->getId(),
            ));
        }

        $fieldset->addField('group_name', 'text', array(
            'name'      => 'group_name',
            'label'     => Mage::helper('newsletter')->__('Group Name'),
            'title'     => Mage::helper('newsletter')->__('Group Name'),
            'required'  => true,
            'value'     => $model->getGroupName(),
        ));

        $fieldset->addField('visible_in_frontend', 'select', array(
            'name'      =>'visible_in_frontend',
            'label'     => Mage::helper('newsletter')->__('Visible in frontend'),
            'title'     => Mage::helper('newsletter')->__('Visible in frontend'),
            'required'  => true,
            'value'     => $model->getVisibleInFrontend(),
            'values'    => array(0 => "No", 1 => "Yes")
        ));

        $fieldset->addField( 'parent_group_id', 'select', array(
            'name'      => 'parent_group_id',
            'label'     => Mage::helper( 'newslettergroup' )->__( 'Parent Group' ),
            'title'     => Mage::helper( 'newslettergroup' )->__( 'Parent Group'),
            'required'  => false,
            'value'     => $model->getParentGroupId(),
            'values'    => $this->_getParentGroupsOptions()
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getParentGroupsOptions()
    {
        $collection = Mage::getResourceModel( 'newslettergroup/group_collection' )
            ->addFieldToFilter( 'parent_group_id', array( 'eq' => 0 ) )
            ->load();
        $options = array();
        $options[0] = '';
        foreach ($collection as $group) {
            $options[ $group->getId() ] = $group->getGroupName();
        }
        return $options;
    }
}
