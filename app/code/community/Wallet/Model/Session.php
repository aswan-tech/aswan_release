<?php

class Wallet_Model_Session extends Mage_Core_Model_Session_Abstract {

    protected $_statuses = array();

    public function __construct() {
        $namespace = 'wallet';
        $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
        
        $this->init($namespace);
        Mage::dispatchEvent('wallet_session_init', array('wallet_session' => $this));
    }

    public function getTransactStatus($orderId) {
        $this->_statuses = $this->getStatusCache();
        return isset($this->_statuses[$orderId]) ? $this->_statuses[$orderId] : null;
    }

    public function setTransactStatus($orderId, $status) {        
        $this->_statuses[$orderId] = $status;
        $this->setStatusCache($this->_statuses);
        return $this;
    }

    public function clearStatusCache() {
        $this->_statuses = array();
        $this->setStatusCache(array());
    }
}