<?php

class Custom_Common_Helper_Data extends Mage_Core_Helper_Abstract{
	function currency($price, $withCurrency = true, $toCurrency = '', $fromCurrency = ''){
		//return '<span class="WebRupee">` '.number_format($price,2).'</span>';
		
		if($withCurrency){
			return Mage::helper('core')->currency($price, true, false);
		}else{
			if($fromCurrency == ''){
				$fromCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
			}else{
				$fromCurrencyCode = $fromCurrency;
			}
			
			if($toCurrency == ''){
				$toCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
			}else{
				$toCurrencyCode = $toCurrency;
			}
			
			$convertedprice	=	Mage::app()->getStore()->roundPrice(Mage::helper('directory')->currencyConvert($price, $fromCurrencyCode, $toCurrencyCode));
			return $convertedprice;
		}
	}
	
	function readCSV(){
		$line_of_text 		= array();
		$codeCurrencyArr 	= array();
		
		$filePath = Mage::getBaseDir('app').DS.'code'.DS.'local'.DS.'Custom'.DS.'Common'.DS.'etc'.DS;
		$fileName = 'currencycountries.csv';
		$file	  =	$filePath.$fileName;
		
		$allowedCountries = Mage::getResourceModel('directory/country_collection')->loadByStore()->toOptionArray(); 

		$cntry = array();

		foreach($allowedCountries as $country) {
			if (empty($country['value'])) {
				continue;
			}
			
			$code = $country['value'];
			$cntry[$code] = $country['label'];
		}
		
		if (count($cntry) == 0) {
			return $codeCurrencyArr;
		}
		
		
		if(is_file($file)){
			try {
				$i = 0;
				$file_handle = fopen($file, 'r');
				while (!feof($file_handle) ) {
					$line_of_text[] = fgetcsv($file_handle, 1024);
					
					$csvcurrency = trim($line_of_text[$i][2]);
					
					if(!empty($csvcurrency)){
						$csvcode = trim($line_of_text[$i][1]);
						
						if (array_key_exists($csvcode, $cntry)) {
						$countyName = $cntry[$csvcode];
						
							if (!empty($countyName)) {
								$codeCurrencyArr[$csvcurrency][$i]['code'] = $csvcode;
								$codeCurrencyArr[$csvcurrency][$i]['country'] = $countyName;
								$codeCurrencyArr[$csvcurrency][$i]['currency'] = $csvcurrency;
							} 
						}
					}
					
					$i++;
				}
				fclose($file_handle);
				
			} catch (Exception $e) {
				Mage::log('CurrencyCountries: '.$e->getMessage());
			}
		}

		return $codeCurrencyArr;
	}
	
