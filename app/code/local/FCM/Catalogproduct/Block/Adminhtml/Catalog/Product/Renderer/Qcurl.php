<?php
class FCM_Catalogproduct_Block_Adminhtml_Catalog_Product_Renderer_Qcurl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
            if ($getter = $this->getColumn()->getGetter()) {
                $val = $row->$getter();
            }
            $html = "";
            $sid = isset($_COOKIE['adminhtml']) ? $_COOKIE['adminhtml'] : '' ;
            if ($row->getData('visibility') == '4') {
                $html = "<a href=\"" . $row->getProductUrl() . "\" onclick=\"var w = window.open('" . $row->getProductUrl() . "sid/" . $sid . "', '" . $row->getId() . "', 'width=' + screen.width + ', height=' + screen.height + ', toolbar=no, scrollbars=yes, status=no, titlebar=no, top=0, left=0'); w.focus(); this.style.color='#CCCCCC'; return false;\" target=\"_blank\">Open</a>";
            }
            return $html;
    }
}