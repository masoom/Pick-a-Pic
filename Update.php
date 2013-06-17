<? 
/*
		File-id@ Update.php
        contains the index or main page
        
*/

$admin=false;//only true when inside the admin folder
include('setting.php'); //include the basic setttings
include(BASE_PATH.'/classes/class.loader.php'); 
$error=""; //it is the error variable
$userData =array();
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

if(secure::checkArrayExist($_POST)){
	try{
			 
			####################################################
							#Add the User
			####################################################

			$objUser = new user($db);
			$userData=$objUser->filterUserRegistrationInput($_POST);#filter the input
			$originalEmail = (isset($_POST['original_email'])) ? secure::getFilterInputBeforeInsertIntoDatabase($_POST['original_email'], true) : "";
			
			
			
						
			/*  First Check if Email is already Registered or Not */
			if($originalEmail!=$userData['user_email'])
			{
				$objUser->_initUser();#initialise varibles
				$objUser->setLinkTable("user");
				$objUser->user_email = $userData['user_email'];
				$objUser->getUserDetail();
				$chkUserArray =array();
				$chkUserArray = $objUser->getUserData();
				 
				if($chkUserArray->RecordCount()){
					throw new exception("This Email is already registered. Please try again.");
					$userData =$_POST;
				} //if($chkUserArray->RecordCount())
			} //if($originalEmail!=$inputElement['user_email'])
			#check uniqueness of email


			$objUser->_initUser();#initialise varibles

			if($userData['user_country_id']=="US"){
				$userData['user_other_state'] ="";
			}else{
				$userData['user_state'] ="";
			}

			foreach($userData as $row=>$value){
				$objUser->$row = $value;
			} //foreach($userData as $row=>$value)

			#get the country full name
			 		 
			$objUser->editUser();
			
			 
			//header("Location:".SITE_URL."Profile.php");

	}catch(Exception $e){
		$error =$e->getMessage(); 

	}

} //if(secure::checkArrayExist($_POST))
	

try{	
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


	#create the object of common class
	$obj_common ="";
	$obj_common =new common($db);
	#fetch all the country
	$countries =array();
	$countries =$obj_common->getAllCountry();
	$smarty->assign("countries", $countries);


	#fetchall the us states
	$states =array();
	$states =$obj_common->getAllStates("country_id='US'");
	$smarty->assign("states", $states);
	
	#fetch random text For Page	
	include(BASE_PATH.'/randomText.php');
	
	
	
}catch(Exception $e){	
	$error =$e->getMessage();	 
}

$smarty->assign("userData", $userData);



$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('update.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);
#Display Main Form
$smarty->display('main.tpl');
?>