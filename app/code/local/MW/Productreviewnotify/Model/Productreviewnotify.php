<?php

class MW_Productreviewnotify_Model_Productreviewnotify extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAILADMIN_PRODUCTREVIEW_TEMPLATE      = 'mw_mageworld_productreviewnotify/mgoptions/mgemail_template';
	const XML_PATH_EMAILADMIN_PRODUCTREVIEW      			= 'mw_mageworld_productreviewnotify/mgoptions/mgrecipient_email';
	const XML_PATH_EMAILADMIN_PRODUCTREVIEW_IDENTITY      = 'mw_mageworld_productreviewnotify/mgoptions/mgsender_email_identity';
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('productreviewnotify/productreviewnotify');
    }
    
	public function sendNewAccountEmailToAdmin($reviewid = '0', $nickname = '', $productid = '0', $productname = '', $ratingprice = '0', $ratingvalue = '0', $ratingquality = '0', $title = '', $detail = '', $backUrl = '', $storeId = '0')
    {
        //$types = array(
        //    'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,  // welcome email, when confirmation is disabled
        //    'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
        //    'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,   // email with confirmation link
        //);
        //if (!isset($types[$type])) {
        //    throw new Exception(Mage::helper('customer')->__('Wrong transactional account email type.'));
        //}

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $storeId = ($storeId == '0')?$this->getSendemailStoreId():$storeId;
        if ($this->getWebsiteId() != '0' && $storeId == '0') {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $storeId = current($storeIds);
        }
		
		$myRating = '<div class="rating-box"><div class="rating" style="width:20%;"></div></div>';
		
		//array('reviewid' => $reviewid, 'nickname' => $nickname, 'productid' => $productid, 'productname' => $productname, 'ratingprice' => $ratingprice, 'ratingvalue' => $ratingvalue, 'ratingquality' => $ratingquality, 'title' => $title, 'detail' => $detail, 'back_url' => $backUrl)
		
        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAILADMIN_PRODUCTREVIEW_TEMPLATE, $storeId),
                Mage::getStoreConfig(self::XML_PATH_EMAILADMIN_PRODUCTREVIEW_IDENTITY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_EMAILADMIN_PRODUCTREVIEW, $storeId),
                'admin',
				array('reviewid' => $reviewid, 'nickname' => $nickname, 'productid' => $productid, 'productname' => $productname, 'ratingquality' => $myRating, 'title' => $title, 'detail' => $detail, 'back_url' => $backUrl)
                );

        $translate->setTranslateInline(true);
		//echo $storeId.'<br>'.Mage::getStoreConfig(self::XML_PATH_EMAILADMIN_PRODUCTREVIEW_TEMPLATE, $storeId).
		//'<br>'.Mage::getStoreConfig(self::XML_PATH_EMAILADMIN_PRODUCTREVIEW_IDENTITY, $storeId).
		//'<br>'.Mage::getStoreConfig(self::XML_PATH_EMAILADMIN_PRODUCTREVIEW, $storeId);
		//exit;
        return $this;
    }
    
}