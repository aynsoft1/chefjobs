<?php
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_COMPANY_DESCRIPTION);
$template->set_filenames(array('company_description' => 'company_description.htm','preview'=>'company_description1.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$edit=false;
$hidden_fields='';
$button1='';
$form1='';
$button=tep_draw_submit_button_field('',''.IMAGE_PREVIEW.'','class="btn btn-primary" onclick=set_action("preview")');//tep_image_submit(PATH_TO_BUTTON.'button_preview.gif',IMAGE_PREVIEW,"onclick=set_action('preview')");

$form=tep_draw_form('preview', FILENAME_RECRUITER_COMPANY_DESCRIPTION,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','preview');

if($row=getAnyTableWhereData(COMPANY_DESCRIPTION_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."'"))
{
 $edit=true;
 $c_description=stripslashes($row['description']);
 $button.=' '.tep_draw_submit_button_field('','Update','class="btn btn-outline-secondary m-btn-block mmt-15" onclick=set_action("update")');//tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE,"onclick=set_action('update')");
}
else
{
 $c_description='';
 $button.=' '.tep_draw_submit_button_field('',''.IMAGE_INSERT.'','class="btn btn-outline-secondary m-btn-block mmt-15" onclick=set_action("update")');//tep_image_submit(PATH_TO_BUTTON.'button_insert.gif',IMAGE_INSERT,"onclick=set_action('update')");

}

$description_string=tep_draw_textarea_field('c_description', 'soft', '70%', '15', stripslashes($c_description), '', '',false);

if(tep_not_null($action))
{
 $c_description=stripslashes($_POST['c_description']);
 switch($action)
	{
  case 'insert':
  case 'update':
   $sql_data_array=array('recruiter_id'=>$_SESSION['sess_recruiterid'],
                         'description'=>$c_description,
                         );
   if($edit)
   {
	   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    tep_db_perform(COMPANY_DESCRIPTION_TABLE, $sql_data_array, 'update', "id='".$row['id']."'");
   }
   else
   {
	   $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    tep_db_perform(COMPANY_DESCRIPTION_TABLE, $sql_data_array);
   }
   tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL));
   break;
  case 'preview':
   $hidden_fields=tep_draw_hidden_field('c_description',$c_description);
   $form=tep_draw_form('update', FILENAME_RECRUITER_COMPANY_DESCRIPTION,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update');
   $form1=tep_draw_form('back', FILENAME_RECRUITER_COMPANY_DESCRIPTION,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','back');
   $description_string=$c_description;
   if($edit)
   {
    $button=tep_draw_submit_button_field('','Update','class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   }
   else
   {
    $button=tep_draw_submit_button_field('','Insert','class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT);
   }
   $button1=tep_draw_submit_button_field('','Back','class="btn btn-outline-secondary"');//tep_image_submit(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK);
  break;
  case 'back':
   $description_string=tep_draw_textarea_field('c_description', 'soft', '80%', '15', stripslashes($c_description), '', '',false);
  break;
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_DESCRIPTION1'=>$description_string,
 'form'=>$form,
 'form1'=>$form1,
 'button'=>$button,
 'button1'=>$button1,
 'hidden_fields'=>$hidden_fields,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
if($action=='preview')
{
 $template->pparse('preview');
}
else
{
 $template->pparse('company_description');
}
?>