<?php
class Custom_Banners_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getBanners() {
		$bannerListArr = Mage::getModel('banners/managebanners')->getHomePageBanners();
		$dataString = '';
		if(isset($bannerListArr) && count($bannerListArr) > 0) {
			foreach($bannerListArr as $data){
				$dataString .='<div><a href="'.$data['banner_url'].'" alt="'.$data['banner_title'].'" title="'.$data['banner_title'].'"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'banner/'.$data['banner_path'].'" alt="" /></a></div>';
			}
		}
		return $dataString;
	}
}
	 
