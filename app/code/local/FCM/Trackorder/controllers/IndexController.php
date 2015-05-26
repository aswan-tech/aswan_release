<?php
/**
 * Magento Controller to Track the Order Status
 *
 * This defines functions to Track the order for Guest and Registered Users.
 *
 * @category    FCM
 * @package     FCM_Trackorder
 * @author		Vishal Verma
 * @author_id	51427958
 * @company		HCL Technologies
 * @created 	Friday, August 10, 2012
 * @copyright	Four cross media
 */


/**
 * Controller for Track Order functions
 *
 * @category    FCM
 * @package     FCM_Trackorder
 * @author      Vishal Verma <51427958>
 */

class FCM_Trackorder_IndexController extends Mage_Core_Controller_Front_Action
{	
	/*
	 * To render the track order form for logged-in user
	 * 
	 */
    public function indexAction(){
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			//If not logged in, redirect to login page
			Mage::getSingleton('customer/session')->addError($this->__('Please login in, to track your order.'));
            $this->_redirect('customer/account/login');
            return;
		}			
		
		$this->renderLayout();
    }
	
	/*
	 * To render the detail page to logged-in user
	 * 
	 */
	public function detailAction(){
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			//If not logged in, redirect the user to login page
			Mage::getSingleton('customer/session')->addError($this->__('Please login in, to track your order.'));
            $this->_redirect('customer/account/login');
            return;
		}
		
		$this->renderLayout();
	}
	
	/*
	 * To render the track order form for guest user
	 * 
	 */
	public function guestAction(){
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
			//If logged in, redirect to my account section
            $this->_redirect('trackorder/index/index');
            return;
		}
		
		$this->renderLayout();
    }
	
	/*
	 * To render the detail page to guest user
	 * 
	 */
	public function orderdetailAction(){
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		
		$this->renderLayout();
	}
}