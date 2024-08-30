<?
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

include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_RECRUITER_ACCOUNTS);
$template->set_filenames(array('r_account' => 'admin1_recruiter_account.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$plan_for = (isset($_GET['plan_for']) ? $_GET['plan_for'] : '');
// check if recruiter exists or not ///
if(!$row_check_recruiter=getAnyTableWhereData(RECRUITER_LOGIN_TABLE." as rl, ".RECRUITER_TABLE." as r " ,"rl.recruiter_id=r.recruiter_id and rl.recruiter_id='".tep_db_input($_GET['rID'])."'","rl.recruiter_id,rl.recruiter_email_address,concat(r.recruiter_first_name,' ',r.recruiter_last_name) as recruiter_name, r.recruiter_company_name"))
{
 $messageStack->add_session(MESSAGE_RECRUITER_ERROR, 'error');
 tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITERS));
}
$rInfo = new objectInfo($row_check_recruiter);
if(tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'account_new':
   $sql_data_array = array();
   $recruiter_id=$rInfo->recruiter_id;
   $sql_data_array['recruiter_id']=tep_db_prepare_input($recruiter_id);
   $sql_data_array['start_date']=tep_db_prepare_input(date("Y-m-d",mktime(0,0,0, $_POST['TR_month'], $_POST['TR_date'], $_POST['TR_year'])));
   $sql_data_array['end_date']=tep_db_prepare_input(date("Y-m-d",mktime(0,0,0, $_POST['TR_Month'], $_POST['TR_Date'], $_POST['TR_Year'])));
   if($plan_for=='job_post')
   {
    $sql_data_array['recruiter_job_status']=tep_db_prepare_input($_POST['TR_job_status']);
    $sql_data_array['recruiter_job'] = (tep_not_null($_POST['ch_no_of_jobs'])?'2147483647':tep_db_prepare_input($_POST['no_of_jobs']));
    $sql_data_array['featured_job']  = tep_db_prepare_input($_POST['featured_job']);
   }
   else
   {
    $sql_data_array['recruiter_cv_status']=tep_db_prepare_input($_POST['TR_cv_status']);
    $sql_data_array['recruiter_cv']  = (tep_not_null($_POST['ch_no_of_days'])?'2147483647':tep_db_prepare_input($_POST['no_of_days']));
    $sql_data_array['featured_job']  = 'No';
			}
   //$sql_data_array['recruiter_sms_status']=tep_db_prepare_input($_POST['TR_sms_status']);
   //$sql_data_array['recruiter_sms']=(tep_not_null($_POST['ch_no_of_sms'])?'2147483647':tep_db_prepare_input($_POST['no_of_sms']));
   $now=date("Y-m-d");
   if($row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."' and plan_for='".tep_db_input($plan_for)."' and start_date <= '$now' and end_date >='$now'","id"))
   {
    $sql_data_array['updated']='now()';
    tep_db_perform(RECRUITER_ACCOUNT_HISTORY_TABLE, $sql_data_array,'update','recruiter_id="'.$rInfo->recruiter_id.'" and id="'.$row['id'].'"');
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   }
   else
   {
    $messageStack->add_session("Sorry, it is not possible to add information here.", 'error');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID=' . $rInfo->recruiter_id.'&plan_for='.$plan_for));
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID=' . $rInfo->recruiter_id.'&plan_for='.$plan_for));
  break;        
 }
}

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_account.php');
$obj_recruiter_account=new recruiter_account($rInfo->recruiter_id,$plan_for);

