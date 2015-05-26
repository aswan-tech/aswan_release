<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $sku = $row->getData($this->getColumn()->getIndex());
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        $size = "";

        if (!empty($product)) {
            $size = $product->getAttributeText('size');
        }

        return $size;
    }

}