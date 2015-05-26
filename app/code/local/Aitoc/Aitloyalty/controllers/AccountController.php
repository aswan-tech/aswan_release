<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/controllers/AccountController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ jSgmMMjIEyjZUkcE('8aec05efa2635f6883d663a21a56cb67'); ?><?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

require_once 'Mage/Customer/controllers/AccountController.php';

class Aitoc_Aitloyalty_AccountController extends Mage_Customer_AccountController
{
    public function promostatsAction()
    {
        $iStoreId = Mage::app()->getStore()->getId();
        $iSiteId  = Mage::app()->getWebsite()->getId();

        /* */
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitloyalty')->getLicense()->getPerformer();
        $ruler     = $performer->getRuler();
        if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
        {
            return $this->_redirect('customer/account/');
        }
        /* */

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Coupons'));
        
        $this->renderLayout();
    }

} } 