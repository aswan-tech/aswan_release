<?php
class FCM_Nosql_Model_Sms extends Mage_Core_Model_Abstract {

    const TEMPLATE_PATH = '/app/locale/en_IN/template/sms/';
    var $sendTo, $message;
    
    public function __construct() {
    
    }
    
    public function send( $template, $data) {
        $this->message = $this->getMessageBody( $template, $data);
        $this->sendTo = $this->getValidNumber($data['send_to']);
        if($this->sendTo && $this->message != '') {
            return $this->__execute();
        } else {
            return false;
        }
    }
    
    public function getValidNumber( $mobile ) {
        $mobile = substr( $mobile, strlen($mobile) -10, 10);
        $mobile = '91' . $mobile;
        return $mobile;
    }
    
    public function getStatus() {
        
    }
    
    public function getTemplate( $templateName ) {
        $template = file_get_contents( Mage::getBaseDir() . self::TEMPLATE_PATH . $templateName );
        return $template;
    }
    
    public function getMessageBody( $template, $data ) {
        $templateData = $this->getTemplate( $template );
        foreach($data as $key => $value) {
            $result['{{'.$key.'}}'] = $value;
        }
        return str_replace(array_keys( $result ), array_values( $result ), $templateData);
    }
    
    protected function __execute() {
		$url = 'http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=340985&username=7838067019&password=gptpp&To='.$this->sendTo.'&Text=';
        #$url = 'http://121.241.247.222:7501/failsafe/HttpLink?aid=572976&pin=am@1&mnumber='.$this->sendTo.'&message=';
        $curl = curl_init();        
        curl_setopt($curl, CURLOPT_URL, $url . urlencode($this->message));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $r = curl_exec($curl);
	//$xml = simplexml_load_string($r);
	//$res = $xml->MID->attributes()->ID;
        //if($res == 1) return 1;
        return 1;
    }
}
