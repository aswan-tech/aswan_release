<?php

 class Custom_Common_Model_Common extends Mage_Core_Model_Abstract
 {
	public function _construct()
    {
        parent::_construct();
        $this->_init('common/common');
    }
    
   // This function is used to get admin email address
    function getAdminEmail(){
    	$model = Mage::getModel('admin/user');
		$admins = $model->getCollection();
		$list_emails = array();
		foreach($admins as $admin){
			$list_emails[] = $admin->getEmail();
		}
		return (isset($list_emails[0]))?$list_emails[0]:'';	
    } // End of getAdminEmail
	
	
	function sendNotificationEmail($message, $subject = 'Cron Tab Notification', $toEmail="", $senderName = 'Lecom System Admin', $senderEmail = 'no-reply@Lecom.com'){
		$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('custom_admin_error_notification');
		$emailTemplate->setSenderName($senderName);
		$emailTemplate->setSenderEmail($senderEmail);
		$emailTemplate->setTemplateSubject($subject);
		
		$emailTemplateVariables = array('message'=>$message);
		 
		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
		
		if (empty($toEmail)) {
			$toEmail = $this->getAdminEmail();
		}
		
		if($toEmail) {
			$emailTemplate->send($toEmail,'Admin', $emailTemplateVariables);
		}	

	}

	//used to remove image directory when a store is being deleted
	public function rrmdir($dir) { 
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
						if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object); 
				} 
			} 
		reset($objects); 
		rmdir($dir); 
		}	 
	}
	
	function curPageURL() {
			$pageURL = 'http';
				if(Mage::app()->getStore()->isCurrentlySecure()) {$pageURL .= "s";}
					$pageURL .= "://";
						if (strstr($_SERVER["REQUEST_URI"],'index.php')) {
							$pageURL .= $_SERVER["SERVER_NAME"].'/index.php/';
						} else {
							$pageURL .= $_SERVER["SERVER_NAME"]."/";
						}
					return $pageURL;
	}
	
	/* create coupons for a customer */
	public function createRegistrationCoupon($email = '',$id = ''){
		
        $result = array();
		
		$shouldGenerateCoupons = Mage::getStoreConfig("registercoupens/general/enable");
		
		$daysToBeSet = (int)Mage::getStoreConfig("registercoupens/general/expiredays");
		
		$ruleIdsToBeProcessed = Mage::getStoreConfig("registercoupens/general/ruleid");
		
		$couponLength = Mage::getStoreConfig("registercoupens/general/coupon_length");
		
		$couponPrefix = Mage::getStoreConfig("registercoupens/general/coupon_prefix");
		
		$couponSuffix = Mage::getStoreConfig("registercoupens/general/coupon_suffix");
		
		$emailIds = Mage::getStoreConfig("registercoupens/general/failure_email");
		
		$couponFormat = Mage::getStoreConfig("registercoupens/general/coupon_format");
		
		$rule_ids = explode(",",$ruleIdsToBeProcessed);
		
		$generated_coupon_array = array();
	
		if($shouldGenerateCoupons == 1){
			$counter = 0;

			/** @var $rule Mage_SalesRule_Model_Rule */
			foreach($rule_ids as $rule_id){
				$rule = Mage::getModel('salesrule/rule')->load($rule_id);

				if (!$rule->getId()) {
					$result[$rule_id] = Mage::helper('salesrule')->__('Rule is not defined');
				} else {
					$_now = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
					$_rule_expiry = $rule->getToDate();
					
					$_promotion_expired = false;
					
					if(isset($_rule_expiry)){
						if($_rule_expiry < $_now){
							$_promotion_expired = true;
						}
					}
					if(($rule->getIsActive() == 1) && (!$_promotion_expired)){
						if($rule->getUseAutoGeneration() == 1){
							$rule_data = $rule->getData();
							
							if(isset($rule_data['uses_per_customer']) && $rule_data['uses_per_customer'] != ''){
								$usesPerCustomer = $rule_data['uses_per_customer'];
							}else{
								$usesPerCustomer = '1';
							}
							if(isset($rule_data['uses_per_coupon']) && $rule_data['uses_per_coupon'] != ''){
								$usesPerCoupon = $rule_data['uses_per_coupon'];
							}else{
								$usesPerCoupon = '1';
							}
							
							$current_date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
												
							$date=date_create($current_date);
							
							date_add($date,date_interval_create_from_date_string("".$daysToBeSet." days"));
												
							$promotionExpiryDate = $rule_data['to_date'];
							
							$date_to_compare = date_format($date,"Y-m-d");
							
							if($promotionExpiryDate != ''){
								if($promotionExpiryDate < $date_to_compare){
									
									$date_promotion=date_create($promotionExpiryDate);
									
									$to_date = date_format($date_promotion,"Y-m-d H:i:s");
								}else{
									$to_date = date_format($date,"Y-m-d H:i:s");						
								}
							}else{
								$to_date = date_format($date,"Y-m-d H:i:s");
							}
							
							try {
								$data = Array(
									"rule_id" => $rule->getId(),
									"qty" => "1",
									"length" => $couponLength,
									"format" => $couponFormat,
									"prefix" => $couponPrefix,
									"suffix" => $couponSuffix,
									"dash" => "0",
									"uses_per_coupon" => $usesPerCoupon,
									"uses_per_customer" => $usesPerCustomer,
									"customer_email" => $email,
									"customer_id" => $id,
									"to_date" => $to_date
								);
								
								/** @var $generator Mage_SalesRule_Model_Coupon_Massgenerator */
								$generator = $rule->getCouponMassGenerator();
												
								if (!$generator->validateData($data)) {
									Mage::log("Invalida Data provided for creating coupon - Coupon creation skipped for rule id:".$rule->getId());
									
									$result[$rule_id] = "Invalida Data provided for creating coupon - Coupon creation skipped for rule id:".$rule->getId();
								} else {
									$generator->setData($data);
									
									$generated_coupon = $generator->generatePoolCustom();
									
									$generated = $generator->getGeneratedCount();
								}
								if($generated_coupon != ''){
									$counter++;
									
									$read_connection = Mage::getSingleton('core/resource')->getConnection('core_read');
									
									$read_data = "SELECT `value` FROM `aitoc_salesrule_display_title` WHERE `rule_id`=".$rule->getId()." AND `store_id`=".Mage::app()->getStore()->getStoreId();
															
									$output = $read_connection->fetchAll($read_data);
									
									if(sizeof($output) > 0){
										$description = $output[0]['value'];
									}else{
										$description = $rule_data['description'];
									}
									$generated_coupon_array['rule'.$counter]['coupon_code'] = substr($generated_coupon,0,strlen($generated_coupon)-1);
									$generated_coupon_array['rule'.$counter]['description'] = $description;
								}
							} catch (Mage_Core_Exception $e) {
								Mage::log("Following Mage Exception occured :".$e." - Coupon creation skipped for rule id:".$rule->getId());
									
								$result[$rule_id] = "Following Mage Exception occured :".$e." - Coupon creation skipped for rule id:".$rule->getId();
							} catch (Exception $e) {
								Mage::log("Following Exception occured :".$e." - Coupon creation skipped for rule id:".$rule->getId());
									
								$result[$rule_id] = "Following Exception occured :".$e." - Coupon creation skipped for rule id:".$rule->getId();
							}
						}
						else{
							$result[$rule_id] = Mage::helper('salesrule')->__('Rule is not set to Auto-Generation mode.');
						}
					}
					else{
						$result[$rule_id] = Mage::helper('salesrule')->__('Rule is Disabled or Expired.');
					}
				}				
			}
		}else{
			Mage::log("Coupon generation during registration is disabled");
		}
		
		if(!empty($result)){
			$_message_tobe_sent = "Following rule id's have some problem, hence coupon was not generated for them.<br /><table><tr><th>RULE ID &nbsp;&nbsp;&nbsp;</th><th>ERROR OCCURED</th></tr>";
			foreach($result as $key=>$value){
				$_message_tobe_sent .="<tr><td>".$key."</td><td>".$value."</td></tr>";
			}
			$_message_tobe_sent .= "</table>";
			
			$emailIds = explode(",",$emailIds);
			
			foreach($emailIds as $emailId){
				$email = trim($emailId);
			
				if (!Zend_Validate::is($email, 'EmailAddress')) {
					$_failure_email_ids = $email.',';
					continue;
				}			
				/* Sending Mail to that specific Id */
				
				$this->sendNotificationEmail($_message_tobe_sent, "Coupon Code Not Generated", $email);
			}
		}
		if(!empty($generated_coupon_array)){
			foreach($generated_coupon_array as $key => $value){
				$array[$key] = new Varien_Object($value);
			}
			
			$array = new Varien_Object($array);
			
			return $array;
		}else{
			return '';
		}
	}
	
	
	/* create coupon for already registered customer */
	public function createCouponForAlreadyRegistered($rule_id, $storeId=1, $webSiteId=1){
        $error = array();
		
		$oDb     = Mage::getSingleton('core/resource')->getConnection('core_read');
		$oSelect = $oDb->select();
		$oSelect->from(array('asac' => 'aitoc_salesrule_assign_cutomer'), array('customer_id', 'coupon_code'));
		$oSelect->joinInner(
			array('main_table' => 'salesrule'),
			'main_table.rule_id = asac.entity_id and main_table.is_active=1 and main_table.use_auto_generation=0',
			//array('rule_id', 'name', 'from_date', 'to_date', 'uses_per_customer', 'discount_amount', 'times_used', 'coupon_type', 'use_auto_generation', 'uses_per_coupon')
			array('rule_id', 'description', 'from_date', 'to_date')
		);
		$oSelect->joinLeft(
			array('asdt' => 'aitoc_salesrule_display_title'),
			'main_table.rule_id = asdt.rule_id and asdt.store_id="'.$storeId.'"',
			array('value')
		); 
		$oSelect->joinInner(
			array('customer' => 'customer_entity'),
			//"customer.entity_id = asac.customer_id and customer.is_active=1 and customer.website_id='".$webSiteId."' and customer.store_id='".$storeId."'",
			"customer.entity_id = asac.customer_id and customer.is_active=1 ",
			array('email', 'group_id')
		);
		$oSelect->where('asac.entity_id = "' . $rule_id . '" and (to_date IS NULL OR to_date >= curdate())');
		$sValue = $oDb->fetchAll($oSelect);
			
		if(sizeof($sValue)){
			foreach($sValue as $ruleData){
				try {
					$coupon['to_date'] 		= $ruleData['to_date'] == '' ? '': Mage::getModel('core/date')->date('d-m-Y', strtotime($ruleData['to_date']));
					$coupon['coupon_code'] 	= $ruleData['coupon_code'];
					$coupon['description'] 	= $ruleData['value'] != '' ? $ruleData['value'] : $ruleData['description'];
					$cpnObj = new Varien_Object($coupon);
					
					$customer = Mage::getModel('customer/customer')->load($ruleData['customer_id']);					
					$customer->sendExistingAccountEmail('alreadyregistered', $cpnObj, $storeId);
					unset($customer);
				} catch (Exception $e) {
					Mage::log("Following Exception occured :".$e->getMessage()." - Coupon(".$ruleData['coupon_code'].") sending skipped for customer:".$ruleData['email']);
					$error[$ruleData['email']] = "- Coupon sending skipped for customer: ".$ruleData['email'];
				}
			}
			//die;
		}else{
			$error[] = "- No customer/coupon to send OR this rule is set to Auto Generation mode OR expiry date for this coupon is already passed.";
		}
		
		return $error;
	}
}