/////
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action) 
{ 
 case "edit_account":
  if($obj_recruiter_account->check_status()==true)
  {
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
   $obj_account=new recruiter_accounts($_GET['rID'],$plan_for);
   //print_r($obj_account->allocated_amount);
   $cv=$obj_account->allocated_amount['cv'];
   $unlimited_job=($rInfo->recruiter_job=="2147483647"?true:false);
   $unlimited_cv=($rInfo->recruiter_cv=="2147483647"?true:false);
   $unlimited_sms=($rInfo->recruiter_sms=="2147483647"?true:false);
   $featured_job=$obj_account->allocated_amount['featured_job'];
   $contents = array('form' => tep_draw_form('edit_account', PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'action=account_new&rID=' . $rInfo->recruiter_id.'&plan_for='.$plan_for, 'post', '')); 
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">
                        ' . tep_db_output($rInfo->recruiter_name) . '
                        <div>'. TEXT_INFO_COMPANY .' '. tep_db_output($rInfo->recruiter_company_name).'</div>
                        </div></div>
                      ');
  //  $contents[] = array('text' => '<b>&nbsp;'.TEXT_INFO_COMPANY.'</b><br>&nbsp;'.tep_db_output($rInfo->recruiter_company_name));
   $contents[] = array('text' => '<div class="py-2">
                                          <span>
                                          '.TEXT_INFO_ACCOUNT_STARTS.'
                                          </span>' . datelisting(date("Y-m-d",mktime(0,0,0, substr($rInfo->start_date,5,2), 
                                                  substr($rInfo->start_date,8,2), 
                                                  substr($rInfo->start_date,0,4))), 
                                                  'name="TR_date" class="form-control form-control-sm"', 
                                                  'name="TR_month" class="form-control form-control-sm"', 'name="TR_year" class="form-control form-control-sm"', '2004', date("Y")+4
                                                  ,'','','mb-1')
                                                .'</div>');
   $contents[] = array('text' => '<div class="py-2">
                                  '.TEXT_INFO_ACCOUNT_ENDS.'
                                  ' . datelisting(date("Y-m-d",mktime(0,0,0, substr($rInfo->end_date,5,2), 
                                            substr($rInfo->end_date,8,2), 
                                            substr($rInfo->end_date,0,4))), 
                                            "name='TR_Date' class='form-control form-control-sm'", "name='TR_Month' class='form-control form-control-sm'", "name='TR_Year' class='form-control form-control-sm'", "2004", date("Y")+4,'','','mb-1')
                        .'</div>');
   if($plan_for=='job_post')
   {
    $contents[] = array('text' => '<div class="py-2">
                          '.TEXT_INFO_ACCOUNT_JOB_STATUS.'&nbsp;&nbsp;</b>' . tep_draw_radio_field("TR_job_status", 'Yes', ($rInfo->recruiter_job_status=='Yes'?true:false)).'&nbsp;Yes&nbsp;'
                            .tep_draw_radio_field("TR_job_status", 'No', ($rInfo->recruiter_job_status=='No'?true:false)).'&nbsp;No' );
    $contents[] = array('text' => '<div class="py-2">
                          '.TEXT_INFO_ACCOUNT_NO_OF_JOBS.'</b>' . tep_draw_input_field('no_of_jobs', ($unlimited_job?'':$rInfo->recruiter_job), 'class="form-control form-control-sm mb-2" size="5" maxlength="5"'.($unlimited_job?' disabled':''))
                            .'<div class="form-check">'
                            .tep_draw_checkbox_field('ch_no_of_jobs', '',($unlimited_job?true:false),'','class="form-check-input" id="check_ch_no_of_jobs" onclick="unlimited();"')
                            .'<label for="check_ch_no_of_jobs">Unlimited</label></div>');
				$featured_job_array   = array();
    $featured_job_array[] = array('id'=>'No','text'=>'No');
    $featured_job_array[] = array('id'=>'Yes','text'=>'Yes');

    $contents[] = array('text' => '
              <div class="py-2">'.TEXT_INFO_ACCOUNT_F_JOBS.'
              ' . tep_draw_pull_down_menu('featured_job', $featured_job_array, $featured_job,'class="form-control form-control-sm mt-2"')
            .'</div>');

   }
   else
   {
    $contents[] = array('text' => '<div class="py-2">'.TEXT_INFO_ACCOUNT_CV_STATUS.'
                                  ' . tep_draw_radio_field("TR_cv_status", 'Yes', ($rInfo->recruiter_cv_status=='Yes'?true:false)).'Yes
                                  '.tep_draw_radio_field("TR_cv_status", 'No', ($rInfo->recruiter_cv_status=='No'?true:false)).'No</div>');
                                  
    $contents[] = array('text' => '<div class="py-2">'.TEXT_INFO_ACCOUNT_NO_OF_CVS.'
                                  ' . tep_draw_input_field('no_of_days', ($unlimited_cv?'':$cv), 'size="5" maxlength="5"'.($unlimited_cv?' disabled':'')).'
                                  '.tep_draw_checkbox_field('ch_no_of_days', '',($unlimited_cv?true:false),'','id="check_ch_no_of_days" onclick="unlimited();"').'
                                  <label for="check_ch_no_of_days">Unlimited</label></div>');
   }
   //$contents[] = array('text' => '<div class="py-2">'.TEXT_INFO_ACCOUNT_SMS_STATUS.'</div><br>&nbsp;' . tep_draw_radio_field("TR_sms_status", 'Yes', ($rInfo->recruiter_sms_status=='Yes'?true:false)).'&nbsp;Yes&nbsp;'.tep_draw_radio_field("TR_sms_status", 'No', ($rInfo->recruiter_sms_status=='No'?true:false)).'&nbsp;No' );
   //$contents[] = array('text' => '<div class="py-2">'.TEXT_INFO_ACCOUNT_NO_OF_SMS.'</div><br>&nbsp;' . tep_draw_input_field('no_of_sms', ($unlimited_sms?'':$rInfo->recruiter_sms), 'size="5" maxlength="5"'.($unlimited_sms?' disabled':'')).'&nbsp;&nbsp;'.tep_draw_checkbox_field('ch_no_of_sms', '',($unlimited_sms?true:false),'','id="check_ch_no_of_sms" onclick="unlimited();"').'&nbsp;<label for="check_ch_no_of_sms">Unlimited</label>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                                    '.tep_button('Update','class="btn btn-primary"').'
                                    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS,'rID='.$rInfo->recruiter_id ) . '">
                                    Cancel</a></div>');
  }
 break;
 default:
  if (is_object($rInfo) && $obj_recruiter_account->check_status()==true) 
  {
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">
                        ' . tep_db_output($rInfo->recruiter_name) . '
                        <div class="h5">'.TEXT_INFO_EDIT_ACCOUNT_INTRO.'</div>
                        </div>
                        </div>
                      ');
  //  $contents[] = array('align' => 'left', 'text' => TEXT_INFO_EDIT_ACCOUNT_INTRO);
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                          <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID=' . $rInfo->recruiter_id . '&action=edit_account&plan_for='.$plan_for) . '">
                          Edit
                          </a>
                      ');// <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID=' . $rInfo->recruiter_id . '&action=del_account') . '">' . tep_image_button(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . '</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID='.$rInfo->recruiter_id.'&action=preview') . '">' . tep_image_button(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW) . '</a><br>&nbsp;');
  }
  else
  {
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">
                            ' . tep_db_output($rInfo->recruiter_name) . '</div></div>
                    ');
   $contents[] = array('text' => '<div class="py-2">'.TEXT_INFO_NEW_ACCOUNT_INTRO.'</div>');
   //$contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID=' . $rInfo->recruiter_id ) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a><br>&nbsp;');
  }
  break;
}
////
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH=0;
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'row_selected'=>' class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';" onmouseout="this.className=\'dataTableRowOver\'" ',
 'TABLE_HEADING_NAME'=>TABLE_HEADING_NAME,
 'TEXT_INFO_FULLNAME1'=>$rInfo->recruiter_name,
 'TABLE_HEADING_EMAIL'=>TABLE_HEADING_EMAIL,
 'TEXT_INFO_EMAIL1'=>$rInfo->recruiter_email_address,
 'TABLE_HEADING_COMPANY'=>TABLE_HEADING_COMPANY,
 'TEXT_INFO_COMPANY1'=>$rInfo->recruiter_company_name,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('r_account');
?>