<?php
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/
if(is_dir('maintenance'))
{
 include('maintenance/index.htm');
 exit;
}
// Start the clock for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());


ini_set('error_reporting',E_ALL ^ ( E_NOTICE| E_WARNING  |E_DEPRECATED ));
ini_set('display_errors','1');
//ini_set('SMTP','');//jobboard7bs
/*
ini_set('error_reporting','0');
ini_set('display_errors','0');
*/

//careerjobs.de
$host_array=array('localhost','www.careerjobs.de','careerjobs.de','127.0.0.1');

$host_name=strtolower($_SERVER['HTTP_HOST']);
if(!in_array($host_name,$host_array))
{
 die("Forbidden :You don't have permission to access");
}
include_once("classinc/session.php");
include_once("classinc/variables.php");
include_once("classinc/main_config.php");

include_once("classinc/file_name.php");
include_once("classinc/table_names.php");

include_once("general_functions/database.php");
include_once("classinc/connect.php");
tep_db_connect() or die('Unable to connect to database server!');
include_once("classinc/variable1.php");
include_once("classinc/functions.php");

//echo (PATH_TO_MAIN_PHYSICAL_LANGUAGE  .$_SESSION['language']. '.php');die();
include_once("general_functions/functions.php");
include_once("general_functions/extra_functions.php");
include_once("general_functions/html_output.php");
include_once("general_functions/validations.php");
include_once("general_functions/recruiter_functions.php");
include_once("general_functions/password_funcs.php");
include_once("general_functions/relative_dates.php");

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'mime.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'email.php');
if(basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_IMAGE)
{
 ///// online users
 include_once("general_functions/whos_online.php");
 tep_update_whos_online();
 ///
}

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'template.php');
$template = new Template(PATH_TO_TEMPLATE);

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'logger.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'table_block.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'table_block_left.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'table_block_right.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'box.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'message_stack.php');
$messageStack = new messageStack;

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'split_page_results.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'object_info.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'upload.php');

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'block_ip_address.php');
$obj_block_ip_address = new block_ip_address;

//////////////////////////
// include currencies class and create an instance
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'currencies.php');
$currencies = new currencies();
// include pagination class and create ajax pagination
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'pagination_class.php');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'pagination_class1.php');
// set the language
if(!tep_not_null($_SESSION['language']) || isset($_GET['language']))
{
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'language.php');
 $lng = new language();
 if (isset($_GET['language']) && tep_not_null($_GET['language']))
 {
  $lng->set_language($_GET['language']);
 }
 else
 {
  $lng->set_language('english');
  //$lng->get_browser_language();
 }
 $_SESSION['language']=$lng->language['directory'];
 $_SESSION['languages_id']=$lng->language['id'];
 $language=$_SESSION['language'];
 $languages_id=$_SESSION['languages_id'];
}
else
{
 $language=$_SESSION['language'];
 $languages_id=$_SESSION['languages_id'];
}
//print_r($_SESSION);
// include the language translations
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE . $language . '.php');

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'title_metakeyword.php');
$obj_title_metakeyword = new title_metakeyword;

define("PATH_TO_BUTTON",PATH_TO_LANGUAGE.$language."/images/button/");        //Path to buttons
define("PATH_TO_MAIN_PHYSICAL_LANGUAGE_MODULE",PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language."/".PATH_TO_MODULE);        //Path to modules

/*if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_INDEX)
{
 include_once("general_functions/mobile_functions.php");
 if(socialCMS_is_MobileBrowser())
 {
  tep_redirect("mobile/");
 }
}
*/
//include_once(FILENAME_BODY);
foreach ($_GET as $secvalue)
{
 if(is_array($secvalue))
 {
   $secvalue1=$secvalue;
   foreach ($secvalue as $secvalue1)
   if ((preg_match("/<[^>]*script*\"?[^>]*>/i", $secvalue1)) || (preg_match("/<[^>]*object*\"?[^>]*>/i", $secvalue1)) || (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $secvalue1)) || (preg_match("/<[^>]*applet*\"?[^>]*>/i", $secvalue1)) || (preg_match("/<[^>]*meta*\"?[^>]*>/i", $secvalue1)) || (preg_match("/<[^>]*style*\"?[^>]*>/i", $secvalue1)) ||(preg_match("/<[^>]*form*\"?[^>]*>/i", $secvalue1)) ||(preg_match("/\([^>]*\"?[^)]*\)/i", $secvalue1)) ||(preg_match("/\"/i", $secvalue1)) ||(preg_match("/'/i", $secvalue1)))
   {
    $messageStack->add_session(ERROR_WRONG_TAG, 'error');
    tep_redirect("error.php");
   }
  }
  elseif ((preg_match("/<[^>]*script*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/<[^>]*object*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/<[^>]*applet*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/<[^>]*meta*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/<[^>]*style*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/<[^>]*form*\"?[^>]*>/i", $secvalue)) ||
     (preg_match("/\([^>]*\"?[^)]*\)/i", $secvalue)) ||
     (preg_match("/\"/i", $secvalue)) ||
     (preg_match("/'/i", $secvalue)))
 {
  $messageStack->add_session(ERROR_WRONG_TAG, 'error');
		tep_redirect("error.php");
 }
}
if(!check_login('admin'))
{
 foreach ($_POST as $secvalue)
 {
  if(is_array($secvalue))
  {
   $secvalue1=$secvalue;
   foreach ($secvalue as $secvalue1)
   if ((preg_match("/<[^>]*script*\"?[^>]*>/i", $secvalue1)) ||	(preg_match("/<[^>]style*\"?[^>]*>/i", $secvalue1)))
   {
    $messageStack->add_session(ERROR_WRONG_TAG, 'error');
    tep_redirect("error.php");
   }
  }
  elseif ((preg_match("/<[^>]*script*\"?[^>]*>/i", $secvalue)) ||	(preg_match("/<[^>]style*\"?[^>]*>/i", $secvalue)))
  {
   $messageStack->add_session(ERROR_WRONG_TAG, 'error');
   tep_redirect("error.php");
  }
 }
}
if((basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_JOB_SEARCH) && (basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_JOB_DETAILS))
{
  unset($_SESSION['sess_jobsearch']);
}
if(check_login("recruiter"))
{
 if(basename(strtolower($_SERVER['PHP_SELF']))==FILENAME_RECRUITER_SEARCH_RESUME)
 {
  include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
  $obj_account=new recruiter_accounts('','resume_search');
  //print_r($obj_account->allocated_amount);
  $cv=$obj_account->allocated_amount['cv'];
  $enjoyed_cv=$obj_account->enjoyed_amount['cv'];
  $incerment=false;
  if($cv!="Unlimited")
  {
   if($enjoyed_cv > $cv || $cv=='0')
   {
    $incerment=false;
   }
   else
    $incerment=true;
  }
  else
     $incerment=true;

  if($incerment==true)
  {
   recruiter_plan_type_name();
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_account_cv.php');
   $obj_recruiter_account_cv = new recruiter_account_cv;
  }
 }
 else if((basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_JOBSEEKER_VIEW_RESUME) && (basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_JOBSEEKER_RESUME_DOWNLOAD) && (basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_RECRUITER_SEARCH_RESUME))
 {
   unset($_SESSION['sess_cvsearch']);
 }
}
$add_language_field_constant='';
if($_SESSION['language']=="german")
 $add_language_field_constant="de_";
define('TEXT_LANGUAGE',$add_language_field_constant);
?>