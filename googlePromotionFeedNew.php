<?php
set_time_limit(0);
#require_once '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
#require_once '/home/cloudpanel/htdocs/www.americanswan.com/current/includes/config.php';

require_once 'app/Mage.php';
#require_once 'includes/config.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
umask(0);
$app = Mage::app('default');
$cat_id = Mage::getSingleton('core/app')->getRequest()->getParam('catid');
$catName = Mage::getModel('catalog/category')->load($cat_id)->getName();
$today = date("Ymd");

try{
        #header("Content-type: text/csv");
        #header("Cache-Control: no-store, no-cache");
        #header('Content-Disposition: attachment; filename="'.$catName.$today.'-product-feed-cat.xml"');
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
        $dataTitleValue = $doc->createTextNode($catName);
        $titleNode->appendChild($dataTitleValue);
        $j = 0;
        $currentDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $currentDate = date("Y-m-d h:m:s", $currentDate);
                
		$_products = Mage::getModel('catalog/product')->getCollection();
		$_products->addAttributeToSelect(array('name','sku','ean','color','size','gender','price','special_price'));
		$_products->getSelect()->join(array('c'=>'catalog_category_product'),'c.product_id = e.entity_id',  array());
		$_products->getSelect()->join(array('pr'=>'catalog_product_relation'),'pr.parent_id = e.entity_id',  array('count( pr.child_id ) AS totSimpleProd'));
		$_products->getSelect()->join(array('st'=>'cataloginventory_stock_item'),'(pr.child_id = st.product_id AND st.is_in_stock = 1)',  array());
		$_products->getSelect()->where("c.category_id = '6'");
		$_products->getSelect()->group("e.entity_id");
		$_products->getSelect()->having("totSimpleProd >=2");
		$_products->addAttributeToFilter('type_id','configurable');
		//echo $_products->getSelect()->__toString();
		#var_dump($_products->count());
		#echo "<pre>";
		
		#$taxHelper = Mage::helper('tax');
		foreach ($_products as $key => $_product) {
			$product = Mage::getModel('catalog/product')->load($_product->getEntityId());
					
			$brand = $product->getAttributeText('brand');
			$product_category = $product->getAttributeText('product_category');
			$attr_bestsellervalue = $product->getAttributeText('inchoo_seller_product');
			$product_department = $product->getAttributeText('product_department');
			
			if($product_category!=null) {
				$_productdata['title'] = "American Swan".' '.ucfirst(strtolower($product_category)).' - '.$product->getName();
			}
			else {
				$_productdata['title'] = "American Swan".' - '.$product->getName();
			}
			
			$_productdata['link'] = Mage::getBaseUrl().$product->getUrlPath();
			$_productdata['description'] = 'Buy '.ucfirst(strtolower($brand)).' '.ucfirst(strtolower($product_category)).' Online- '.$product->getDescription()." Shop Online Now!";
			$_productdata['g:id'] = $product->getSku();
			
			$fprice = 0;
			/*
			$specialToDate = $product->getSpecialToDate();
			$specialFromDate = $product->getSpecialFromDate();
			
			if ($currentDate >= $specialFromDate && ($currentDate < $specialToDate || $specialToDate != "")) {
				$specialprice = $product->getSpecialPrice();
				if(isset($specialprice) && ($specialprice != '')){
					$fprice = $specialprice;
				}
			}else{
				$_finalPrice = $taxHelper->getPrice($product, $product->getFinalPrice());
				if($_finalPrice){ $fprice = $_finalPrice; }else{ $fprice = $product->getFinalPrice();}
			}
			*/
						
			$fprice = (int)$product->getFinalPrice();
			$_productdata['g:price'] = round(number_format($fprice, 2, null, ''));
            
            $_productdata['g:availability'] = ( $product->getIsInStock() == 1 ? 'In Stock' : 'Out Of Stock');
            $_productdata['g:custom_label_0'] = ((int)$product->getPrice() == $fprice ? 'New arrivals' : 'Sale' );
                       
            if($attr_bestsellervalue == 'Yes') {
				$_productdata['g:custom_label_1'] = 'Best Seller';
			}
            
            $_productdata['g:image_link'] = Mage::helper('catalog/image')->init($product, 'image')->__toString();
            
            
            $cats = $product->getCategoryIds();
			$_parentcatname = array();
			foreach ($cats as $category_id) {
				$_cat = Mage::getModel('catalog/category')->load($category_id);
				if($_cat->getLevel() == 2){
					$_parentcatname[] = strtolower($_cat->getName());
				}
			}
			
			if(in_array('men', $_parentcatname) || in_array('women', $_parentcatname)) {
				$_productdata['g:google_product_category'] = 'Apparel & Accessories > Clothing';
				$_productdata['g:product_type'] = 'Apparel & Accessories > Clothing > '.$product_department.' '.$product_category ;
			}
			else if(in_array('footwear', $_parentcatname)) {
				$_productdata['g:google_product_category'] = 'Apparel & Accessories > Shoes';
				$_productdata['g:product_type'] = 'Apparel & Accessories > Shoes > '.$product_department.' '.$product_category ;
			}
			else if(in_array('accessories', $_parentcatname)) {
				$_productdata['g:google_product_category'] = 'Apparel & Accessories > Clothing Accessories';
				$_productdata['g:product_type'] = 'Apparel & Accessories > Clothing Accessories > '.$product_category;
			}
			else if(in_array('home', $_parentcatname)) {
				$_productdata['g:google_product_category'] = 'Home & Garden > Linens & Bedding for Home Products';
				$_productdata['g:product_type'] = 'Home & Garden > Linens & Bedding for Home Products > '.$product->getName();
			}
			else if(in_array('beauty', $_parentcatname)) {
				$_productdata['g:google_product_category'] = 'Health & Beauty > Personal Care for Beauty Products';
				$_productdata['g:product_type'] = 'Health & Beauty > Personal Care for Beauty Products > '.$product->getName();
			}
			else {
				$_productdata['g:google_product_category'] = 'Apparel & Accessories > Clothing Accessories';
				$_productdata['g:product_type'] = 'Apparel & Accessories > Clothing Accessories > '.$product_department.' '.$product_category;
			}
                        
                        
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
			
			$mpn = 'AS-'.$product->getSku();
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
			$dataBrandValue = $doc->createTextNode("American Swan");
			$dataBrand->appendChild($dataBrandValue);
			$dataMpn = $doc->createElement( 'g:mpn' );
			$productNode->appendChild( $dataMpn );
			$dataMpnValue = $doc->createTextNode($mpn);
			$dataMpn->appendChild($dataMpnValue);
			$dataGender = $doc->createElement( 'g:gender' );
			$productNode->appendChild( $dataGender );
			$dataGenderValue = $doc->createTextNode($product->getAttributeText('gender'));
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
