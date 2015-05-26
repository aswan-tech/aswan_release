<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Mysql4/Catalog/Layer/Filter/Price.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ kUrqBiwjDyIZBmrk('80ef602b3b0f5554fa249c932f27828b'); ?><?php
class AdjustWare_Nav_Model_Mysql4_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Price
{
    /**
     * Retrieve array with products counts per price range
     * Should be used only from magento 1.4-1.6
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $range
     * @return array
     */
    public function getCount($filter, $range)
    {
        $select     = $this->_getSelect($filter);
        $connection = $this->_getReadAdapter();
        $response   = $this->_dispatchPreparePriceEvent($filter, $select);
        $table      = $this->_getIndexTableAlias();

        $additional = join('', $response->getAdditionalCalculations());
        $rate       = $filter->getCurrencyRate();

        /**
         * Check and set correct variable values to prevent SQL-injections
         */
        $rate       = floatval($rate);
        $range      = floatval($range);
        if ($range == 0) {
            $range = 1;
        }
        # <<< AITOC_FIX - if several filters were selected this will clean SELECT query from equal items
        $countExpr  = new Zend_Db_Expr("COUNT(DISTINCT {$table}.entity_id)");
        # >>> AITOC_FIX
        $rangeExpr  = new Zend_Db_Expr("FLOOR((({$table}.min_price {$additional}) * {$rate}) / {$range}) + 1");

        $select->columns(array(
            'range' => $rangeExpr,
            'count' => $countExpr
        ));
        $select->group($rangeExpr);

        return $connection->fetchPairs($select);
    }
} } 