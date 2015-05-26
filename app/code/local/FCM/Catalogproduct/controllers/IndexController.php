<?php
class FCM_Catalogproduct_IndexController extends Mage_Core_Controller_Front_Action
{
    public function product_optionsAction()
    {
        $product_id = $this->getRequest()->getParam('prodId');
        
        $_product = Mage::getModel('catalog/product')->load($product_id);
        $product_style = $_product->getStyle();
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter(array(array('attribute'=>'style','eq'=>$product_style)));
        $collection->addFieldToFilter(array(array('attribute'=>'visibility','neq'=>'1')));
        $collection->addFieldToFilter(array(array('attribute'=>'status','eq'=>'1')));
        $collection->getSelect()->limit(10);
        
        $block = $this->getLayout()->createBlock('core/template')
                                   ->setTemplate('catalogproduct/ajax/product_options.phtml');
        $block->setData(array('productid' => $product_id, 'collection' => $collection));
        $this->getResponse()->setBody(
            $block->toHtml()
        );
    }
}