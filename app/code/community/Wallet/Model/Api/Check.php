<?php 

class Wallet_Model_Api_Check extends Varien_Object 
{

    public static $CHECK_ENDPOINT = '';
    
    public function check($orderId)
    {
        $request = Mage::getModel('wallet/api_request');
        $request->setWalletConfig(Mage::getStoreConfig('payment/wallet'))
            ->setUrl(self::$CHECK_ENDPOINT)
            ->addParam('orderId', $orderId)
            ->send();
        $this->setResponseCode($request->getResponseCode());
        $this->setResponseDescription($request->getResponseDescription());
    }
}
