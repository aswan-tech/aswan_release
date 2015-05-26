<?php
class Custom_Myorder_Model_Observer 
{
	public function saveProductMrpInOrder($observer) {
		$order = $observer->getEvent()->getOrder();
		foreach($order->getAllItems() as $item) {
			$price = $item->getProduct()->getPrice();
			$item->setProductMrp($price);
		}
		return $this;
	}
	
	public function saveProductMrpInInvoice($observer) {
		$invoice = $observer->getEvent()->getInvoice();
		foreach($invoice->getAllItems() as $item) {
			$price = $item->getProductMrp();
			$item->setProductMrp($price);
		}
		return $this;
	}
	
	public function saveProductMrpInShipment(Varien_Event_Observer $observer){
		$shipment = $observer->getEvent()->getShipment();
		foreach($shipment->getAllItems() as $item) {
			$price = $item->getProduct()->getPrice();
			$item->product_mrp = $price;
		}
		return $this;
	}
	
	public function saveProductMrpInCreditMemo($observer) {
		$creditMemo = $observer->getEvent()->getCreditmemo();	
		foreach($creditMemo->getAllItems() as $item) {
			$price = $item->getProductMrp();
			$item->setProductMrp($price);
		}
		return $this;
	}
}