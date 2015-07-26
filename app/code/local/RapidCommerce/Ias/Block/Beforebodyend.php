<?php
class RapidCommerce_Ias_Block_Beforebodyend extends Mage_Core_Block_Template 
{
	const CONFIG_PATH_USE_AJAX_SCROLL = 'catalog/frontend/useajaxscroll';
	const CONFIG_PATH_USE_SCROLL_TO_TOP = 'catalog/frontend/useuitotop';
	const CONFIG_PATH_USE_IAS_TRIGGERPAGETHRESHOLD = 'catalog/frontend/ias_triggerpagethreshold';
	const CONFIG_PATH_USE_IAS_THRESHOLDMARGIN = 'catalog/frontend/ias_thresholdmargin';
	const CONFIG_PATH_USE_IAS_TO_HISTORY = 'catalog/frontend/ias_history';
	const CONFIG_PATH_DEFAULT_LIST_MODE = 'catalog/frontend/list_mode';
	const CONFIG_PATH_GA_ENABLED = 'google/analytics/active';
	const PARAM_LIST_MODE = 'mode';

	public function getUseIas() {
		return Mage::getStoreConfig(self::CONFIG_PATH_USE_AJAX_SCROLL);
	}

	public function getUseScrollToTop() {
		return Mage::getStoreConfig(self::CONFIG_PATH_USE_SCROLL_TO_TOP);
	}

	public function getDefaultCatalogListMode() {
		return Mage::getStoreConfig(self::CONFIG_PATH_DEFAULT_LIST_MODE);
	}

	public function getCurrentListMode() {
		return $this->getRequest()->getParam(self::PARAM_LIST_MODE);
	}

	public function getIasTriggerpagethreshold() {
		return Mage::getStoreConfig(self::CONFIG_PATH_USE_IAS_TRIGGERPAGETHRESHOLD);
	}

	public function getIasThresholdmargin() {
		return Mage::getStoreConfig(self::CONFIG_PATH_USE_IAS_THRESHOLDMARGIN);
	}

	public function getIasHistory() {
		return Mage::getStoreConfig(self::CONFIG_PATH_USE_IAS_TO_HISTORY);
	}

	public function getGoogleAnalyticsEnabled() {
		return Mage::getStoreConfig(self::CONFIG_PATH_GA_ENABLED);
	}
}
