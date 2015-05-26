<?php
/**
 * Magento Model to read shipment feed and process 
 *
 * This model defines the functions process shipment feeds and generate shipments.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Monday, June 18, 2012
 * @copyright	Four cross media
 */

/**
 * Order Shipment model class
 *:
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
class FCM_Fulfillment_Model_Shipment extends Mage_Core_Model_Abstract
{
	private $localFolder;
	private $localInbound;
	
	private $localInboundOrdship;
	private $localInboundOrdshipArcv;
	private $localInboundOrdshipErr;
	
	private $remoteInboundOrdship;
	
	private $notifyCustomer = 1;
	private $visibleFrontEnd = 0;
	private $includeCommentEmail = true;
	
	private $xmlPartialShipment = 'Partial Shipped';
	private $xmlFullShipment = 'Shipped';
	private $xmlAllowedStatuses = array();
	private $xmlAllowedStatusCodes = array();
	private $xmlOrderStatusCodes = array();
	
	public $hasException = false;
	public $exceptionMessage = "";
	
	private $otfOrderShipReadSuccess = array();
	private $otfOrderShipReadFailure = array();
	
	private $otfShipOrderNotFound = array();
	private $otfShipTypeUndefined = array();
	private $otfShipNoTrack = array();
	private $otfShipInvalidCarrier = array();
	private $otfShipNotCreated = array();
	private $otfShipCreated = array();
	
	private $filesProcessed = array();
	
	protected function _construct() 
	{
		$this->_init('fulfillment/shipment');		
	}
	
	public function __construct() 
	{
		$this->localFolder = Mage::getBaseDir('var') . DS . 'lecom';
		$this->localInbound = $this->localFolder . DS .'inbound';
		
		$this->localInboundOrdship = $this->localInbound . DS . 'ordshipment' . DS ;	
		$this->localInboundOrdshipArcv = $this->localInbound . DS . 'ordshipment_arcv' . DS ;	
		$this->localInboundOrdshipErr = $this->localInbound . DS . 'ordshipment_err' . DS ;	
		
		$remoteInboundOrdship = Mage::getStoreConfig('orders/paths/otfshipment');	
		$this->remoteInboundOrdship = trim($remoteInboundOrdship);
		
		Mage::getModel('logger/logger')->saveLogger("order_shipment", "Information", __FILE__, "DB Inbound Path:". $this->remoteInboundOrdship);
		
		if (empty($this->remoteInboundOrdship)) {
			$this->remoteInboundOrdship = '/mnt/lecomotf/inbound/ordshipment/';
		}
			
		if (!is_dir($this->localFolder)) {
			mkdir($this->localFolder, 0777);
			chmod($this->localFolder, 0777);
		}
		
		if (!is_dir($this->localInbound)) {
			mkdir($this->localInbound, 0777);
			chmod($this->localInbound, 0777);
		}
					
		if (!is_dir($this->localInboundOrdship)) {
			mkdir($this->localInboundOrdship, 0777);
			chmod($this->localInboundOrdship, 0777);
		}
		
		if (!is_dir($this->localInboundOrdshipArcv)) {
			mkdir($this->localInboundOrdshipArcv, 0777);
			chmod($this->localInboundOrdshipArcv, 0777);
		}
		
		if (!is_dir($this->localInboundOrdshipErr)) {
			mkdir($this->localInboundOrdshipErr, 0777);
			chmod($this->localInboundOrdshipErr, 0777);
		}
	}
	
	/**
     * Read Orders Shipment Feed and create shipment
	 *
     */
	 
	public function otfshipping() 
	{
		Mage::log('Entering otfshipping function', Zend_Log::DEBUG, 'fulfillment');
		
		//$this->remoteInboundOrdship = trim($this->remoteInboundOrdship);
		
		if (empty($this->remoteInboundOrdship)) {
			throw new Exception("Remote inbound folder path not specified for the order shipment feed");
		}
		
		$loggerModel = Mage::getModel('logger/logger');
		$loggerModel->saveLogger("order_shipment", "Information", __FILE__, "Moving order shipment feed files from remote server to local server");
		
		$processModel = Mage::getModel('fulfillment/process');
		$readStatus = $processModel->readFromRemote($this->remoteInboundOrdship, $this->localInboundOrdship, 'order_shipment');
		
		if (isset($readStatus['success'])) {
			$this->otfOrderShipReadSuccess = $readStatus['success'];
		}
		
		if (isset($readStatus['error'])) {
			$this->otfOrderShipReadFailure = $readStatus['error'];
		}
		
		
		if (count($this->otfOrderShipReadFailure) > 0) {
			//Some files could not be read
			$efiles = implode(", ", $this->otfOrderShipReadFailure);
			Mage::log('Error transferring files from remote server: '. $efiles, Zend_Log::DEBUG, 'fulfillment');
			
			$loggerModel->saveLogger("order_shipment", "Information", __FILE__, "Error tranferring ". count($this->otfOrderShipReadFailure) ." file(s) from remote server");
			
			$this->hasException = true;
			$this->exceptionMessage .= "Error tranferring ". count($this->otfOrderShipReadFailure) ." file(s) from remote server";
		}
		
		if (count($this->otfOrderShipReadSuccess) > 0) {
			$rfiles = implode(", ", $this->otfOrderShipReadSuccess);	
			Mage::log('Transferred files from remote server: '. $rfiles, Zend_Log::DEBUG, 'fulfillment');
			
			$loggerModel->saveLogger("order_shipment", "Information", __FILE__, "Transferred ". count($this->otfOrderShipReadSuccess) ." file(s) from remote server");
			//Delete the files on the remote server
			//The files are already deleted by the read function once they are read to the local server 
			
			$processModel->enableLibXmlErrors(true);
			
			$this->xmlAllowedStatuses = $processModel->getDcStatuses();
			$this->xmlAllowedStatusCodes = $processModel->getDcStatusCodes();
			$this->xmlOrderStatusCodes = $processModel->getOrderStatusCodes();
			
			$loggerModel->saveLogger("order_shipment", "Information", __FILE__, "Processing order shipment feeds");
			
			try {
				if ($handle = opendir($this->localInboundOrdship)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry == "." || $entry == "..") {
							continue;
						}
						
						$xmlfile = $this->localInboundOrdship . DS . $entry;	
						$this->filesProcessed[] = $entry;
						
						Mage::log('Processing file: '. $xmlfile, Zend_Log::DEBUG, 'fulfillment');
						
						$doc = new DOMDocument();
						
						try {
							if ($doc->load( $xmlfile )) {
								$shipments = $doc->getElementsByTagName( "Shipment" );
								
								//Get DB Connections
								$resource = Mage::getSingleton('core/resource');
								$writeConnection = $resource->getConnection('core_write');
								$salestable = $resource->getTableName('sales/order');
								
								foreach ($shipments as $shipment)	{
									$orderIdTag = $shipment->getElementsByTagName( "OrderNumber" );
									$orderId = trim($orderIdTag->item(0)->nodeValue);
									
									$typeTag = $shipment->getElementsByTagName( "Type" ); //P => Partial Shipment C => Full Shipment
									$type = trim($typeTag->item(0)->nodeValue);
									
									/*
									if ($type == $this->xmlPartialShipment) {
										$shipmentType = 'partial_shipment';
									} else if ($type == $this->xmlFullShipment) {
										$shipmentType = 'shipment';
									} else {
										$shipmentType = '';
									}
									*/
									
									if ($type == $this->xmlFullShipment and in_array($type, $this->xmlAllowedStatuses)) {
										$shipmentType = $this->xmlOrderStatusCodes[$type];
									} else if($type == $this->xmlPartialShipment and in_array($type, $this->xmlAllowedStatuses)){
											$shipmentType = $this->xmlOrderStatusCodes[$type];
									}else{
										$shipmentType = '';
									}
																									
									if (!empty($shipmentType)) {
										$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
										
										$realOrderId = $order->getId();
										
										if ($realOrderId) {
											if($order->canShip()) {
												$order_items = array();
												foreach($order->getAllItems() as $eachOrderItem){
														$o_sku = $eachOrderItem->getSku();
														if(isset($order_items[$o_sku]))
															continue;
														else
															$order_items[$o_sku] = $eachOrderItem->getId();
												}
												$orderItemsTag = $shipment->getElementsByTagName( "Items" );
												$itemsQty = array();
												
												if (!is_null($orderItemsTag->item(0))) {
													$orderItems = $orderItemsTag->item(0)->getElementsByTagName( "Item" );
																													
													foreach ($orderItems as $item) {
														$orderItemSkuTag = $item->getElementsByTagName( "Sku" );
														$orderItemSku = trim($orderItemSkuTag->item(0)->nodeValue); 
														$orderItemId = $order_items[$orderItemSku];
														//$orderItemIdTag = $item->getElementsByTagName( "OrderItemId" );
														//$orderItemId = trim($orderItemIdTag->item(0)->nodeValue); 
														
														/*
														//Item Id is the sku received CR Begin
														$itemData = Mage::getModel('fulfillment/order')->getOrderItemDataBySku($orderId, $orderItemId);
														
														//Configurable product
														if ($itemData['parent_product_type'] == 'configurable') {
															$orderItemId = $itemData['parent_item_id'];
														} else {
															$orderItemId = $itemData['item_id'];
														}
														//Item Id is the sku received End
														*/
														
														$itemQtyTag = $item->getElementsByTagName( "ItemQty" );
														$itemQty = trim($itemQtyTag->item(0)->nodeValue);

														$itemsQty[$orderItemId] = $itemQty;
													}
												}
											
												$shipmentObj = $order->prepareShipment($itemsQty);
												
												$shipDateTag = $shipment->getElementsByTagName( "ShipDate" );
												
												if (!is_null($shipDateTag->item(0))) {
													$shipDate = trim($shipDateTag->item(0)->nodeValue);
																								
													$shipDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $shipDate);
													
													//Set shipment date
													$shipmentObj->setCreatedAt($shipDate);
													$shipmentObj->setUpdatedAt($shipDate);
												}
																								
												$carrierCodeTag = $shipment->getElementsByTagName( "CarrierCode" );
												$carrierCode = trim($carrierCodeTag->item(0)->nodeValue);
												
												$trackingTitleTag = $shipment->getElementsByTagName( "TrackingTitle" );
												$trackingTitle = trim($trackingTitleTag->item(0)->nodeValue);
												
												$trackNumberTag = $shipment->getElementsByTagName( "TrackNumber" );
												$trackNumber = trim($trackNumberTag->item(0)->nodeValue);
												
												if (!empty($trackNumber)) {
													//Check carrier is valid
													$carriers = Mage::getModel('fulfillment/order')->getCarriers($order->getStoreId());
													
													if (isset($carriers[$carrierCode])) {
														$dataTrack = array('carrier_code' => $carrierCode, 'title' => $trackingTitle, 'number' => $trackNumber );
													
														$track = Mage::getModel('sales/order_shipment_track')->addData($dataTrack);
														$shipmentObj->addTrack($track);
													} else {
														//Invalid carrier specified
														$this->otfShipInvalidCarrier[] = $orderId;
													}
												} else {
													//Tracking number cannot be empty
													$this->otfShipNoTrack[] = $orderId;
												}
															
												if ($shipmentObj) {
													$shipmentObj->register();
													
													$commentTag = $shipment->getElementsByTagName( "Comment" );
													$comment = trim($commentTag->item(0)->nodeValue);
													
													/*
													
													$sendEmailTag = $shipment->getElementsByTagName( "SendEmail" );
													
													if ($sendEmailTag->item(0)) {
														$sendEmail = trim($sendEmailTag->item(0)->nodeValue);						
														$sendEmail == 'true'? $notifyCustomer = 1: $notifyCustomer = 0;
														//$sendEmail == 'true'? $notifyCustomer = true: $notifyCustomer = false;
													} else {
														$sendEmail = $this->notifyCustomer;
														$sendEmail? $notifyCustomer = 1: $notifyCustomer = 0;
													}
													
													$includeCommentTag = $shipment->getElementsByTagName( "IncludeCommentEmail" );
													
													if ($includeCommentTag->item(0)) {
														$includeComment = trim($includeCommentTag->item(0)->nodeValue);
														$includeComment == 'true'? $mailComment = 1: $mailComment = 0;
														//$includeComment == 'true'? $mailComment = true: $mailComment = false;
													} else {
														$includeComment = $this->includeCommentEmail;
														$includeComment? $mailComment = 1: $mailComment = 0;
													}
													
													*/
																										
													//$shipmentObj->addComment($comment, $notifyCustomer && $mailComment);
													$shipmentObj->addComment($comment, $this->notifyCustomer);
													if ($this->notifyCustomer) {
														$shipmentObj->setEmailSent(true);
													}
													
													//$shipmentObj->getOrder()->setCustomerNoteNotify($notifyCustomer);			
													$shipmentObj->getOrder()->setIsInProcess(true);
													
													try {
														$transactionSave = Mage::getModel('core/resource_transaction')
															->addObject($shipmentObj)
															->addObject($shipmentObj->getOrder())
															->save();
														
														$this->otfShipCreated[] = $orderId;
														
														if ($shipmentObj->getOrder()->getState() == 'processing') {
															$order->setState('processing', $shipmentType, '', $this->notifyCustomer)->setIsVisibleOnFront($this->visibleFrontEnd)->save();
															$order->sendOrderUpdateEmail($this->notifyCustomer, $comment);
															
															//$order->setState($orderState, $orderStatus, $comment, $notifyCustomer)->save();																											
														}																									
													
														$shipmentObj->sendEmail($this->notifyCustomer, ($this->includeCommentEmail ? $comment : ''));
														
														$query = "UPDATE {$salestable} SET sent_to_erp = '{$this->xmlAllowedStatusCodes[$type]}' WHERE entity_id = {$realOrderId}";
														$writeConnection->query($query);
												
													} catch (Mage_Core_Exception $e) {
												
														$this->otfShipNotCreated[] = $orderId;
														
														$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
														$loggerModel->saveLogger("order_shipment", "Exception", __FILE__, $errmsg);
														
														$this->hasException = true;
														$this->exceptionMessage .= $errmsg;
														
														//Send Notification Mail
														$processModel->notify("order_shipment", $errmsg);	
													}	
												} else {
													$this->otfShipNotCreated[] = $orderId;
												}
																										
											} else {
												//Cannot create shipment
												$this->otfShipNotCreated[] = $orderId;
											}
										} else {
											//Order not found
											$this->otfShipOrderNotFound[] = $orderId;
										}
									} else {
										//Shipment type not defined partial/full
										$this->otfShipTypeUndefined[] = $orderId;
									}
									
								}
										
								//Move the file to the archive folder
								$archivePath = $this->localInboundOrdshipArcv . $entry;
															
								$loggerModel->saveLogger("order_shipment", "Information", __FILE__, "Moving local order shipment file to archive folder");
								if (copy($xmlfile, $archivePath)) {
									unlink($xmlfile);
								}
								
							} else {
								//Order Shipment XML error
								$errors = $processModel->libxmlGetErrors();
								$errorDesc = implode("\n", $errors);							
								
								throw new Exception($errorDesc);	
							}	
						} catch(Exception $e) {
							//Move the file to Error folder
							$errorPath = $this->localInboundOrdshipErr . $entry;
	
							if (copy($xmlfile, $errorPath)) {
								unlink($xmlfile);
							}
							
							$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
							$loggerModel->saveLogger("order_shipment", "Exception", __FILE__, $errmsg);
							Mage::log($errmsg, Zend_Log::ERR, 'fulfillment');
							
							$this->hasException = true;
							$this->exceptionMessage .= $errmsg;
							
							//Send Notification Mail
							$processModel->notify("order_shipment", $errmsg);
						}
					}
				
					closedir($handle);
					
					$this->logOrdersProcessingStatus();	
				} else {
					//Cannot open the local directory for reading files
					throw new Exception("Cannot open the local directory for reading files");
				}
			} catch (Exception $e) {
				//Exception Message
				throw $e;
			}
		} else {
			//Error no shipment file found
			Mage::log('No shipment file found to process', Zend_Log::DEBUG, 'fulfillment');
			$loggerModel->saveLogger("order_shipment", "Information", __FILE__, "No shipment file found to process");
		}	
		
		Mage::log('Exited otfshipping function', Zend_Log::DEBUG, 'fulfillment');
	}
	
	/**
     * Log the Order ids processed so far
	 *
	 * @param boolean
	 * @return string[optional]
     */
	 
	 public function logOrdersProcessingStatus() 
	 {
		$loggerModel = Mage::getModel('logger/logger');
		
		$summary = $this->getDetailProcessSummary();
		
		if ($summary) {
			Mage::log($summary, Zend_Log::DEBUG, 'fulfillment');
			$loggerModel->saveLogger("order_shipment", "Information", __FILE__, $summary);
		}
	 }

	/**
     * Return the processing summary
	 *
	 * @return array
     */
	 
	  public function processSummary($detail=false) {
		$summary = array();
		
		$filesProcessed = count($this->filesProcessed);
		
		if ($filesProcessed > 0) {
			$fproccess = implode(", ", $this->filesProcessed);
			$summary[] = "File(s) processed: ". $fproccess;
				
			$totShipCreated = count($this->otfShipCreated);
			$ids = "";	
			if ($totShipCreated > 0 and $detail) {
				$shippedOids = implode(", ", $this->otfShipCreated);
				$ids = " [". $shippedOids ."]";
			}
			$summary[] = "Shipment created: ". $totShipCreated . " order(s)". $ids;
		
			$totShipNotCreated = count($this->otfShipNotCreated);
			$ids = "";	
			if ($totShipNotCreated > 0 and $detail) {
				$shippedNotOids = implode(", ", $this->otfShipNotCreated);
				$ids = " [". $shippedNotOids ."]";
			}
			$summary[] = "Shipment not created: ". $totShipNotCreated . " order(s)". $ids;
			
			$totOrderNotFound = count($this->otfShipOrderNotFound);
			$ids = "";	
			if ($totOrderNotFound > 0 and $detail) {
				$orderNotOids = implode(", ", $this->otfShipOrderNotFound);
				$ids = " [". $orderNotOids ."]";
			}
			$summary[] = "Orders not found: ". $totOrderNotFound . " order(s)". $ids;		
						
			$totShipTypeUndefined = count($this->otfShipTypeUndefined);
			$ids = "";
			if ($totShipTypeUndefined > 0 and $detail) {
				$shipUndefinedOids = implode(", ", $this->otfShipTypeUndefined);
				$ids = " [". $shipUndefinedOids ."]";
			}
			$summary[] = "Shipment type undefined: ". $totShipTypeUndefined . " order(s)". $ids;
			
			$totShipNoTrackIds = count($this->otfShipNoTrack);
			$ids = "";
			if ($totShipNoTrackIds > 0 and $detail) {
				$shipNoTrackOids = implode(", ", $this->otfShipNoTrack);
				$ids = " [". $shipNoTrackOids ."]";
			}
			$summary[] = "No tracking number: ". $totShipNoTrackIds . " order(s)". $ids;				
			
			$totShipInvalidCarrier = count($this->otfShipInvalidCarrier);
			$ids = "";
			if ($totShipInvalidCarrier > 0 and $detail) {
				$shipInvalidCarrierOids = implode(", ", $this->otfShipInvalidCarrier);
				$ids = " [". $shipInvalidCarrierOids ."]";
			}
			$summary[] = "Invalid Carrier: ". $totShipInvalidCarrier . " order(s)". $ids;
			
		} else {
			$summary[] = "No File found to process.";
		}
	  
		return $summary;  
	  }
	 
	/**
     * Return the detailed processing summary as string
	 *
	 * @return array
     */
	 
	  public function getDetailProcessSummary() {
		$summary = "";
		$s = $this->processSummary(true);
		
		if (count($s) > 0) {
			$summary = implode(" <br /> ", $s);
		}
		
		return $summary;
	  }
	  
	/**
	 * Return the short processing summary as string
	 *
	 * @return array
	 */
	 
	  public function getShortProcessSummary() {
		$summary = "";
		$s = $this->processSummary();
		
		if (count($s) > 0) {
			$summary = implode(" <br /> ", $s);
		}
		
		return $summary;
	  }
}