<?php

require_once 'setting.php';
require_once BASE_PATH.'/classes/class.loader.php';

$objDataGrid	=	new dataGrid();
$objOrder	=   new order($db);
$data=array();

if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
	$user_id = 	$_SESSION['user_id'];			
}else{ //if(isset($_SESSION['order_id']) && !empty($_SESSION['user_type_id']) && isset($_SESSION['user_type_id']) && !empty($_SESSION['order_id']))
	header("Location:login.php");
}

$controller= basename($_SERVER['SCRIPT_FILENAME']);//basename($_SERVER['SCRIPT_FILENAME']);
	//parse form data array to get the values of selection criteria

	$endLimit = SHOWRECORDS;
	$showRecords = $endLimit;
	
	// make the search / sort and pagin conditions for the grid
	(empty($_REQUEST['orderBy']))		?	($orderBy = 'order.order_id')	: ($orderBy = $_REQUEST['orderBy']);
	(empty($_REQUEST['sortBy'])) 		?	($sortBy = 'DESC')				: ($sortBy = $_REQUEST['sortBy']);
	(empty($_REQUEST['start']))  		?	($startLimit = 0)				: ($startLimit = $_REQUEST['start']);
	(empty($_REQUEST['filter'])) 		?	($filter = '')   			: ($filter = $_REQUEST['filter']);
	(empty($_REQUEST['filterBy']))		?	($filterBy = '')			: ($filterBy = $_REQUEST['filterBy']);
	(empty($_REQUEST['showAllExist']))	?	($show_all_records =0 )			: ($show_all_records = $_REQUEST['showAllExist']);
	
	/*---------->SEARCH IN ALL FIELDS<--------------#START*/
	$str_needle="|:|";
	$condition='';
	$str_condition='';
	$append='';
	if ($filter!=''){
		if(strpos($filterBy, $str_needle)===false){
			$condition = $filterBy." LIKE '%".$filter."%' ";
		}else{
			$filter_fields=explode($str_needle,$filterBy);
			$str_filter=" LIKE '%".$filter."%' OR ";
			for($i=0;$i<sizeof($filter_fields);$i++){
				$append= ($i>0) ? ' OR ' : '';
				$str_condition .= $append."".$filter_fields[$i]." LIKE '%".$filter."%'";
			}
			//$str_condition = implode($append, $filter_fields);
			if(strlen($str_condition)>0){
				$condition=" ( ".$str_condition." ) ";
			}
		}
	}else{
		$condition = '';	
	}
	
	if(empty($condition)){
		$condition = "user.user_id=".$user_id." ";
	}else{
		$condition = "user.user_id=".$user_id." and ".$condition;
	}
	
	if ($orderBy!=''){
		if(strpos($orderBy, $str_needle)===false){
			$orderByString = $orderBy;
		}else{
			$filter_fields=explode($str_needle,$orderBy);
			$orderByString=implode(',', $filter_fields);
			//$orderBy=$orderByString;
			
		}
	}else{
		$orderByString = $orderBy;	
	}
	//echo 'orderBY='.$orderBy.'  SortBy='.$sortBy .' StartLimit= '.$startLimit.' Filter='.$filter.' FilterBy='.$filterBy.'show ALL:'.$show_all_records.' condition:'.$condition;
	/*---------->SEARCH IN ALL FIELDS<--------------#END*/
			
	/*------>SHOW ALL THE RECORDS AT ONCE OR PAGINATE THE RECORDS<------#START*/
		
	if(isset($show_all_records) && $show_all_records>0){
		$offset='';
		$data =$objOrder->getOrderDataGrid($condition,$orderByString,  $sortBy ,$showRecords, '');
		$totalRecords = $data->RecordCount();
		$showRecords = $totalRecords;
		$paging= '';		
	}else{//echo "Out";
		$offset = $startLimit .','. $endLimit;
		$data = $objOrder->getOrderDataGrid($condition,$orderByString,  $sortBy ,$showRecords, '');
		$totalRecords = $data->RecordCount();
		//$data=array_slice($data,$startLimit, $endLimit);
	}
	/*------>SHOW ALL THE RECORDS AT ONCE OR PAGINATE THE RECORDS<------#END*/
	//echo "Cond ".$condition." Order : ".$orderBy." sort ".  $sortBy." SHOW ".$showRecords." Offset ".$offset ;		
	
	//$this->users = $users->fetchAll($condition," $orderBy  $sortBy ",$showRecords,$offset);
	
		
	$data= $objOrder->getOrderDataGrid($condition,$orderByString,  $sortBy ,$showRecords, $offset);
	
	 
	
	//$data=array_slice($data,$startLimit, $endLimit);
	//echo "<pre>DATA ARRAY INDEX :";print_r($data);die();
	/*-------------------------------------------------------------
		CREATE THE ARRAY FOR TABLE HEADER
		IT WILL REQUIRE VALUES FOR 5 KEYS:
		1. COLUMN ID => THIS IS ARRAY POSITION
		2. COLUMN DB NAME=> THIS IS THE DATABASE FIELD NAME.
		3. COLUMN DISPLAY NAME=> THIS IS THE NAME TO BE DISPLAYED ON THE GRID FOR THE COLUMN
		4. SORT => IF A COLUMNS NEEDS SORTING
		5. SEARCHABLE=> IF WE WANT TO PRVIDE SEARCH ON A COLUMN	
	---------------------------------------------------------------*/
	$array_table_header[0] = array("columnId"=>"0", "columnDBName"=>"`order`.`order_id`", "columnDisplay"=>"Q #.", "sort"=>false, "searchable"=>false);
	$array_table_header[1] = array("columnId"=>"1", "columnDBName"=>"`order`.`order_id`", "columnDisplay"=>"S#", "sort"=>false, "searchable"=>false);
	$array_table_header[2] = array("columnId"=>"2", "columnDBName"=>"`order`.`order_added_date`", "columnDisplay"=>"Order Added Date", "sort"=>true, "searchable"=>true);
	$array_table_header[3] = array("columnId"=>"3", "columnDBName"=>"`order`.`order_year`", "columnDisplay"=>"Calender Year", "sort"=>true, "searchable"=>true);	
	 
	 
	
	$array_table_data='';
	$counter	=	sizeof($data);
	if($data){
		if ($counter){
			$i=0;
			foreach($data as $row){		
					$array_table_data[$i]=array('col_0'=>base64_encode($row['order_id']),
													'col_1'=>$i+1+$startLimit,
													'col_2'=>ucfirst($row['order_added_date']),												 
													'col_3'=>ucfirst($row['order_year']),
													 								 					 
													 									 		
											);
				$i++;
			} //foreach($data as $row)
			
		}else{
		
			$array_table_data=array();
		}
	}else{
		$array_table_data=array();
	}
