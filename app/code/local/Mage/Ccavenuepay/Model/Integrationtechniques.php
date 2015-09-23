<?php

	class Mage_Ccavenuepay_Model_Integrationtechniques{
			public function toOptionArray() {
		return array(
			array( 'value' => 'redirect', 'label' => 'Redirect' ),
			array( 'value' => 'iframe', 'label' => 'IFRAME' ),
		);
	}

	public function toArray() {
		return array(
			'redirect' => 'Redirect',
			'iframe' => 'Iframe',
		);
	}
	}