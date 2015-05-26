<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rewrite/FrontSalesOrderPdfCreditmemo.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ rSDejjrQVBrENgRV('eb01aa0284d9e4fe0de7778bb3ec3ba4'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rewrite_FrontSalesOrderPdfCreditmemo extends Mage_Sales_Model_Order_Pdf_Creditmemo
{
    protected function insertTotals($page, $source){
        $order = $source->getOrder();
//        $font = $this->_setFontBold($page);

        $totals = $this->_getTotalsList($source);

        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $amount = $source->getDataUsingMethod($total['source_field']);
            $displayZero = (isset($total['display_zero']) ? $total['display_zero'] : 0);

            if ($amount != 0 || $displayZero) {
// AITOC modifications 
                if ('discount_amount' == $total['source_field'])
                {
                    $amount = 0 - $amount;
                    if ($amount > 0)
                    {
                        $total['title'] = $total['title_positive'];
                    }
                }
// end of AITOC modifications 

                $amount = $order->formatPriceTxt($amount);

                if (isset($total['amount_prefix']) && $total['amount_prefix']) {
                    $amount = "{$total['amount_prefix']}{$amount}";
                }
                
                $fontSize = (isset($total['font_size']) ? $total['font_size'] : 7);
                //$page->setFont($font, $fontSize);

                $label = Mage::helper('sales')->__($total['title']) . ':';

                $lineBlock['lines'][] = array(
                    array(
                        'text'      => $label,
                        'feed'      => 475,
                        'align'     => 'right',
                        'font_size' => $fontSize,
                        'font'      => 'bold'
                    ),
                    array(
                        'text'      => $amount,
                        'feed'      => 565,
                        'align'     => 'right',
                        'font_size' => $fontSize,
                        'font'      => 'bold'
                    ),
                );

//                $page->drawText($label, 475-$this->widthForStringUsingFontSize($label, $font, $fontSize), $this->y, 'UTF-8');
//                $page->drawText($amount, 565-$this->widthForStringUsingFontSize($amount, $font, $fontSize), $this->y, 'UTF-8');
//                $this->y -=15;
            }
        }

//        echo '<pre>';
//        var_dump($lineBlock);

        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
} } 