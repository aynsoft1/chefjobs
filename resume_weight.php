<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_RESUME_WEIGHT);
include_once(FILENAME_BODY);
$template->set_filenames(array('resume_weight' => 'resume_weight.htm'));
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$job_id         =(int) (isset($_GET['jobID']) ? $_GET['jobID'] : '');
if(!$row_check=getAnyTableWhereData(JOB_TABLE ," job_id='".$job_id."'  and recruiter_id='".$_SESSION['sess_recruiterid']."'","job_id"))
{
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);
}
$error=false;
switch($action)
{
 case'edit_weight':
 {
  $location_weight  = tep_db_prepare_input($_POST['IN_location_weight']);
  $industry_weight=tep_db_prepare_input($_POST['IN_industry_weight']);
  $job_type_weight=tep_db_prepare_input($_POST['IN_job_type_weight']);
  $experience_weight=tep_db_prepare_input($_POST['IN_experience_weight']);
  $total=0;
  $total=$location_weight+$industry_weight+$job_type_weight+$experience_weight;
  if($total!=100)
  {
   $error=true;
   if($total>100)
    $messageStack->add(TOTAL_EXCEED_ERROR,'error');
   else
    $messageStack->add(TOTAL_BELOW_ERROR,'error');
  }
  else
  {
   $sql_data_array=array('job_id'=>$job_id,
                         'location'=>$location_weight,
                         'industry'=>$industry_weight,
                         'job_type'=>$job_type_weight,
                         'experience'=>$experience_weight,
                         );

  if(!$rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='".$job_id."'",'job_id'))
   tep_db_perform(RESUME_WEIGHT_TABLE, $sql_data_array);
  else
   tep_db_perform(RESUME_WEIGHT_TABLE, $sql_data_array,'update',"job_id='".$job_id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_SAVE, 'success');
   tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS));
  }
 }
 break;
}

if(!$error)
{
 if($rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='".$job_id."'"))
 {
 }
 else
  $rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='0'");

 $location_weight= (int) tep_db_output($rows['location']);
 $industry_weight= (int)tep_db_output($rows['industry']);
 $job_type_weight= (int)tep_db_output($rows['job_type']);
 $experience_weight= (int)tep_db_output($rows['experience']);
 $total=$location_weight+$industry_weight+$job_type_weight+$experience_weight;
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'form'=>tep_draw_form('weighting', FILENAME_RECRUITER_RESUME_WEIGHT,'jobID='.$job_id,'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','edit_weight'),
 'INFO_TEXT_FIELDS'    => INFO_TEXT_FIELDS,
 'INFO_TEXT_WEIGHTS'   => INFO_TEXT_WEIGHTS,
 'INFO_TEXT_LOCATION'  => INFO_TEXT_LOCATION,
 'INFO_TEXT_LOCATION1'=>tep_draw_input_field('IN_location_weight', $location_weight,'onFocus="calculate_total();" onBlur="calculate_total();" class="form-control"'),
 'INFO_TEXT_INDUSTRY'=>INFO_TEXT_INDUSTRY,
 'INFO_TEXT_INDUSTRY1'=>tep_draw_input_field('IN_industry_weight', $industry_weight,'onFocus="calculate_total();" onBlur="calculate_total();" class="form-control"'),
 'INFO_TEXT_EXPERIENCE'=>INFO_TEXT_EXPERIENCE,
 'INFO_TEXT_EXPERIENCE1'=>tep_draw_input_field('IN_experience_weight', $experience_weight,'onFocus="calculate_total();" onBlur="calculate_total();" class="form-control"'),
 'INFO_TEXT_JOB_TYPE'=>INFO_TEXT_JOB_TYPE,
 'INFO_TEXT_JOB_TYPE1'=>tep_draw_input_field('IN_job_type_weight', $job_type_weight,'onFocus="calculate_total();" onBlur="calculate_total();" class="form-control"'),
 'INFO_TEXT_TOTAL'=>INFO_TEXT_TOTAL,
 'INFO_TEXT_TOTAL1'=>tep_draw_input_field('total', $total,' class="form-control" readonly style="background-color: #f8f8f8;color:red;"'),
 'save_button'    => tep_draw_submit_button_field('','Save','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE),

 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('resume_weight');
?>