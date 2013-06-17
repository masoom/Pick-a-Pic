<?php


#set admin true flag variable
$admin =false; 	 

#include the setting file
require_once 'setting.php';
require_once BASE_PATH.'/classes/class.loader.php';

 
$error =""; 		//error variable
$mode='AddNewRecord';	//Set the Mode
$orderData =array();

$resizeImageThub = BASE_PATH.'/files/store_image/thumb/';
$resizeImageFull = BASE_PATH.'/files/store_image/';

#creates object of User
$objOrder =new order($db);

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

#get the class
$order_id =(isset($_GET['id'])) ? base64_decode(trim($_GET['id'])) : NULL;

if($order_id!=NULL){
	#user has open the edit page
	try{
		$mode='EditNewRecord';
		
		#get the order  info		 				
		$objOrder->_initOrder();			
		$objOrder->setLinkTable("order");
		$objOrder->order_id = $order_id;
		$objOrder->getOrderDetail();
		$orderData =array();
		$orderData = $objOrder->getOrderData();				
		if($orderData->RecordCount()){		 
			$orderData =$orderData->fields;	
			
			#fetch Order Images
			$objOrderCalender = new orderCalender($db);	
			$objOrderCalender->_initOrderCalender();			
			$objOrderCalender->setLinkTable("order_calender");
			$objOrderCalender->setOrderCalenderBy("category_photo_id");	//Set the order by variable
			$objOrderCalender->setSortBy("DESC");	//Set the sort by variable
			$objOrderCalender->order_id = $orderData['order_id'];
			$objOrderCalender->getOrderCalenderDetail();
			$orderCalenderData =array();
			$orderCalenderData = $objOrderCalender->getOrderCalenderData();
			$smarty->assign('orderCalenderData',$orderCalenderData); 
			$smarty->assign('countOrderCalenderData',$orderCalenderData->RecordCount()); 	
			
			 
					
		} //if($chkUserArray->RecordCount())		
	}catch(Exception $e){
		$error =$e->getMessage();		 
	}			
			
}else{ //if($order_id!=NULL)
	#check the mode
	try{
		#check if the user has submit the page or not
		if(secure::checkArrayExist($_POST)){ 
		
		} //if(secure::checkArrayExist($_POST))	
		
	}catch(Exception $e){
		$error =$e->getMessage();		 
	}				
} //else of if($order_id!=NULL)



$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('manageOrder.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);

#Display Main Form
$smarty->display('main.tpl');
?>

 