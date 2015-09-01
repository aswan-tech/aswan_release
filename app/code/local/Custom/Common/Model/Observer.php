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
		public function productSaveAfter(Varien_Event_Observer $observer){
			$product = $observer->getProduct();
			$this->setInventoryDate($product);
		}

		public function inventorySaveAfter($observer){
				$model = Mage::getModel('catalog/product');
				$sku = $observer->getSku(); 
				$_productId = $model->getIdBySku($sku);
				$product = Mage::getModel('catalog/product')->load($_productId);
				$this->setInventoryDate($product);
		}
		public function setInventoryDate($product){
			$qty = 0;
			$log_file = 'inv_date.log';
			$model = Mage::getModel('catalog/product');
			if($product->getTypeId()=='simple'){
			  $stockItem =Mage::getModel('cataloginventory/stock_item');
			  $stockItem->assignProduct($product);
			  $qty = round($stockItem ->getData('qty'));

			  if($qty > 0){		    
			    $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
			    if(isset($parentIds[0])){
			    	$configurable_product = Mage::getModel('catalog/product')->load($parentIds[0]);
				    $inventory_date = Mage::getModel('catalog/product')->getResource()->getAttribute('inventory_date')->getFrontend()->getValue($configurable_product);
				    if($inventory_date==null || $inventory_date==''){
					    $created_date=date("Y-m-d H:i:s" , Mage::getModel('core/date')->timestamp(time()));
					    Mage::log("Configurable Product Id:".$configurable_product->getId().$parentIds[0].":".$created_date, null, $log_file);
					    $configurable_product->setData('inventory_date',$created_date)->getResource()->saveAttribute($configurable_product, 'inventory_date');
			    	}
			    	else Mage::log("Already Exists:".$configurable_product->getId().":".$inventory_date, null, $log_file);
			    } 
			    else Mage::log("No Parent Id:".$product->getId(), null, $log_file);
			  } else Mage::log("Less Inventory:".$product->getId().":".$qty, null, $log_file);
			 } 
			else{
				 if($product->getTypeId() == 'configurable') {
		               $itemsinstock = false;
		               $inventory_date = Mage::getModel('catalog/product')->getResource()->getAttribute('inventory_date')->getFrontend()->getValue($product);
		               if($inventory_date==null || $inventory_date==''){
			               $childProducts = $product->getTypeInstance(true)->getUsedProducts ( null, $product);
			               foreach ($childProducts as $simple) {
			                   $stock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($simple)->getQty();
			                   if($stock>0)
			                   	$itemsinstock = true;

			               }
			               if($itemsinstock){
			               		$created_date=date("Y-m-d H:i:s" , Mage::getModel('core/date')->timestamp(time()));
			               		Mage::log("Product Id:".$product->getId()."Created At:".$created_date, null, $log_file);
								$product->setData('inventory_date',$created_date)->getResource()->saveAttribute($product, 'inventory_date');
			               }
			        }
			        else 
			        	Mage::log("Already Exists:".$product->getId().":".$inventory_date, null, $log_file);				}
			}
		}
	  public function customerRegisterSuccess(Varien_Event_Observer $observer) {
	      $event = $observer->getEvent();
	      $customer = $event->getCustomer();
	      $email = $customer->getEmail();
	      $id = $customer->getId();
	      if($id){
	      	$gaCookies = Mage::getModel( 'nosql/parse_ga' )->getCookies();
	      	if(!is_array($gaCookies) || count($gaCookies) <= 0) {
				$gaCookies = Mage::helper('common')->getCustomCookies();
			}
           	$source = strtolower($gaCookies['campaign']['source']);
            $campaign = strtolower($gaCookies['campaign']['name']);
            $customer->setData('campaign', $campaign);
        	$customer->setData('source', $source);
        	$customer->save();
	      }
	      
	      if($email){
	      		Mage::getModel('core/cookie')->set('nw_omgpm','yes',3600,'/',null,null,false);
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