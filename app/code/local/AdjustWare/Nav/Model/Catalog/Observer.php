<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 8/8/12
 * Package:     AdjustWare_Nav_10.4.7_10.0.0_350989
 * Purchase ID: JlpoJTtn90BoYkBFboOB7FJ0XKOaO2LswSbNsOVuj9
 * Generated:   2012-08-09 08:56:53
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Observer.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ rOmagQpaPRWNfhRk('797a1cb76a3b594d093cd9751f0a27f2'); ?><?php
class AdjustWare_Nav_Model_Catalog_Observer extends Mage_Core_Model_Abstract
{
    public function onAdminhtmlControllerActionPredispatchStart($observer)
    {
        if(!Mage::registry('aitpagecache_check_14') && Mage::getConfig()->getNode('modules/Aitoc_Aitpagecache/active')==='true')
        {
            if(file_exists(Mage::getBaseDir('magentobooster').DS.'use_cache.ser'))
            {
                Mage::register('aitpagecache_check_14', 1);
            }
            elseif(file_exists(Mage::getBaseDir('app/etc').DS.'use_cache.ser'))
            {
                Mage::register('aitpagecache_check_13', 1);
            }
        }
    }
} } 