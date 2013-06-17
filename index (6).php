<? 
/*
		File-id@ index.php
        contains the index or main page
        
*/
include('setting.php'); //include the basic setttings

$admin=false;//only true when inside the admin folder
include(BASE_PATH.'/classes/class.loader.php'); 
$error=""; //it is the error variable

try{	
	#fetch random text For Page	
	include(BASE_PATH.'/randomText.php');
	
}catch(Exception $e){	
	$error =$e->getMessage();	 
}

$inputElement =array(); 
		
try{	
	#if user submit the form
	if(secure::checkArrayExist($_POST)){
		if(empty($_POST['user_email']) || empty($_POST['user_password'])){
			throw new exception("Invalid Email or Password");	
		} //if(empty($_POST['user_email']) || empty($_POST['user_password']))
			 	
		#filter the user_full_name				
		$objUser =new user($db);					
		$objUser->_initUser();
		$inputElement =$objUser->filterUserLoginInput($_POST); #dilter the input			
		$objUser->setLinkTable("user");
		$objUser->user_email = $inputElement['user_email'];
		$objUser->user_password = $inputElement['user_password'];				 
		$objUser->getUserDetail();
		$chkUserArray =array();
		$chkUserArray = $objUser->getUserData();				
		if($chkUserArray->RecordCount()){
					############################# If User Found, Check its Active or Not								
					$chkUserArray = $chkUserArray->fields;							
					if(trim($chkUserArray['user_status'])=="active"){ 
						#set user session
						foreach($chkUserArray as $key=>$value){
							$_SESSION[$key]	= $value;						
						}					
						header("Location:".SITE_URL."ControlPanel.php");					
					}else{
						#set session of user
						throw new exception("Your Account is Disabled.Please use Contact Form for more details.");
						$inputElement =$_POST; 			
					}						
				}else{ //if($chkUserArray->RecordCount())	
					throw new exception("Invalid Email or Password.");
					$inputElement =$_POST;
				} //else of if($chkUserArray->RecordCount())						
			
	} //if(secure::checkArrayExist($_POST))
	
	#fetch random text For Page	
	include(BASE_PATH.'/randomText.php');
	
}catch(Exception $e){	
	$error =$e->getMessage();	 
}




$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('index.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);
#Display Main Form
$smarty->display('main.tpl');
?>