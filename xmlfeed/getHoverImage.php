<?php
require_once '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
umask(0);
Mage::app();

$sku = $_GET['sku'];
if(isset($sku))
{
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
	if($product)
	{
		$imgUrl = Mage::helper('catalog/image')->init($product, 'hover_image')->resize(235,250);
		echo $imgUrl;
	}
	else 
	{
		echo "Error: Sku not found in website";
	}

}
else
{
	echo "Error: Please enter some sku!";
}


