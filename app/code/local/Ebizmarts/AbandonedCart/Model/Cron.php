<?php
class Ebizmarts_AbandonedCart_Model_Cron
{
//    const EMAIL_TEMPLATE_XML_PATH = 'ebizmarts_abandonedcart/general/template';
//    const EMAIL_TEMPLATE_XML_PATH_W_COUPON = 'ebizmarts_abandonedcart/general/coupon_template';

    /**
     *
     */
    public function abandoned()
    {
        $allStores = Mage::app()->getStores();
        foreach($allStores as $storeid => $val)
        {
            if(Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::ACTIVE,$storeid)) {
                $this->_proccess($storeid);
            }
        }
    }

    /**
     * @param $store
     */
    protected function _proccess($store)
    {
        Mage::app()->setCurrentStore($store);

        $adapter = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $days = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::DAYS, $store);
        $maxtimes = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::MAXTIMES, $store);
        $sendcoupondays = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::COUPON_DAYS, $store);
        $sendcoupon = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::SEND_COUPON, $store);
        $firstdate = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::FIRST_DATE, $store);
        $unit = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::UNIT, $store);
        $customergroups = explode(",",Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::CUSTOMER_GROUPS, $store));
        $mailsubject = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::SUBJECT, $store);
        $mandrillTag = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::MANDRILL_TAG, $store)."_$store";

        if(!$days) {
            return;
        }
        $expr = sprintf('DATE_SUB(%s, %s)', $adapter->quote(now()), $this->_getIntervalUnitSql($days, 'DAY'));
        $from = new Zend_Db_Expr($expr);

		// get a collection of abandoned carts
		$collection = Mage::getResourceModel('reports/quote_collection');
		$collection->addFieldToFilter('items_count', array('neq' => '0'))
				   ->addFieldToFilter('main_table.is_active', '1')
				   ->addFieldToFilter('main_table.store_id',array('eq'=>$store))
				   ->addSubtotal($store)
				   ->setOrder('updated_at');

		$collection->addFieldToFilter('main_table.converted_at', array(array('null'=>true),$this->_getSuggestedZeroDate()))
				   ->addFieldToFilter('main_table.updated_at', array('to' => $from,'from' => $firstdate))
				   ->addFieldToFilter('main_table.ebizmarts_abandonedcart_counter',array('lt' => $maxtimes));

		$collection->addFieldToFilter('main_table.customer_email', array('neq' => ''));
		if(count($customergroups)) {
			$collection->addFieldToFilter('main_table.customer_group_id', array('in', $customergroups));
		}
        
        // for each cart
        foreach($collection as $quote)
        {
            srand((double)microtime()*1000000);
			$token = md5(rand(0,9999999));
			$url = Mage::getModel('core/url')->setStore($store)->getUrl('',array('_nosid'=>true)).'ajax/index/loadquote?id='.$quote->getEntityId().'&token='.$token;

			//sending mail
			$senderid =  Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::SENDER, $store);
			$sender = array('name'=>Mage::getStoreConfig("trans_email/ident_$senderid/name",$store), 'email'=> Mage::getStoreConfig("trans_email/ident_$senderid/email",$store));

			$email = $quote->getCustomerEmail();
			$_productcount = count($quote->getAllVisibleItems());
			
			if(true){ //'subscription not required' $this->_isSubscribed($email,'abandonedcart',$store)) {
				$name = $quote->getCustomerFirstname().' '.$quote->getCustomerLastname();
				$templateId = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::EMAIL_TEMPLATE_XML_PATH);
				$vars = array('customer_name'=>$name,'quote'=>$quote,'quote_id'=>$quote->getId(),'recovery_url'=>$url,'product_count'=>$_productcount,'products'=>$_products);
			}
			$translate = Mage::getSingleton('core/translate');
			$mail = Mage::getModel('core/email_template')->setTemplateSubject($mailsubject)->sendTransactional($templateId,$sender,$email,$name,$vars,$store);
			$translate->setTranslateInLine(true);
			$quote->setEbizmartsAbandonedcartCounter($quote->getEbizmartsAbandonedcartCounter()+1);
			$quote->setEbizmartsAbandonedcartToken($token);
			$quote->save();
        }
    }

    /**
     * @param $store
     * @param $email
     * @return array
     */
    protected function _createNewCoupon($store,$email)
    {
        $couponamount = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::COUPON_AMOUNT, $store);
        $couponexpiredays = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::COUPON_EXPIRE, $store);
        $coupontype = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::COUPON_TYPE, $store);
        $couponlength = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::COUPON_LENGTH, $store);
        $couponlabel = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::COUPON_LABEL, $store);
        $websiteid =  Mage::getModel('core/store')->load($store)->getWebsiteId();

        $fromDate = date("Y-m-d");
        $toDate = date('Y-m-d', strtotime($fromDate. " + $couponexpiredays day"));
        if($coupontype == 1) {
            $action = 'cart_fixed';
            $discount = Mage::app()->getStore($store)->getCurrentCurrencyCode()."$couponamount";
        }
        elseif($coupontype == 2) {
            $action = 'by_percent';
            $discount = "$couponamount%";
        }
        $customer_group = new Mage_Customer_Model_Group();
        $allGroups  = $customer_group->getCollection()->toOptionHash();
        $groups = array();
        foreach($allGroups as $groupid=>$name) {
            $groups[] = $groupid;
        }
        $coupon_rule = Mage::getModel('salesrule/rule');
        $coupon_rule->setName("Abandoned coupon $email")
                    ->setDescription("Abandoned coupon $email")
                    ->setFromDate($fromDate)
                    ->setToDate($toDate)
                    ->setIsActive(1)
                    ->setCouponType(2)
                    ->setUsesPerCoupon(1)
                    ->setUsesPerCustomer(1)
                    ->setCustomerGroupIds($groups)
                    ->setProductIds('')
                    ->setLengthMin($couponlength)
                    ->setLengthMax($couponlength)
                    ->setSortOrder(0)
                    ->setStoreLabels(array($couponlabel))
                    ->setSimpleAction($action)
                    ->setDiscountAmount($couponamount)
                    ->setDiscountQty(0)
                    ->setDiscountStep('0')
                    ->setSimpleFreeShipping('0')
                    ->setApplyToShipping('0')
                    ->setIsRss(0)
                    ->setWebsiteIds($websiteid);
        $uniqueId = Mage::getSingleton('salesrule/coupon_codegenerator', array('length' => $couponlength))->generateCode();
        $coupon_rule->setCouponCode($uniqueId);
        $coupon_rule->save();
        return array($uniqueId,$discount,$toDate);
    }

    /**
     * @param $interval
     * @param $unit
     * @return string
     */
    function _getIntervalUnitSql($interval, $unit)
    {
        return sprintf('INTERVAL %d %s', $interval, $unit);
    }

    /**
     * @return string
     */
    function _getSuggestedZeroDate()
    {
        return '0000-00-00 00:00:00';
    }
    protected function _isSubscribed($email,$list,$storeId)
    {
        $collection = Mage::getModel('ebizmarts_autoresponder/unsubscribe')->getCollection();
        $collection->addFieldtoFilter('main_table.email',array('eq'=>$email))
            ->addFieldtoFilter('main_table.list',array('eq'=>$list))
            ->addFieldtoFilter('main_table.store_id',array('eq'=>$storeId));
        return $collection->getSize() == 0;
    }
}
