<?php

class FCM_Provider_Model_Provider extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init("provider/provider");
    }

    public function getDropDownOptions() {

        /**
         * Get the resource model
         */
        $resource = Mage::getSingleton('core/resource');

        /**
         * Retrieve the read connection
         */
        $readConnection = $resource->getConnection('core_read');

        $query = 'SELECT * FROM ' . $resource->getTableName('fcm_shippingcarriers');

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);
        $options = array();

        foreach ($results as $key => $result) {
            $options[$result['carrier_name']] = $result['carrier_name'];
        }

        /**
         * Print out the results
         */
        return $options;
    }

    public function getBlinkeCarrierId($carrier_name) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT blinkecarrier_id FROM ' . $resource->getTableName('fcm_shippingcarriers') . ' WHERE carrier_name = "' . $carrier_name . '" ';
        $result = $readConnection->fetchRow($query);
        return $result['blinkecarrier_id'];
    }

    public function getFlagProvider($carrier_name) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT provider_id FROM ' . $resource->getTableName('provider') . ' WHERE shippingprovider_name = "' . $carrier_name . '" ';
        $result = $readConnection->fetchRow($query);
        return $result['provider_id'] != '' ? true : false;
    }

}