<?php
/**
 * Block to extend the Magento sales orders create items grid
 *
 * This block overrides the core block to set the isPackaging Product.
 *
 * @category    FCM
 * @package     FCM_Packaging
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Thursday, September 27, 2012
 * @copyright	Four cross media
 */

/**
 * Extending Adminhtml sales order create items grid block
 *
 * @category   FCM
 * @package    FCM_Packaging
 * @author	   Pawan Prakash Gupta <51405591>
 */

class FCM_Packaging_Block_Adminhtml_Sales_Order_Create_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
{

    public function getItems()
    {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            // To dispatch inventory event sales_quote_item_qty_set_after, set item qty
            $item->setQty($item->getQty());
            $stockItem = $item->getProduct()->getStockItem();
            if ($stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                // This check has been performed properly in Inventory observer, so it has no sense
                /*
                $check = $stockItem->checkQuoteItemQty($item->getQty(), $item->getQty(), $item->getQty());
                $item->setMessage($check->getMessage());
                $item->setHasError($check->getHasError());
                */
                if ($item->getProduct()->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
                    $item->setMessage(Mage::helper('adminhtml')->__('This product is currently disabled.'));
                    $item->setHasError(true);
                }
            }
			/*			
			//Check if product is a premium packaging product or not
			$categoryIds = $item->getProduct()->getCategoryIds();
			$isPremiumPackaging = false;
		
			$isPremiumPackaging	=	Mage::getModel('packaging/packaging')->bool_isPremiumPackaging($categoryIds);
		
			$item->setIsPackagingProduct($isPremiumPackaging);
			*/
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }

}
