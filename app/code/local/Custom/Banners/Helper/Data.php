<?php
class Custom_Banners_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getBanners() {
		$bannerListArr = Mage::getModel('banners/managebanners')->getHomePageBanners();
		$dataString = '';
		if(isset($bannerListArr) && count($bannerListArr) > 0) {
			$dataString = '<ul class="bxslider">';
			foreach($bannerListArr as $data){
				$dataString .='<li><a href="'.$data['banner_url'].'" alt="'.$data['banner_title'].'" title="'.$data['banner_title'].'"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'banner/'.$data['banner_path'].'" alt="" /></a></li>';
			}
			$dataString .= '</ul>';
		}
		
		/*
		 * if banner not uploaded by modules, call homepage banner by static block
		 */ 
		
		if(empty($dataString) || $dataString == '') {
			$block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('aswan-homepage-slider');
			$filterModel = Mage::getModel('cms/template_filter');
			$dataString = $filterModel->filter($block->getContent());
		}
		
		return $dataString;
	}
}
	 
