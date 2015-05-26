<?php
/**
 * Newsletter subscribe block
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Block_Subscribe extends Mage_Core_Block_Template
{
    protected $_groupCollection;

    public function getSuccessMessage()
    {
        $message = Mage::getSingleton('newsletter/session')->getSuccess();
        return $message;
    }

    public function getErrorMessage()
    {
        $message = Mage::getSingleton('newsletter/session')->getError();
        return $message;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', array('_secure' => true));
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
