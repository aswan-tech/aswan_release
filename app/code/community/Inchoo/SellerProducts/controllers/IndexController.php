<?php
/**
 * @category     Inchoo
 * @package     Inchoo Seller Products
 * @author        Domagoj Potkoc, Inchoo Team <web@inchoo.net>
 * @modified    Mladen Lotar <mladen.lotar@surgeworks.com>, Vedran Subotic <vedran.subotic@surgeworks.com>
 */
class Inchoo_SellerProducts_IndexController extends Mage_Core_Controller_Front_Action
{
	/*
	 * Check settings set in System->Configuration and apply them for seller-products page
	 * */
	public function indexAction()
	{
		$template = Mage::getConfig()->getNode('global/page/layouts/'.Mage::getStoreConfig("sellerproducts/general/layout").'/template');
		
		$this->loadLayout();

		$this->getLayout()->getBlock('root')->setTemplate($template);
		$this->getLayout()->getBlock('head')->setTitle($this->__(Mage::getStoreConfig("sellerproducts/general/meta_title")));
		$this->getLayout()->getBlock('head')->setDescription($this->__(Mage::getStoreConfig("sellerproducts/general/meta_description")));
		$this->getLayout()->getBlock('head')->setKeywords($this->__(Mage::getStoreConfig("sellerproducts/general/meta_keywords")));
		
                $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
                $breadcrumbsBlock->addCrumb('seller_products', array(
                    'label'=>Mage::helper('sellerproducts')->__(Mage::helper('sellerproducts')->getPageLabel()),
                    'title'=>Mage::helper('sellerproducts')->__(Mage::helper('sellerproducts')->getPageLabel()),
                ));
                
		$this->renderLayout();
	}
}