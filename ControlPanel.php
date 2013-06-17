<? 
/*
		File-id@ index.php
        contains the index or main page
        
*/

$admin=false;//only true when inside the admin folder
include('setting.php'); //include the basic setttings
include(BASE_PATH.'/classes/class.loader.php'); 
$error=""; //it is the error variable
$userData =array();
try{	
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
	
	#fetch User Info
	$objUser =new user($db);					
	$objUser->_initUser();
	$objUser->setLinkTable("user");
	$objUser->user_id = $user_id;
	$objUser->getUserDetail();
	$userData = $objUser->getUserData();
	if($userData->RecordCount()){
		$userData = $userData->fields;		
		#take if user has US country
		if($userData['user_country_id']=="US"){		
			#fetch the use State Name
			$objCommon = new common($db);
			$state_data =array();
			$state_data = $objCommon->getAllStates("state_id=".$userData['user_state']);
			$userData['state_name'] = $state_data->fields['state_name'];
		}else{ //if($userData['user_country_id']=="US")
			$userData['state_name'] = $userData['user_other_state'];
		} //else of if($userData['user_country_id']=="US")		 
	}else{	//if($chkUserArray->RecordCount())
		?>
        	<script type="text/javascript">
            	alert("Invalid Data");
				window.location.href ='<?=SITE_URL?>index.php';
            </script>
        <?php	
	}//else of if($chkUserArray->RecordCount())	


	#fetch random text For Page	
	include(BASE_PATH.'/randomText.php');
	
}catch(Exception $e){	
	$error =$e->getMessage();	 
}


///get the user save calender data 11-1-2012
$objUserOrderCalender1 = new order($db);		
$objUserOrderCalender1->_initOrder();
$objUserOrderCalender1->setLinkTable("order");
//$objUserOrderCalender->setLimit("12");
$savecalinfor1=array();
$objUserOrderCalender1->user_id =$user_id;
$objUserOrderCalender1->getOrderDetail();
$savecalinfor1 = $objUserOrderCalender1->getOrderData();	
//echo $savecalinfor->RecordCount();die;
$smarty->assign("totorder",$savecalinfor1->RecordCount());
///////////////////11-1-2012

$smarty->assign("userData", $userData);

#fetch sidebar
$sideBar ="";			//variable which will use to hold the data displayed in content 
$sideBar =$smarty->fetch('sideBar.tpl');				//fetch the content template 
$smarty->assign("sideBar", $sideBar);				//assign content variable in smarty

$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('controlPanel.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);
$smarty->assign("year", date("Y"));
#Display Main Form
$smarty->display('main.tpl');
?>