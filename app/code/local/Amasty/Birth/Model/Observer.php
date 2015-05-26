<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
class Amasty_Birth_Model_Observer
{
    private function debug($str)
    {
        if(isset($_GET['debug'])) echo "\r\n<br><br>" . $str;
    }


    public function send()
    {
        $types = array('reg', 'birth', 'activity', 'wishlist');
        foreach ($types as $type){
            if (Mage::getStoreConfig('ambirth/' . $type . '/enabled')){
                $this->debug("Called " . '_send' . ucfirst($type) . 'Coupon');
                call_user_method('_send' . ucfirst($type) . 'Coupon', $this);
            }
        }


        $this->_removeOldCoupons();

        return $this;
    }

    protected function _getCollection()
    {
        // !! todo add condition (not in log table)
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->setPageSize(300)
            ->setCurPage(1);

        return $collection;
    }

    protected function _sendBirthCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/birth/days'));
        $time = time();
        if ($days > 0) // afer birthday
            $time = strtotime("-$days days");
        else {
            $days = abs($days);
            $time = strtotime("+$days days");
        }

        $collection = $this->_getCollection()
            ->addAttributeToSelect('dob')
            ->addAttributeToFilter('dob', array('field_expr'=>"DATE_FORMAT(#?, '%m-%d')", 'eq'=>date('m-d', $time)))
        ;

        $this->debug("Birth SQL: " . $collection->getSelect());

        foreach ($collection as $customer){
		    $this->_emailToCustomer($customer, 'birth');
        }
    }

    protected function _sendRegCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/reg/days'));
        if ($days < 0)
            return;

        $collection = $this->_getCollection();
        $select = $collection->getSelect();
        $select->where(new Zend_Db_Expr(
            "DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime("-$days days"))."'"
        ));

        $this->debug("Reg SQL: " . $collection->getSelect());

        foreach ($collection as $customer){
		    $this->_emailToCustomer($customer, 'reg');
        }
    }

    protected function _sendActivityCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/activity/days'));
        if ($days < 0)
            return;

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $select = $db->select()
            ->from($resource->getTableName('log/customer'), array('customer_id'))
            ->having('MAX(login_at) < "'.date('Y-m-d', strtotime("-$days days")) .'"')
            ->group('customer_id')
            ->limit(1000);

        $this->debug("Log-IN SQL(1): " . $select);

        $ids = $db->fetchCol($select);
        if (!$ids)
            return;

        $collection = $this->_getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$ids));
        $collection->getSelect()->order('entity_id DESC');
        $collection->load();

        $this->debug("Log-IN SQL(2): " . $collection->getSelect());

        foreach ($collection as $customer){
		    $this->_emailToCustomer($customer, 'activity');
        }
    }

    protected function _sendWishlistCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/wishlist/days'));
        if ($days < 0)
            return;

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $select = $db->select()
            ->from(array('w'=>$resource->getTableName('wishlist/wishlist')), array('customer_id'))
            ->joinInner(array('i'=>$resource->getTableName('wishlist/item')), 'w.wishlist_id=i.wishlist_id', array())
            ->having('COUNT(i.product_id)>0 AND MAX(updated_at) <= "'.date('Y-m-d', strtotime("-$days days")) .'"')
            ->group('customer_id')
            ->limit(1000);

        $this->debug("wishlist SQL(1): " . $select);

        $ids = $db->fetchCol($select);
        if (!$ids)
            return;

        $collection = $this->_getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$ids));
        $collection->getSelect()->order('entity_id DESC');
        $collection->load();

        $this->debug("Wishlist SQL(2): " . $collection->getSelect());

        foreach ($collection as $customer){
		    $this->_emailToCustomer($customer, 'wishlist');
        }
    }

    protected function _emailToCustomer($customer, $type)
    {
		$logCollection = Mage::getResourceModel('ambirth/log_collection')
			->addFieldToFilter('type', $type)
			->addFieldToFilter('customer_id', $customer->getId());

        if (in_array($type, array('birth', 'wishlist', 'activity')))
            $logCollection->addFieldToFilter('y', date('Y'));

	    $this->debug("CHECK SQL: " . $logCollection->getSelect());

        if ($logCollection->getSize() > 0)
            return;

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $store = Mage::app()->getStore($customer->getStoreId());
        $tpl = Mage::getModel('core/email_template');
        $tpl->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
            ->sendTransactional(
                Mage::getStoreConfig('ambirth/' . $type . '/template', $store),
                Mage::getStoreConfig('ambirth/general/identity', $store),
                $customer->getEmail(),
                $customer->getName(),
                array(
                    'website_name'  => $store->getWebsite()->getName(),
                    'group_name'    => $store->getGroup()->getName(),
                    'store_name'    => $store->getName(),
                    'coupon'        => Mage::helper('ambirth')->generateCoupon($type, $store),
                    'coupon_days'   => Mage::getStoreConfig('ambirth/'.$type.'/coupon_days', $store),
                    'customer_name' => $customer->getName(),
                )
        );
        $logModel = Mage::getModel('ambirth/log')
			->setY(date('Y'))
			->setType($type)
			->setCustomerId($customer->getId())
			->setSentDate(date('Y-m-d H:i:s'));
	    $logModel->save();

        $translate->setTranslateInline(true);
    }

    protected function _removeOldCoupons()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/general/remove_days'));
        if ($days <= 0)
            return;

        $rules = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('name', array('like'=>'Special Coupon%'))
            ->addFieldToFilter('from_date', array('lt' => date('Y-m-d', strtotime("-$days days"))))
            ;

        $errors = '';
        foreach ($rules as $rule){
            try {
                $rule->delete();
            }
            catch (Exception $e) {
                $errors .= "\r\nError when deleting rule #" . $rule->getId() . ' : ' . $e->getMessage();
            }
        }

        if ($errors)
            throw new Exception($errors);
    }

}