<? 
/*
		File-id@ index.php
        contains the index or main page
       
*/

$admin=false;//only true when inside the admin folder
include('setting.php'); //include the basic setttings
include(BASE_PATH.'/classes/class.loader.php'); 
$error=""; //it is the error variable

if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
	$user_id = 	$_SESSION['user_id'];			
}else{ //if(!isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))	
	?>
		<script type="text/javascript">
			alert("Invalid Data");
			window.location.href ='<?=SITE_URL?>index.php';
			</script>
	<?php
} //else of if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))		 

 

try{ 

}catch(Exception $e){	
		 $error =$e->getMessage();	 
}



$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('order.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);

#Display Main Form
$smarty->display('main.tpl');
?>