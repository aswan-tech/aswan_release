<?php

class FCM_Packaging_Model_Order_Creditmemo extends Mage_Sales_Model_Order_Creditmemo
{
    

    protected $_additionalItems;
   
    public function getAllAdditionalItems()
    {
        return $this->_additionalItems;
    }

    /**
     * Register creditmemo
     *
     * Apply to order, order items etc.
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(
                Mage::helper('sales')->__('Cannot register an existing credit memo.')
            );
        }

		$this->_additionalItems = array();
		
        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
            }
            else {
				if ($item->getPckQty() > 0) {
					$item->registerPremium();
					$this->_additionalItems[] = $item;
				}
				
                $item->isDeleted(true);
            }
        }

        $this->setDoTransaction(true);
        if ($this->getOfflineRequested()) {
            $this->setDoTransaction(false);
        }
        $this->refund();

        if ($this->getDoTransaction()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded()+$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded()+$this->getBaseGrandTotal()
            );
        }
        else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded()+$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded()+$this->getBaseGrandTotal()
            );
        }

        $this->getOrder()->setBaseTotalInvoicedCost(
            $this->getOrder()->getBaseTotalInvoicedCost()-$this->getBaseCost()
        );

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }
        return $this;
    }
}
