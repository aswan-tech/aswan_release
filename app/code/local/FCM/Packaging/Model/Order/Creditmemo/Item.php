<?php
/**
 * Magento Model to override core order creditmemo item for packing change model
 *
 *
 * @category    FCM
 * @package     FCM_Packaging
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Friday, September 28, 2012
 * @copyright	Four cross media
 */

/**
 * Model order creditmemo item class
 *
 * @category    FCM
 * @package     FCM_Packaging
 * @author      Pawan Prakash Gupta <51405591>
 */


class FCM_Packaging_Model_Order_Creditmemo_Item extends Mage_Sales_Model_Order_Creditmemo_Item
{
     /**
     * Applying qty to order item
     *
     * @return Mage_Sales_Model_Order_Shipment_Item
     */
    public function register()
    {
        $orderItem = $this->getOrderItem();

        $orderItem->setQtyRefunded($orderItem->getQtyRefunded() + $this->getQty());
        $orderItem->setTaxRefunded($orderItem->getTaxRefunded() + $this->getTaxAmount());
        $orderItem->setBaseTaxRefunded($orderItem->getBaseTaxRefunded() + $this->getBaseTaxAmount());
        $orderItem->setHiddenTaxRefunded($orderItem->getHiddenTaxRefunded() + $this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxRefunded($orderItem->getBaseHiddenTaxRefunded() + $this->getBaseHiddenTaxAmount());
        $orderItem->setAmountRefunded($orderItem->getAmountRefunded() + $this->getRowTotal());
        $orderItem->setBaseAmountRefunded($orderItem->getBaseAmountRefunded() + $this->getBaseRowTotal());
        $orderItem->setDiscountRefunded($orderItem->getDiscountRefunded() + $this->getDiscountAmount());
        $orderItem->setBaseDiscountRefunded($orderItem->getBaseDiscountRefunded() + $this->getBaseDiscountAmount());
		
		$pksku = $this->getPckSku();
		if (!empty($pksku)) {
			$pckQty = $orderItem->getPckQty();
			$refundedPckQty = $this->getPckQty();
			
			if ($pckQty - $refundedPckQty <= 0) {
				$orderItem->setPckOption(0);
				$orderItem->setPckSku(null);
				$orderItem->setPckQty(0);
			} else {
				$orderItem->setPckQty($pckQty - $refundedPckQty);
			}
		}

        return $this;
    }
	
	/**
     * Declare pck_qty
     *
     * @param   float pck_qty, float $qty
     * @return  Mage_Sales_Model_Order_Creditmemo_Item
     */
    public function setPckQty($pckQty, $qty, $s=false)
    {
		if (!$s) {
			$this->setData('pck_qty', $pckQty);
			return $this;
		}
	
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float) $qty;
        }
        else {
            $qty = (int) $qty;
        }
        $qty = $qty > 0 ? $qty : 0;
		
		if ($this->getOrderItem()->getIsQtyDecimal()) {
            $pckQty = (float) $pckQty;
        }
        else {
            $pckQty = (int) $pckQty;
        }
        $pckQty = $pckQty > 0 ? $pckQty : 0;
		
        /**
         * Check qty availability
         */
		 $netQty = $this->getOrderItem()->getQtyToRefund() - $qty;
		 $netQty = $netQty > 0 ? $netQty : 0;
		 
        if ($pckQty <= $this->getOrderItem()->getPckQty()) {
			$this->setData('pck_qty', $pckQty);
			/*
			$netpckQty = $this->getOrderItem()->getPckQty() - $pckQty ;
			
			if ($netpckQty <= $netQty) {
				$this->setData('pck_qty', $pckQty);
			} else {
				Mage::throwException(
					Mage::helper('sales')->__('Invalid Pack. qty to refund for item "%s"', $this->getName())
				);
			}*/
        }
        else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid Pack. qty to refund for item "%s"', $this->getName())
            );
        }
        return $this;
    }
	
	/**
     * Update packaging fields for items in case the item is not being refunded itself
     *
     * @return Mage_Sales_Model_Order_Shipment_Item
     */
	public function registerPremium()
	{
		$orderItem = $this->getOrderItem();
		
		$pksku = $this->getPckSku();
		if (!empty($pksku)) {
			$pckQty = $orderItem->getPckQty();
			$refundedPckQty = $this->getPckQty();
			
			if ($pckQty - $refundedPckQty <= 0) {
				$orderItem->setPckOption(0);
				$orderItem->setPckSku(null);
				$orderItem->setPckQty(0);
			} else {
				$orderItem->setPckQty($pckQty - $refundedPckQty);
			}
		}

        return $this;
	}
}
