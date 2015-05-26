<?php
/**
 * @copyright   Copyright (c) 2009-11 Amasty
 */
class Amasty_Rules_Model_Observer
{
    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var array
     */    
    protected $descrPerItem = array();
    
    /**
     * Process sales rule form creation
     * @param   Varien_Event_Observer $observer
     */
    public function handleFormCreation($observer)
    {
        $actionsSelect = $observer->getForm()->getElement('simple_action');
        if ($actionsSelect){
            $actionsSelect->setValues(array_merge(
                $actionsSelect->getValues(), 
                Mage::helper('amrules')->getDiscountTypes()
            ));
            
            $actionsSelect->setOnchange('ampromo_hide()'); //ampromo is correct name
        }
        
        $fldSet = $observer->getForm()->getElement('action_fieldset');
        if ($fldSet){
            if ('true' != (string)Mage::getConfig()->getNode('modules/Amasty_Promo/active')){ 
                $fldSet->addField('promo_sku', 'text', array(
                    'name'     => 'promo_sku',
                    'label' => Mage::helper('amrules')->__('Promo Items'),
                    'note'  => Mage::helper('amrules')->__('Comma separated list of the SKUs'),
                    ),
                    'discount_amount'
                );         
            }
           
            $fldSet->addField('promo_cats', 'text', array(
                'name'     => 'promo_cats',
                'label' => Mage::helper('amrules')->__('Promo Categories'),
                'note'  => Mage::helper('amrules')->__('Comma separated list of the category ids'),
                ),
                'discount_amount'
            );        
        }
        
        
        return $this; 
    }
    
    /**
     * Adds new conditions
     * @param   Varien_Event_Observer $observer
     */
    public function handleNewConditions($observer)
    {
        $transport = $observer->getAdditional();
        $cond = $transport->getConditions();
        if (!is_array($cond)){
            $cond = array();
        }
        
        $types = array(
            'customer' => 'Customer attributes',
            'orders'   => 'Purchases history',
        );
        foreach ($types as $typeCode => $typeLabel){
            $condition           = Mage::getModel('amrules/rule_condition_' . $typeCode);
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();
            
            $attributes = array();
            foreach ($conditionAttributes as $code=>$label) {
                $attributes[] = array(
                    'value' => 'amrules/rule_condition_'.$typeCode.'|' . $code, 
                    'label' => $label,
                );
            }         
            $cond[] = array(
                'value' => $attributes, 
                'label' => Mage::helper('amrules')->__($typeLabel), 
            );            
        }
        $transport->setConditions($cond);
        
        return $this; 
    }       
    
    /**
     * Process quote item validation and discount calculation
     * @param   Varien_Event_Observer $observer
     */
    public function handleValidation($observer) 
    {

        try {
            $amountToDisplay = 0.00;
            $rule = $observer->getEvent()->getRule();
            $item = $observer->getEvent()->getItem();
            
            $types = Mage::helper('amrules')->getDiscountTypes(true);
            if (isset($types[$rule->getSimpleAction()])) {
                // init total discount info for the rule first time
                if (!isset($this->rules[$rule->getId()])) {
                    $this->rules[$rule->getId()] = $this->_initRule(
                        $rule, 
                        $observer->getEvent()->getAddress(),
                        $observer->getEvent()->getQuote()
                    );
                }  
                
                $r = $this->rules[$rule->getId()];
                     
                $itemId = $item->getId();
                // there is matching item
                if (!empty($r[$itemId])){
                    $result = $observer->getEvent()->getResult();
                    $result->setDiscountAmount($r[$itemId]['discount']);
                    $result->setBaseDiscountAmount($r[$itemId]['base_discount']);
                    
                    $amountToDisplay = $r[$itemId]['discount'];
                    $item->setIsSpecialPromotion(true);
                }
            } 
            else { //it's default rule
                $amountToDisplay = $observer->getEvent()->getResult()->getDiscountAmount();  
            }
            
            $amountToDisplay = $observer->getEvent()->getQuote()
                ->getStore()->roundPrice($amountToDisplay);
    
                
            if ($this->skip($rule, $item)){
                
                $amountToDisplay = 0;
                $result = $observer->getEvent()->getResult();
                $result->setDiscountAmount(0);
                $result->setBaseDiscountAmount(0);                
            }
           
            if ($amountToDisplay > 0.0001) {
                $this->_addFullDescription($observer->getEvent()->getAddress(), $rule, $item, $amountToDisplay);                
            }
            
        } catch (Exception $e){
            if (isset($_GET['debug'])) {
                print_r($e->getMessage());
                exit;
            }
        }
        
        return $this;
    }
 
