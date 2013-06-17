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

$category_id = (isset($_GET['category_id'])) ? secure::getFilterInputBeforeInsertIntoDatabase($_GET['category_id'], true) : "";
try{
	if(secure::checkArrayExist($_POST)){
		 
		if(isset($_POST['select_image']) && !empty($_POST['select_image'])){
			$tmp =array();
			$tmp =explode("#", $_POST['select_image']);
			$_SESSION['calender'][$_POST['month']]['category_photo_id'] = (isset($tmp[0])) ? trim($tmp[0]) : "";
			$_SESSION['calender'][$_POST['month']]['category_photo_name'] = (isset($tmp[1])) ? trim($tmp[1]) : "";
		}else{ //
			throw new exception("Please Select a Image")	;
		}
		
		#Put Everything in Session 
		$_SESSION['calender'][$_POST['month']]['category_id'] = (isset($_POST['category_id'])) ? trim($_POST['category_id']) : "";
		#for image
		
		
		
		
		$_SESSION['calender'][$_POST['month']]['year'] = (isset($_POST['year'])) ? trim($_POST['year']) : "";
		$_SESSION['calender'][$_POST['month']]['month'] = (isset($_POST['month'])) ? trim($_POST['month']) : "";
		header("Location:Create.php?year=$_POST[year]");  
	}
}catch(Exception $e){	
		 $error =$e->getMessage();	 
}

try{ 
	#fetch AlL category
	$objCategory=new category($db);
	$objCategory->_initCategory();
	$objCategory->setLinkTable("category");
	$objCategory->setOrderBy("category_id");
	$objCategory->setSortBy("DESC");	
	$categoryData=array();
	$objCategory->getCategoryDetail();
	$allCategoryData=$objCategory->getCategoryData();	
	$smarty->assign("allCategoryData",$allCategoryData);
	
	if(!empty($category_id)){
		#Fetch category Info
		$objCategory=new category($db);
		$objCategory->_initCategory();
		$objCategory->setLinkTable("category");
		$objCategory->setOrderBy("category_id");
		$objCategory->setSortBy("DESC");
		$objCategory->setLimit("1");
		$categoryData=array();
		$objCategory->category_id = $category_id;	
		$objCategory->getCategoryDetail();
		$categoryData=$objCategory->getCategoryData();
		if($categoryData->RecordCount()){
			 $smarty->assign("countCategoryData", $categoryData->RecordCount());
			 $categoryData = $categoryData->fields;	 
			 $smarty->assign("categoryData", $categoryData);	
			 
			 #fetch images of category
			 $objCategoryImage=new categoryImage($db);
			 $objCategoryImage->_initCategoryImage();
			 $objCategoryImage->setOrderBy("category_photo_id");
			 $objCategoryImage->setLinkTable("category_photo");
			 $objCategoryImage->category_id = $categoryData['category_id'];
			 $objCategoryImage->setSortBy("DESC");
 			 $objCategoryImage->getCategoryImageDetail();
			 $categoryImageData =array();
			 $categoryImageData=$objCategoryImage->getCategoryImageData();	
			 $smarty->assign("categoryImageData",$categoryImageData);
			 $smarty->assign("countCategoryImageData",$categoryImageData->RecordCount());
			 		 
		}else{ //if($categoryData->RecordCount())
			
		} //else of if($categoryData->RecordCount()) 		 
	}else{ //if(!empty($category_id))
				
	} //else of if(!empty($category_id))
}catch(Exception $e){	
		 $error =$e->getMessage();	 
}



$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('select.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);

#Display Main Form
$smarty->display('main.tpl');
?>