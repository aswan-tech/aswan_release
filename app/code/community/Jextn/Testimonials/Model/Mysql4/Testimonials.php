<?php

class Jextn_Testimonials_Model_Mysql4_Testimonials extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the testimonials_id refers to the key field in your database table.
        $this->_init('testimonials/testimonials', 'testimonials_id');
    }
}