    protected function _initRule($rule, $address, $quote) 
    {
        $types = array(Amasty_Rules_Helper_Data::TYPE_XY_PERCENT, Amasty_Rules_Helper_Data::TYPE_XY_FIXED);
        if (in_array($rule->getSimpleAction(), $types)){
            return $this->_initRuleXY($rule, $address, $quote);
        }
        
        $r = array();        
        
        $prices = $this->_getSortedCartPices($rule, $address);

        if (!$prices){
            return $r;
        }
        
        $qty = $this->_getQty($rule, count($prices));
        if ($qty < 1){
            return $r;
        }
        
        $step = (int)$rule->getDiscountStep();
        
        //discount types that requires small-big sorting
        $types = array(Amasty_Rules_Helper_Data::TYPE_EACH_N, Amasty_Rules_Helper_Data::TYPE_FIXED);
        
        if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_CHEAPEST){
            $prices = array_slice($prices, 0, $qty);
        } 
        elseif ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_EXPENCIVE){
            $prices = array_slice($prices, -$qty, $qty);
        } 
        elseif (in_array($rule->getSimpleAction(), $types)) {
            $prices = array_reverse($prices); // now it is from  big to small (80, 50, 50, 30 ...)
        }
        
        //discount types that requires small-big sorting
        $afterN = array(Amasty_Rules_Helper_Data::TYPE_AFTER_N_FIXED, Amasty_Rules_Helper_Data::TYPE_AFTER_N_DISC);
        if (in_array($rule->getSimpleAction(), $afterN)){
            $qty = max(0, $rule->getDiscountQty()); // qty should be positive  
            if ($qty){
                $qty = min($qty, count($prices));
            }
            else {
                $qty = count($prices);
            }
            
            $offset = max(0, $step);
            $offset = min($step, count($prices));
            
            $prices = array_reverse($prices);
            $prices = array_slice($prices, $offset, $qty);
            
            $step   = 1; // we do not use it any more
            
        }
        
        
        $percentage  = floatVal($rule->getDiscountAmount());
        if (!$percentage){
            $percentage  = 100;
        }
        $percentage = ($percentage / 100.0);

        // money amount begin
        $sum = $baseSum = 0;
        foreach ($prices as $price){
            $sum     += $price['price'];    
            $baseSum += $price['base_price'];    
        }
        
        $amount     = floor($sum / max(1, $quote->getStore()->convertPrice($step))) * $quote->getStore()->convertPrice($rule->getDiscountAmount());
        $baseAmount = floor($baseSum / max(1, $step)) * $rule->getDiscountAmount();
        
        if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_AMOUNT){
            if ($baseAmount <= 0.001){
                 return $r;
            }
            $percentage = $baseAmount / $baseSum;
        }        
        // end

        $lastId  = -1;
        $currQty = 0; // for each N we need to limit Max applied qty also
        
        $countPrices = count($prices);
                
        foreach ($prices as $i => $price){
            // skip items beside each, say 3-d, depends on the $step
            
            if (in_array($rule->getSimpleAction(), $types) && ($step >1) && 
            (($i+1) % $step) ){
                continue;
            }
            // introduce limit for each N with discount or each N with fixed.

            if ((($currQty >= $qty) && ($rule->getSimpleAction() != Amasty_Rules_Helper_Data::TYPE_GROUP_N)) 
            || (($rule->getDiscountQty() <= $currQty) && ($rule->getDiscountQty()) && ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_GROUP_N)) ){
                continue;
            }
            ++$currQty;
            
            $discount     = $price['price'] * $percentage;
            $baseDiscount = $price['base_price'] * $percentage;


            if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_AMOUNT){
                $discount     = floor($discount*100) / 100;
                $baseDiscount = floor($baseDiscount*100) / 100;
            }

            
            if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_FIXED
                || $rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_AFTER_N_FIXED 
            ){
                $discount     = $price['price']      - $quote->getStore()->convertPrice($rule->getDiscountAmount());
                $baseDiscount = $price['base_price'] - $rule->getDiscountAmount();

            }
            
            if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_GROUP_N){
                
                if ($i < $countPrices - ($countPrices % $step)){  
                    $discount     =  $price['price'] - $quote->getStore()->convertPrice($rule->getDiscountAmount())/ $step;
                    $baseDiscount =  $price['base_price'] - $rule->getDiscountAmount() / $step;                 
                } else {
                    $discount     = 0;
                    $baseDiscount = 0;                     
                }                           
            }
            
            if ($price['id'] != $lastId){
                $lastId = intVal($price['id']);
                
                $r[$lastId] = array();
                $r[$lastId]['discount']      = $discount;
                $r[$lastId]['base_discount'] = $baseDiscount;
            }
            else {
                $r[$lastId]['discount']      += $discount;
                $r[$lastId]['base_discount'] += $baseDiscount;
            }

            // to handle rounding errors
            $amount     -= round($discount, 2);
            $baseAmount -= round($baseDiscount, 2);
        }


        // if we have lost 0.01, add it to the last item
        if ($lastId > 0 && $rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_AMOUNT){
            if (abs(100 * $amount) >= 1){
                $r[$lastId]['discount'] += $amount;    
            }
            if (abs(100 * $baseAmount * 100) >= 1){
                $r[$lastId]['base_discount'] += $baseAmount;    
            }
        }

        
        return $r;
    }
    
    protected function _initRuleXY($rule, $address, $quote) 
    {
        // no conditions for Y elements
        if (!$rule->getPromoSku() && !$rule->getPromoCats())
            return array();

        
        // find all X (trigger) elements
        $realQty = 0;
        $arrX = array();
        foreach ($this->_getAllItems($address) as $item) {
            if (!$rule->getActions()->validate($item)) {
                continue;
            }
            
            if ($item->getParentItemId()){
                continue;
            }
                        
            $arrX[$item->getId()] = $item; 
            $realQty += $this->_getItemQty($item);
        } 
        
        $maxQty  = $this->_getQty($rule, $realQty);
        
        // find all allowed Y (discounted) elements and calculate total discount
        $currQty = 0; // there can be less elemnts to discont than $maxQty
        
        $discount     = 0;
        $baseDiscount = 0;
        
        $sku  = explode(',', $rule->getPromoSku());
        $cats = explode(',', $rule->getPromoCats());
        
        foreach ($this->_getAllItems($address) as $item) {
            if ($currQty >= $maxQty){
                break;
            }   
            
            //do not apply discont on triggers
            if (isset($arrX[$item->getId()]))     
                continue;    
            
            if ($this->skip($rule, $item)) {
                continue;
            } 
               
            if ($item->getParentItemId()){
                continue;
            }  
                       
            $product = $item->getProduct(); 
            // for configurable product we need to use the child
            if ($item->getProductId() != $product->getId()){
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
            }
            
            if (!in_array($product->getSku(), $sku) && !array_intersect($cats, $product->getCategoryIds())) {
                continue;
            }
            
            $qty = $this->_getItemQty($item);
            $qty = min($maxQty - $currQty, $qty);
          
            $currQty += $qty;
            
            if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_XY_PERCENT){
                $percent = min(100, $rule->getDiscountAmount()); 
                
                $discount     += (($qty  * $this->_getItemPrice($item)     - $item->getDiscountAmount()) * $percent) / 100;
                $baseDiscount += (($qty  * $this->_getItemBasePrice($item) - $item->getBaseDiscountAmount()) * $percent) / 100;
            }
            elseif ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_XY_FIXED){
                $fixed = $rule->getDiscountAmount(); // in base currency
                
                $discount     += $qty  * ($this->_getItemPrice($item) - $quote->getStore()->convertPrice($fixed));
                $baseDiscount += $qty  * ($this->_getItemBasePrice($item) - $fixed);
            }
        } 
        //echo $currQty; exit;
        // apply discount on X trigger elements, cause we can't guarantee that none of Y elements    
        // have been missed before we call _initXY
        $part     = $discount / $realQty;
        $basePart = $baseDiscount / $realQty;
       
        // 1 cent fix
        $centCorrect = round($part,2)*$realQty - $discount;
        $baseCentCorrect = round($basePart,2)*$realQty - $baseDiscount;
       
        $r = array();
        foreach ($arrX as $x){
            $qty = $this->_getItemQty($x);
            $r[$x->getId()] = array();
            $r[$x->getId()]['discount']      = $part * $qty;
            $r[$x->getId()]['base_discount'] = $basePart * $qty;
        }
        $r[$x->getId()]['discount']      -= $centCorrect;
        $r[$x->getId()]['base_discount'] -= $baseCentCorrect;
        return $r;
    }
    
    /**
     * Determines qty of the discounted items
     *
     * @param Mage_Sales_Model_Rule $rule
     * @return int qty
     */
    protected function _getQty($rule, $cartQty)
    {
        $discountQty    = 1;
        $discountStep   = (int) $rule->getDiscountStep();

        if ($rule->getSimpleAction() == Amasty_Rules_Helper_Data::TYPE_AMOUNT){
            return $cartQty; // apply for all
        }

        
        if ($discountStep) {
            $discountQty = floor($cartQty / $discountStep);
            
            $maxDiscountQty = (int) $rule->getDiscountQty();
            if (!$maxDiscountQty) {
                $maxDiscountQty = $cartQty;
            }
            
            $discountQty = min($discountQty, $maxDiscountQty);
        } 
        return $discountQty;        
    }
    
    protected function _getAllItems($address)
    {
        $items = $address->getAllNonNominalItems();
        if (!$items){ // CE 1.3 version
            $items = $address->getAllVisibleItems();    
        }
        if (!$items){ // cart has virtual products
            $cart = Mage::getSingleton('checkout/cart'); 
            $items = $cart->getItems();
        }  
        return $items;      
    }
    
    /**
     * Creates an array of the all prices in the cart
     *
     * @return array
     */
    protected function _getSortedCartPices($rule, $address)
    {
        $prices = array();
        foreach ($this->_getAllItems($address) as $item) {
            
            if ($item->getParentItemId()) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }
            
            if ($this->skip($rule, $item)) {
                continue;
            }    
            
            $price     = $this->_getItemPrice($item);
            $basePrice = $this->_getItemBasePrice($item);
            
            // CE 1.3 version
            $qty = $this->_getItemQty($item);
            
            for ($i=0; $i < $qty; ++$i){
                $prices[] = array(
                    'price'       => $price, // don't call the function in a long cycle
                    'base_price'  => $basePrice,
                    'id'          => $item->getId(),
                 );
            }
        } // foreach
        
        usort($prices, array($this, 'comparePrices'));   
        
        return $prices;     
    }
    
    /**
     * Return item price in the store base currency
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }
    
    /**
     * Return item price in currently active for quote currency
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $price : $item->getCalculationPrice();
    }
    
    protected function _getItemQty($item)
    {
        //comatibility with CE 1.3 version
        return $item->getTotalQty() ? $item->getTotalQty() : $item->getQty();
    }    
    
    /**
     * Adds a detailed description of the discount
     */
    protected function _addFullDescription($address, $rule, $item, $discount)
    {
        // we need this to fix double prices with one step checkouts
        $ind = $rule->getId() . '-' . $item->getId();
        if (isset($this->descrPerItem[$ind])){
            return $this;
        }
        $this->descrPerItem[$ind] = true;


        $descr = $address->getFullDescr();
        if (!is_array($descr)){
            $descr = array();
        }
        
        if (empty($descr[$rule->getId()])){
            $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStore());
            if (!$ruleLabel && $address->getCouponCode()) {
                $ruleLabel = $address->getCouponCode();      
            }
            if (!$ruleLabel && $rule->getCouponCode()){ // for 1.3 versions
                $ruleLabel = $rule->getCouponCode(); 
            }            
            $descr[$rule->getId()] = array('label'=>'<strong>' . htmlspecialchars($ruleLabel) . '</strong>', 'amount' => 0);
        }
        // skip the rule as it adds discount to each item
        // version before 1.4.1 has no class constants for actions
        $skipTypes = array('cart_fixed', Amasty_Rules_Helper_Data::TYPE_XY_PERCENT, Amasty_Rules_Helper_Data::TYPE_XY_FIXED, Amasty_Rules_Helper_Data::TYPE_AMOUNT);

        
        if (!in_array($rule->getSimpleAction(), $skipTypes) && Mage::getStoreConfig('amrules/general/breakdown_products')){ 
            $sep = ($descr[$rule->getId()]['amount'] > 0) ? ', <br/> ' : ': ';
            $descr[$rule->getId()]['label'] = $descr[$rule->getId()]['label'] . $sep . htmlspecialchars($item->getName());
        }
        
        $discount = min($this->_getItemPrice($item)  * $this->_getItemQty($item) - $item->getDiscountAmount(), $discount);
        
        $descr[$rule->getId()]['amount'] += $discount;
        
        $address->setFullDescr($descr);
    }  
    
    /**
     * determines if we should skip the items with special price or other (in futeure) conditions
     * @return bool
     */
    protected function skip($rule, $item)
    {
        if ($rule->getSimpleAction() == 'cart_fixed')
            return false;
        
        if (!Mage::getStoreConfig('amrules/general/skip_special_price'))
            return false;
          
        $p = $item->getProduct();
        if (!$p)
            return false;
        
        if (!$p->getSpecialPrice() || floatval($p->getSpecialPrice()) < 0.0001)
            return false;
            
        // changed from CE 1.7 version
        $from = $p->getSpecialPriceFrom() ? $p->getSpecialPriceFrom() : $p->getSpecialFromDate();
        $to   = $p->getSpecialPriceTo() ?  $p->getSpecialPriceTo() : $p->getSpecialToDate();
     
        if (!Mage::app()->getLocale()->isStoreDateInInterval(Mage::app()->getStore(), $from, $to))
            return false;
          
        if ($p->getSpecialPrice() >= $p->getPrice())
            return false;  
        
        return true;        
    }       

    public static function comparePrices($a, $b)
    {
        $res = ($a['price'] < $b['price']) ? -1 : 1; 
        if ($a['price'] == $b['price']) {
            $res = ($a['id'] < $b['id']) ? -1 : 1;
            if ($a['id'] == $b['id'])
                $res = 0;
        }
        return $res;       
    }
    
}