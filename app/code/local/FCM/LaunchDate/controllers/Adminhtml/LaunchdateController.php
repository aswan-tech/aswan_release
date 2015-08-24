<?php
class FCM_LaunchDate_Adminhtml_LaunchdateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */
    public function productAction()
    {
        	$products = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', 1)
			->addAttributeToSort('entity_id', 'DESC')
			 ->addAttributeToFilter('type_id', 'configurable')
			->addAttributeToFilter('launch_date' , array('null' => true));
			foreach($products as $product){

				 $product_id = $product->getData('entity_id');
				 $productSku = $product->getData('sku');
				 $conf_product = Mage::getModel('catalog/product')->load($product_id);
				 $mediaimages = $conf_product->getMediaGallery();
				 $created_at = $product->getData('created_at');
			     $inventory_date = $product->getData('inventory_date');
			     $imageCreationDate = $product->getData('image_date');

			     		if(($inventory_date!='' || $inventory_date!=null) && ($imageCreationDate!='' || $imageCreationDate!=null)){
			     			
			     			$launchDate = max(array($created_at, $inventory_date, $imageCreationDate));
							echo "<div style='color:#3d6611;'>"."Launch date created Products :- Product Sku- ".'<strong>'.$productSku.'</strong>'."- Launch Date - ".$launchDate."<br>"."</div>";
							$conf_product->setData('launch_date',$launchDate)->getResource()->saveAttribute($conf_product, 'launch_date');
			     		} else {
			     			echo "Inventory Date does not exists  Product Sku :-".'<strong>'.$productSku.'</strong>'."<br>";
			     			continue;
			     		}
			     
			}

    }
}