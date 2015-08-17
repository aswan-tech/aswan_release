<?php
include_once('config.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);
$error_mgs = '';
if(isset($_POST['btnChangePass'])) {
	#print_r($_POST);
	if(!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
		$from_date_arr = explode('-', $_POST['from_date']);
		$to_date_arr = explode('-', $_POST['to_date']);
		
		$fdate = checkdate (  $from_date_arr[1] ,  $from_date_arr[2] ,  $from_date_arr[0] );
		$tdate = checkdate (  $to_date_arr[1] ,  $to_date_arr[2] ,  $to_date_arr[0] );
		
		if($fdate == true && $tdate == true) {
			$headers = "'OrderID','CustomerBalanceAmount','OrderStatus',' DC_Status','couponCode','CouponRuleName','CustomerID','GrandTotal','CustomerEmail','GiftCardsAmount','Source','Campaign','itemName','SKU','MRP','OriginalPrice','TaxPercent','CouponDiscountAmount','CouponDiscountPercent', 'AmountToCustomer','CatalogDiscountAmount','CatalogDiscountPercentage','orderDate','QTY','ItemID','Courier','AWB','ShipmentDate','PaymentMethod'";
			$csv_file_name = $_media_path."orders-".$_POST['from_date']."-to-".$_POST['to_date']."-".time().".csv";
			try{
				$query = "
					SELECT ".$headers."
						UNION ALL
					SELECT od.increment_id,od.customer_balance_amount,od.status, od.sent_to_erp,
						IFNULL(od.coupon_code, '-') as coupon_code,
						IFNULL(od.coupon_rule_name, '-') as couponrule_name,
						od.customer_id,
						od.grand_total,
						od.customer_email,
						od.gift_cards_amount,
						IFNULL(od.source, '-') as source,
						IFNULL(od.campaign, '-') as campaign, 
						items.name,items.sku, 
						IFNULL(items.product_mrp,0) as mrp,
						IFNULL(items.original_price, 0) as originalprice, 
						items.tax_percent,
						items.discount_amount,
						items.discount_percent, 
						round((items.row_total + items.tax_amount + IFNULL(items.hidden_tax_amount, 0) + items.weee_tax_applied_row_amount)-items.discount_amount) AS `amount_to_customer`, 
						IF(IFNULL(items.product_mrp, 0) > IFNULL(items.original_price, 0) , ( IFNULL(items.product_mrp, 0) - IFNULL(items.original_price, 0) ), 0 ) AS catalog_discount_amount,
						IF(IFNULL(items.product_mrp, 0) > IFNULL(items.original_price,0) , ((( IFNULL(items.product_mrp, 0) - IFNULL(items.original_price,0) ) / IFNULL(items.product_mrp,0) ) * 100 ) , 0) AS catalog_discount_percentage,
						items.created_at,
						items.qty_ordered,
						items.item_id,
						IFNULL(shipment_track.title, '-') as title,
						IFNULL(shipment_track.track_number, '-') as AWB, 
						IFNULL(shipment_track.created_at, '-') as shipmentDate, 
						payment.method 
					FROM sales_flat_order AS od 
						RIGHT JOIN `sales_flat_order_item` AS `items` ON od.entity_id = items.order_id 
						LEFT JOIN .`sales_flat_shipment_track` AS `shipment_track` ON shipment_track.order_id = od.entity_id 
						LEFT JOIN .`sales_flat_order_payment` AS `payment` ON payment.parent_id = od.entity_id 
						LEFT JOIN .`catalog_category_product` AS `category_product` ON category_product.product_id = items.product_id 
					WHERE (product_type = 'configurable')
					AND ( od.created_at between '".$_POST['from_date']." 00:00:00' and '".$_POST['to_date']." 23:59:59')
					GROUP BY `items`.`item_id`";
					$query .= "INTO OUTFILE '".$csv_file_name."' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\n'";
					//$query .= "INTO OUTFILE '/tmp/orders".mt_rand(1,10).".csv' FIELDS TERMINATED BY ','";
					#echo $query;
					$result = $core_read->query($query);
					//var_dump($result);
					
					$zip = new ZipArchive();
					
					if ($zip->open($csv_file_name.'.zip',  ZipArchive::OVERWRITE)) {
						$zip->addFile($csv_file_name);
						$zip->close();
						$_SESSION['succMsg'] = 'Report has been exported successfully.';
						header("location:index.php");
						exit();
	
					} else {
						$error_mgs = 'Failed to archive';
					}
			}
			catch(Exception $e) {
				$error_mgs = 'Failed to download csv';
			}			
		}
		else{
			$error_mgs = 'Invalide date format.';
		}
	}
	else {
		$error_mgs = 'Invalide date format.';
	}
}

$filesArr = scandir($_media_path, 1);

$error_mgs = '';
include_once('includes/inner_header.php');
?>
<div id="main-content">          
<div class="login-content">
	<div class="login-form-change-pass">
		<p style="text-align:center;color:red;"><?php echo $error_mgs; ?><?php echo (isset($_SESSION['succMsg']) ? $_SESSION['succMsg'] : ''); unset($_SESSION['succMsg']); ?></p>
		<form name="changepassform" method="POST" action="order_detail_report.php" id="changepass">
			<ul>
				<li><label>From Date:</label><input size="10" type="text" id="from_date" name="from_date" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : '' ?>" id="new_pass" class=""></li>
				<li><label>To Date:</label><input size="10" type="text" id="to_date" name="to_date" id="confirm_pass" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : '' ?>" class=""></li>
				<li><label>&nbsp;</label><input type="submit" name="btnChangePass" value="DOWNLOAD IN CSV" class=""></li>
			</ul>
		</form>
		<h2>List of downloaded reports</h2>
		<?php 
			foreach($filesArr as $file) {
				if($file !='.' && $file !='..') {
		?>
		<div><?php echo $file ?> <h4><a href="<?php echo $_media_url.$_media_path.$file ?>">Download</a></h4></div>
		<?php 
				}
			} 
		?>
	</div>
</div> 
</div>
  <script>
  jQuery(function() {
    jQuery( "#from_date" ).datepicker({
  dateFormat: "yy-mm-dd"
});
    jQuery( "#to_date" ).datepicker({
  dateFormat: "yy-mm-dd"
});
  });
  </script>
<?php include_once('includes/inner_footer.php');?>
