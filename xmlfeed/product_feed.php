<?php
error_reporting(E_ALL);
$mageFilename = '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
require_once $mageFilename;
ini_set('display_errors', 1);
Mage::app();
$categories = array(17,19,23,25,26,29,34,37,40,42,45,54,59,66,67,68,79,80,83,86,87,88,92,94,96,98,99,100,101,103,125,126,127,133,138,142,143,144,146,148,176,185,186,189,227,229,230,231,232,233,234,235,236,237,239,240,241,242,251,253,277,299,300,371,372,392,417,561,571,621,631,641,651,661,671,681,691,701,711,721,731,741,841,851,861);//category id
$collection = Mage::getResourceModel('catalog/product_collection')
                       ->setStoreId(0)
                       ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
                       ->addAttributeToFilter('category_id', array('in' => $categories))
                       ->addAttributeToSelect('*')
                       ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                       ->addAttributeToFilter('type_id', 'configurable');
               $collection->getSelect()->group('product_id')->distinct(true);
               $collection->load();

header( 'Content-Type: text/csv' );
$header = array('Item Number', 'Item Name', 'Item URL','Item image URL', 'Item 1 Name' ,'Item 1 ID' ,'Discount', 'Price');
header( "Content-Disposition: attachment;filename=product-url.csv");
$output = fopen('php://output', 'w');
fputcsv($output, $header);

	foreach($collection as $product)
	{
	    $categoryIds = $product->getCategoryIds();
	    $categoryMatchedIds = array_intersect($categoryIds, $categories);
	    foreach ($categoryMatchedIds as $key => $value) {
	    	$category_id = $value;
	    	break;
	    }
	    $specialPrice = $product->getSpecialPrice();
	    $mrpPrice =  $product->getPrice();
	    if($specialPrice == '')
	    {
	    	$discount =$mrpPrice;
	    }
	    else if($mrpPrice > $specialPrice) {
	    	$discount =$specialPrice;
	    }

	    $category = Mage::getModel('catalog/category')->load($category_id);
            $dataArr = array(
                            $product->getSku(),
                            $product->getName(),
                            $product->getProductUrl(),
                            $product->getImageUrl(),
                    	    $category->getName(),
                             $category_id,
                            $discount,
                            $mrpPrice
                            );
                fputcsv($output, $dataArr);
		
	}
		fclose($output);
?>
