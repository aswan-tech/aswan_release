<?php
/**
 * @category     Inchoo
 * @package     Inchoo Seller Products
 * @author        Domagoj Potkoc, Inchoo Team <web@inchoo.net>
 * @modified    Mladen Lotar <mladen.lotar@surgeworks.com>, Vedran Subotic <vedran.subotic@surgeworks.com>
 */
class Inchoo_SellerProducts_Adminhtml_SellerController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _initProduct()
    {
        
        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));

    	
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
                    
        $product->setData('_edit_mode', true);
        
        Mage::register('product', $product);
       
        return $product;
    }
	
	
	
	
	
	
	public function indexAction()
	{
        $this->_initProduct();
        
		$this->loadLayout()->_setActiveMenu('catalog/sellerproduct');
			
		$this->_addContent($this->getLayout()->createBlock('sellerproducts/adminhtml_edit'));
        
        $this->renderLayout();
	
	}
	
	public function gridAction()
	{
		 
	$this->getResponse()->setBody(
            $this->getLayout()->createBlock('sellerproducts/adminhtml_edit_grid')->toHtml()
        );
	
	}
	
	public function saveAction()
	{
		$data = $this->getRequest()->getPost(); 
        $collection = Mage::getModel('catalog/product')->getCollection();
		$storeId        = $this->getRequest()->getParam('store', 0);
		
		         
        parse_str($data['seller_products'], $seller_products);
		
		
        $collection->addIdFilter(array_keys($seller_products));
        
		 try {
		 	
			foreach($collection->getItems() as $product)
			{
				
				$product->setData('inchoo_seller_product',$seller_products[$product->getEntityId()]);
				$product->setStoreId($storeId);		
		  		$product->save();	
			} 	
		 	
			
		 	$this->_getSession()->addSuccess($this->__('BestSeller product was successfully saved.'));
			$this->_redirect('*/*/index', array('store'=> $this->getRequest()->getParam('store')));	
		 	
		 }catch (Exception $e){
			$this->_getSession()->addError($e->getMessage());
			$this->_redirect('*/*/index', array('store'=> $this->getRequest()->getParam('store')));
		 }
	
	}
	
	protected function _validateSecretKey()
	{
		return true;
	}

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/catalog/sellerproduct');
	}
	
}