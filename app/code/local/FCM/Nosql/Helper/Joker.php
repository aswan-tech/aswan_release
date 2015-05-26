<?php
/**
 * Implements message queues to send data (transactional) via sms
 */
class FCM_Nosql_Helper_Joker extends Mage_Core_Helper_Abstract {

    function send($data, $type, $template) {
        $model = $this->__loadModel( $type );
        $status = $model->send($template, $data);
        return true;
    }

    function sendNow($data, $type, $template) {
        $model = $this->__loadModel( $type );
        $status = $model->send($template, $data);
        return true;
    }
    
    private function __loadModel( $type ) {
        return Mage::getModel('nosql/' . $type);
    }
}