<?php
class Jextn_Testimonials_Block_Testimonials extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('testimonials')->getTestimonialsTitle());

		$this->_addCrumb('home', Mage::helper('testimonials')->__('Home'), Mage::getUrl(''));
		$this->_addCrumb('testimonials', Mage::helper('testimonials')->__('Testimonials'));
		return parent::_prepareLayout();
    }
    
    public function getTestimonials()     
     { 		
		$collection = Mage::getModel('testimonials/testimonials')->getCollection()
							->addIsActiveFilter();
							
		$collection->setOrder('update_time', 'desc');
		
							
		$pager = $this->getLayout()
                      ->createBlock('page/html_pager', 'testimonials.customer.pager');
		
		//$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));	
		//$pager->setLimit(5);
		
		$pager->setTemplate('testimonials/pager_testimonials.phtml');		  
		$pager->setCollection($collection);		
					  
        $this->setChild('pager', $pager);
		
		$collection->load();

		
        return $collection;        
    }
	
	public function getSidebarTestimonials()     
    { 
		$collection = Mage::getModel('testimonials/testimonials')->getCollection()
							->addSidebarFilter()
							->addIsActiveFilter();
		
		$count = Mage::helper('testimonials')->getSideBarCount();		
		if (!empty($count)) {
			$collection->setPageSize($count);
		}
		
		$collection->setOrder('update_time', 'desc');
			
        return $collection;        
    }
	
	public function getFooterTestimonials()     
    { 
		$collection = Mage::getModel('testimonials/testimonials')->getCollection()
							->addFooterFilter()
							->addIsActiveFilter();
							
		$count = Mage::helper('testimonials')->getFooterCount();
		
		if (!empty($count)) {
			$collection->setPageSize($count);
		}
		
		$collection->setOrder('update_time', 'desc');
			
        return $collection;        
    }
	
	public function getFormAction()
	{
		return $this->getUrl('testimonials/submit/post', array('_secure' => true));	
	}
	
	protected function _addCrumb($index, $label, $link = ''){
		if ($breadcrumbsBlock = $this->_getBreadcrumbsBlock()){
			$breadcrumbsBlock->addCrumb($index, array(
								'label'=>$label, 
								'link'=>$link,
								)
					);
		}
		return $this;
	}
	
	protected function _getBreadcrumbsBlock(){
		if (is_null($this->_breadcrumbsBlock)){
			$this->_breadcrumbsBlock = $this->_getLayout()->getBlock('breadcrumbs');
		}
		return $this->_breadcrumbsBlock;
	}
	
	protected function _getLayout(){
		if (is_null($this->_layout)){
			$this->_layout = Mage::getSingleton('core/layout');
		}
		return $this->_layout;
	}
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    } 
}