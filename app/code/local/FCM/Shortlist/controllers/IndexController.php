<?php

/**
 * @category    FCM
 * @package     FCM_Shortlist
 * @author      Arun Pundir
 * @author_id	51427958
 * @company     HCL Technologies
 * @created 	Friday, January 28, 2013
 * @copyright	Four cross media
 */

class FCM_Shortlist_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function popupAction() {
		$session = Mage::getSingleton("customer/session", array("name" => "frontend"));
        $shortlist_array = unserialize($session->getData("shortlistedProducts"));
		$_cart_items = Mage::getSingleton("checkout/session")->getQuote()->getAllItems();
		if(count($_cart_items) > 0){
			foreach($_cart_items as $_item){
				$product_id = $_item->getProduct()->getId();
				if (is_array($shortlist_array) && in_array($product_id, $shortlist_array)){
					$shortlist_array = array_flip($shortlist_array);
					unset($shortlist_array[$product_id]);
					$shortlist_array = array_flip($shortlist_array);
				}
			}
		}
		krsort($shortlist_array);
		$session->setData("shortlistedProducts", serialize($shortlist_array));

        $html = $this->getLayout()->createBlock('shortlist/shortlist')->setTemplate('shortlist/popup.phtml')->toHtml();
        
		$this->getResponse()->setBody($html);
    }

    public function shortlistAddAction() {
        $session = Mage::getSingleton("customer/session", array("name" => "frontend"));
        $array = unserialize($session->getData("shortlistedProducts"));
		
        if (!in_array($this->getRequest()->getParam('prodId'), $array)){
            $array[time()] = $this->getRequest()->getParam('prodId');
        }
        krsort($array);
        $session->setData("shortlistedProducts", serialize($array));

        $this->popupAction();
    }

    public function shortlistDeleteAction(){

        $session = Mage::getSingleton("customer/session", array("name" => "frontend"));
        $array = unserialize($session->getData("shortlistedProducts"));
		
        if (in_array($this->getRequest()->getParam('prodId'), $array)){
            $array = array_flip($array);
            unset($array[$this->getRequest()->getParam('prodId')]);
            $array = array_flip($array);            
        }
        krsort($array);
        $session->setData("shortlistedProducts", serialize($array));

        $this->popupAction();
    }

    public function shortlistDeleteAllAction(){

        $session = Mage::getSingleton("customer/session", array("name" => "frontend"));
        $session->setData("shortlistedProducts", serialize(array()));

        $this->popupAction();
        
    }
	
	public function asmypixelAction(){
		$pcatid  = $this->getRequest()->getParam('pcatid');
		if($pcatid){
		$gaCookies = Mage::getModel('nosql/parse_ga')->getCookies();
		$utmSource = strtolower( $gaCookies['campaign']['source'] ); 
		if($utmSource == 'mydala'){
		$sessionId =  Mage::getModel('core/session')->getSessionId();
			$parentIds = array(3, 4, 6, 8);
			if(in_array($pcatid, $parentIds)) {	
				echo '<img src="http://www.mydala.com/alliance/pixel/pixserverlead/0/114/0/0/md-'.$sessionId.'" width="1" height="1" border="0"/>';
			}
		}
		}
    }

}