<?php

class Custom_Common_Block_Popup extends Mage_Core_Block_Template {
	protected function _construct() {
            $this->addData(array(
				'cache_lifetime' => 1,
				'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG),
				'cache_key' => 'popup_'.time()
            ));
        }

}