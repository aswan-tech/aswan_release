<?php

class TransactionSearchCollection extends CitrusPay_ApiResource
{
	private $resp_code;
	
	private $resp_msg;
	
	private $transaction;
	
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
	
	public function get_transaction() {
		return $this->transaction;
	}
	
	public function set_transaction($transaction) {
		$this->transaction = $transaction;
	}

}
