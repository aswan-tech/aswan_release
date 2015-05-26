<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Quote/Observer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ cCBMeDBMjooCseDq('3797cb991e2aee33ac5f3cc8d4bd23d8'); ?><?php
class Aitoc_Aitindividpromo_Model_Quote_Observer extends Mage_Core_Model_Abstract
{
    public function onCoreLocaleSetLocale($observer)
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