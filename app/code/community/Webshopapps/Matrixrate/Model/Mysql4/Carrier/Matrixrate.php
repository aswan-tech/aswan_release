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
class Webshopapps_Matrixrate_Model_Mysql4_Carrier_Matrixrate extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('shipping/matrixrate', 'pk');
    }

    public function getNewRate(Mage_Shipping_Model_Rate_Request $request, $zipRangeSet=0) {
        $newdata = array();
		
		$collection = Mage::getResourceModel('matrixrate_shipping/carrier_matrixrate_collection');
        $collection->setConditionFilter($request->getConditionName())->setWebsiteFilter($request->getWebsiteId());
		$collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('website_id', 'zone', 'condition_name', 'condition_from_value', 'condition_to_value', 'shipping_charge'));
		$collection->getSelect()->join(array('zones' => 'shipping_zones'), "zones.zone=s.zone and zones.delivery_type='standard' and zones.country_code='".$request->getDestCountryId()."' AND (condition_from_value<='".$request->getData($request->getConditionName())."') AND (condition_to_value>='".$request->getData($request->getConditionName())."')" , array('delivery_type', 'shipping_provider'));
		//print $collection->getSelect();die;
		
		if($collection->count()){
			foreach ($collection->getData() as $data) {
				$newdata[] = $data;
			}
		}
		
        return $newdata;
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
		
        $csvFile = $_FILES["groups"]["tmp_name"]["matrixrate"]["fields"]["import"]["value"];
		
		if (!empty($csvFile)) {
			if($flg = $this->isValidFile($_FILES["groups"]["name"]["matrixrate"]["fields"]["import"]["value"])){
				$csv = trim(file_get_contents($csvFile));

				$table = Mage::getSingleton('core/resource')->getTableName('matrixrate_shipping/matrixrate');
				
				Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);
				Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
				//$websiteId = $object->getScopeId();
				$websiteId = Mage::app()->getStore()->getWebsiteId();
				/* Setting store back to admin for this product */
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);	
				Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMIN, Mage_Core_Model_App_Area::PART_EVENTS);
				
				$websiteModel = Mage::app()->getWebsite($websiteId);
				
				if (isset($_POST['groups']['matrixrate']['fields']['condition_name']['inherit'])) {
					$conditionName = (string) Mage::getConfig()->getNode('default/carriers/matrixrate/condition_name');
				} else {
					$conditionName = $_POST['groups']['matrixrate']['fields']['condition_name']['value'];
				}
				
				$conditionFullName = Mage::getModel('matrixrate_shipping/carrier_matrixrate')->getCode('condition_name_short', $conditionName);
				
				if (!empty($csv)) {
					$exceptions = array();
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
							if ($csvLine[0] != '1') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid website ID specified in the Row #%s', ($k + 1));
							}
							
							if (!$this->_isPositiveDecimalNumber($csvLine[2]) || $csvLine[2] == '*' || $csvLine[2] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid %s From "%s" in the Row #%s', $conditionFullName, $csvLine[2], ($k + 1));
							} else {
								$csvLine[2] = (float) $csvLine[2];
								$fromFlag	=	true;
							}

							if (!$this->_isPositiveDecimalNumber($csvLine[3]) || $csvLine[3] == '*' || $csvLine[3] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid %s To "%s" in the Row #%s', $conditionFullName, $csvLine[2], ($k + 1));
							} else {
								$csvLine[3] = (float) $csvLine[3];
								$toFlag		=	true;
							}
							
							//Make check for "Shipping charge" also
							if (!$this->_isPositiveDecimalNumber($csvLine[4]) || $csvLine[4] == '*' || $csvLine[4] == '') {
								$exceptions[] = Mage::helper('shipping')->__('Invalid Shipping charge "%s" in the Row #%s', $csvLine[4], ($k + 1));
							} else {
								$csvLine[4] = (float) $csvLine[4];
							}
							
							//Check that "Order To" value should be greater than "Order From" value
							if($fromFlag && $toFlag){
								//OrderSubtotalTo should be greater than OrderSubtotalFrom
								if($csvLine[3] < $csvLine[2]){
									$exceptions[] = Mage::helper('shipping')->__('"%s To" can not be less than "%s From" in the Row #%s', $conditionFullName, $conditionFullName, ($k + 1));
								}
							}
							
							$data[] = array('website_id' => $csvLine[0], 'zone' => $csvLine[1], 'condition_name' => $conditionName, 'condition_from_value' => $csvLine[2], 'condition_to_value' => $csvLine[3], 'shipping_charge' => $csvLine[4]);
							
							$dataDetails[] = array('zone' => $csvLine[1]);
						}
					}
					if (empty($exceptions)) {
						$connection = $this->_getWriteAdapter();
						
						$condition = array(
							$connection->quoteInto('website_id = ?', $websiteId),
							$connection->quoteInto('condition_name = ?', $conditionName),
						);
						$connection->delete($table, $condition);
						
						foreach ($data as $k => $dataLine) {
							//pr($dataLine);
							try {
								$connection->insert($table, $dataLine);
							} catch (Exception $e) {
								$exceptions[] = $e->getMessage();
								//$exceptions[] = Mage::helper('shipping')->__('Duplicate Row #%s (Zone "%s", Value From "%s" and Value To "%s")', ($k + 1), $dataDetails[$k]['zone'], $dataLine['condition_from_value'], $dataLine['condition_to_value']);
							}
						}
					}
				}
			}else{
				$exceptions[] = Mage::helper('shipping')->__('Invalid Matrix Rate file, only "csv" file is allowed.');
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