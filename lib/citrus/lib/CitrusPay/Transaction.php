<?php

class Transaction extends CitrusPay_ApiResource
{
	
	private $redirect_url;
	
	private $resp_code;
	
	private $resp_msg;
	
	public function get_resp_code() {
		return $this->resp_code;
	}
	
	public function set_resp_code($resp_code) {
		$this->resp_code = $resp_code;
	}
	
	public function get_resp_msg() {
		return $this->resp_msg;
	}
	
	public function set_resp_msg($resp_msg) {
		$this->resp_msg = $resp_msg;
	}
	
	public function get_redirect_url() {
		return $this->redirect_url;
	}
	
	public function set_redirect_url($redirect_url) {
		$this->redirect_url = $redirect_url;
	}
	
	public static function create($params=null, $apiKey=null)
	{
		$class = get_class();
		$transObj = new Transaction();
		$transObj = self::_genericCreate($class, $params, $apiKey); 		
		return $transObj;
	}
}
