<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rewrite/SalesTotalQuoteTax.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ kYZjDDkNdekPfBQd('b9d9c4a0e00bb82b600974b642e8b811'); ?><?php
  
class Aitoc_Aitloyalty_Model_Rewrite_SalesTotalQuoteTax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    protected function _aggregateTaxPerRate($item, $rate, &$taxGroups)
    {
        if(Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion(">=1.4.1.1"))
        {
            $inclTax        = $item->getIsPriceInclTax();
            $rateKey        = (string) $rate;
            $subtotal       = $item->getTaxableAmount() + $item->getExtraRowTaxableAmount();
            $baseSubtotal   = $item->getBaseTaxableAmount() + $item->getBaseExtraRowTaxableAmount();
            $item->setTaxPercent($rate);

            if (!isset($taxGroups[$rateKey]['totals'])) {
                $taxGroups[$rateKey]['totals'] = array();
                $taxGroups[$rateKey]['base_totals'] = array();
            }

            $hiddenTax     = null;
            $baseHiddenTax = null;
            switch ($this->_helper->getCalculationSequence($this->_store)) {
                case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                    $rowTax             = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                    $baseRowTax         = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);
                    break;
                case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                    $discount       = $item->getDiscountAmount();
                    $baseDiscount   = $item->getBaseDiscountAmount();
                       //AITOC discount FIX
                    if($inclTax)
                    {
                        //use for surcharge only
                        if($discount < 0)
                        {
                            $discountTaxAmount = $discount * $rate / 100;
                            $discount += $discountTaxAmount;     
                        }
                        if($baseDiscount < 0)
                        {
                            $baseDiscountTaxAmount = $discount * $rate / 100;
                            $baseDiscount += $baseDiscountTaxAmount; 
                        }    
                    } 
                    //AITOC discount FIX 
                    $subtotal       -= $discount;
                    $baseSubtotal   -= $baseDiscount;
                    $rowTax         = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                    $baseRowTax     = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);
                    break;
            }

            $rowTax     = $this->_deltaRound($rowTax, $rateKey, $inclTax);
            $baseRowTax = $this->_deltaRound($baseRowTax, $rateKey, $inclTax, 'base');
            if ($inclTax && !empty($discount)) {
                //AITOC surcharrge fix
                if($discount > 0)
                {
                    $hiddenTax      = $item->getRowTotalInclTax() - $item->getRowTotal() - $rowTax;    
                }
                if($baseDiscount > 0)
                {
                    $baseHiddenTax  = $item->getBaseRowTotalInclTax() - $item->getBaseRowTotal() - $baseRowTax;    
                } 
                //AITOC surcharrge fix 
            }

            $item->setTaxAmount(max(0, $rowTax));
            $item->setBaseTaxAmount(max(0, $baseRowTax));
            $item->setHiddenTaxAmount(max(0, $hiddenTax));
            $item->setBaseHiddenTaxAmount(max(0, $baseHiddenTax));

            $taxGroups[$rateKey]['totals'][]        = max(0, $subtotal);
            $taxGroups[$rateKey]['base_totals'][]   = max(0, $baseSubtotal);           
        }
        else
        {
            $store   = $item->getStore();
            $inclTax = $this->_usePriceIncludeTax($store);

            if ($inclTax) {
                $subtotal       = $item->getTaxCalcRowTotal();
                $baseSubtotal   = $item->getBaseTaxCalcRowTotal();
            } else {
                if ($item->hasCustomPrice() && $this->_helper->applyTaxOnCustomPrice($store)) {
                    $subtotal       = $item->getRowTotal();
                    $baseSubtotal   = $item->getBaseRowTotal();
                } else {
                    $subtotal       = $item->getTotalQty()*$item->getOriginalPrice();
                    $baseSubtotal   = $item->getTotalQty()*$item->getBaseOriginalPrice();
                }
            }
            $discountAmount     = $item->getDiscountAmount();
            $baseDiscountAmount = $item->getBaseDiscountAmount();
            $qty                = $item->getTotalQty();
            $rateKey            = (string) $rate;
            /**
             * Add extra amounts which can be taxable too
             */
            $calcTotal          = $subtotal + $item->getExtraRowTaxableAmount();
            $baseCalcTotal      = $baseSubtotal + $item->getBaseExtraRowTaxableAmount();

            $item->setTaxPercent($rate);
            if (!isset($taxGroups[$rateKey]['totals'])) {
                $taxGroups[$rateKey]['totals'] = array();
            }
            if (!isset($taxGroups[$rateKey]['totals'])) {
                $taxGroups[$rateKey]['base_totals'] = array();
            }

            $calculationSequence = $this->_helper->getCalculationSequence($store);
            switch ($calculationSequence) {
                case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                    $rowTax             = $this->_calculator->calcTaxAmount($calcTotal, $rate, $inclTax, false);
                    $baseRowTax         = $this->_calculator->calcTaxAmount($baseCalcTotal, $rate, $inclTax, false);
                    break;
                case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                    $rowTax             = $this->_calculator->calcTaxAmount($calcTotal, $rate, $inclTax, false);
                    $baseRowTax         = $this->_calculator->calcTaxAmount($baseCalcTotal, $rate, $inclTax, false);
                    $discountPrice = $inclTax ? ($subtotal/$qty) : ($subtotal+$rowTax)/$qty;
                    $baseDiscountPrice = $inclTax ? ($baseSubtotal/$qty) : ($baseSubtotal+$baseRowTax)/$qty;
                    $item->setDiscountCalculationPrice($discountPrice);
                    $item->setBaseDiscountCalculationPrice($baseDiscountPrice);
                    break;
                case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                       //AITOC discount FIX
                    if($inclTax)
                    {
                        //use for surcharge only
                        if($discountAmount < 0)
                        {
                            $discountTaxAmount = $discountAmount * $rate / 100;
                            $discountAmount += $discountTaxAmount;     
                        }
                        if($baseDiscountAmount < 0)
                        {
                            $baseDiscountTaxAmount = $baseDiscountAmount * $rate / 100;
                            $baseDiscountAmount += $baseDiscountTaxAmount; 
                        }    
                    } 
                    //AITOC discount FIX 
                    $calcTotal          = $calcTotal-$discountAmount;
                    $baseCalcTotal      = $baseCalcTotal-$baseDiscountAmount;
                    $rowTax             = $this->_calculator->calcTaxAmount($calcTotal, $rate, $inclTax, false);
                    $baseRowTax         = $this->_calculator->calcTaxAmount($baseCalcTotal, $rate, $inclTax, false);
                    break;
            }

            /**
             * "Delta" rounding
             */
            $delta      = isset($this->_roundingDeltas[$rateKey]) ? $this->_roundingDeltas[$rateKey] : 0;
            $baseDelta  = isset($this->_baseRoundingDeltas[$rateKey]) ? $this->_baseRoundingDeltas[$rateKey] : 0;

            $rowTax     += $delta;
            $baseRowTax += $baseDelta;

            $this->_roundingDeltas[$rateKey]     = $rowTax - $this->_calculator->round($rowTax);
            $this->_baseRoundingDeltas[$rateKey] = $baseRowTax - $this->_calculator->round($baseRowTax);
            $rowTax     = $this->_calculator->round($rowTax);
            $baseRowTax = $this->_calculator->round($baseRowTax);

            /**
             * Renew item amounts in case if we are working with price include tax
             */
            if ($inclTax) {
                $unitTax = $this->_calculator->round($rowTax/$qty);
                $baseUnitTax = $this->_calculator->round($baseRowTax/$qty);
                if ($item->hasCustomPrice()) {
                    $item->setCustomPrice($item->getPriceInclTax()-$unitTax);
                    $item->setBaseCustomPrice($item->getBasePriceInclTax()-$baseUnitTax);
                } else {
                    $item->setOriginalPrice($item->getPriceInclTax()-$unitTax);
                    $item->setPrice($item->getBasePriceInclTax()-$baseUnitTax);
                    $item->setBasePrice($item->getBasePriceInclTax()-$baseUnitTax);
                }
                $item->setRowTotal($item->getRowTotalInclTax()-$rowTax);
                $item->setBaseRowTotal($item->getBaseRowTotalInclTax()-$baseRowTax);
            }

            $item->setTaxAmount($rowTax);
            $item->setBaseTaxAmount($baseRowTax);

            $taxGroups[$rateKey]['totals'][]        = $calcTotal;
            $taxGroups[$rateKey]['base_totals'][]   = $baseCalcTotal;
        }
        return $this;
    }    
} } 