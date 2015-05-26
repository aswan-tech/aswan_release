<?php
class FCM_Nosql_Helper_Product extends Mage_Core_Helper_Abstract {
    public function getProductAllSizes( $productIdArray = array() ) {
        $productIds = implode( "','", $productIdArray );
        $sql = "SELECT cs.product_id, ps.parent_id FROM cataloginventory_stock_status cs, catalog_product_super_link ps
                WHERE cs.product_id = ps.product_id AND ps.parent_id IN ('" . $productIds . "')
                AND cs.website_id = " . Mage::app()->getStore()->getWebsiteId() . " AND  cs.qty > 0 AND cs.stock_status = 1";
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sizeArray = $read->fetchAll( $sql );
        $simpleProducts = $parent = array();
        foreach($sizeArray as $v) {
            $simpleProducts[] = $v['product_id'];
            $parent[$v['product_id']] = $v['parent_id'];
        }
        $children = implode("', '", $simpleProducts);
        $sql = "SELECT cpei.entity_id, eaov.value FROM catalog_product_entity_int cpei, eav_attribute_option_value eaov, eav_attribute_option eao WHERE cpei.entity_id IN ('" . $children . "') AND cpei.value = eaov.option_id AND cpei.value = eao.option_id AND cpei.attribute_id = 175 ORDER BY eao.sort_order";
        $options = $read->fetchAll( $sql );
        foreach($options as $option) {
            $sizes[$option['entity_id']]= $option['value'];
        }
        foreach( $sizes as $key => $val ) {
            $return['sizes'][$parent[$key]][] = '<a href="javascript:void(0)">'.$val.'</a>';
        }
        return count($return) > 0 ? $return : false;
    }
	
	 public function getshippingInfo($OrderId) {
        $tableName1 = 'sales_flat_shipment_track';
        $tableName2 = 'sales_flat_shipment_grid';   
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "select * from  {$tableName1} st , {$tableName2} gt where st.order_id='".$OrderId."' and st.order_id=gt.order_id";
        $ShipInfo = $read->query($sql);
        $ShipRes = $ShipInfo->fetch();
        return $ShipRes;
    }
	
    public function cod_text(){
        $length = 6;
        $options = "9865014237";
        $option_length = (strlen($options)-1);
        $captcha = "";
        $i = 0;
        while ($i < $length){
            $random = mt_rand(0, $option_length);
            $captcha .= $options[$random];
            $i++;
        }
        return $captcha;
    }
	
	public function cod_varify($oID,$codvarcode,$incrementId){
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$value=$write->query("Select count(*) as count from sales_flat_order where entity_id='".$oID."' and codvarcode='".$codvarcode."' ");				
		$row = $value->fetch();
		$today = date("Y-m-d H:i:s");
		$comment = 'By OTP:'.$codvarcode;
		if($row['count']>0){
			$sql = "update sales_flat_order set status = 'COD_Verification_Successful' where entity_id='".$oID."' and codvarcode='".$codvarcode."' ";
			$write->query($sql);			
			$sqlgrid = "update sales_flat_order_grid set status = 'COD_Verification_Successful' where entity_id='".$oID."' and increment_id='".$incrementId."' ";
			$write->query($sqlgrid);
			$sqlstatus = "insert into sales_flat_order_status_history (`parent_id`,`is_customer_notified`,`is_visible_on_front`,`comment`,`status`,`created_at`,`entity_name`) values ('".$oID."','1','0','".$comment."','COD_Verification_Successful','".$today."','order')";
			$write->query($sqlstatus);
                        $order = Mage::getModel('sales/order')->load($oID);
                        $order->sendNewOrderEmail();
			echo $this->__('COD verification successfull.');
		}else{
			echo $this->__('Please insert correct code.');
		}
	}
	
	public function new_Otp($incrementId,$mobile){
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $re_otp_array = Mage::getSingleton('core/session')->getReotp();
            Mage::getSingleton('core/session')->unsetReotp($re_otp_array);
            $re_otp_count = 0;
            if (isset($re_otp_array) && is_array($re_otp_array) && isset($re_otp_array[$incrementId])) {
                $re_otp_count = (int)$re_otp_array[$incrementId];
                $re_otp_array[$incrementId] = ++$re_otp_count;
            } else {
                $re_otp_array = array($incrementId => ++$re_otp_count);
            }
            if ($re_otp_count >= 3) {
                echo $this->__('You have reached your maximum re-generation limit, Please contact our customer support.');
            } else {
                Mage::getSingleton('core/session')->setReotp($re_otp_array);
                $template = 'OTP_COD';
                $data['send_to'] = $mobile;
                $data['name'] = 'User';
                $data['orderid'] = $incrementId;
                $codCode = Mage::helper('nosql/product')->cod_text();
                $data['codvarcode'] = $codCode;
                $write->query("UPDATE sales_flat_order SET codvarcode='".$codCode."' WHERE increment_id='".$incrementId."';");
                //$orderModel = Mage::getModel('sales/order')->load($incrementId, 'increment_id')->setCodvarcode($codCode)->save();
                //$data['codvarcode'] = $row['codvarcode'];
                $data['template'] = $template;
                $helper = Mage::helper('nosql/joker');
                $helper->sendNow($data, 'sms', $template);
                echo $this->__('New OTP code sent to your registered mobile number.');
            }
	}
}
