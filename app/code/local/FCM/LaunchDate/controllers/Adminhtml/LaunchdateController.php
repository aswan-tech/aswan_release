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
				 $conf_product = Mage::getModel('catalog/product')->load($product_id);
				 $mediaimages = $conf_product->getMediaGallery();
				 $created_at = $product->getData('created_at');
			     $inventory_date = $product->getData('inventory_date');

			     if(count($mediaimages['images'])>0){
			     		if($inventory_date!='' || $inventory_date!=null){
			     			$created_date=date("Y-m-d H:i:s" , Mage::getModel('core/date')->timestamp(time()));
							//put echo 
							echo "<div style='color:#3d6611;'>"."Launch date created Products :- Product Id- ".'<strong>'.$product_id.'</strong>'."- Launch Date - ".$created_date."<br>"."</div>";
							$conf_product->setData('launch_date',$created_date)->getResource()->saveAttribute($conf_product, 'launch_date');
			     		}

			     		else{
			     			echo "Inventory Date does not exists  Product Id :-".'<strong>'.$product_id.'</strong>'."<br>";
			     			continue;
			     		}
			     }else{
			     	echo "No images found against the Product Id :-".'<strong>'.$product_id.'</strong>'."<br>";
			     	continue;
			     }
			}

    }
}