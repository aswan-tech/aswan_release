<?php

class Custom_Newarrival_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function dataListing(){
		
		$getData = Mage::getModel('newarrival/managenewarrival')->newArrivalListing();
		$dataString = '';
		foreach($getData as $data){
			$catname = '';
			$is_default = '';
			if($data['cat_id'] == 6) {$catname = 'Men'; }else{ $catname = 'Women';}
			if($data['is_default'] == 1){$is_default = 'Default';}else{$is_default = $data['sku'];}
			$dataString .= '<tr>
			<td class="a-left">'.$catname.'</td>
			<td class="">'.$is_default.'</td>
			<td class=""><a href="'.Mage::getBaseUrl().'newarrival/adminhtml_index/add/id/'.$data['newarrival_id'].'">Edit</a></td>
			</tr>';
		}
		return $dataString;
	}

}
	 
