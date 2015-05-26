<?php
/**
 * FCM Order Tracker Module 
 *
 * Module for tracking Customer Order
 *
 * @category    FCM
 * @package     FCM_Tracker
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Tracker_Block_Tracker extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
		//get email or order id from from request
		$request = $this->getRequest()->getParam('login');
		$email = $request['username'];
		$order = $request['id'];
		if(isset($request) && $email !=''){
		//create order collection filter based of email id
        $collection = Mage::getModel('sales/order')->getCollection()
					->addFieldToFilter('customer_email', $email);
		} else if (isset($request) && $order !=''){
		//create order collection filter based of order id
		$collection = Mage::getModel('sales/order')->getCollection()
					->addFieldToFilter('increment_id', $order);
		}
		else {
			$collection = Mage::getModel('sales/order')->getCollection()
							->addFieldToFilter('increment_id', '0000');
		}
		$this->setCollection($collection);
    }
	public function _prepareLayout()
    {
		parent::_prepareLayout();
		//it will create pager based on collection count
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    
     public function getTracker()     
     { 
        if (!$this->hasData('tracker')) {
            $this->setData('tracker', Mage::registry('tracker'));
        }
        return $this->getData('tracker');
        
    }
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	
	public function getPostActionUrl()
    {
        return $this->helper('tracker')->getLoginPostUrl();
    }
	
	 public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }
	public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }
}