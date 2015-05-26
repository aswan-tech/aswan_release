<?php
class Mage_Customer_Block_Mypreferences extends Mage_Customer_Block_Account_Dashboard
{
    public function getData() {
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customerData->getId();
        $customerEmail = $customerData->getEmail();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM customer_preferences WHERE customer_id = "'.$customerId.'"';
        $results = $readConnection->fetchAll($query);
        return $results;
    }
}
