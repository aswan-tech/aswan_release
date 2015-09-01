<?php 
include_once('config.php');
include_once('includes/inner_header.php');
$time = date('g:i a'); 
$month = date("j, F, Y");  
?>  
<div id="main-content">
<div class="admin-content">
<div class="row">
&nbsp;
</div>
<div class="row">
	<div class="col-1"><strong style="color:red;">
	<?php if(isset($_SESSION['changeMsg'])){ echo $_SESSION['changeMsg']; unset($_SESSION['changeMsg']);}?></strong>
	</div>
	Welcome to Dashboard
</div> 
</div>  
<div class="row">
&nbsp;
</div>
<div class="row">
&nbsp;
</div>
<div class="row">
&nbsp;
</div>
<div class="row">
&nbsp;
</div>
</div>
<?php include_once('includes/inner_footer.php');?> 
