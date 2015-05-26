<?php

class FCM_Newsletters_Block_Myemail extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	public function getMailContent() {
		$template = Mage::getModel('newsletter/template');
		
		$param = $this->getRequest()->getParam('id');
		
		if($param){
			$decodedStr = base64_decode($param);
			
			$paramArr = explode("/", $decodedStr);
			$subs_id  = $paramArr[4];//SubscriberID
			$id 	  = $paramArr[6];//NewsletterID
			
			if($id){
				//print $decodedStr;
				//print "<br><br>";
				//print_r($paramArr);
				//print Mage::helper('core/url')->getHomeUrl()."newsletter/myemail/detail/id/".base64_encode('newsletter/myemail/detail/subs_id/24/id/3');
				//print "<br><br>";
				
				try{
					$queue = Mage::getModel('newsletter/queue');
					$queue->load($id);
					$template->setTemplateType($queue->getNewsletterType());
					$template->setTemplateText($queue->getNewsletterText());
					$template->setTemplateStyles($queue->getNewsletterStyles());

					$storeId = Mage::app()->getDefaultStoreView()->getId();
					
					$vars = array();
					$vars['subscriber'] = Mage::getModel('newsletter/subscriber')->load($subs_id);

					$template->emulateDesign($storeId);
					$templateProcessed = $template->getProcessedTemplate($vars, true);
					$template->revertDesign();

					if($template->isPlain()) {
						$templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
					}
					
					return $templateProcessed;
				}catch (Exception $e) {
					Mage::getSingleton('core/session')->addError($this->__('Newsletter no more exist into the system.'));
					//Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
					Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/error"));
				}
			}else{
				Mage::getSingleton('core/session')->addError($this->__('Newsletter no more exist into the system.'));
				Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/error"));
			}
		}
	}
	
	public function showData() {
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		print "<b>Params to use:</b><br>{{tbl}}<br>{{where}} as base64_encode(where)<br>{{orderby}}<br>{{sortorder}}<br><br>";
		//print_r($this->getRequest()->getParams());
		
		print base64_encode("user like '%rahul%'")."<br><br>";
		
		$tbl 		= trim($this->getRequest()->getParam('tbl'), " ");
		$where		= $this->getRequest()->getParam('where');//do not eliminate spaces here
		$orderBy 	= trim($this->getRequest()->getParam('orderby'), " ");
		$sortOrder 	= trim($this->getRequest()->getParam('sortorder'), " ");
		
		$table 		= "";
		
		//Use either one to get the db_name
		######### source http://blog.decryptweb.com/database-connection-details-magento/      ##########
		$config	= Mage::getConfig()->getResourceConnectionConfig("default_setup");
        $host 	= $config->host;
        $uname 	= $config->username;
        $pass 	= $config->password;
        $dbname	= $config->dbname;
		print "<br>".$host."@@".$uname."##".$pass."**".$dbname."<br><br>";
		
		##########	http://stackoverflow.com/questions/4614705/in-magento-how-do-you-get-the-database-name	##########
		//$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
		
		if(isset($tbl) && $tbl!=''){
			//Check if table name exist
			$result = $read->fetchRow("SELECT count(*) as total FROM information_schema.TABLES WHERE (TABLE_SCHEMA='".$dbname."')AND(TABLE_NAME='".$tbl."')");
			
			if($result['total']){
				$qry = "select * from ".$tbl;
				if($where){
					$where = base64_decode($where);
					$qry .= " where ".$where;
				}
				if($orderBy){
					$qry .= " order by ".$orderBy;
				}
				if($sortOrder){
					$qry .= " ".$sortOrder;
				}
				$res = $read->fetchAll($qry);
				
				print $qry."<br><br>";
				
				if(count($res)){
					print "<br>Total ".count($res)." record(s) found.<br><br>";
					
					$table = "<table style='border:1px solid #000; border-collapse:collapse;' cellpadding='5' border='2'>";
					$i = 0;
					
					foreach($res as $index=>$innerArr){
						$tblHeader 	= "<tr><td valign='top' bgcolor='#3261FA' style='color:#FFF; border:1px solid #000;'><b>Sl.</b></td>";
						
						if($i%2 == 0){
							$bgColor = "bgcolor='#D0D9F7'";
						}else{
							$bgColor = "bgcolor='#E6EAF5'";
						}
						
						$tblRow 	= "<tr><td valign='top' ".$bgColor." style='border:1px solid #000;'>".($i+1)."</td>";
						
						foreach($innerArr as $col=>$val){
							if($i==0){
								//Creating the table header columns
								$tblHeader .= "<td valign='top' bgcolor='#3261FA' style='color:#FFF; border:1px solid #000;'><b>".$col."</b></td>";
							}
							$tblRow .= "<td valign='top' ".$bgColor." style='border:1px solid #000;'>".$val."</td>";
						}
						$tblHeader .= "</tr>";
						$tblRow .= "</tr>";
						
						if($i==0){
							$table .= $tblHeader;
						}
						
						$table .= $tblRow;
						
						$i++;
					}
					$table .= "</table>";
				}
				
				return $table;
			}else{
				print "<br><div style='color:red;'><b>Error!!! Table '".$tbl."' does not exist.</b></div><br>";
			}
		}
	}
}