	function importCSVData(){
		//from 
		//C:\wamp\www\lecomnew\app\code\community\RocketWeb\ProductVideo\Model\Videos.php
		//C:\wamp\www\lecomnew\app\code\community\RocketWeb\ProductVideo\Helper\Data.php
		
		error_reporting(E_ALL);
		
		$logger = Mage::getModel('logger/logger');	// logger model object will be created
		print "Currency Countries read into array Process started...<br>";
		//$filePath, will define on which folder the required CSV file will be uploaded
		$filePath = Mage::getBaseDir('media').DIRECTORY_SEPARATOR.'CurrencyCountries'.DIRECTORY_SEPARATOR;
		
		//$archiveDir, will define on which folder the required CSV file will be moved after importing all the data
		$archiveDir = Mage::getBaseDir('var').DIRECTORY_SEPARATOR.'CurrencyCountriesArchive'.DIRECTORY_SEPARATOR;
		
		if(!is_dir($filePath)) {
			mkdir($filePath, 0777, true);
		}
		if(!is_dir($archiveDir)) {
			mkdir($archiveDir, 0777, true);
		}
		
		if(!$dh  = opendir($filePath)){
			$msg = "ERROR::could not open directory for reading!";
			$logger->saveLogger('CurrencyCountries', 'Error', 'currencycountries.csv', $msg);
			print $msg;
			return;			
		}
			
		$file_array	= array();
		//$files_to_be_archived = array();
		while (false !== ($filename = readdir($dh))) {
			if(stristr($filename, '.csv'))
			  $file_array[] = $filename;
		}
		
		$files_to_be_archived = $file_array;
		
		//It will check whether any file is available for uploading in $filePath or not
		if(count($file_array) == 0){
			$msg = 'ERROR::No file available to Archive!';
			$logger->saveLogger('CurrencyCountries', 'Error', 'currencycountries.csv', $msg);
			print $msg;
			return;
		}
		
		//Extra code added to pick up the latest file uploaded to read Currency Countries
		$file_array_length = count($file_array);
		if($file_array_length > 0){
			$file_array = $file_array[$file_array_length - 1];
		}
		
		$path = $filePath.$file_array;
		$pathArchive = $archiveDir.$file_array;
		
		//Open required file in readable mode
		$dataStr = fopen($path, "r");
		if(!$dataStr){
			$msg = 'ERROR::Could not open up the file for reading!';
			$logger->saveLogger('CurrencyCountries', 'Error', 'currencycountries.csv', $msg);
			print $msg;
			return;
		}
		
		/*
        $headersData = fgetcsv($dataStr);
		while ($data = fgetcsv($dataStr)) {
			print "<pre>";
				print_r($data);
			print "</pre>";
		}
		*/
		
		try {
			$csvHeader = Array ('code', 'currency', 'country');
			
			$filesRead = fopen($path, 'r');
			$headersData = fgetcsv($filesRead);
			$match = 0;
			foreach($headersData as $data){
				if(in_array(strtolower($data), $csvHeader)){
						$match = 1;
				}else{
					$match = 0;				
				}
			}
			if($match==0){
				echo 'ERROR::Header not match!'; 
				$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
				exit;
			}
			
			$rowCount = 0;
			$cnt = 1;
			$filesRead = fopen($path, 'r');
			$headersData = fgetcsv($filesRead);
			while ($data = fgetcsv($filesRead)) {
			
			
			}
		
			//CSV file should not be blank.
			if($rowCount == 0){
				$msg = 'ERROR::No records found in CSV file!<br/><br/>';
				$logger->saveLogger('CurrencyCountries', 'Error', $file, $msg);
				$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
				exit;
			}
		
            $temp = Mage::getModel('productvideo/videos');
			$temp->loadDataInfile($path, $files_to_be_archived, $filePath ,$archiveDir);
			$msg = 'Sucess::Your file has sucessfully been imported!';
			//put status in logger after sucessful import
			$logger->saveLogger('CurrencyCountries', 'Success', 'currencycountries.csv', $msg);
			//moving the processed file to archive folder and remove it from original one
			fclose($dataStr);
			Mage::log('CurrencyCountries: '.$msg);
        } catch (Exception $e) {
			Mage::log('CurrencyCountries: '.$e->getMessage());
			print $e->getMessage();
            die();
        }
		
		
		//extra code added to move all extra files to archive directory
		foreach($files_to_be_archived as $archive){
			chmod($filePath.$archive, 0777);
			copy($filePath.$archive, $archiveDir.$archive);
			if (!unlink($filePath.$archive))
				echo ("Error deleting $archive<br/>"); 
		}
		print $msg;
	}
	
	public function moveToArchive($files_to_be_archived, $filePath ,$archiveDir){
		/* extra code added to move all extra files to archive directory */
		foreach($files_to_be_archived as $archive){
			chmod($filePath.$archive, 0777);
			copy($filePath.$archive, $archiveDir.$archive);
			if (!unlink($filePath.$archive)){
				echo ("Error deleting $archive<br/>"); 
			}
		}
	}
	
	public function getSwitchCurrencyCountryUrl($params = array())
    {
        $params = is_array($params) ? $params : array();

        if ($this->_getRequest()->getAlias('rewrite_request_path')) {
            $url = Mage::app()->getStore()->getBaseUrl() . $this->_getRequest()->getAlias('rewrite_request_path');
        }
        else {
            $url = Mage::helper('core/url')->getCurrentUrl();
        }
        $params[Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED] = Mage::helper('core')->urlEncode($url);

        return $this->_getUrl('common/index/switchcurrency', $params);
    }
    
    public function getItemByType() {
		$cart = Mage::getModel('checkout/cart')->getQuote();
		$itemArr = array();
		foreach ($cart->getAllItems() as $item) {
			$itemArr[] = $item->getProduct()->getTypeID();           
		}
		return $itemArr;
	}
	
	/*
	 * getCouponCodeByPrice() method is used to get coupon code
	 * @param $productPrice Integer
	 * @return String
	 */
	 
