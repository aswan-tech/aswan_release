<?php

/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_Helper_Data extends Mage_Core_Helper_Abstract {
    const QUERY_VAR_NAME = 'q';
    const MAX_QUERY_LEN = 200;

    function rangeWeek($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        $res['start'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        $res['end'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
        return $res;
    }

    public function getUserEmail() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }

    public function getUserName() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim("{$customer->getFirstname()} {$customer->getLastname()}");
    }

    public function getRoute($custom="") {
        $homeUrl = Mage::getBaseUrl();
        $module = Mage::app()->getRequest()->getModuleName();
        $cntrlr = Mage::app()->getRequest()->getControllerName();
        //$actn		=	Mage::app()->getRequest()->getActionName();
        //print $homeUrl.$module.'/'.$cntrlr.'/'.$actn;die;

        if($custom){
            return $homeUrl . $module . '/' . $cntrlr . '/'. $custom;
        }else{
            return $homeUrl . $module . '/' . $cntrlr;
        }
    }

    public function getGalleryResultUrl($query = null) {
        return $this->_getUrl('gallery/*/search', array(
            '_query' => array(self::QUERY_VAR_NAME => $query),
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
    }

    /**
     * Retrieve search query parameter name
     *
     * @return string
     */
    public function getQueryParamName() {
        return self::QUERY_VAR_NAME;
    }

    /**
     * Retrieve maximum query length
     *
     * @param mixed $store
     * @return int|string
     */
    public function getMaxQueryLength($store = null) {
        return Mage::getStoreConfig(Mage_CatalogSearch_Model_Query::XML_PATH_MAX_QUERY_LENGTH, $store);
    }

    public function getArchiveThumbUrl($img_name, $type) {
		if($type == 'trends'){
			$archiveWidth = Mage::getStoreConfig('gallery/trendsettings/archivethumbwidth') ? Mage::getStoreConfig('gallery/trendsettings/archivethumbwidth') : 175;
			$archiveHeight = Mage::getStoreConfig('gallery/trendsettings/archivethumbheight') ? Mage::getStoreConfig('gallery/trendsettings/archivethumbheight') : 243;
		}elseif($type == 'day'){
			$archiveWidth = Mage::getStoreConfig('gallery/lookoftheday/archivethumbwidth') ? Mage::getStoreConfig('gallery/lookoftheday/archivethumbwidth') : 144;
			$archiveHeight = Mage::getStoreConfig('gallery/lookoftheday/archivethumbheight') ? Mage::getStoreConfig('gallery/lookoftheday/archivethumbheight') : 193;
		}else{
			$archiveWidth = Mage::getStoreConfig('gallery/lookoftheweek/archivethumbwidth') ? Mage::getStoreConfig('gallery/lookoftheweek/archivethumbwidth') : 144;
			$archiveHeight = Mage::getStoreConfig('gallery/lookoftheweek/archivethumbheight') ? Mage::getStoreConfig('gallery/lookoftheweek/archivethumbheight') : 193;
		}
		
        $path = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'thumbs' . DS;
        $thumbname = preg_replace("/\.[^\.]+$/", ".png", $archiveWidth . '_' . $archiveHeight . '_' . $img_name);

        if (file_exists($path . $thumbname)) {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'gallery/thumbs/' . $thumbname;
        }

        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $img_name;
    }

    public function magentoLess14() {
        return version_compare(Mage::getVersion(), '1.4', '<');
    }

    public function getCommentsPerPage($store = null) {
        $perPageCount = intval(Mage::getStoreConfig('gallery/weekcomments/page_count', $store));
        if ($perPageCount < 1)
            $perPageCount = 10;
        return $perPageCount;
    }

}