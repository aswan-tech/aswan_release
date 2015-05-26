<?php
class FCM_FacebookConversion_Helper_Data extends Mage_Core_Helper_Abstract
{	
	/**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'facebookconversion/code/active';
	const XML_PATH_CHECKOUTSUCCESS = 'facebookconversion/code/checkoutsuccess';

    /**
     * Whether FB is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isFacebookConversionAvailable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }
}