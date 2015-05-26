<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Helper/Legal.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ZSegBBZTWMZrRmUW('a77a52ac449bfdfe346bd5c5883e1a5f'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
 
class Aitoc_Aitloyalty_Helper_Legal extends Mage_Core_Helper_Abstract
{
    protected $_hasLoyaltyFeatures = false;
    protected $_isNotifyRestrictedFeatures = false;

    public function setHasLoyaltyFeatures()
    {
        $this->_hasLoyaltyFeatures = true;

        return $this;
    }

    public function getHasLoyaltyFeatures()
    {
        return $this->_hasLoyaltyFeatures;
    }

    public function setIsNotifyRestrictedFeatures()
    {
        $this->_isNotifyRestrictedFeatures = true;

        return $this;
    }

    public function getIsNotifyRestrictedFeatures()
    {
        return $this->_isNotifyRestrictedFeatures;
    }

    public function notifyRestrictedFeatures()
    {
        if ($this->getIsNotifyRestrictedFeatures())
        {
            $session = Mage::getSingleton('core/session');

            /* @var $session Mage_Core_Model_Session */

            $session->addWarning($this->__('Aitoc Loyalty Program functionality is disabled for some websites you specified'));
        }
    }
} } 