<? 
/*
		File-id@ index.php
        contains the index or main page
       
*/

$admin=false;//only true when inside the admin folder
include('setting.php'); //include the basic setttings
include(BASE_PATH.'/classes/class.loader.php'); 
$error=""; //it is the error variable

$category_id = (isset($_GET['category_id'])) ? secure::getFilterInputBeforeInsertIntoDatabase($_GET['category_id'], true) : "";

try{
	
	#Fetch category Info
	$objCategory=new category($db);
	$objCategory->_initCategory();
	$objCategory->setLinkTable("category");
	$objCategory->setOrderBy("category_id");
	$objCategory->setSortBy("DESC");
	$objCategory->setLimit("1");
	$categoryData=array();
	if(!empty($category_id)){		
		$objCategory->category_id = $category_id;	
	}	//if(!empty($category_id))
	$objCategory->getCategoryDetail();
	$categoryData=$objCategory->getCategoryData();	
	$smarty->assign("categoryData",$categoryData);
	$smarty->assign("countCategoryData",$categoryData->RecordCount());
	
	if($categoryData->RecordCount()){	
			$categoryData = $categoryData->fields;
			
			 
		
		#Fetch Category Images	
		$objCategoryImage=new categoryImage($db);
		$objCategoryImage->_initCategoryImage();
		$objCategoryImage->setOrderBy("category_photo_id");
		$objCategoryImage->category_id = $categoryData['category_id'];
		$objCategoryImage->setSortBy("DESC");
		$objCategoryImage->setLinkTable("category_photo");	
		$objCategoryImage->getCategoryImageDetail();
		$categoryImageData =array();
		$categoryImageData=$objCategoryImage->getCategoryImageData();	
		$smarty->assign("categoryImageData",$categoryImageData);
		$smarty->assign("countCategoryImageData",$categoryImageData->RecordCount());
	}	
	
	
	#fetch AlL category
	$objCategory=new category($db);
	$objCategory->_initCategory();
	$objCategory->setLinkTable("category");
	$objCategory->setOrderBy("category_id");
	$objCategory->setSortBy("DESC");	
	$categoryData=array();
	$objCategory->getCategoryDetail();
	$allCategoryData=$objCategory->getCategoryData();	//print_r($allCategoryData);
	$smarty->assign("allCategoryData",$allCategoryData);
	$smarty->assign("countAllCategoryData",$allCategoryData->RecordCount());
	
	
	#fetch random text For Page	
	include(BASE_PATH.'/randomText.php');
	
}catch(Exception $e){	
		 $error =$e->getMessage();	 
}


$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('category.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$smarty->assign("error", $error);

#Display Main Form
$smarty->display('main.tpl');
?>