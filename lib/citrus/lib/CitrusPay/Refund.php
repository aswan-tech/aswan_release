<?php

class Refund extends CitrusPay_ApiResource
{
	private $resp_code;

	private $resp_msg;

	private $txn_id;

	private $pg_txn_id;

	private $auth_id_code;

	private $rrn;

	private $amount;

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

	public function get_amount() {
		return $this->amount;
	}

	public function set_amount($amount) {
		$this->amount = $amount;
	}
	
	public static function create($params=null, $apiKey=null)
	{
		$class = get_class();
		$refObj = new Refund();
		$refObj = self::_genericCreate($class, $params, $apiKey);
		return $refObj;
	}
}
