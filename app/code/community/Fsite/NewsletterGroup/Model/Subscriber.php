<?php
/**
 * Subscriber model
 *
 * @category   Fsite
 * @package    Fsite_NewsletterGroup
 * @author     Fsite
 */
class Fsite_NewsletterGroup_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    protected $_groupName;

    public function groupSubscribe($email, $group)
    {
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED || $this->getStatus() == self::STATUS_NOT_ACTIVE) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $ownerId = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();
                $isOwnSubscribes = ($customerSession->isLoggedIn() && $ownerId == $customerSession->getId());
                if ($isOwnSubscribes == true){
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                }
                else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
            
            if ( is_array( $group ) ) {
                $groupString = implode( ',', $group );
                $this->setNewsletterGroupId( $groupString );
            }
            else {
                $this->setNewsletterGroupId( $group );
            }
            
        }

        if ($customerSession->isLoggedIn()) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }

            return $this->getStatus();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Overriden
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
        $this->loadByCustomer($customer);

        if ($customer->getImportMode()) {
            $this->setImportMode(true);
        }

        if (!$customer->getIsSubscribed() && !$this->getId()) {
            // If subscription flag not set or customer is not a subscriber
            // and no subscribe below
            return $this;
        }

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

       /*
        * Logical mismatch between customer registration confirmation code and customer password confirmation
        */
       $confirmation = null;
       if ($customer->isConfirmationRequired() && ($customer->getConfirmation() != $customer->getPassword())) {
           $confirmation = $customer->getConfirmation();
       }

        $subscribed_on_confirm = false;
        if($customer->hasIsSubscribed()) {
            $status = $customer->getIsSubscribed() ? (!is_null($confirmation) ? self::STATUS_UNCONFIRMED : self::STATUS_SUBSCRIBED) : self::STATUS_UNSUBSCRIBED;
        } elseif (($this->getStatus() == self::STATUS_UNCONFIRMED) && (is_null($confirmation))) {
            $status = self::STATUS_SUBSCRIBED;
            $subscribed_on_confirm = true;
        } else {
            $status = ($this->getStatus() == self::STATUS_NOT_ACTIVE ? self::STATUS_UNSUBSCRIBED : $this->getStatus());
        }

        if($status != $this->getStatus()) {
            $this->setIsStatusChanged(true);
        }

        $this->setStatus($status);

        if(!$this->getId()) {
            $this->setStoreId($customer->getStoreId())
                ->setCustomerId($customer->getId())
                ->setEmail($customer->getEmail());
        } else {
            $this->setEmail($customer->getEmail());
        }

        $isSubscribed = Mage::app()->getRequest()->getParam( 'is_subscribed', 1 );
        if ( is_array( $isSubscribed ) ) {
            $groupString = implode( ',', $isSubscribed );
            $this->setNewsletterGroupId( $groupString );
        }
        else {
            $this->setNewsletterGroupId( $isSubscribed );
        }
		
        $this->save();
        $sendSubscription = $customer->getData('sendSubscription') || $subscribed_on_confirm;
		if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();
        } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
				$this->setCoupon($this->getCouponCustom());
				 $this->save();
                $this->sendConfirmationSuccessEmail();
        }
        return $this;
    }

    /**
     * Get the newsletter group name
     */
    public function getNewsletterGroupName()
    {
        if (!$this->_groupName) {
            $groupId = $this->getNewsletterGroupId();
            if ($groupId) {
                $group = Mage::getModel('newslettergroup/group')->load($groupId);
                $this->_groupName = $group->getGroupName();
            }
        }

        return $this->_groupName;

    }

    /**
     * Get newsletter groups
     */
    public function getGroups()
    {
        if ( !$this->_groupCollection ) {
            // Add filter
            $collection = Mage::getResourceModel( 'newslettergroup/group_collection' )
                ->addFieldToFilter( 'visible_in_frontend', array( "eq" => 1 ) )
                ->load();

            $this->_groupCollection = $collection->getItems();
        }

        return $this->_groupCollection;
    }
	
	public function getCouponCustom()
    {
        $counpon = '';
        if (Mage::getStoreConfig('ambirth/newsletter/enabled') && !Mage::registry('status_subscr')) {
        $counpon = Mage::helper('ambirth')->generateCoupon('newsletter');
        }
        return $counpon;
    }
}
