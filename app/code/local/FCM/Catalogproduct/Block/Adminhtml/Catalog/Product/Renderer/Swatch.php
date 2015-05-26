<?php

class FCM_Catalogproduct_Block_Adminhtml_Catalog_Product_Renderer_Swatch extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
		
		$html = ""; 
		
		$typeId = $row->getData('type_id');
		
		if ($typeId == 'simple') {
			$val = $row->getData($this->getColumn()->getIndex());
			//$val = str_replace("no_selection", "", $val);
			
			$_swatchImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN). 'frontend/enterprise/lecom/images/NA.jpg';
			$_mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product';
			
			/*
			$swatchImgWidth = Mage::getStoreConfig('colorswatch/general/swatch_image_width');
			if($swatchImgWidth == "" || strtolower($swatchImgWidth) == "null") {
				$swatchImgWidth = 20;
			}
			
			$swatchImgHeight = Mage::getStoreConfig('colorswatch/general/swatch_image_height');
			
			if($swatchImgHeight == "" || strtolower($swatchImgHeight) == "null") {
				$swatchImgHeight = 20;
			}
			*/
			
			if (!empty($val) && ($val != "no_selection")  && file_exists(Mage :: getBaseDir('media') . '/catalog/product/' . $val)) {
				$_swatchImage = $_mediaUrl . $val;
			
				//$_swatchImage = Mage::helper('catalog/image')->init($_productchild,'color_swatch_image')->resize($swatchImgWidth,$swatchImgHeight);		
			}
			
			$html = '<img ';
			$html .= 'id="' . $this->getColumn()->getId() . '" ';
			$html .= 'src="' . $_swatchImage . '"';
			$html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '" width="20" height="20" />';
		}
		
		return $html;
    }
}