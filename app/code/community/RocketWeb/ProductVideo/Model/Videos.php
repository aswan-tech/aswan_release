<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   RocketWeb
 * @package    RocketWeb_ProductVideo
 * @copyright  Copyright (c) 2011 RocketWeb (http://rocketweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     RocketWeb
 */

class RocketWeb_ProductVideo_Model_Videos extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productvideo/videos');
    }
	
	/* getConnected function is used to make database connection using magento adapter  */
    public function getConnected() {
        $dbArray = $this->_getWriteAdapter()->getConfig();

        $connect = mysql_connect($dbArray['host'], $dbArray['username'], $dbArray['password']);
        if (!$connect) {
            die('Could not connect: ' . mysql_error());
        }
        $db_selected = mysql_select_db($dbArray['dbname'], $connect);
        if (!$db_selected) {
            die('Database Not Connected : ' . mysql_error());
        }
        mysql_set_charset("utf8", $connect);
        mysql_query($dbArray['initStatements'], $connect);
    }
	
	public function loadDataInfile($file, $files_to_be_archived, $filePath ,$archiveDir) {
        try {
            $this->loadDataOperation($file, $files_to_be_archived, $filePath ,$archiveDir);
        } catch (Exception $e) {
			print $e->getMessage();
            die();
        }
    }
	
	public function loadDataOperation($file, $files_to_be_archived, $filePath ,$archiveDir) {
        set_time_limit(0);
		$logger = Mage::getModel('logger/logger');
		
		$connection_read = Mage::getModel('core/resource')->getConnection('core_read');
		$select = $connection_read->select()->from('rw_youtube_videos', array('*')); 
		$carriersArray = $connection_read->fetchAll($select);
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//headers array to match/vaidate csv headers
		$tableHeader = Array ('sku', 'video_code', 'video_title', 'video_width', 'video_height');
		
		$filesRead = fopen($file, 'r');
        $headersData = fgetcsv($filesRead);
		$match = 0;
		foreach($headersData as $data){
			if(in_array(strtolower($data), $tableHeader)){
					$match = 1;
			}else{
				$match = 0;				
			}
		}
		if($match==0){
			echo 'ERROR::Header not match!'; 
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
			exit;
		}
		
		$connection->query("DROP TABLE IF EXISTS `rw_youtube_videos_temp`");
		$connection->query("CREATE TABLE IF NOT EXISTS `rw_youtube_videos_temp` (
								`video_id` int(10) NOT NULL AUTO_INCREMENT,
								`product_id` int(10) unsigned NOT NULL,
								`sku` varchar(256) DEFAULT NULL,
								`video_code` varchar(256) DEFAULT NULL,
								`video_title` text,
								`video_width` varchar(256) DEFAULT NULL,
								`video_height` varchar(256) DEFAULT NULL,
								PRIMARY KEY (`video_id`)
							) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
		
		$product = Mage::getModel('catalog/product');
		//$productId = $product->getIdBySku('100837');
		
		$rowCount = 0;
		$cnt = 1;
		$filesRead = fopen($file, 'r');
        $headersData = fgetcsv($filesRead);
		while ($data = fgetcsv($filesRead)) {
			$mergedData = Mage::helper('productvideo')->importVideoData($headersData, $data, $product, $cnt);
			
			if($mergedData['error'] == ''){
				$connection->insert('rw_youtube_videos_temp', $mergedData['success']);
				$rowCount++;
			}else{
				$msg = '>Skipping import row, '.$mergedData['error'].'<br/>';
				$logger->saveLogger('Product Videos', 'Error', $file, $msg);
				print $msg;
			}
			
			$cnt++;
		}
		
		//CSV file should not be blank.
		if($rowCount == 0){
			$msg = 'ERROR::No records found in CSV file!<br/><br/>';
			$logger->saveLogger('Product Videos', 'Error', $file, $msg);
			$connection->query("DROP TABLE IF EXISTS `rw_youtube_videos_temp`");
			print $msg;
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
			exit;
		}
		
		//Delete records from temp table where same video_code for same product_id already exist.
		$videoTblData = $connection_read->fetchAll("SELECT product_id, video_code FROM `rw_youtube_videos`");
		foreach($videoTblData as $k=>$v){
			$videoTempData = $connection->query("Delete FROM `rw_youtube_videos_temp` where product_id='".$v['product_id']."' and video_code='".$v['video_code']."'");
		}
		
		// no issues found in the CSV data, so proceed further.
		$connection->beginTransaction();
		$connection->query("INSERT INTO rw_youtube_videos (product_id, video_code, video_title, video_width, video_height) SELECT product_id, video_code, video_title, video_width, video_height FROM rw_youtube_videos_temp");
		$connection->query("DELETE FROM core_config_data where path LIKE 'rocketweb_productvideo/uploadcsv/bannerfile'");
		$connection->commit();
		
		$connection->query("DROP TABLE IF EXISTS `rw_youtube_videos_temp`");
    }
	
	public function moveToArchive($files_to_be_archived, $filePath ,$archiveDir){
		/* extra code added to move all extra files to archive directory */
		foreach($files_to_be_archived as $archive){
			chmod($filePath.$archive, 0777);
			copy($filePath.$archive, $archiveDir.$archive);
			if (!unlink($filePath.$archive)){
				echo ("Error deleting $archive<br/>"); 
			}
		}
	}
}