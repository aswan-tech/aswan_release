<?php
/**
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Model_Group extends Mage_Core_Model_Abstract
{
    protected $_children;
    
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('newslettergroup/group');
    }

    /**
     *
     */
    public function getSubGroups()
    {
        if ( !$this->_children ) {
            $collection = Mage::getResourceModel( 'newslettergroup/group_collection' )
                ->addFieldToFilter( 'parent_group_id', array( 'eq' => $this->getId() ) )
                ->load();

            if ( count( $collection ) > 0 ) {
                $this->_children = $collection;
            }
        }

        return $this->_children;
    }
}
