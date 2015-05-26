<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Cron.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ mREwjpoZaMTrjgEm('748ffca6ec6c7eb8f77f7f109da81a2c'); ?><?php

class AdjustWare_Nav_Model_Cron extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('adjnav/cron');
    }

    public function canRunJob($code)
    {
        $this->load($code);

        if (time() - strtotime($this->getLastRun()) > Mage::helper('adjnav/featured')->collectPeriod() * 60)
        {
            return true;
        }

        return false;
    }
} } 