	 public function getCouponCodeByPrice($productPrice) {
		 $write = Mage::getSingleton('core/resource')->getConnection('core_write');
		 
		 $query = "SELECT code 
					FROM salesrule_price_range 
					WHERE (
							price_to >= ".(int)$productPrice." 
							AND 
							price_from <= ".(int)$productPrice."
						)
					AND is_active = 1	";
		$result = $write->query($query);
		$rows = $result->fetch();
		
		if(isset($rows['code']) && !empty($rows)) {
			return $rows['code'];
		}
		else {
			$query = "SELECT code 
						FROM salesrule_price_range 
						WHERE (
								price_from <= ".(int)$productPrice." 
								AND 
								price_to = 0
							)
						AND is_active = 1	";
			$result = $write->query($query);
			$rows = $result->fetch();
			return $rows['code'];
		}		
	 }
	 
	 /*
	  * getCouponDiscountByCode() method is used to get coupon discount with discounted price
	  * @param $couponCode String
	  * @param $productPrice Integer
	  * @return Integer
	  */ 
	 public function getCouponDiscountByCode($couponCode, $productPrice) {
		if(!empty($couponCode)) {
			$oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
			$oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
			$simple_action = $oRule->getData('simple_action');
			if($simple_action == 'cart_fixed') {
				$discount = (int)$oRule->getDiscountAmount();
				return (int)($productPrice - $discount);
			}
			else if($simple_action == 'by_percent') {
				$discount = (int)$oRule->getDiscountAmount();
				return (int)($productPrice - (($productPrice * $discount) / 100));
			}
		}
	 }
	
	/*
	 * getTopProducts() method is used to get top products on home & catalog page
	 * @param $filter String, default homepage
	 * @return Array
	 */
	    
    public function getTopProducts($filter = 'homepage', $pagename) {
		$department = str_replace('.html', '', (trim($pagename, '/')));
		if($department == 'womenswear') {
		$category_id = 8;	
		}
		else if($department == 'menswear') {
		$category_id = 6;
		}
		$catagory_model = Mage::getModel('catalog/category')->load($category_id);
		$products = Mage::getModel('catalog/product')->getCollection();
		if($filter == 'catalog') {
			$products->addAttributeToFilter('top_products_on_catalog', 1);
		}
		else{
			$products->addAttributeToFilter('top_products_on_homepage', 1);
		}
		$products->addCategoryFilter($catagory_model);		
		$products->addAttributeToSelect('*');
		$products->setPageSize(9);
		$products->load();
		$returnArr = array();
		$prdImg = '';
		foreach($products as $_product) {
			$returnArr[] = array('sku'=>$_product->getSku(), 'image'=>$_product, 'prdUrl'=>$_product->getProductUrl());
		}
		
		return array_chunk($returnArr, 3);
	} 

   public function checkMinPurchageAmount($amount) {
        if($amount <=0 ) {
                    return false;
        }
        $totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals();
        $grandTotal = round($totals["grand_total"]->getValue());
        $subTotal = round($totals["subtotal"]->getValue());
        $discount = 0;
        if(isset($totals['discount'])) {
            $discount = (int) $totals['discount']->getValue();
        }
                #pr('(' . $subTotal . ' + ' . $discount . ') = ' . ($subTotal + $discount) . ' >= ' . $amount, 0);
        if(($subTotal + $discount) >= $amount) {
                    return true;
        }
        else {
                    return false;
        }
    }



    function generateOtp($mobile,$email=null){
                $template = 'NEW_REG';
                $data['send_to'] = $mobile;
                if($emailid)
                	$data['emailid'] = $email;
                $data['name'] = 'User';
                $codCode = Mage::helper('nosql/product')->cod_text();
                Mage::getSingleton('core/session')->setRegOtp($codCode);
                $data['codvarcode'] = $codCode;
                $data['template'] = $template;
                $helper = Mage::helper('nosql/joker');
                try{
                	$logfile = 'registerOtp.log';
                	Mage::log("Mobile -".$mobile." OTP-".$codCode,null,$logfile);
                	$helper->sendNow($data, 'sms', $template);
                	return true;
                }catch(Exception $e){
                	return $e->getMessage();
                }
                
    }

