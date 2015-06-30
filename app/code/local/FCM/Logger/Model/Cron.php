<?php
/**
 * FCM Logger Module 
 *
 * Module for tracking Log and Cron Detail
 *
 * @category    FCM
 * @package     FCM_Logger
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */

class FCM_Logger_Model_Cron extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('logger/cron');
    }
	
		
	public function updateCron($cronKey, $status, $startTime='', $finishTime='', $message='')
	{
		try{	
			$id = $this->getCronId($cronKey);
			if($id  == false){
				return false;
			}
			$model = Mage::getModel('logger/cron')->load($id);
	        	if($model){
			if($startTime != '')
		 	{
		  	$model->setStartTime($startTime);
		 	}
		 	if($finishTime != '')
		 	{
		  	$model->setFinishTime($finishTime);
		 	}
		 
		 	if ($message) {
				$message = strip_tags($message);
		 	}
		        
		  		$model->setStatus($status)
				->setMessage($message)
				->save();
		 	}
		}
		catch(Exception $e){
                                Mage::log('Unable to update'.$cronKey, Zend_Log::DEBUG, 'fulfillment');
                        }
 
	}
	
	public function getCronId($cronKey)
	{	
		 try{
		 $Collection = Mage::getModel('logger/cron')->getCollection()
				->addFieldToSelect('cron_id')
				->addFieldToFilter('cron_key', array('like' => $cronKey));
				foreach($Collection as $val){
					$value=$val;
				}
			  return $value['cron_id'];
		}
		catch(Exception $e){
		  return false;	
		}
	}
	
	public function getCronStatus($cronKey)
	{		
		 $Collection = Mage::getModel('logger/cron')->getCollection()
				->addFieldToSelect('status ')
				->addFieldToFilter('cron_key', array('like' => $cronKey));
				foreach($Collection as $val){
					$val=$val;
				}
			  return $val['status '];
	}
	
	public function getFinishedDate($cronKey)
	{
		$Collection = Mage::getModel('logger/cron')->getCollection()
				->addFieldToSelect('finish_time')
				->addFieldToFilter('cron_key', array('like' => $cronKey));
				foreach($Collection as $val){
					$val=$val;
				}
			  return $val['finish_time'];
	}
}
