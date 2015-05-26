<?php
class FCM_GoogleConversion_Helper_Data extends Mage_Core_Helper_Abstract
{	
	/**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'googleconversion/code/active';
    const XML_PATH_CHECKOUTLOGIN = 'googleconversion/code/checkoutlogin';
	const XML_PATH_CHECKOUTSUCCESS = 'googleconversion/code/checkoutsuccess';

    /**
     * Whether GC is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isGoogleConversionAvailable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }
}