<? 
/*
		File-id@ index.php
        contains the index or main page
        
*/

$admin=false;//only true when inside the admin folder
include('setting.php'); //include the basic setttings
include(BASE_PATH.'/classes/class.loader.php'); 
$error=""; //it is the error variable

#check session
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))
{
	$user_id = 	$_SESSION['user_id'];			
}
else
{ //if(!isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))	
?>
	<script type="text/javascript">
		alert("Invalid Data");
		window.location.href ='<?=SITE_URL?>index.php';
	</script>
<?php
} //else of if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))		 

if(isset($_GET['year']) && !empty($_GET['year']))
{
	//echo "<pre>";print_r($_SESSION);
}else{ //if(isset($_GET['year']) && !empty($_GET['year']))
	unset($_SESSION['calender']);
} //else of if(isset($_GET['year']) && !empty($_GET['year']))
if(isset($_POST['Submit'])){
try
{
	
	
	////////////////////////////////////////////////////// 11-1-2012
	if($_POST['Submit']=='Save Photo Selection' )
	{
		if(isset($_SESSION['calender']) && count($_SESSION['calender']))
		{			 
			#Set the order
			$order_year = (isset($_POST['order_year'])) ? trim($_POST['order_year']) : "";
			#ad calender
			/*$objOrder = new userorder($db);
			$objOrder->user_id = $user_id;
			$objOrder->order_status = "active";
			$objOrder->order_added_date = date("Y-m-d H:i:s");;
			$objOrder->order_year = $order_year;
			$objOrder->addOrder();
			$order_id = $objOrder->order_id;
			
			if(empty($order_id))
			{
				throw new exception("Unable to generate order");
			}			*/
	
			$objUserOrderCalender = new userorder($db);		
			$objUserOrderCalender->_initUserOrder();
			$objUserOrderCalender->user_id =$user_id;
			$objUserOrderCalender->deleteuserOrder();
						
			foreach($_SESSION['calender'] as $key=>$value)
			{
				$objUserOrderCalender->_initUserOrder();
				$objUserOrderCalender->user_id =$user_id;
				$objCommon = new common($db);
				$objUserOrderCalender->month = $objCommon->getMonthName($value['month']);
				$objUserOrderCalender->category_id = $value['category_id'];
				$objUserOrderCalender->category_photo_id = $value['category_photo_id'];	
				$objUserOrderCalender->order_year = $_POST['order_year'];								 			 
				$objUserOrderCalender->adduserOrder();			
			} //foreach($_SESSION['calender'] as $key=>$value)
			header("Location:Thanks.php");
		}
		else
		{ //if(isset($_SESSION['calender']) && count($_SESSION['calender'])=="12")
			throw new exception("Please select all Month image");	
		} //else of i
	}
	else
	{
	//////////////////////////////////////////////////////11-1-2012
		if(secure::checkArrayExist($_POST))
		{		 
			if(isset($_SESSION['calender']) && count($_SESSION['calender']))
			{			 
				#Set the order
				$order_year = (isset($_POST['order_year'])) ? trim($_POST['order_year']) : "";
				#ad calender
				$objOrder = new order($db);
				$objOrder->user_id = $user_id;
				$objOrder->order_status = "active";
				$objOrder->order_added_date = date("Y-m-d H:i:s");;
				$objOrder->order_year = $order_year;
				$objOrder->addOrder();
				$order_id = $objOrder->order_id;
			
				if(empty($order_id))
				{
					throw new exception("Unable to generate order");
				}			
		
				$objUserOrderCalender = new userorder($db);		
				$objUserOrderCalender->_initUserOrder();
				$objUserOrderCalender->user_id =$user_id;
				$objUserOrderCalender->deleteuserOrder();
			
				$objOrderCalender = new orderCalender($db);	
				
				foreach($_SESSION['calender'] as $key=>$value)
				{
					$objOrderCalender->_initOrderCalender();
					$objOrderCalender->order_id =$order_id;
					$objCommon = new common($db);
					$objOrderCalender->month = $objCommon->getMonthName($value['month']);
					$objOrderCalender->category_id = $value['category_id'];
					$objOrderCalender->category_photo_id = $value['category_photo_id'];				 			 
					$objOrderCalender->addOrderCalender();		
					
					//enter save table info 12-1-2012
					$objUserOrderCalender->_initUserOrder();
					$objUserOrderCalender->user_id =$user_id;
					$objCommon = new common($db);
					$objUserOrderCalender->month = $objCommon->getMonthName($value['month']);
					$objUserOrderCalender->category_id = $value['category_id'];
					$objUserOrderCalender->category_photo_id = $value['category_photo_id'];	
					$objUserOrderCalender->order_year = $_POST['order_year'];								 			 
					$objUserOrderCalender->adduserOrder();			
					//12-1-2012	
				} //foreach($_SESSION['calender'] as $key=>$value)

				header("Location:Thanks.php");
			}
			else
			{ //if(isset($_SESSION['calender']) && count($_SESSION['calender'])=="12")
				throw new exception("Please select all Month image");	
			} //else of if(isset($_SESSION['calender']) && count($_SESSION['calender'])=="12")	
		}//if(secure::checkArrayExist($_POST)) 	 
	}	 
}
catch(Exception $e)
{
	 $error =$e->getMessage();	 
}

try{	
	#fetch random text For Page	
	include(BASE_PATH.'/randomText.php');
	
}catch(Exception $e){	
	$error =$e->getMessage();	 
}}

