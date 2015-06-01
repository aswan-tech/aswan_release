<?php 
class Custom_Banners_Model_Mysql4_Banners extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('banners/banners', 'banner_id');
    }
}
