<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_SEO);
$template->set_filenames(array('seo' => 'admin1_admin_seo.htm'));
include_once(FILENAME_ADMIN_BODY);

$site_title       = SITE_TITLE1;
$data=$obj_title_metakeyword->metakeywords;
$mata_array=array();
$add_meta_keywords=$add_meta_description=true;
if(preg_match_all('/<meta *name="([^"]+)".*content\="([^"]*)"*>/i',$data, $matches))
{
 $total_names=count($matches[1]);
 for($i=0;$i<$total_names;$i++)
 {
  $mata_name=strtolower(tep_db_prepare_input($matches[1][$i]));
  $mata_value=strtolower(tep_db_prepare_input($matches[2][$i]));
  $mata_array[]=array('name'=>$mata_name ,'value'=>$mata_value);
  if($mata_name=='keywords')
  {
   $meta_keyword=$mata_value;
   $add_meta_keywords=false;
  }
  elseif($mata_name=='description')
  {
   $meta_description=$mata_value;
   $add_meta_description=false;
  }
 }
}

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');

if (tep_not_null($action))
{
 switch ($action)
	{
  case 'update':
   $site_title       = tep_db_prepare_input($_POST['TR_site_title']);
   $meta_keyword     = tep_db_prepare_input($_POST['meta_keyword']);
   $meta_description = tep_db_prepare_input($_POST['meta_description']);

   if($site_title=='')
   $site_title = SITE_TITLE1;
   $sql_data_array=array('configuration_value'=>$site_title,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'SITE_TITLE1'");
   $total_data=count($mata_array);
   $meta_keyword1='';
   for($i=0;$i<$total_data;$i++)
   {
    if($mata_array[$i]['name']=='keywords')
     $meta_keyword1.='<meta name="'.$mata_array[$i]['name'].'" content="'.$meta_keyword.'">'."\n";
    elseif($mata_array[$i]['name']=='description')
     $meta_keyword1.='<meta name="'.$mata_array[$i]['name'].'" content="'.$meta_description.'">'."\n";
    else
    $meta_keyword1.='<meta name="'.$mata_array[$i]['name'].'" content="'.$mata_array[$i]['value'].'">'."\n";
   }
   if($add_meta_keywords)
    $meta_keyword1.='<meta name="keywords" content="'.$meta_keyword.'">'."\n";
   if($add_meta_description)
    $meta_keyword1.='<meta name="description" content="'.$meta_description.'">'."\n";

   $sql_data_array=array('meta_keyword'=>$meta_keyword1);
   tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE, $sql_data_array,'update', "file_name= 'default.php'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_ADMIN_SEO);
  break;
 }
}

$template->assign_vars(array(
 'HEADING_TITLE'                => HEADING_TITLE,
 'seo_form'                     => tep_draw_form('site_seo',PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SEO,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_SITE_TITLE'         => INFO_TEXT_SITE_TITLE,
 'INFO_TEXT_SITE_TITLE1'        => tep_draw_input_field('TR_site_title',$site_title,'size="60" class="form-control form-control-sm"'),
 'INFO_TEXT_SITE_META_KEYWORDS' =>INFO_TEXT_SITE_META_KEYWORDS,
 'INFO_TEXT_SITE_META_KEYWORDS1'=>tep_draw_textarea_field('meta_keyword', true,70,7,$meta_keyword, 'class="form-control form-control-sm"'),
 'INFO_TEXT_SITE_META_DESCRIPTION'=>INFO_TEXT_SITE_META_DESCRIPTION,
 'INFO_TEXT_SITE_META_DESCRIPTION1'=>tep_draw_textarea_field('meta_description', true,70,5,$meta_description, 'class="form-control form-control-sm"'),
 'button' => tep_draw_submit_button_field('','Update','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE),
 'update_message'=>$messageStack->output()));
$template->pparse('seo');
?>