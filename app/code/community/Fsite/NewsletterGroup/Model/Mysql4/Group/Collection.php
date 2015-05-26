<?php
/**
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Model_Mysql4_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * newsletter group table name
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Constructor
     *
     * Configures collection
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName('newslettergroup/group');
        $this->_init('newslettergroup/group');

    }
}