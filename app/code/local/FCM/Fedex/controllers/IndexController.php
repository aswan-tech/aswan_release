<?php

/**
 * Invitation frontend controller
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class FCM_Fedex_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction(){
        echo "Module Works";
        $model = Mage::getModel('fedex/shipping_carrier_fedex');
        pr(get_class_methods($model));
    }


}
