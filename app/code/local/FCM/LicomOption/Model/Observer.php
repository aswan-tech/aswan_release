<?php
/**
 * Magento Model observer to remove old wishlist
 *
 *
 * @category    FCM
 * @package     FCM_LicomOption
 * @author	Dhananjay Kumar
 * @author_id	51399184
 * @company	HCL Technologies
 * @created Thursday, July 4, 2012
 */


class FCM_LicomOption_Model_Observer
{

	  /**
     * Main observer function
     * @Description: This is for removal of old wishlist data of customer during login
     * @param datetime $currentTime current time
     * @param int $daysLimit No. of days configurable from admin
     */
	
 public function removeWishlist($observer)
    {	
	  $currentTime = $this->getCurrentDateTime('Y-m-d H:i:s');
	  $daysLimit = $csvExportFolder = Mage::getStoreConfig('licomoption/wishlistcounter/custome_wishlistcounter');
	 
	  $wishLists = $this->getWishList();
      if(!empty($daysLimit) && $daysLimit!='')
	   {
		   foreach($wishLists as $wl)
		   {
			  $days = $this->dateDiff($wl['added_at'],$currentTime);
			  if($days >= $daysLimit)
			  {
				Mage::getModel('wishlist/item')->load($wl['wishlist_item_id'])->delete();
			  }
		   }
       }
    }
	
	 /**
     * Wishlist function
     * @Description: To get list of wishlist item of a customer
     */
	
public function getWishList() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
		$wishList = Mage::getSingleton('wishlist/wishlist')->loadByCustomer($customer);
        $wishListItemCollection = $wishList->getItemCollection();

		if (count($wishListItemCollection)) {
			$arrProductIds = array();

			foreach ($wishListItemCollection as $item) {
				/* @var $product Mage_Catalog_Model_Product */
				$product['wishlist_item_id'] = $item->getWishlistItemId();
				$product['added_at'] = $item->getAddedAt();
				$arrProductIds[] = $product;
			}
			return $arrProductIds;
           }

    }
	
	 /**
     * getCurrentDateTime function
     * @Description: To get the current datetime
     */

public function getCurrentDateTime($format="")
	{
		if (empty($format)) {
			$format = $this->datetimeformat;
		}
		$dt = Mage::getModel('core/date')->date($format);
		
		return $dt;
	}

	 /**
     * dateDiff function
     * @Description: To get the no. of days between two days
     */
public function dateDiff($start, $end) {

		$start_ts = strtotime($start);

		$end_ts = strtotime($end);

		$diff = $end_ts - $start_ts;

		return round($diff / 86400);

		}

}