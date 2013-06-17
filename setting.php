<?php
// $Id: settings.php  2009/10/30  $

/**
 * @file
 * The PHP page that contain all configration settings.
 *
 **
  
*/

define("BASE_PATH", pathinfo(__FILE__, PATHINFO_DIRNAME)); 
//define("DOCUMENT_ROOT", pathinfo(__FILE__, PATHINFO_DIRNAME)); 
//define("Current_Page", basename($_SERVER['REQUEST_URI']));
define("Current_Page", basename($_SERVER['PHP_SELF'])); 

if(!defined("SITE_URL")) 
define("SITE_URL", "http://pick-a-pic.in/");  
//define("SITE_URL", "http://hosting-reviews.co.uk/"); 
#upload path
define("UPLOAD_PATH", pathinfo(__FILE__, PATHINFO_DIRNAME)."/files/"); 

#admin email
define("Admin_Email", "masoom.tulsiani@gmail.com"); 

/* Error Define */
error_reporting(E_ALL);
ini_set('display_errors', '1'); 
//ini_set('display_errors', 'Off');
ob_start(); 

/* load classes */
include(BASE_PATH.'/classes/class.secure.php'); 
include(BASE_PATH.'/classes/class.common.php');


/********************************Database Settings ************************/

#database cache directory
$ADODB_CACHE_DIR=pathinfo(__FILE__, PATHINFO_DIRNAME).'/cache/'; 		
 #define cache time in seconds
define("cacheTime", 3600);

#Including the database file
include(BASE_PATH.'/includes/adodb/adodb.inc.php'); 

#database error file
include(BASE_PATH.'/includes/adodb/adodb-exceptions.inc.php'); 

$db = NewADOConnection("mysqli");
//$db = NewADOConnection('mysqli');

try { 
	/* Database Section */
#database connectivity 
	$connect =$db->Connect("localhost", "pickavhx_Gtyj65t", "masoom$", "pickavhx_calendar") 
	or die("Unable to connect!"); #original
	//$connect =$db->Connect("blog234.db.5473847.hostedresource.com", "blog234", "sdfghhT456", "blog234") or die("Unable to connect!");
	 
} catch (exception $e) {
          secure::errorEncounter($e);  
		  // print($e);
           //adodb_backtrace($e->gettrace());
}
 
#set the fetch mode of adodc library 
$db->SetFetchMode(ADODB_FETCH_ASSOC);

 
#include smarty class
/* Library path   */
require(BASE_PATH.'/includes/Smarty.class.php');
$smarty = new Smarty; 

//If Admin is open then load admin folders 
if(!isset($admin) || empty($admin)){	 
	$admin =false;	
}

if($admin){
	$path ='admin/';
}else{
	$path ="";
}

/* Smarty Path*/
$smarty->template_dir = BASE_PATH.'/'.$path."templates/"; 
$smarty->compile_dir = BASE_PATH.'/'.$path."templates_c/"; 
$smarty->config_dir = BASE_PATH.'/'.$path."configs/"; 
$smarty->cache_dir = BASE_PATH.'/'.$path."cache/"; 



 
/* Meta Tags */ 
$smarty->assign("META_TITLE", "Pick-a-pic");
$smarty->assign("META_KEYWORDS", "Pick-a-pic");
$smarty->assign("META_DESCRIPTION", "Pick-a-pic");

/* grid Constant */
define('CSS_CLASS_ADMIN_TABLE','quizlist');
define('CSS_CLASS_ADMIN_TABLE_FOOTER','quizlist-foot');
define('SET_DEBUG', false);
define('INCLUDE_FILES','File is a include file');
define('SHOWRECORDS',1000);
session_start();
?>