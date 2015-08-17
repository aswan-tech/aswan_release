<?php
include_once('config.php');
$error_mgs = '';
$obj = new Review();
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
if(isset($_POST['btnsubmit'])){
	$review = $obj->getReviewById($_POST['review_status'], $id );
	if($review){
		header('location:managereview.php');
		exit;
	}
}
$getData  = $obj->getAllReviewById($id);
$getRecord = mysql_fetch_assoc($getData);
include_once('includes/inner_header.php');
?>
<div id="main-content">          
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table-a2">
	<tr>
		<td width="38%">Name</td>
		<td width="2%">:</td>
		<td width="60%"><?php echo $getRecord['name'];?></td>
	</tr>
	<tr>
		<td>E-mail</td>
		<td>:</td>
		<td><?php echo $getRecord['email'];?></td>
	</tr>
	<tr>
		<td>Review</td>
		<td>:</td>
		<td><?php echo $getRecord['review'];?></td>
	</tr>
	<form name="review" method="POST" action="">
	<input type="hidden" name="review_id" id="review_id" value="<?php echo $getRecord['id'];?>">
	<tr>
		<td>Review Status</td>
		<td></td>
		<td>
		<select name="review_status" id="review_status">
			<option value="">Select Status</option>
			<option value="1">Yes</option>
			<option value="0">No</option>
		</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type="submit" name="btnsubmit" value="submit" onclick="return reviewStatusVal();"></td>
	</tr>
	</form>
</table>
</div>  
<?php include_once('includes/inner_footer.php');?>