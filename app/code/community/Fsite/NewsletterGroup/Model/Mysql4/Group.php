<?php
/**
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Model_Mysql4_Group extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    /**
     * Name of subscriber DB table
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Initialize resource model
     *
     * Get tablename from config
     */
    protected function _construct()
    {
        $this->_init('newslettergroup/group', 'id');
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName("newslettergroup/group");
        $this->_read = $this->_getReadAdapter();
        $this->_write = $this->_getWriteAdapter();
    }
}
