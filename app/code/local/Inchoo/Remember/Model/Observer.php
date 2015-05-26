<?php
class Inchoo_Remember_Model_Observer {
    public function checkRememberMe($observer) {
        $session = $observer->getEvent()->getCustomerSession();
        if(!$session->isLoggedIn() and isset($_COOKIE['anastasia'])) {
            $cookieData = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 'taslc99274', base64_decode( $_COOKIE['anastasia'] ), MCRYPT_MODE_ECB);
            $userId = (int) $cookieData;
            $user = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->load( $userId );
            if( $user ) {
                $observer->getEvent()->getCustomerSession()->setCustomerAsLoggedIn( $user );
                header("Location: ".Mage::helper('core/url')->getCurrentUrl());
                exit;
            } else {
                setcookie('anastasia', null, time() - 60 * 60 * 24 * 7, '/');
            }
        }
        return;
    }
}
