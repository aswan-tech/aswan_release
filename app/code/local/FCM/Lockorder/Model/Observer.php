<?php

/* * *********************************************************
 * Lock Order Observers
 *
 * Module for locking order while editing or credit memo generation.
 *
 * @category    Mage
 * @package     Mage_LockOrder
 * @author	Shikha Raina
 * @company	HCL Technologies
 * @created Monday, July 3, 2012
 * @copyright	Four cross media
 * ******************************************************** */

class FCM_LockOrder_Model_Observer {

    /**
     *
     * @param <type> $observer
     * @return <type>
     * @desciption After saving order, Relase Lock
     */
    public function sales_order_save_after($observer) {

        $order = $observer->getOrder();
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $fields = array();
        $fields['status'] = '0';
        $fields['lock_released'] = Varien_Date::now();
        $where = $connection->quoteInto('order_id =?', $order->getRelationParentRealId());
        $connection->update('lockorder', $fields, $where);
        $connection->commit();

        return;
    }

    /**
     *
     * @param <type> $observer
     * @return <type>
     * @desciption After saving credit memo, Relase Lock
     */
    public function sales_order_creditmemo_refund($observer) {

        $orderId = Mage::app()->getFrontController()->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $fields = array();
        $fields['status'] = '0';
        $fields['lock_released'] = Varien_Date::now();
        $where = $connection->quoteInto('order_id =?', $order->getIncrementId());
        $connection->update('lockorder', $fields, $where);
        $connection->commit();

        return;
    }
	
	/* function to run solr index */
	
	public function runsolrindex(){
		try{
			Mage::log('observer ran solr indexes');
			$directory = Mage::getBaseDir();
			$pCollection = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
			foreach ($pCollection as $process) {
				$checkVar = $process->getUnprocessedEventsCollection()->count() > 0 ? 1 : 0;
				if($checkVar == 1){
					$code = $process->getIndexerCode();
					$out = shell_exec("php -f ".$directory."/shell/indexer.php -- -reindex ".$code);
				}
				unset($checkVar);
			}
		}catch(exception $e){
			Mage::log('Exception occured while refreshing indexes through CRON :'.$e);
		}
	}
}
