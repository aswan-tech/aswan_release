<?php

/**
 * Webshopapps Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Shipping MatrixRates
 *
 * @category   Webshopapps
 * @package    Webshopapps_Matrixrate
 * @copyright  Copyright (c) 2010 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Karen Baker <sales@webshopapps.com>
 */
class Webshopapps_Matrixrate_Model_Mysql4_Carrier_Providers extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('shipping/zones', 'id');
    }
	
	public function isValidFile($fileName, $extArr = null){
		if(empty($extArr)){
			$extArr	=	array('csv');
		}
		
		$extension = substr($fileName, strrpos($fileName, '.')+1);
		
		if(in_array(strtolower($extension), $extArr)){
			return true;
		}
		
		return false;
	}
	
    public function uploadAndImport(Varien_Object $object) {
        $exceptions = array();
		
		$csvFile = $_FILES["groups"]["tmp_name"]["shippingproviders"]["fields"]["importcsv"]["value"];
		
		if (!empty($csvFile)) {
			if($flg = $this->isValidFile($_FILES["groups"]["name"]["shippingproviders"]["fields"]["importcsv"]["value"])){
				$csv = trim(file_get_contents($csvFile));

				$table = 'shipping_zones';//Mage::getSingleton('core/resource')->getTableName('matrixrate_shipping/zones');
				
				$websiteId = $object->getScopeId();
				$websiteModel = Mage::app()->getWebsite($websiteId);
				
				/*
				if (isset($_POST['groups']['shippingproviders']['fields']['condition_name']['inherit'])) {
					$conditionName = (string) Mage::getConfig()->getNode('default/carriers/matrixrate/condition_name');
				} else {
					$conditionName = $_POST['groups']['shippingproviders']['fields']['condition_name']['value'];
				}
				
				$conditionFullName = Mage::getModel('matrixrate_shipping/carrier_matrixrate')->getCode('condition_name_short', $conditionName);
				*/
				
				$conditionName = 'standard';
				$conditionFullName = 'Standard';
				
				if (!empty($csv)) {
					$csvLines = explode("\n", $csv);
					$csvLine = array_shift($csvLines);
					$csvLine = $this->_getCsvValues($csvLine);
					
					if (count($csvLine) < 4) {
						$exceptions[0] = Mage::helper('shipping')->__('Invalid Matrix Rates File Format');
					}

					$countryCodes = array();
					$regionCodes = array();
					foreach ($csvLines as $k => $csvLine) {
						$csvLine = $this->_getCsvValues($csvLine);
						if (count($csvLine) > 0 && count($csvLine) < 4) {
							$exceptions[0] = Mage::helper('shipping')->__('Invalid Matrix Rates File Format');
						} else {
							$countryCodes[] = $csvLine[0];
							$regionCodes[] = $csvLine[1];
						}
					}
					
					if (empty($exceptions)) {
						$data = array();
						
						foreach ($csvLines as $k => $csvLine) {
							$fromFlag	=	false;
							$toFlag		=	false;
							
							$csvLine = $this->_getCsvValues($csvLine);

							if ($csvLine[0] == '*' || $csvLine[0] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid Country code in the Row #%s', ($k + 1));
							}
							
							if ($csvLine[1] == '*' || $csvLine[1] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid Zone in the Row #%s', ($k + 1));
							}
							
							if ($csvLine[2] == '*' || $csvLine[2] == '' || strtolower($csvLine[2]) != 'standard') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid Delivery type "%s" in the Row #%s', $csvLine[2], ($k + 1));
							}
							
							if ($csvLine[3] == '*' || $csvLine[3] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid Shipping provider in the Row #%s', ($k + 1));
							}
							
							$data[] = array('country_code' => $csvLine[0], 'zone' => $csvLine[1], 'delivery_type' => $csvLine[2], 'shipping_provider' => $csvLine[3]);
							
							$dataDetails[] = array('zone' => $csvLine[1]);
						}
					}
					
					if (empty($exceptions)) {
						$connection = $this->_getWriteAdapter();
						
						$condition = array(
							$connection->quoteInto('delivery_type = ?', $conditionName),
						);
						$connection->delete($table, $condition);

						foreach ($data as $k => $dataLine) {
							try {
								$connection->insert($table, $dataLine);
							} catch (Exception $e) {
								$exceptions[] = Mage::helper('shipping')->__('Duplicate Row #%s (Country Code "%s", Zone "%s", Delivery Type "%s" and Shipping Provider "%s")', ($k + 1), $dataLine['country_code'], $dataDetails[$k]['zone'], $dataLine['delivery_type'], $dataLine['shipping_provider']);
							}
						}
					}
				}
			}else{
				$exceptions[] = Mage::helper('shipping')->__('Invalid Shipping Provider file, only "csv" file is allowed.');
			}
			
			if (!empty($exceptions)) {
				throw new Exception("<br>" . implode("<br>", $exceptions));
			}
		}
    }

    private function _getCsvValues($string, $separator=",") {
        $elements = explode($separator, trim($string));
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes % 2 == 1) {
                for ($j = $i + 1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') > 0) {
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j - $i + 1, implode($separator, array_slice($elements, $i, $j - $i + 1)));
                        break;
                    }
                }
            }
            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr = & $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
            $elements[$i] = trim($elements[$i]);
        }
        return $elements;
    }

    private function _isPositiveDecimalNumber($n) {
        return preg_match("/^[0-9]+(\.[0-9]*)?$/", $n);
    }

}
