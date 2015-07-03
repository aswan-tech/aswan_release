<?php
set_time_limit(0);
require_once '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
umask(0);
$app = Mage::app('default');
$cat_id = Mage::getSingleton('core/app')->getRequest()->getParam('catid');
if(empty($cat_id)){
	die("Invalid category id");
}
$today = date("Ymd");
$_parentcatnameArr = array('6'=>'men', '8'=>'women', '4'=>'footwear', '3'=>'accessories', '7'=>'beauty', '5'=>'home');
$_parentcatname = array($_parentcatnameArr[$cat_id]);
$core_read = Mage::getSingleton('core/resource')->getConnection('core_read');
try{
        header("Content-type: text/csv");
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename="'.$_parentcatnameArr[$cat_id].$today.'-product-feed-cat.xml"');
        $fp = fopen("php://output", 'w');
        $doc  = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $rssNode = $doc->createElement( "rss" );
        $doc->appendChild( $rssNode );
        $gna = $doc->createAttribute("xmlns:g");
        $rssNode->appendChild($gna);
        $gnaValue = $doc->createTextNode("http://base.google.com/ns/1.0");
        $gna->appendChild($gnaValue);
        $gnaVer = $doc->createAttribute("version");
        $rssNode->appendChild($gnaVer);
        $gnaVerValue = $doc->createTextNode("2.0");
        $gnaVer->appendChild($gnaVerValue);
        $productsNode = $doc->createElement( "channel" );
        $rssNode->appendChild( $productsNode );
        $titleNode = $doc->createElement( "title" );
        $productsNode->appendChild( $titleNode );
        $dataTitleValue = $doc->createTextNode($_parentcatnameArr[$cat_id]);
        $titleNode->appendChild($dataTitleValue);
                
        $query = "select * from promotion_feed where availability = 'In Stock' and category_id = '$cat_id'";
		$result = $core_read->query($query);
		$_products = $result->fetchAll();
		
		#echo "<pre>";print_r($_products);die;
		
		foreach ($_products as $key => $_product) {
			$brand = $_product['brand'];
			$product_category = $_product['product_category'];
			$attr_bestsellervalue = isset($_product['inchoo_seller_product']) ? $_product['inchoo_seller_product'] : '';
			$product_department = $_product['product_department'];
			$_productdata['title'] = $_product['title'];
			$_productdata['link'] = $_product['product_url'];
			$_productdata['description'] = $_product['description'];
			$_productdata['g:id'] = $_product['sku'];
			$_productdata['g:price'] = round(number_format($_product['price'], 2, null, ''));
            $_productdata['g:availability'] = $_product['availability'];
            $_productdata['g:custom_label_0'] = $_product['custom_label_0'];
                       
            if($attr_bestsellervalue == 'Yes') {
				$_productdata['g:custom_label_1'] = 'Best Seller';
			}
            
            $_productdata['g:image_link'] = $_product['image_link'];
            $_productdata['g:google_product_category'] = $_product['google_product_category'];
			$_productdata['g:product_type'] = $_product['product_type'];
			
			/*
             * creating items
             */
                          
			$productNode = $doc->createElement( "item" );
			$productsNode->appendChild( $productNode );
			foreach ($_productdata as $tag => $value) {
				$dataTag = $doc->createElement( $tag );
				$productNode->appendChild( $dataTag );
				if($tag == 'g:google_product_category' || $tag == 'g:product_type'){
						$valueTag = $doc->createCDATASection($value);
						$dataTag->appendChild( $valueTag );
				}else{
						$valueTag = $doc->createTextNode($value);
						$dataTag->appendChild( $valueTag );
				}
			}
			
			$mpn = 'AS-'.$_product['sku'];
			$dataShip = $doc->createElement( 'g:shipping' );
			$productNode->appendChild( $dataShip );
			$valueTagservice = $doc->createElement('g:service');
			$dataShip->appendChild( $valueTagservice );
			$textService = $doc->createTextNode("Standard");
			$valueTagservice->appendChild($textService);
			$valueTagprice = $doc->createElement('g:price');
			$dataShip->appendChild( $valueTagprice );
			$textPrice = $doc->createTextNode("0.0");
			$valueTagprice->appendChild($textPrice);
			$dataCondition = $doc->createElement( 'g:condition' );
			$productNode->appendChild( $dataCondition );
			$dataConditionValue = $doc->createTextNode("new");
			$dataCondition->appendChild($dataConditionValue);
			$dataBrand = $doc->createElement( 'g:brand' );
			$productNode->appendChild( $dataBrand );
			$dataBrandValue = $doc->createTextNode($brand);
			$dataBrand->appendChild($dataBrandValue);
			$dataMpn = $doc->createElement( 'g:mpn' );
			$productNode->appendChild( $dataMpn );
			$dataMpnValue = $doc->createTextNode($mpn);
			$dataMpn->appendChild($dataMpnValue);
			$dataGender = $doc->createElement( 'g:gender' );
			$productNode->appendChild( $dataGender );
			$dataGenderValue = $doc->createTextNode($_product['gender']);
			$dataGender->appendChild($dataGenderValue);					
		}				       
        $XML =  $doc->saveXML();
        echo $XML;
        fclose($fp);
}catch(exception $e){
        pr("Exception Occured during XML Generation : ".$e);
}
/*scripts/googlePromotionFeed.php?catid=105*/
?>
