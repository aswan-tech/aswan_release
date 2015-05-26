<?php
/**
 * Magento Model to process order confirmations
 *
 * This model defines the functions to process order confirmations.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Tuesday, June 12, 2012
 * @copyright	Four cross media
 */

/**
 * Order Confirmation model class
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
class FCM_Fulfillment_Model_Confirmation extends Mage_Core_Model_Abstract  
{
	private $localFolder;
	private $localInbound;
	
	private $localInboundOrdcnf;
	private $localInboundOrdcnfArcv;
	private $localInboundOrdcnfErr;
	
	private $remoteInboundOrdcnf;

	private $notifyCustomer = 1;
	private $visibleFrontEnd = 0;
	//private $xmlPartialConfirmedStatus = 'P';
	//private $xmlConfirmedStatus = 'C';
	private $xmlAllowedStatuses = array();
	private $xmlAllowedStatusCodes = array();
	private $xmlOrderStatusCodes = array();
	
	public $hasException = false;
	public $exceptionMessage = "";
	
	private $otfOrderConfirmReadSuccess = array();
	private $otfOrderConfirmReadFailure = array();
		
	private $otfOrderConfirmation = array();
	private $otfOrderNotStatusUpdated = array();
	private $otfOrderNotFound = array();
	private $otfOrderStatusUndefined = array();
	
	private $filesProcessed = array();
		 
	protected function _construct() 
	{
		$this->_init('fulfillment/confirmation');		
	}
	
	public function __construct() 
	{
		$this->localFolder = Mage::getBaseDir('var') . DS . 'lecom';
		$this->localInbound = $this->localFolder . DS .'inbound';
				
		$this->localInboundOrdcnf = $this->localInbound . DS . 'ordconfirm' . DS ;	
		$this->localInboundOrdcnfArcv = $this->localInbound . DS . 'ordconfirm_arcv' . DS ;		
		$this->localInboundOrdcnfErr = $this->localInbound . DS . 'ordconfirm_err' . DS ;
		$remoteInboundOrdcnf = Mage::getStoreConfig('orders/paths/otfconfirm');
		
		$this->remoteInboundOrdcnf = trim($remoteInboundOrdcnf);
		
		Mage::getModel('logger/logger')->saveLogger("order_confirm", "Information", __FILE__, "DB Inbound Path:". $this->remoteInboundOrdcnf);
		
		if (empty($this->remoteInboundOrdcnf)) {
			$this->remoteInboundOrdcnf = '/mnt/lecomotf/inbound/ordconfirm/';
		}
		
		
		if (!is_dir($this->localFolder)) {
			mkdir($this->localFolder, 0777);
			chmod($this->localFolder, 0777);
		}
		
		if (!is_dir($this->localInbound)) {
			mkdir($this->localInbound, 0777);
			chmod($this->localInbound, 0777);
		}
		
		if (!is_dir($this->localInboundOrdcnf)) {
			mkdir($this->localInboundOrdcnf, 0777);
			chmod($this->localInboundOrdcnf, 0777);
		}
		
		if (!is_dir($this->localInboundOrdcnfArcv)) {
			mkdir($this->localInboundOrdcnfArcv, 0777);
			chmod($this->localInboundOrdcnfArcv, 0777);
		}
		
		if (!is_dir($this->localInboundOrdcnfErr)) {
			mkdir($this->localInboundOrdcnfErr, 0777);
			chmod($this->localInboundOrdcnfErr, 0777);
		}	
	}

	/**
     * Read Orders Confirmation Feed and update status
	 *
     */
	 
	public function otfconfirm() 
	{
		Mage::log('Entered otfconfirm function', Zend_Log::DEBUG, 'fulfillment');
		
		//$this->remoteInboundOrdcnf = trim($this->remoteInboundOrdcnf);
		
		if (empty($this->remoteInboundOrdcnf)) {
			throw new Exception("Remote inbound folder path not specified for the order confirmation feed");
		}
		
		$loggerModel = Mage::getModel('logger/logger');
		$loggerModel->saveLogger("order_confirm", "Information", __FILE__, "Moving order confirmation feed files from remote server to local server
		");
		
		$processModel = Mage::getModel('fulfillment/process');
		$readStatus = $processModel->readFromRemote($this->remoteInboundOrdcnf, $this->localInboundOrdcnf, 'order_confirm');
		
		if (isset($readStatus['success'])) {
			$this->otfOrderConfirmReadSuccess = $readStatus['success'];
		}
		
		if (isset($readStatus['error'])) {
			$this->otfOrderConfirmReadFailure = $readStatus['error'];
		}
		
		if (count($this->otfOrderConfirmReadFailure) > 0) {
			//Some files could not be read
			$efiles = implode(", ", $this->otfOrderConfirmReadFailure);
			Mage::log('Error transferring files from remote server: '. $efiles, Zend_Log::DEBUG, 'fulfillment');
			
			$loggerModel->saveLogger("order_confirm", "Information", __FILE__, "Error tranferring ". count($this->otfOrderConfirmReadFailure) ." file(s) from remote server");
			
			$this->hasException = true;
			$this->exceptionMessage .= "Error tranferring ". count($this->otfOrderConfirmReadFailure) ." file(s) from remote server";
		}
		
		if (count($this->otfOrderConfirmReadSuccess) > 0) {
			$rfiles = implode(", ", $this->otfOrderConfirmReadSuccess);
			Mage::log('Transferred files from remote server: '. $rfiles, Zend_Log::DEBUG, 'fulfillment');
			
			$loggerModel->saveLogger("order_confirm", "Information", __FILE__, "Transferred ". count($this->otfOrderConfirmReadSuccess) ." file(s) from remote server");
			
			//Delete the files on the remote server
			//The files are already deleted by the read function once they are read to the local server 
			
			$processModel->enableLibXmlErrors(true);
			
			$this->xmlAllowedStatuses = $processModel->getDcStatuses();
			$this->xmlAllowedStatusCodes = $processModel->getDcStatusCodes();
			$this->xmlOrderStatusCodes = $processModel->getOrderStatusCodes();
			
			$loggerModel->saveLogger("order_confirm", "Information", __FILE__, "Processing order confirmation feeds");
			
			try {
				if ($handle = opendir($this->localInboundOrdcnf)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry == "." || $entry == "..") {
							continue;
						}
						
						$xmlfile = $this->localInboundOrdcnf . $entry;	
						$this->filesProcessed[] = $entry;
						
						Mage::log('Processing file: '. $xmlfile, Zend_Log::DEBUG, 'fulfillment');
						
						$doc = new DOMDocument();
						
						try {
							if ($doc->load( $xmlfile )) {
								$confirmations = $doc->getElementsByTagName( "Confirmation" );
								
								//Get DB Connections
								$resource = Mage::getSingleton('core/resource');
								$writeConnection = $resource->getConnection('core_write');
								$salestable = $resource->getTableName('sales/order');
								
								foreach ($confirmations as $confirmation) {
									$orderIdTag = $confirmation->getElementsByTagName( "OrderNumber" );
									$orderId = trim($orderIdTag->item(0)->nodeValue);
																	
									$statusTag = $confirmation->getElementsByTagName( "Status" ); //P => Partial Confirmatin C => Complete Confirmation
									$status = trim($statusTag->item(0)->nodeValue);
									
									if (in_array($status, $this->xmlAllowedStatuses)) {
										$orderStatus = $this->xmlOrderStatusCodes[$status];
									} else {
										$orderStatus = '';
									}
																			
									//Update Order Status
									//Get the order
									if (!empty($orderStatus)) {
										$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
										
										$realOrderId = $order->getId();
										
										if ($realOrderId) {
										
											$orderState = $order->getState();
											$commentTag = $confirmation->getElementsByTagName( "Comment" );
											$comment = trim($commentTag->item(0)->nodeValue);
																								
											$currentOrderStatus = $order->getStatus();
																								
											if ((($status == 'Confirmed') AND !($currentOrderStatus == 'shipped' OR $currentOrderStatus == 'complete')) OR ($status == 'Not Delivered') ) {
												
												//Update the order status
												//if ($status == 'Confirmed' OR $status == 'Not Delivered') {
													$this->notifyCustomer = 1;
													$this->visibleFrontEnd = 0;
												//} else {
												//	$this->notifyCustomer = false;
												//	$this->visibleFrontEnd = 0;
												//}
												
												$order->setState($orderState, $orderStatus, $comment, $this->notifyCustomer)->setIsVisibleOnFront($this->visibleFrontEnd)->save();								
												//$order->sendOrderUpdateEmail($this->notifyCustomer, $comment);
												
												if ($status == 'Not Delivered') {
													//Mail Customer Care
													$processModel->notify("order_confirm",  "Order #". $orderId ." Not Delivered." . $comment);
												}
												
												$query = "UPDATE {$salestable} SET sent_to_erp = '{$this->xmlAllowedStatusCodes[$status]}' WHERE entity_id = {$realOrderId}";
												$writeConnection->query($query);
												
												//Add to the list of orders being updated
												$this->otfOrderConfirmation[] = $orderId;
												
											}  

											else if(($status == 'Partial Shipped')){
												$orderItemsTag = $confirmation->getElementsByTagName( "Items" );
												$itemsQty = array();
												if (!is_null($orderItemsTag->item(0))) {
													$orderItems = $orderItemsTag->item(0)->getElementsByTagName( "Item" );
																													
													foreach ($orderItems as $item) {
														$orderItemSkuTag = $item->getElementsByTagName( "OrderItemNumber" );
														$orderItemSku = trim($orderItemSkuTag->item(0)->nodeValue); 
														$orderItemStatusTag = $item->getElementsByTagName( "OrderItemStatus" );
														$orderItemStatus = trim($orderItemStatusTag->item(0)->nodeValue); 
														$orderItemQtyTag = $item->getElementsByTagName( "OrderItemQty" );
														$orderItemQty = trim($orderItemQtyTag->item(0)->nodeValue); 
														if($orderItemStatus =='Cancelled'){
															foreach ($order->getAllItems() as $o_item) {
    																if($o_item->getSku() == $orderItemSku){
    																	$o_item->setQtyCanceled($orderItemQty);
    																	$o_item->save();
    																}
    																
															}
														}
													}
												}
												$order->setState($orderState, $orderStatus, $comment, $this->notifyCustomer)->setIsVisibleOnFront($this->visibleFrontEnd)->save();
												$query = "UPDATE {$salestable} SET sent_to_erp = '{$this->xmlAllowedStatusCodes[$status]}' WHERE entity_id = {$realOrderId}";
												$writeConnection->query($query);
												$this->otfOrderConfirmation[] = $orderId;


											}

											else if (($status == 'Rejected')  AND !($currentOrderStatus == 'shipped' OR $currentOrderStatus == 'complete')) {
											
												$query = "UPDATE {$salestable} SET sent_to_erp = '{$this->xmlAllowedStatusCodes[$status]}' WHERE entity_id = {$realOrderId}";
												$writeConnection->query($query);
												
												//Add to the list of orders being updated
												$this->otfOrderConfirmation[] = $orderId;
												
											} else if ($status == 'Delivered') {
												$this->notifyCustomer = 1;
												$this->visibleFrontEnd = 0;
													
												//Create invoice for COD orders
												$paymentMethod = $order->getPayment()->getMethod();
												
												if ($paymentMethod == 'cashondelivery') {
													//Create Invoice 
													$data['itemsQty'] = array();
													$data['comment'] = null;
													$data['email'] = false;
													$data['includeComment'] = false;
																										
													if ($this->createInvoice($order, $data)) {
														$orderState = $order->getState(); //Check if state is coming correct or not else chnage it to 'closed'
														//$order->setState($orderState, $orderStatus, $comment, $this->notifyCustomer)->setIsVisibleOnFront($this->visibleFrontEnd)->save();								
														$this->setOrderStatus($order, $orderStatus, $comment, $this->notifyCustomer, $this->visibleFrontEnd);
														$order->save();
														
														$order->sendOrderUpdateEmail($this->notifyCustomer, $comment);
														
														$query = "UPDATE {$salestable} SET sent_to_erp = '{$this->xmlAllowedStatusCodes[$status]}' WHERE entity_id = {$realOrderId}";
														$writeConnection->query($query);
														
														//Add to the list of orders being updated
														$this->otfOrderConfirmation[] = $orderId;
													} else {
														//Add to the list of orders not being updated
														$this->otfOrderNotStatusUpdated[] = $orderId;
													}
								
												} else {
													//Update the order status
																									
													//$order->setState($orderState, $orderStatus, $comment, $this->notifyCustomer)->setIsVisibleOnFront($this->visibleFrontEnd)->save();								
													$this->setOrderStatus($order, $orderStatus, $comment, $this->notifyCustomer, $this->visibleFrontEnd);
													$order->save();
													
													$order->sendOrderUpdateEmail($this->notifyCustomer, $comment);
													
													$query = "UPDATE {$salestable} SET sent_to_erp = '{$this->xmlAllowedStatusCodes[$status]}' WHERE entity_id = {$realOrderId}";
													$writeConnection->query($query);
													
													//Add to the list of orders being updated
													$this->otfOrderConfirmation[] = $orderId;
												}
											
											} else {
												//Add to the list of orders not being updated
												$this->otfOrderNotStatusUpdated[] = $orderId;
											}
											
										} else {
											//Order not found
											$this->otfOrderNotFound[] = $orderId;
										}
									} else {
											//Order Status not defined
											$this->otfOrderStatusUndefined[] = $orderId;
									}
								}
							
								//Move the file to the archive folder
								$archivePath = $this->localInboundOrdcnfArcv . $entry;
															
								$loggerModel->saveLogger("order_confirm", "Information", __FILE__, "Moving local order confirmation file to archive folder");
								if (copy($xmlfile, $archivePath)) {
									unlink($xmlfile);
								}
								
							} else {
								//Order Confirmation XML error
								$errors = $processModel->libxmlGetErrors();
								$errorDesc = implode("\n", $errors);							
								
								throw new Exception($errorDesc);						
							}
						} catch (Exception $e) {
							//Move the file to Error folder
							$errorPath = $this->localInboundOrdcnfErr . $entry;
	
							if (copy($xmlfile, $errorPath)) {
								unlink($xmlfile);
							}
							
							$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
							$loggerModel->saveLogger("order_confirm", "Exception", __FILE__, $errmsg);
							Mage::log($errmsg, Zend_Log::ERR, 'fulfillment');
							
							//Update cron status
							//Set Cron status to 'Failed'
							//$errTime = $processModel->getCurrentDateTime();
							//Mage::getModel('logger/cron')->updateCron("order_confirm", "Failed", "", $errTime, "Exception generated in ". __FILE__ . " at ". __LINE__  );
							$this->hasException = true;
							$this->exceptionMessage = $errmsg;
							
							//Send Notification Mail
							$processModel->notify("order_confirm", $errmsg);
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
			//Error no confirmation file found
			Mage::log('No order confirmation file found to process', Zend_Log::DEBUG, 'fulfillment');
			$loggerModel->saveLogger("order_confirm", "Information", __FILE__, "No order confirmation file found to process");
		}	
		
		Mage::log('Exited otfconfirm function', Zend_Log::DEBUG, 'fulfillment');
	}
	
	/**
     * Function to set the order status
	 *
	 * @param Order
	 * @param boolean
     */
	 
	public function setOrderStatus($order, $status, $comment, $isCustomerNotified=false, $visible=0) 
	{
		$order->setStatus($status);
        $history = $order->addStatusHistoryComment($comment, false); // no sense to set $status again
        $history->setIsCustomerNotified($isCustomerNotified)->setIsVisibleOnFront($visible); // for backwards compatibility
	}
	 
	/**
     * Function to cancel the order
	 *
	 * @param Order
	 * @param boolean
     */
	 
	private function cancelOrder($order) 
	{
		try {
			if ($order->canCancel()) {
				$order->cancel()->save();
				$order->sendOrderUpdateEmail();
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Exception", __FILE__, $errmsg);
			
			$this->hasException = true;
			$this->exceptionMessage .= $errmsg;
			
			//Send Notification Mail
			Mage::getModel('fulfillment/process')->notify("order_confirm", $errmsg);

			return false;
		}
	}
	
	/**
     * Function to create invoice for the order
	 *
	 * @param Order, array
	 * @param boolean
     */
	 
	private function createInvoice($order, $data) 
	{
        /**
         * Check invoice create availability
         */
        if (!$order->canInvoice()) {
             return false;
        }

		$itemsQty = $this->_prepareItemQtyData($data['itemsQty']);
		
        $invoice = $order->prepareInvoice($itemsQty);

        $invoice->register();

        if ($data['comment'] !== null) {
            $invoice->addComment($data['comment'], $data['email']);
        }

        if ($data['email']) {
            $invoice->setEmailSent(true);
        }

        $invoice->getOrder()->setIsInProcess(true);

        try {
            Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder())->save();
            $invoice->sendEmail($data['email'], ($data['includeComment'] ? $data['comment'] : ''));
			
			return true;
        } catch (Mage_Core_Exception $e) {
            $errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Exception", __FILE__, $errmsg);
			
			$this->hasException = true;
			$this->exceptionMessage .= $errmsg;
			
			//Send Notification Mail
			Mage::getModel('fulfillment/process')->notify("order_confirm", $errmsg);

			return false;
        }

        //return $invoice->getIncrementId();
	
	}
	
	/**
     * Prepare items quantity data
     *
     * @param array $data
     * @return array
     */
    private function _prepareItemQtyData($data)
    {
        $quantity = array();
        foreach ($data as $item) {
            if (isset($item->order_item_id) && isset($item->qty)) {
                $quantity[$item->order_item_id] = $item->qty;
            }
        }
        return $quantity;
    }
	
	/**
     * Function to create credit memo
	 *
	 * @param Order, array
	 * @param boolean
     */
	 
	private function createCreditMemo($order, $data) 
	{
		try {
			$service = Mage::getModel('sales/service_order', $order);
			$creditmemo = $service->prepareCreditmemo();

			if ($creditmemo) {
				if (($creditmemo->getGrandTotal() <=0) && (!$creditmemo->getAllowZeroGrandTotal())) {
					Mage::throwException('Credit memo\'s total must be positive.');
				}

				$comment = '';
				if (!empty($data['comment_text'])) {
					$creditmemo->addComment(
						$data['comment_text'],
						isset($data['comment_customer_notify']),
						isset($data['is_visible_on_front'])
					);
					if (isset($data['comment_customer_notify'])) {
						$comment = $data['comment_text'];
					}
				}

				if (isset($data['do_refund'])) {
					$creditmemo->setRefundRequested(true);
				}
				if (isset($data['do_offline'])) {
					$creditmemo->setOfflineRequested((bool)(int)$data['do_offline']);
				}

				$creditmemo->register();
				if (!empty($data['send_email'])) {
					$creditmemo->setEmailSent(true);
				}

				$creditmemo->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
				
				$transactionSave = Mage::getModel('core/resource_transaction')
					->addObject($creditmemo)
					->addObject($creditmemo->getOrder());
				if ($creditmemo->getInvoice()) {
					$transactionSave->addObject($creditmemo->getInvoice());
				}
				$transactionSave->save();
				
				$creditmemo->sendEmail(!empty($data['send_email']), $comment);
				
				return true;
			} else {
				return false;
			}
		} catch (Mage_Core_Exception $e) {
			$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Exception", __FILE__, $errmsg);
			
			$this->hasException = true;
			$this->exceptionMessage .= $errmsg;
			
			//Send Notification Mail
			Mage::getModel('fulfillment/process')->notify("order_confirm", $errmsg);

			return false;
			
		} catch (Exception $e) {
			$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();								
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Exception", __FILE__, $errmsg);
			
			$this->hasException = true;
			$this->exceptionMessage .= $errmsg;
			
			//Send Notification Mail
			Mage::getModel('fulfillment/process')->notify("order_confirm", $errmsg);

			return false;
		}
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
			$loggerModel->saveLogger("order_confirm", "Information", __FILE__, $summary);
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
			
			$totStatusUpdated = count($this->otfOrderConfirmation);
			$ids = "";
			if ($totStatusUpdated > 0 and $detail) {
				$processedOids = implode(", ", $this->otfOrderConfirmation);
				$ids = " [". $processedOids ."]";
			}		
			$summary[] = "Status updated: ". $totStatusUpdated . " order(s)". $ids;
			
			$totStatusNotUpdated = count($this->otfOrderNotStatusUpdated);
			$ids = "";
			if ($totStatusNotUpdated > 0 and $detail) {
				$notProcessedOids = implode(", ", $this->otfOrderNotStatusUpdated);		
				$ids = " [". $notProcessedOids ."]";
			}
			
			$summary[] = "Status not updated: ". $totStatusNotUpdated . " order(s)". $ids;
			
			$totNotFound = count($this->otfOrderNotFound);
			$ids = "";
			if ($totNotFound > 0 and $detail) {
				$orderNotFound = implode(",", $this->otfOrderNotFound);
				$ids = " [". $orderNotFound ."]";
			}
			$summary[] = "Not Found: ". $totNotFound . " order(s)". $ids;
			
			$totStatusNotDefined = count($this->otfOrderStatusUndefined);
			$ids = "";
			if ($totStatusNotDefined > 0 and $detail) {
				$statusUndefined = implode(", ", $this->otfOrderStatusUndefined);
				$ids = " [". $statusUndefined ."]";
			}
			$summary[] = "Status not defined/improper in feed: ". $totStatusNotDefined . " order(s)". $ids;
			
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