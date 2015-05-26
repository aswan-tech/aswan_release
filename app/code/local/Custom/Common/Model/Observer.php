<?php

class Custom_Common_Model_Observer extends Varien_Object
{	
		public function cacherefresh($observer){
			$allTypes = Mage::app()->useCache();
			foreach($allTypes as $key => $value) {
				if($type = "full_page"){
					Mage::app()->getCacheInstance()->cleanType($type); // clear FPC
				}
			}
		}
		
		public function cacherefreshblock($observer){
			$allTypes = Mage::app()->useCache();
			foreach($allTypes as $key => $value) {
				if($type = "block_html"){
					Mage::app()->getCacheInstance()->cleanType($type); // clear Block_Html
				}
			}
		}

	  public function customerRegisterSuccess(Varien_Event_Observer $observer) {
	      $event = $observer->getEvent();
	      $customer = $event->getCustomer();
	      $email = $customer->getEmail();
	      $id = $customer->getId();
	      if($id){
	      	$gaCookies = Mage::getModel( 'nosql/parse_ga' )->getCookies();
           	$source = strtolower($gaCookies['campaign']['source']);
            $campaign = strtolower($gaCookies['campaign']['name']);
            $customer->setData('campaign', $campaign);
        	$customer->setData('source', $source);
        	$customer->save();
	      }
	      
	      if($email){
	      		Mage::getModel('core/cookie')->set('nw_omgpm','yes',3600,'/',null,null,false);
	      		Mage::getSingleton('core/session')->setNewRegistrationUser('complete');
	      }
	  }	
	
	public function addressSave() {
	
			$address_id = Mage::app()->getRequest()->getParam('address_id');			
			$controller = strtolower(Mage::app()->getRequest()->getControllerName());
			$action = strtolower(Mage::app()->getRequest()->getActionName());
			$module = strtolower(Mage::app()->getRequest()->getModuleName());
			
			if(strstr($action,'edit')) {
				$action = 'edit';
				$action2 = 'address_edit';
			} else if(strstr($action,'save')) {
				$action = 'save';
				$action2 = 'address_save';
			} else if(strstr($action,'delete')) {
				$action = 'delete';
				$action2 = 'address_delete';
			} else {
				$action = $action;
				$action2 = $action;
			}
			
			$fullactionname = 'adminhtml_'.$controller.'_'.$action2;
			
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $userId = Mage::getSingleton('admin/session')->getUser()->getId();
            $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        }
		
		$errors = Mage::getModel('adminhtml/session')->getMessages()->getErrors();
        $loggingEvent = Mage::getModel('enterprise_logging/event')->setData(array(			
			'action'        => ucwords(strtolower($action)),
			'event_code'    => 'sales_orders',
            'ip'            => Mage::helper('core/http')->getRemoteAddr(),
            'x_forwarded_ip'=> Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR'),
            'user'          => $username,
            'user_id'       => $userId,
            'is_success'    => empty($errors),
            'fullaction'    => $fullactionname,
			'info'    		=> $address_id,
            'error_message' => implode("\n", array_map(create_function('$a', 'return $a->toString();'), $errors)),
        ));
		$loggingEvent->save();		
	}
}