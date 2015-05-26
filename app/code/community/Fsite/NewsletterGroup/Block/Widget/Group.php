<?php
/**
 * Block to render customer's newsletter options
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Block_Widget_Group extends Mage_Customer_Block_Widget_Abstract
{
    protected $_groupCollection;
    
    /**
     * Initialize block
     */
    public function _construct()
    {
        Mage_Core_Block_Template::_construct();
        $this->setTemplate('newslettergroup/widget/group.phtml');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function isCustLoggedIn()
    {
        return $this->_getSession()->isLoggedIn();
    }
    

    /**
     * Check if any groups exist
     *
     * @return bool
     */
    public function hasGroups()
    {
        $groups = $this->getGroups();
        if ( is_array( $groups ) && count( $groups ) ) {
            return true;
        }
        return false;
    }

    /**
     * Get newsletter groups
     */
    public function getGroups()
    {
        if ( !$this->_groupCollection ) {
            // Add filter
            $collection = Mage::getResourceModel( 'newslettergroup/group_collection' )
                ->addFieldToFilter( 'visible_in_frontend', array( "eq" => 1 ) )
                ->load();

            $this->_groupCollection = $collection->getItems();
        }

        return $this->_groupCollection;
    }

}