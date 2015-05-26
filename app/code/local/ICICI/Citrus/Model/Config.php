<?php

class ICICI_citrus_Model_Config extends Varien_Object
{
    
    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
            $value = Mage::getStoreConfig('payment/citrus_standard/'.$key);
            if (is_null($value) || false===$value) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    public function getMerchantId ()
    {
        return $this->getConfigData('account_id');
    }
	
	public function getResponseURL()
	{
	  return $this->getConfigData('return_path');
	}
	
	public function getAccessKey()
	{
	  return $this->getConfigData('access_key');
	}
	
	public function getSecretKey()
	{
	  return $this->getConfigData('secret_key');
	}
	
	public function getVanityUrl()
	{
	  return $this->getConfigData('vanity_url');
	}

}