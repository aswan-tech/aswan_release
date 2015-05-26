<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/FrontAccountNavigation.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ZSegBBZTWMZrRmUW('ae36f793c228d71b35d24941fd596859'); ?><?php

class Aitoc_Aitloyalty_Block_Rewrite_FrontAccountNavigation extends Mage_Customer_Block_Account_Navigation
{
    public function addLink($name, $path, $label, $urlParams = array())
    {
        $isAddLink = true;

        if ('aitloyalty' == $name)
        {
            $iStoreId = Mage::app()->getStore()->getId();
            $iSiteId  = Mage::app()->getWebsite()->getId();

            /* */
            $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitloyalty')->getLicense()->getPerformer();
            $ruler     = $performer->getRuler();
            if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
            {
                $isAddLink = false;
            }
            /* */
        }

        if ($isAddLink)
        {
            parent::addLink($name, $path, $label, $urlParams);
        }

        return $this;
    }
} } 