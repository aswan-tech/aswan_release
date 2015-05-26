<?php
class FCM_Newsletters_MyemailController extends Mage_Core_Controller_Front_Action{
	
	public function detailAction(){
		$this->loadLayout();
        
		$this->renderLayout();
	}
	
	public function setdataAction() {
		print "<b>Params to use:</b><br>{{email}}<br>{{queue_id}}<br><br>";
		//print_r($this->getRequest()->getParams());
		//print Mage::helper('core')->getHash("newsletter_queue"."sending"."761hgeUzTyBWARvT");die;
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//print "select subscriber_id from newsletter_subscriber where subscriber_email='vishal.v@hcl.com'";
		$res = $read->fetchRow("select subscriber_id from newsletter_subscriber where subscriber_email='".$this->getRequest()->getParam('email')."'");
		print "<br>subs_id = ".$subs_id = $res['subscriber_id'];
		
		//$qry = "select name FROM user_data WHERE namer LIKE '%a%' ";//query
		//$res = $read->fetchAll($qry); //get array
		
		$sql  = "update newsletter_queue set queue_status='0', queue_finish_at=NULL where queue_id='".$this->getRequest()->getParam('queue_id')."'";
		$write->query($sql);
		
		$sql2  = "update newsletter_queue_link set letter_sent_at=NULL where queue_id='".$this->getRequest()->getParam('queue_id')."' and subscriber_id='".$subs_id."'";
		$write->query($sql2);
	}
	
	public function showdataAction() {
		$this->loadLayout();
        
		$this->renderLayout();
	}
}
?>