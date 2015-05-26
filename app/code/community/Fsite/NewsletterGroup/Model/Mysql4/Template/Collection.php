<?php
/**
 * Templates collection
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Model_Mysql4_Template_Collection extends Mage_Newsletter_Model_Mysql4_Template_Collection
{

    public function __construct()
    {
        Varien_Data_Collection_Db::__construct(Mage::getSingleton('core/resource')->getConnection('newsletter_read'));
        $this->_templateTable = Mage::getSingleton('core/resource')->getTableName('newsletter/template');
        $this->_select->from($this->_templateTable, array('template_id','template_code',
                                                             'template_type',
                                                             'template_subject','template_sender_name',
                                                             'template_sender_email',
                                                             'added_at',
                                                             'modified_at',
                                                             'newsletter_group_id'
                                                             ));
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('newsletter/template'));
    }
    
}
