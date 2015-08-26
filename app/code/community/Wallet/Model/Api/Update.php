<?php 

class Wallet_Model_Api_Update extends Varien_Object 
{

    public static $UPDATE_ENDPOINT = '';

    public static $STATUS_CANCELLED = '8';

    public static $STATUS_REFUNDED = '10';

    public static $STATUS_SETTLED = '7';

    public static $CANCELLED_RESP_CODES = array(226, 198);
    
    public function send($orderId, $updateDesired, $updateReason) 
    {
        $request = Mage::getModel('wallet/api_request');
        $request->setWalletConfig(Mage::getStoreConfig('payment/wallet'))
            ->setUrl(self::$UPDATE_ENDPOINT)
            ->addParam('orderId', $orderId)
            ->addParam('updateDesired', $updateDesired)
            ->addParam('updateReason', $updateReason)
            ->send();
        $this->setResponseCode($request->getResponseCode());
        $this->setResponseDescription($request->getResponseDescription());
        // unset the statuscache 
        $session = Mage::getSingleton('wallet/session');
        $session->clearStatusCache();
    }
}
