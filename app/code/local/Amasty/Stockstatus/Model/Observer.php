<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Model_Observer
{
    public function onModelSaveBefore($observer)
    {
        $model = $observer->getObject();
        if ($model instanceof Mage_Catalog_Model_Resource_Eav_Attribute)
        {
            if ('custom_stock_status' == $model->getAttributeCode())
            {
                Mage::getModel('amstockstatus/range')->clear(); // deleting all old values
                $ranges = Mage::app()->getRequest()->getPost('amstockstatus_range');
                // saving quantity ranges
                if ($ranges && is_array($ranges) && !empty($ranges))
                {
                    foreach ($ranges as $range)
                    {
                        $data = array(
                            'qty_from'   => $range['from'],
                            'qty_to'     => $range['to'],
                            'status_id'  => $range['status'],
                        );
                        $rangeModel = Mage::getModel('amstockstatus/range');
                        $rangeModel->setData($data);
                        $rangeModel->save();
                    }
                }
            }
        }
    }
    
    /**
    * Used to show configurable product attributes in case when all elements are out-of-stock
    * 
    * "$_product->isSaleable() &&" should be commented out at line #100 (where "container2" block is outputted) in catalog/product/view.phtml
    * to make this work
    * 
    * @see Mage_Catalog_Model_Product::isSalable
    * @param object $observer
    */
    public function onCatalogProductIsSalableAfter($observer)
    {
        if (Mage::getStoreConfig('catalog/general/outofstock'))
        {
            $salable = $observer->getSalable();
            $stack = debug_backtrace();
            foreach ($stack as $object)
            {
                if ( isset($object['file']) && false !== strpos($object['file'], 'options' . DIRECTORY_SEPARATOR . 'configurable'))
                {
                    $salable->setData('is_salable', true);
                }
            }
        }
    }
}