#fe
///get the user save calender data 11-1-2012
$objUserOrderCalender = new userorder($db);		
$objUserOrderCalender->_initUserOrder();
$objUserOrderCalender->setLinkTable("user_order");
//$objUserOrderCalender->setLimit("12");
$savecalinfor=array();
$objUserOrderCalender->user_id =$user_id;
$objUserOrderCalender->getuserOrderDetail();
$savecalinfor = $objUserOrderCalender->getuseOrderData();	
$smarty->assign("totorder",$savecalinfor->RecordCount());
//echo $savecalinfor->RecordCount();die;



if($savecalinfor->RecordCount() > 0)
{
	foreach($savecalinfor as $sinf)
	{
	
		//print_r($sinf['month']);
		//print_r($sinf['category_id']);
		//print_r($sinf['category_photo_id']);
		$chkdata = array();
		$syear=2012;
		if($sinf['month']=='12')
		{
			if($sinf['order_year']==3)
			{
				$syear=2013;
			}	
			elseif($sinf['order_year']==4)
			{
				$syear=2014;
			}
			elseif($sinf['order_year']==5)
			{
				$syear=2015;
			}
			
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
	
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'December' );
			if(!isset($_SESSION['calender']['December']))
			{
				$_SESSION['calender']['December']=$chkdata;
			}
		}
		elseif($sinf['month']=='11')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'November' );
			if(!isset($_SESSION['calender']['November']))
			{
				$_SESSION['calender']['November']=$chkdata;
			}
		}
		elseif($sinf['month']=='10')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'October' );
			if(!isset($_SESSION['calender']['October']))
			{
				$_SESSION['calender']['October']=$chkdata;
			}	
		}
		elseif($sinf['month']=='9')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'September' );
			if(!isset($_SESSION['calender']['September']))
			{
				$_SESSION['calender']['September']=$chkdata;
			}	
		}
		elseif($sinf['month']=='8')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'August' );
			if(!isset($_SESSION['calender']['August']))
			{
				$_SESSION['calender']['August']=$chkdata;
			}
		}
		elseif($sinf['month']=='7')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'July' );
			if(!isset($_SESSION['calender']['July']))
			{
				$_SESSION['calender']['July']=$chkdata;
			}
		}
		elseif($sinf['month']=='6')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'June' );
			if(!isset($_SESSION['calender']['June']))
			{
				$_SESSION['calender']['June']=$chkdata;
			}
		}
		elseif($sinf['month']=='5')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'May' );
			if(!isset($_SESSION['calender']['May']))
			{
				$_SESSION['calender']['May']=$chkdata;
			}
		}
		elseif($sinf['month']=='4')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'April' );
			if(!isset($_SESSION['calender']['April']))
			{
				$_SESSION['calender']['April']=$chkdata;
			}
		}
		elseif($sinf['month']=='3')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'March' );
			if(!isset($_SESSION['calender']['March']))
			{
				$_SESSION['calender']['March']=$chkdata;
			}	
		}
		elseif($sinf['month']=='2')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'February' );
			if(!isset($_SESSION['calender']['February']))
			{
				$_SESSION['calender']['February']=$chkdata;
			}	
		}
		elseif($sinf['month']=='1')
		{
			$objUserOrderCalenderimage = new categoryImage($db);		
			$objUserOrderCalenderimage->_initCategoryImage();
			$objUserOrderCalenderimage->setLinkTable("category_photo");
			$objUserOrderCalenderimage->category_photo_id =$sinf['category_photo_id'];
			$objUserOrderCalenderimage->category_id = $sinf['category_id'];
			$objUserOrderCalenderimage->getCategoryImageDetail();
			$ordimagename='';
			foreach($objUserOrderCalenderimage->getCategoryImageData() as $ordimage)
			{
				$ordimagename = $ordimage['file_name'];
			}
			$chkdata = array('category_photo_id'=>$sinf['category_photo_id'],'category_photo_name'=>$ordimagename,'category_id'=>$sinf['category_id'],'year'=>$syear,'month'=>'January' );
			if(!isset($_SESSION['calender']['January']))
			{	
				$_SESSION['calender']['January']=$chkdata;
			}	
			//$_SESSION['calender']['month']=
		}
	}		
}

//$smarty->assign("savecalinfor", $savecalinfor);

$objOrder1 = new order($db);
$objOrder1->_initOrder();
$objOrder1->setLinkTable("order");
$objOrder1->setLimit("1");
$objOrder1->user_id = $user_id;
$objOrder1->getOrderDetail();
$myobjOrder1= $objOrder1->getOrderData();
//echo $myobjOrder1->RecordCount();die;
$smarty->assign("mytotorder",$myobjOrder1->RecordCount());
//echo "<pre>";print_r($savecalinfor);			die;
///// 11-1-2012
$content ="";			//variable which will use to hold the data displayed in content 
$content =$smarty->fetch('create.tpl');				//fetch the content template 
$smarty->assign("content", $content);				//assign content variable in smarty
$imgcnt=0;

$smarty->assign("imgcnt", $imgcnt);	
$smarty->assign("error", $error);
#Display Main Form
$smarty->display('main.tpl');
?>