/*---------------------------------------------------------------------------------------------
CREATE THE CONFIGURATION ARRAY FOR THE GRID
NOTES:
1. STRICTILY PROVIDE VALUES FOR EACH CONFIGURATION KEY IN THE ARRAY.
2. ADD/EDIT/VIEW/DELETE ARRAYS ARE FOR ADD/EDIT/VIEW/DELETE FUNCTIONALITIES.
------------------------------------------------------------------------------------------------*/
$configuration_array=array('start'=>$startLimit, 
							'limit'=>$showRecords, 
							'orderBy'=>$orderBy,
							'sortBy'=>$sortBy,
							'filter'=>$filter,
							'filterBy'=>$filterBy,
							'totalRecords'=>$totalRecords,
							'controller'=>$controller,
							'controller_fn'=>'orderDataGrid',
							'showAllRecordsJs'=>'showAllRecords',
							'paginationGridControl'=>'paginationGridControl',
							'showIdOnFront'=>false,
							'show_pagination'=>true,
							'show_all_records'=>$show_all_records,
							'no_records_found_message'=>"No data found",
							'search'=>array('show'=>true,
										'botton_label'=>'Search',
										'click_function'=>'searchRecords',
										'name_filter'=>'filter',
										'name_filterBy'=>'filterBy',
										'link_title'=>'Search Record'),
							'add'=>array('show'=>false,
										'botton_label'=>'Add New',
										'click_function'=>'',
										'link_title'=>'Add New Data',
										'show_link_type'=>'href_link',//href_link or button
										'url'=>'<a href="Create.php">Add A Order</a>'//<a href="'.$baseUrl.'/company_add">'.COMPANY_ADD_NEW.'</a>
										),
							 
							'view'=>array('show'=>true,
										'botton_label'=>'View',
										'click_function'=>'editOrder',
										'link_title'=>'View This Record'),
							'delete'=>array('show'=>false,
										'botton_label'=>'Delete',
										'click_function'=>'deleteData',
										'class_name'=>'order',
										'link_title'=>'Delete This Record')
							);
//echo "<pre>";print_r($configuration);echo"</pre>";
/*($array_table_header, $array_table_data,$startLimit,$showRecords,$orderBy,$sortBy,$filter,$filterBy,$totalRecords, true, true, true, false, $controller, true, 'Add Company', 'openAddRecordsBox', false, $configuration)*/	

	echo $objDataGrid->showDataGrid($array_table_header, $array_table_data,$configuration_array);
?>