<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_SOCIAL_FOOTER_LINKS);
$template->set_filenames(array('social_footer_links' => 'admin1_social_footer_links.htm'));
include_once(FILENAME_ADMIN_BODY);
$error=0;

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');

// call values from database
 $f_link=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_group_id='9' && configuration_name='MODULE_FACEBOOK_FOOTER_LINK'","configuration_value");
$facebook_footer_link=$f_link['configuration_value'];

 $l_link=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_group_id='9' && configuration_name='MODULE_LINKEDIN_FOOTER_LINK'","configuration_value");
 $linkedin_footer_link =$l_link['configuration_value'];

 $t_link=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_group_id='9' && configuration_name='MODULE_TWITTER_FOOTER_LINK'","configuration_value");
 $twitter_footer_link = $t_link['configuration_value'];

 $g_link=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_group_id='9' && configuration_name='MODULE_GOOGLEPLUS_FOOTER_LINK'","configuration_value");
$googleplus_footer_link=$g_link['configuration_value'];
/////////////////

if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'update':
   $facebook_footer_link = tep_db_prepare_input($_POST['facebook_footer_link']);
   $linkedin_footer_link = tep_db_prepare_input($_POST['linkedin_footer_link']);
   $twitter_footer_link = tep_db_prepare_input($_POST['twitter_footer_link']);
   $googleplus_footer_link = tep_db_prepare_input($_POST['googleplus_footer_link']);
//check
$posf=strpos($facebook_footer_link,"//"); //gives position no of :
$posl=strpos($linkedin_footer_link,"//"); //gives position no of :
$post=strpos($twitter_footer_link,"//"); //gives position no of :
$posg=strpos($googleplus_footer_link,"//"); //gives position no of :
//echo "position=".$pos."<br>";
if(tep_not_null($facebook_footer_link))
if(!(substr($facebook_footer_link,0,$posf) =='http:' || substr($facebook_footer_link,0,$posf) =='https:'))
	$error=1;
if(tep_not_null($linkedin_footer_link))
if(!(substr($linkedin_footer_link,0,$posl) =='http:' || substr($linkedin_footer_link,0,$posl) =='https:'))
	$error=1;
if(tep_not_null($twitter_footer_link))
if(!(substr($twitter_footer_link,0,$post) =='http:' || substr($twitter_footer_link,0,$post) =='https:'))
	$error=1;
if(tep_not_null($googleplus_footer_link))
if(!(substr($googleplus_footer_link,0,$posg) =='http:' || substr($googleplus_footer_link,0,$posg) =='https:'))
	$error=1;

//echo substr($linkedin_footer_link,0,$posl);
//echo $error;
if($error=='0')
{
  $sql_data_array=array('configuration_value'=>$facebook_footer_link,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_FOOTER_LINK'");

   $sql_data_array=array('configuration_value'=>$linkedin_footer_link,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_LINKEDIN_FOOTER_LINK'");

   $sql_data_array=array('configuration_value'=>$twitter_footer_link,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_FOOTER_LINK'");

   $sql_data_array=array('configuration_value'=>$googleplus_footer_link,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_GOOGLEPLUS_FOOTER_LINK'");
   ///////////////////////
 

   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_SOCIAL_FOOTER_LINKS);   
}
else
{	$error=1;
   $messageStack->add_session(MESSAGE_HTTP_ERROR, 'Error');
   tep_redirect(FILENAME_ADMIN1_SOCIAL_FOOTER_LINKS);   
}

  break;
}
 }

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'social_footer_form' => tep_draw_form('social_footer_form',PATH_TO_ADMIN.FILENAME_ADMIN1_SOCIAL_FOOTER_LINKS,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),

 'INFO_TEXT_FACEBOOK_FOOTER_LINK' => INFO_TEXT_FACEBOOK_FOOTER_LINK,
 'INFO_TEXT_FACEBOOK_FOOTER_LINK1' => tep_draw_input_field('facebook_footer_link',$facebook_footer_link,'class="form-control form-control-sm"'),
 'INFO_TEXT_LINKEDIN_FOOTER_LINK' => INFO_TEXT_LINKEDIN_FOOTER_LINK,
 'INFO_TEXT_LINKEDIN_FOOTER_LINK1' => tep_draw_input_field('linkedin_footer_link',$linkedin_footer_link,'class="form-control form-control-sm"'),
 'INFO_TEXT_TWITTER_FOOTER_LINK' => INFO_TEXT_TWITTER_FOOTER_LINK,
 'INFO_TEXT_TWITTER_FOOTER_LINK1' => tep_draw_input_field('twitter_footer_link',$twitter_footer_link,'class="form-control form-control-sm"'),
 'INFO_TEXT_GOOGLEPLUS_FOOTER_LINK' => INFO_TEXT_GOOGLEPLUS_FOOTER_LINK,
 'INFO_TEXT_GOOGLEPLUS_FOOTER_LINK1' => tep_draw_input_field('googleplus_footer_link',$googleplus_footer_link,'class="form-control form-control-sm"'),

 'button' => tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),
 'update_message'=>$messageStack->output()));
$template->pparse('social_footer_links');
?>