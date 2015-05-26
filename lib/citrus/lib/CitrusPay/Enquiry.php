<?php

class Enquiry extends CitrusPay_ApiResource 
{

	private $resp_code;

	private $resp_msg;

	private $txn_id;

	private $pg_txn_id;

	private $auth_id_code;

	private $rrn;

	private $txn_type;

	private $txn_date_time;

	private $amount;
	
	private $payment_mode;
	
	private $issuer_code;
	
	private $masked_card_number;
	
	private $card_type;
	
	private $txn_gateway;

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

	public function get_txn_id() {
		return $this->txn_id;
	}

	public function set_txn_id($txn_id) {
		$this->txn_id = $txn_id;
	}

	public function get_pg_txn_id() {
		return $this->pg_txn_id;
	}

	public function set_pg_txn_id($pg_txn_id) {
		$this->pg_txn_id = $pg_txn_id;
	}

	public function get_auth_id_code() {
		return $this->auth_id_code;
	}

	public function set_auth_id_code($auth_id_code) {
		$this->auth_id_code = $auth_id_code;
	}

	public function get_rrn() {
		return $this->rrn;
	}

	public function set_rrn($rrn) {
		$this->rrn = $rrn;
	}

	public function get_txn_type() {
		return $this->txn_type;
	}

	public function set_txn_type($txn_type) {
		$this->txn_type = $txn_type;
	}

	public function get_txn_date_time() {
		return $this->txn_date_time;
	}

	public function set_txn_date_time($txn_date_time) {
		$this->txn_date_time = $txn_date_time;
	}

	public function get_amount() {
		return $this->amount;
	}

	public function set_amount($amount) {
		$this->amount = $amount;
	}
	
	public function get_payment_mode() {
		return $this->payment_mode;
	}
	
	public function set_payment_mode($payment_mode) {
		$this->payment_mode = $payment_mode;
	}
	
	public function get_issuer_code() {
		return $this->issuer_code;
	}
	
	public function set_issuer_code($issuer_code) {
		$this->issuer_code = $issuer_code;
	}
	
	public function get_masked_card_number() {
		return $this->masked_card_number;
	}
	
	public function set_masked_card_number($masked_card_number) {
		$this->masked_card_number = $masked_card_number;
	}
	
	public function get_card_type() {
		return $this->card_type;
	}
	
	public function set_card_type($card_type) {
		$this->card_type = $card_type;
	}
	
	public function get_txn_gateway() {
		return $this->txn_gateway;
	}
	
	public function set_txn_gateway($txn_gateway) {
		$this->txn_gateway = $txn_gateway;
	}

	public static function create($params=null, $apiKey=null)
	{
		$class = get_class();
		$enqObj = new Enquiry();
		$enqObj = self::_genericCreate($class, $params, $apiKey);
		return $enqObj;
	}
}

?>