    function checkMob($mobile){
    	$model = Mage::getSingleton('customer/customer');
		$customerCollection = $model->getCollection()
					    	->addAttributeToSelect('*')
					    	->addAttributeToFilter('telephone', array('like' => $mobile));

					foreach($customerCollection as $customerObject) 
					{       
					    $customerId = $customerObject->getId();
					    if($customerId)
					    	break;

					}

					if($customerId)
						return $customerId;
					else
						return false;
    }
    function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9), range('A', 'Z'));

	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }

	    return $key;
	}

	public function getNewUserCouponCode($rule_id,$customer_id){
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "select coupon_code from aitoc_salesrule_assign_cutomer where entity_id='$rule_id' && customer_id='$customer_id'";
		$coupon_code= $connection->fetchOne($sql); //fetchRow($sql), fetchOne($sql),...
		if($coupon_code)
			return $coupon_code;
		else
			return false;
	}
	
	/*
	 * verifyLiveDomain() method is used to check live domain url
	 * @param Null
	 * @return Boolean
	 */
	  
	public function verifyLiveDomain() {
		$domainName = trim(str_replace(array('http://', 'https://', '/'), '', Mage::app()->getStore()->getBaseUrl()));
		if($domainName == "www.americanswan.com") {
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getGTMCode(){
		if($this->verifyLiveDomain()) {
			return "<!-- Google Tag Manager -->
					<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=GTM-K2R8KS\"
					height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
					<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
					new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
					'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,'script','dataLayer','GTM-K2R8KS');</script>
					<!-- Google Tag Manager -->";
		}
		else {
			return "<!-- Google Tag Manager -->
					<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=GTM-WBL5XF\"
					height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
					<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
					new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
					'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,'script','dataLayer','GTM-WBL5XF');</script>
					<!-- Google Tag Manager -->";
		}
	}
	
	public function saveSourceCampaign($order, $customerType) {
		$customer_id = $order->getData('customer_id');
		$orderId = $order->getData('entity_id');
		
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		
		$gaCookies = Mage::getModel( 'nosql/parse_ga' )->getCookies();
		if(!is_array($gaCookies) || count($gaCookies) <= 0) {
			$gaCookies = $this->getCustomCookies();
		}
		$source = strtolower($gaCookies['campaign']['source']);
		$campaign = strtolower($gaCookies['campaign']['name']);
	
		/*
		 * save source & campaign in customer table
		 */
		  
		if(empty($customer->getSource()) && empty($customer->getCampaign()) && $customerType = 'NEWUSER') {
		   $customer_data = Mage::getModel('customer/customer')->load($customer_id)
						   ->setSource($source)
						   ->setCampaign($campaign);
		   $customer_data->setId($customer_id)->save();
		 }
		 
		 /*
		  * save source & campaign in order table
		  */ 
	   
		$orderModel = Mage::getModel('sales/order')->load($orderId)
					->setSource($source)
					->setCampaign($campaign);
		$orderModel->setId($orderId)->save();
	}
	
	/*
	 * 
	 */
	 
	 public function checkUtmzscCookies(){
		$getData = Mage::app()->getRequest()->getParams();
		$_refererUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
		$_refererDomain = str_ireplace('www.', '', parse_url($_refererUrl, PHP_URL_HOST));
		$utm_source = isset($getData['utm_source']) ? $getData['utm_source'] : (isset($_refererDomain) ? $_refererDomain : '(direct)');
		$utm_medium = isset($getData['utm_medium']) ? $getData['utm_medium'] : '';
		$utm_campaign = isset($getData['utm_campaign']) ? $getData['utm_campaign'] : '';

		$__utmzsc = Mage::getModel('core/cookie')->get('__utmzsc');
		$__utmzscVal = '';
		if(empty($__utmzsc) || strlen($__utmzsc) <= 0 ) {
			$__utmzscVal =  $utm_source.":".$utm_campaign.":".$utm_medium;
		}
		
		if(!empty($__utmzscVal) && $__utmzscVal != '::') {
			return $__utmzscVal;
		}
	 }
	 
	 public function getCustomCookies() {
		$__utmzsc = Mage::getModel('core/cookie')->get('__utmzsc');
		
		if(empty($__utmzsc) || strlen($__utmzsc) <= 0 ) {
			return false;
		}
		else {
			$__utmzscArr = split('[:]', $__utmzsc);
			$cookieData = array();
			$cookieData['campaign']['source'] = $__utmzscArr[0];
			$cookieData['campaign']['name'] = $__utmzscArr[1];
			$cookieData['campaign']['medium'] = $__utmzscArr[2];
		}
		return $cookieData;
	}	
}
