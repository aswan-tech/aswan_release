<?php
/**
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Adminhtml_NewsletterGroup_GroupController extends Mage_Adminhtml_Controller_Action
{
	
    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed ()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('newslettergroup/group');
    }

    /**
     * Set title of page
     *
     * @return Mage_Adminhtml_Newsletter_TemplateController
     */
    protected function _setTitle()
    {
        return $this->_title($this->__('Newsletter'))->_title($this->__('Newsletter Groups'));
    }

    /**
     * View Templates list
     *
     */
    public function indexAction ()
    {
        $this->_setTitle();

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();
        $this->_setActiveMenu('newslettergroup/group');
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Groups'), Mage::helper('newsletter')->__('Newsletter Groups'));
        $this->_addContent($this->getLayout()->createBlock('newslettergroup/adminhtml_group', 'group'));
        $this->renderLayout();
    }

    /**
     * JSON Grid Action
     *
     */
    public function gridAction ()
    {
        $this->loadLayout();
        $grid = $this->getLayout()->createBlock('newslettergroup/adminhtml_group_grid')
            ->toHtml();
        $this->getResponse()->setBody($grid);
    }

    /**
     * Create new Newsletter Template
     *
     */
    public function newAction ()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Newsletter Template
     *
     */
    public function editAction ()
    {
        $this->_setTitle();

        $model = Mage::getModel( 'newslettergroup/group' );
        if ( $id = $this->getRequest()->getParam( 'id' ) ) {
            $model->load( $id );
        }

        Mage::register( '_current_group', $model );

        $this->loadLayout();
        $this->_setActiveMenu( 'newslettergroup/group' );
        
        $this->_addContent( $this->getLayout()->createBlock( 'newslettergroup/adminhtml_group_edit', 'group_edit') );

        if ( $model->getId() ) {
            $breadcrumbTitle = Mage::helper( 'newsletter' )->__( 'Edit Group' );
            $breadcrumbLabel = $breadcrumbTitle;
        }
        else {
            $breadcrumbTitle = Mage::helper( 'newsletter' )->__( 'New Group' );
            $breadcrumbLabel = Mage::helper( 'newsletter' )->__( 'Create Newsletter Group' );
        }

        $this->_title( $model->getId() ? $model->getGroupName() : $this->__( 'New Group' ) );

        $this->_addBreadcrumb( $breadcrumbLabel, $breadcrumbTitle );

        // restore data
        if ( $values = $this->_getSession()->getData( 'newsletter_group_form_data', true ) ) {
            $model->addData( $values );
        }

        if ( $editBlock = $this->getLayout()->getBlock( 'group_edit' ) ) {
            $editBlock->setEditMode( $model->getId() > 0 );
        }

        $this->renderLayout();
    }

    /**
     * Save Newsletter Template
     *
     */
    public function saveAction ()
    {
        $request = $this->getRequest();
        if ( !$request->isPost() ) {
            $this->getResponse()->setRedirect( $this->getUrl( '*/newsletter_group' ) );
        }
        $group = Mage::getModel( 'newslettergroup/group' );

        if ( $id = (int) $request->getParam( 'id' ) ) {
            $group->load( $id );
        }

        try {
            $group->addData( $request->getParams() )
                ->setGroupName( $request->getParam( 'group_name' ) )
                ->setVisibleInFrontend( $request->getParam( 'visible_in_frontend' ) )
                ->setParentGroupId( $request->getParam( 'parent_group_id' ) );

            $group->save();
            $this->_redirect( '*/*' );
        }
        catch ( Mage_Core_Exception $e ) {
            $this->_getSession()->addError( nl2br( $e->getMessage() ) );
            $this->_getSession()->setData( 'newsletter_group_form_data', $this->getRequest()->getParams() );
        }
        catch (Exception $e) {
            $this->_getSession()->addException( $e, Mage::helper( 'newslettergroup' )->__( 'An error occurred while saving this template.' ) );
            $this->_getSession()->setData( 'newsletter_group_form_data', $this->getRequest()->getParams() );
        }
        $this->_forward( 'new' );
    }

    /**
     * Delete newsletter Template
     *
     */
    public function deleteAction ()
    {
        $group = Mage::getModel( 'newslettergroup/group' )
            ->load( $this->getRequest()->getParam('id') );

        if ( $group->getId() ) {
            try {
                $group->delete();
            }
            catch ( Mage_Core_Exception $e ) {
                $this->_getSession()->addError( $e->getMessage() );
            }
            catch ( Exception $e ) {
                $this->_getSession()->addException( $e, Mage::helper( 'newslettergroup ')->__( 'An error occurred while deleting this group.' ) );
            }
        }
        $this->_redirect( '*/*' );
    }
    
    
}
