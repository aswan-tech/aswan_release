<?php

/**
 * Magento Model to define the OTF feed functions 
 *
 * This model defines the functions to generate order feeds.
 * The feeds include Orders feed, Order feed acknowledgement, Order confirmation, Order Shipment.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Thursday, June 5, 2012
 * @copyright	Four cross media
 */

/**
 * Otf model class
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
class FCM_Fulfillment_Model_Otf extends FCM_Fulfillment_Model_Order 
{
	private $localFolder;
	private $localOutbound;
	private $localOutboundOtf;
	
	private $localOutboundOtfArcv;
	private $localOutboundOtfErr;
	
	private $remoteOutboundOtf;	
	
	private $lockPeriod;
	public $initFileOtfName = 'Ordtf';
		
	private $otfOrderIds = array();
	private $feedfile = "";
	
	private $ctime = "";
	
	//Number of orders to be sent in a single file
	private $numOrdersInSingleFile = 1;
		 
	protected function _construct() 
	{
		$this->_init('fulfillment/otf');		
	}
	
	public function __construct() 
	{
		$this->localFolder = Mage::getBaseDir('var') . DS . 'lecom';
		$this->localOutbound = $this->localFolder . DS . 'outbound';
		
		$this->localOutboundOtf = $this->localOutbound . DS . 'ordtofulfill' . DS ;	
		$this->localOutboundOtfArcv = $this->localOutbound  . DS . 'ordtofulfill_arcv' . DS ;
		$this->localOutboundOtfErr = $this->localOutbound . DS . 'ordtofulfill_err' . DS ;		
		
		$remoteOutboundOtf = Mage::getStoreConfig('orders/paths/otf');
		$this->remoteOutboundOtf = trim($remoteOutboundOtf);
		
		Mage::getModel('logger/logger')->saveLogger("order_fulfillment", "Information", __FILE__, "DB Outbound Path:". $this->remoteOutboundOtf);
		
		if (empty($this->remoteOutboundOtf)) {
			$this->remoteOutboundOtf = '/mnt/lecomotf/outbound/ordtofulfill/';
		}
				
		if (!is_dir($this->localFolder)) {
			mkdir($this->localFolder, 0777);
			chmod($this->localFolder, 0777);
		}
				
		if (!is_dir($this->localOutbound)) {
			mkdir($this->localOutbound, 0777);
			chmod($this->localOutbound, 0777);
		}
		
		if (!is_dir($this->localOutboundOtf)) {
			mkdir($this->localOutboundOtf, 0777);
			chmod($this->localOutboundOtf, 0777);
		}
				
		if (!is_dir($this->localOutboundOtfArcv)) {
			mkdir($this->localOutboundOtfArcv, 0777);
			chmod($this->localOutboundOtfArcv, 0777);
		}
		
		if (!is_dir($this->localOutboundOtfErr)) {
			mkdir($this->localOutboundOtfErr, 0777);
			chmod($this->localOutboundOtfErr, 0777);
		}
		
		$this->lockPeriod = Mage::getStoreConfig('lockorder/time_setting/release_after');
		
		if (empty($this->lockPeriod)) {
			$this->lockPeriod = 1;
		}
		$this->ctime = Varien_Date::now();
	}

	/**
     * Orders Feed Generation
	 *
    */
	 
	public function otffeed() 
	{	
		Mage::log('Entered otffeed function', Zend_Log::DEBUG, 'fulfillment');
				
		//Fetch orders with senttoerp status 0 and order status new and processing 
		//$ostate = $this->otfOrderState;
		//$this->remoteOutboundOtf = trim($this->remoteOutboundOtf);
		
		if (empty($this->remoteOutboundOtf)) {
			throw new Exception("Remote outbound folder path not specified");
		}
		
		//Unlock the order that have acuired lock for more than 1 hour
		Mage::log('Unlocking the order which have exceeded the locking criteria.', Zend_Log::DEBUG, 'fulfillment');
		$this->unlockOrders();
		
		$lockingTable = Mage::getSingleton('core/resource')->getTableName('lockorder/lockorder');
		
		$orders = Mage::getModel('sales/order')->getCollection()->addFieldToSelect('*')
			   ->addAttributeToFilter('state', array('neq'=>'holded'))
			   ->addAttributeToFilter('main_table.status', array(array('eq'=>'created'), array('eq'=>'processing'), array('eq'=>'COD_Verification_Successful'), array('eq'=>'closed')))
			   ->addAttributeToFilter('sent_to_erp', '0');
		$orders->getSelect()
				->joinLeft(array('locking_table'=>$lockingTable), 'main_table.increment_id = locking_table.order_id', array())
				->where('locking_table.status=0 OR locking_table.status is NULL')
				->where("DATE_ADD(created_at, INTERVAL ". $this->lockPeriod . " MINUTE) < '". $this->ctime ."'");
					
		Mage::log('Reading Orders', Zend_Log::DEBUG, 'fulfillment');
		
		$loggerModel = Mage::getModel('logger/logger');
		
		$totOrders = count($orders);
		
		if ($totOrders > 0) {
			$loggerModel->saveLogger("order_fulfillment", "Information", __FILE__, "Reading orders");
			
			$numfilesToCreate = ceil($totOrders / $this->numOrdersInSingleFile);
			
			for ($i=1; $i <= $numfilesToCreate; $i++) {
				$doc  = new DOMDocument('1.0', 'utf-8');
				$doc->formatOutput = true;
			
				$ordersNode = $doc->createElement( "Orders" );
				$doc->appendChild( $ordersNode );
				
				$j = 0;
			
				foreach ($orders as $key => $order) {
					//$otfOrderIds = array();$order->getRealOrderId()
					$j++;
					$orderId = $order->getId();
					$this->otfOrderIds[$orderId] = $orderId;
					
					$orderData = $this->getOrderData($order);

					$orderNode = $doc->createElement( "Order" );
					$ordersNode->appendChild( $orderNode );
					
					foreach ($orderData as $tag => $value) {
						$dataTag = $doc->createElement( $tag );
						$orderNode->appendChild( $dataTag );
						
						$valueTag = $doc->createCDATASection($value);
						$dataTag->appendChild( $valueTag );
					}
					
					/*
					 * Tender type code
					 */ 
					$TenderTypeNode = $doc->createElement( "TenderType" );
					$orderNode->appendChild( $TenderTypeNode );
					
					$TenderTypeArr = $this->getActivePaymentMethods($order);
					foreach($TenderTypeArr as $tag => $value) {
						$tenderDataTag = $doc->createElement( $tag );
						$TenderTypeNode->appendChild( $tenderDataTag );
						
						$tenderValueTag = $doc->createCDATASection($value);
						$tenderDataTag->appendChild( $tenderValueTag );
					}
					
					/*
					 * Payment discount code
					 */ 
					 
					$couponDiscountArr = $this->getCoupons($order);
					$discountNode = $doc->createElement( "DiscountCoupons" );
					$orderNode->appendChild( $discountNode );
					
					foreach($couponDiscountArr  as $tag => $value ) {
						$discountDataTag = $doc->createElement( $tag );
						$discountNode->appendChild( $discountDataTag );
						
						$discountValueTag = $doc->createCDATASection($value);
						$discountDataTag->appendChild( $discountValueTag );
					}
					
					$itemsNode = $doc->createElement( "Items" );
					$orderNode->appendChild( $itemsNode );
					
					$orderItems = $order->getItemsCollection();
					
					Mage::log('Reading Order#'. $orderId . ' items', Zend_Log::DEBUG, 'fulfillment');
					
					//Add Order Items
					foreach($orderItems as $item) {
						if (!$item->isDummy()) {
						//if ($item->getProductType() == 'simple') {
							$itemNode = $doc->createElement( "Item" );
							$itemsNode->appendChild( $itemNode );
						
							$itemData = $this->getOrderItemData($item, $order);
							
							foreach ($itemData as $tag => $value) {
								$itemdataTag = $doc->createElement( $tag );
								$itemNode->appendChild( $itemdataTag );
								
								$itemvalueTag = $doc->createCDATASection($value);
								$itemdataTag->appendChild( $itemvalueTag );
							}
							
							/*
							 * gift card code
							 */
							  
							$giftcardCodesArr = $this->getGiftCardData($item, $order);
							if(count($giftcardCodesArr) > 0 ){
								$GvNode = $doc->createElement( "GvCode" );
								$itemNode->appendChild( $GvNode );
								foreach($giftcardCodesArr as $tag=>$value) {
									$GVitemdataTag = $doc->createElement( $tag );
									$GvNode->appendChild( $GVitemdataTag );
									
									$GVitemvalueTag = $doc->createCDATASection($value);
									$GVitemdataTag->appendChild( $GVitemvalueTag );
								}
							}	
							
						//}
						}
					}
					
					$orders->removeItemByKey($key);
					
					if ($j == $this->numOrdersInSingleFile) {
						break;
					}
				}
				
				$otfffed = $doc->saveXML();
				Mage::log('xml data: '.$otfffed, Zend_Log::DEBUG, 'fulfillment');
				
				
				//Update the sent to erp field
				$this->updateStatusOtf(1);
				
				$processModel = Mage::getModel('fulfillment/process');
				
				if ($numfilesToCreate == 1) {
					$fileName = $processModel->getFilename($this->initFileOtfName, 'xml',"",$order->getRealOrderId());
				} else {
					$fileName = $processModel->getFilename($this->initFileOtfName, 'xml', $i,$order->getRealOrderId());
				}
				
				$this->feedfile = $fileName;
				
				$filepath = $this->localOutboundOtf . $fileName;
				$archivePath = $this->localOutboundOtfArcv . $fileName;
				
				Mage::log('Writing OTF Feed to local server', Zend_Log::DEBUG, 'fulfillment');
				$loggerModel->saveLogger("order_fulfillment", "Information", __FILE__, "Writing OTF Feed to local server");
				
				if ($processModel->writexml($filepath, $otfffed)) {
					//File written successfully
					//Move to remote folder
					$remotefile = $this->remoteOutboundOtf . $fileName;
					
					$loggerModel->saveLogger("order_fulfillment", "Information", __FILE__, "Sending OTF feed file to remote server");
					Mage::log('Sending order feed to remote server...', Zend_Log::DEBUG, 'fulfillment');
					
					if ($processModel->sendToRemote($filepath, $remotefile, 'order_fulfillment')) {
						//Archive
						$loggerModel->saveLogger("order_fulfillment", "Information", __FILE__, "Moving OTF Feed file to archive folder");
							if (copy($filepath, $archivePath)) {
							unlink($filepath);
						}
					} else {
						//Error file cannot be copied to remote folder
						$loggerModel->saveLogger("order_fulfillment","Information",__FILE__,"trying to move without sftp");
						if(rename($filepath,$remotefile))
						{
							echo "yes";
							$loggerModel->saveLogger("order_fulfillment","Information",__FILE__,"file moved");
						}
						else
						{	
						$loggerModel->saveLogger("order_fulfillment","Information",__FILE__,"file moved failed");
						$loggerModel->saveLogger("order_fulfillment", "Exception", __FILE__, "Error moving OTF Feed file to remote server, rollback performed");
					  	$this->rollbackOtf();

                                                //Copy the file to the error folder
                                                $errorPath = $this->localOutboundOtfErr . $fileName;
                                                copy($filepath, $errorPath);
                                                unlink($filepath);

                                                throw new Exception("Error moving OTF Feed file to remote server [". $filepath . " >> $remotefile " . "],  rollback performed");

						}

/*						$this->rollbackOtf();
						
						//Copy the file to the error folder
						$errorPath = $this->localOutboundOtfErr . $fileName;			
						copy($filepath, $errorPath);
						unlink($filepath);
						
						throw new Exception("Error moving OTF Feed file to remote server [". $filepath . " >> $remotefile " . "],  rollback performed");*/
					}
									
				} else {
					//Error file cannot be copied to local folder
					$loggerModel->saveLogger("order_fulfillment", "Exception", __FILE__, "Error writing OTF Feed to local server, rollback performed");
					
					$this->rollbackOtf();
					
					throw new Exception("Error writing OTF Feed to local server [" . $filepath ."], rollback performed");
				}
			}
		} else {
			$loggerModel->saveLogger("order_fulfillment", "Information", __FILE__, "No Order found");
			Mage::log('No Order found', Zend_Log::DEBUG, 'fulfillment');
		}
		
		Mage::log('Exited otffeed function', Zend_Log::DEBUG, 'fulfillment');
	}
	
	/*
	 *	This function is used to roll back if file is not saved to ftp
	 */
	private function rollbackOtf()
	{
		$this->updateStatusOtf(0);	
	}

	
	/*
	 *	This function is used to update send_to_erp field in the order table
	 */
	 
	private function updateStatusOtf($status) 
	{
	
		if(count($this->otfOrderIds)){
			$resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');
			$salestable = $resource->getTableName('sales/order');
			$oids = implode(",", $this->otfOrderIds);
			$query = "UPDATE {$salestable} SET sent_to_erp = '{$status}' WHERE entity_id in ({$oids})";
			$writeConnection->query($query);
		}
		
	}
	
	/*
	 *	This function unlock the order that have acuired lock for more than 1 hour
	 */
	 
	private function unlockOrders()
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$locktable = $resource->getTableName('lockorder/lockorder');
		
		//$lockPeriod = Mage::getStoreConfig('lockorder/time_setting/release_after');
		
		/*$query = "UPDATE {$locktable} SET status = '0', lock_released = UTC_TIMESTAMP() WHERE DATE_ADD(lock_acquired, INTERVAL ". $this->lockPeriod . " MINUTE) > UTC_TIMESTAMP()";*/
		
		$query = "UPDATE {$locktable} SET status = '0', lock_released = '". $this->ctime ."' WHERE DATE_ADD(lock_acquired, INTERVAL ". $this->lockPeriod . " MINUTE) < '". $this->ctime ."'";
		
		$writeConnection->query($query);
	}
	
	/*
	 *	This function return the cron orders processed summary
	 *
	 *	@return string
	 */
	 
	public function getOrdersProcessedSummary() {
		$totalOrders = count($this->otfOrderIds);
		if ($totalOrders > 0) {
			$summary = $totalOrders . " order(s) sent in ". $this->feedfile;
		} else {
			$summary = "No new orders found.";
		}
		
		return $summary;
	}
}

