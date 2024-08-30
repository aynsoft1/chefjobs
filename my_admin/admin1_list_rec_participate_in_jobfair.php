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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR);
$template->set_filenames(array('rec_jobfair' => 'admin1_list_rec_participate_in_jobfair.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
//$_GET['page'] = ((int)$_POST['page'] > 0 ? (int)$_POST['page'] : '1');
$search_status1=tep_db_prepare_input($_GET['search_status']);
unset($rInfo); //required
// check if recruiter exists or not ///
if(isset($_GET['rID']))
{
 $recruiter_id=(int)tep_db_input($_GET['rID']);
 if(!$row_check_recruiter=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id='".$recruiter_id."'","recruiter_id,recruiter_email_address"))
 {
  $messageStack->add_session(MESSAGE_RECRUITER_ERROR, 'error');
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR));
 }
}
$whereClause='';
if(tep_not_null($action1))
{
 switch ($action1)
 {
  case 'delete':
    if(isset($_POST['recruiter_ids']))
     $recruiter_ids= implode(',',tep_db_prepare_input($_POST['recruiter_ids']));
    if(count($_POST['recruiter_ids'])>0)
     $whereClause =' and rl.recruiter_id in ('.tep_db_input($recruiter_ids).')';
    else
     unset($action1);
   break;
  case 'confirm_bulk_delete':
    if(isset($_POST['recruiter_ids']) && count($_POST['recruiter_ids'])>0)
    {
     $total_recruiter_ids=count($_POST['recruiter_ids']);
     for($i=0;$i<$total_recruiter_ids;$i++)
     {
      $recruiter_id=(int)tep_db_prepare_input($_POST['recruiter_ids'][$i]);
      $logo_check=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$recruiter_id."'","recruiter_logo");
      if(tep_not_null($logo_check['recruiter_logo']))
      {
       if(is_file(PATH_TO_MAIN_PHYSICAL_LOGO.$logo_check['recruiter_logo']))
       {
        @unlink(PATH_TO_MAIN_PHYSICAL_LOGO.$logo_check['recruiter_logo']);
       }
      }
      tep_db_query("delete from ".SCREENER_TABLE." where recruiter_id='".$recruiter_id."'");
      ///////////////////////////////////////////////////////////////////////////////
      $result_jobs=tep_db_query("select job_id  from ".JOB_TABLE." where recruiter_id='".$recruiter_id."'");
      while($jobs=tep_db_fetch_array($result_jobs))
      {
       $job_id=$jobs['job_id'];
       $result_application=tep_db_query("select id,application_id,resume_name from ".APPLICATION_TABLE." where job_id='".$job_id."'");
       while($row11=tep_db_fetch_array($result_application))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$row11['resume_name']))
        {
         @unlink(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$row11['resume_name']);
        }
        $result_applicant_interaction=tep_db_query("select id,application_id,attachment_file from ".APPLICANT_INTERACTION_TABLE." where application_id='".$row11['id']."'");
        while($row12=tep_db_fetch_array($result_applicant_interaction))
        {
         if(tep_not_null($row12['attachment_file']) && (no_of_records(APPLICANT_INTERACTION_TABLE," application_id ='".$row12['application_id']."' and attachment_file ='".tep_db_input($row12['attachment_file'])."' ",'id')==1))
         {
          $file_directory_name=get_file_directory($row12['attachment_file']);
          if(is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory_name.'/'.$row12['attachment_file']))
          {
           @unlink(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory_name.'/'.$row12['attachment_file']);
          }
         }
         tep_db_query("delete from ".APPLICANT_INTERACTION_TABLE." where id='".$row12['id']."'");
        }
        tep_db_free_result($result_applicant_interaction);
        tep_db_query("delete from ".APPLICANT_STATUS_TABLE." where application_id='".$row11['id']."'");
        tep_db_query("delete from ".APPLICATION_RATING_TABLE." where application_id='".$row11['id']."'");
        tep_db_query("delete from ".APPLICATION_TABLE." where id='".$row11['id']."'");
       }
       tep_db_free_result($result_application);
       tep_db_query("delete from ".APPLY_TABLE." where job_id='".$job_id."'");
       tep_db_query("delete from ".JOB_STATISTICS_TABLE." where job_id='".$job_id."'");
       tep_db_query("delete from ".JOB_JOB_CATEGORY_TABLE." where job_id='".$job_id."'");
       tep_db_query("delete from ".RESUME_WEIGHT_TABLE." where job_id='".$job_id."'");
       tep_db_query("delete from ".JOB_POINT_HISTORY_TABLE." where job_id='".$job_id."' ");
       tep_db_query("delete from ".INDEED_JOB_TABLE." where job_id='".$job_id."'");
       tep_db_query("delete from ".JOB_TABLE." where job_id='".$job_id."'");
      }
      tep_db_free_result($result_jobs);
      /////////////////////////////////////////////////////////////////
      /////////////////////////
      $result_tr_id=tep_db_query("select r.topic_id  from ".TOPIC_REPLY_TABLE." as r left outer join ".FORUM_TOPICS_TABLE."  as t on (r.topic_id=t.id) where r.user_type='recruiter' and r.user_id ='".$recruiter_id."'");
      while($row=tep_db_fetch_array($result_tr_id))
      {
       $topic_id=$row['topic_id'];
       tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id ='".$topic_id."'");
      }
      tep_db_free_result($result_tr_id);
      tep_db_query("delete from ".TOPIC_REPLY_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
      tep_db_query("delete from ".FORUM_TOPICS_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
      /////////////////////////////////////

      //tep_db_query("delete from ".JOB_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".ORDER_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".RECRUITER_ACCOUNT_HISTORY_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".RECRUITER_USERS_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".JOBSEEKER_RATING_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".COMPANY_DESCRIPTION_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".SEARCH_RESUME_RESULT_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".SAVE_RESUME_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".USER_CONTACT_TABLE." where user_id ='".$recruiter_id."' and user_type='recruiter'");
      tep_db_query("delete from ".LINKEDIN_USER_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
      tep_db_query("delete from ".TWITTER_USER_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
      tep_db_query("delete from ".VIADEO_USER_TABLE." where user_type='recruiter' and user_id='".$recruiter_id."'");
      tep_db_query("delete from ".RECRUITER_LOGIN_TABLE." where recruiter_id='".$recruiter_id."'");
      tep_db_query("delete from ".RECRUITER_TABLE." where recruiter_id='".$recruiter_id."'");
     }
     $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR));
    }
    else
     unset($action1);
   break;
 }
}

if(tep_not_null($action))
{
 switch ($action)
 {
  case 'confirm_delete':
   $logo_check=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$recruiter_id."'","recruiter_logo");
   if(tep_not_null($logo_check['recruiter_logo']))
   {
    if(is_file(PATH_TO_MAIN_PHYSICAL_LOGO.$logo_check['recruiter_logo']))
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_LOGO.$logo_check['recruiter_logo']);
    }
   }
   tep_db_query("delete from ".SCREENER_TABLE." where recruiter_id='".$recruiter_id."'");
   ///////////////////////////////////////////////////////////////////////////////
   $result_jobs=tep_db_query("select job_id  from ".JOB_TABLE." where recruiter_id='".$recruiter_id."'");
   while($jobs=tep_db_fetch_array($result_jobs))
   {
    $job_id=$jobs['job_id'];
    $result_application=tep_db_query("select id,application_id,resume_name from ".APPLICATION_TABLE." where job_id='".$job_id."'");
    while($row11=tep_db_fetch_array($result_application))
    {
     if(is_file(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$row11['resume_name']))
     {
      @unlink(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$row11['resume_name']);
     }
     $result_applicant_interaction=tep_db_query("select id,application_id,attachment_file from ".APPLICANT_INTERACTION_TABLE." where application_id='".$row11['id']."'");
     while($row12=tep_db_fetch_array($result_applicant_interaction))
     {
      if(tep_not_null($row12['attachment_file']) && (no_of_records(APPLICANT_INTERACTION_TABLE," application_id ='".$row12['application_id']."' and attachment_file ='".tep_db_input($row12['attachment_file'])."' ",'id')==1))
      {
       $file_directory_name=get_file_directory($row12['attachment_file']);
       if(is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory_name.'/'.$row12['attachment_file']))
       {
        @unlink(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory_name.'/'.$row12['attachment_file']);
       }
      }
      tep_db_query("delete from ".APPLICANT_INTERACTION_TABLE." where id='".$row12['id']."'");
     }
     tep_db_free_result($result_applicant_interaction);
     tep_db_query("delete from ".APPLICANT_STATUS_TABLE." where application_id='".$row11['id']."'");
     tep_db_query("delete from ".APPLICATION_RATING_TABLE." where application_id='".$row11['id']."'");
     tep_db_query("delete from ".APPLICATION_TABLE." where id='".$row11['id']."'");
    }
    tep_db_free_result($result_application);
    tep_db_query("delete from ".APPLY_TABLE." where job_id='".$job_id."'");
    tep_db_query("delete from ".JOB_STATISTICS_TABLE." where job_id='".$job_id."'");
    tep_db_query("delete from ".JOB_JOB_CATEGORY_TABLE." where job_id='".$job_id."'");
    tep_db_query("delete from ".RESUME_WEIGHT_TABLE." where job_id='".$job_id."'");
    tep_db_query("delete from ".JOB_POINT_HISTORY_TABLE." where job_id='".$job_id."' ");
    tep_db_query("delete from ".INDEED_JOB_TABLE." where job_id='".$job_id."'");
    tep_db_query("delete from ".JOB_TABLE." where job_id='".$job_id."'");
   }
   tep_db_free_result($result_jobs);
   /////////////////////////////////////////////////////////////////
   /////////////////////////
   $result_tr_id=tep_db_query("select r.topic_id  from ".TOPIC_REPLY_TABLE." as r left outer join ".FORUM_TOPICS_TABLE."  as t on (r.topic_id=t.id) where r.user_type='recruiter' and r.user_id ='".$recruiter_id."'");
   while($row=tep_db_fetch_array($result_tr_id))
   {
    $topic_id=$row['topic_id'];
    tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id ='".$topic_id."'");
   }
   tep_db_free_result($result_tr_id);
   tep_db_query("delete from ".TOPIC_REPLY_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
   tep_db_query("delete from ".FORUM_TOPICS_TABLE." where user_type='recruiter' and user_id='".$recruiter_id."'");
   /////////////////////////////////////
   //tep_db_query("delete from ".JOB_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".ORDER_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".RECRUITER_ACCOUNT_HISTORY_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".RECRUITER_USERS_TABLE." where recruiter_id='".$recruiter_id."'");
   //tep_db_query("delete from webcal_user  where cal_login='".tep_db_input($row_check_recruiter['recruiter_email_address'])."'");
   tep_db_query("delete from ".JOBSEEKER_RATING_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".COMPANY_DESCRIPTION_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".SEARCH_RESUME_RESULT_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".SAVE_RESUME_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".USER_CONTACT_TABLE." where user_id ='".$recruiter_id."' and user_type='recruiter'");
   tep_db_query("delete from ".LINKEDIN_USER_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
   tep_db_query("delete from ".TWITTER_USER_TABLE." where user_type='recruiter' and user_id ='".$recruiter_id."'");
   tep_db_query("delete from ".VIADEO_USER_TABLE." where user_type='recruiter' and user_id='".$recruiter_id."'");
   tep_db_query("delete from ".RECRUITER_LOGIN_TABLE." where recruiter_id='".$recruiter_id."'");
   tep_db_query("delete from ".RECRUITER_TABLE." where recruiter_id='".$recruiter_id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR));
   break;
  case 'recruiter_active':
  case 'recruiter_inactive':
 //  tep_db_query("update ".RECRUITER_JOBFAIR_TABLE." set approved='".($action=='recruiter_active'?'Yes':'No')."' where recruiter_id='".$_GET['rID']."'&id='".(tep_not_null($_GET['jfID'])?$_GET['jfID']:0)."'");
   tep_db_query("update ".RECRUITER_JOBFAIR_TABLE." set approved='".($action=='recruiter_active'?'Yes':'No')."' where recruiter_id='".$_GET['rID']."'"); $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR,tep_get_all_get_params(array('action','selected_box'))));
   break;
 }
}
//////////////////
///only for sorting starts
$sort_array=array("jf.jobfair_title","rl.recruiter_email_address","r.recruiter_company_name","jf.approved");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);
///only for sorting ends
if(tep_not_null($search_status1))
{
 if($search_status1=='active')
 {
  $db_recruiter_query_raw = "select rl.recruiter_id, concat(r.recruiter_first_name,' ',r.recruiter_last_name) as recruiter_name,jf.inserted, rjf.approved, jf.jobfair_title, jf.id, r.recruiter_featured, rl.recruiter_email_address, r.recruiter_company_name, rl.recruiter_status from " . RECRUITER_JOBFAIR_TABLE." as rjf left join ".JOBFAIR_TABLE." as jf on (rjf.jobfair_id=jf.id and jf.jobfair_status='Yes') left join ".RECRUITER_LOGIN_TABLE . " as rl on (rjf.recruiter_id=rl.recruiter_id) left join " . RECRUITER_TABLE . " as r on ( rl.recruiter_id=r.recruiter_id) where rl.recruiter_status='Yes' $whereClause order by ".$order_by_clause;
 }
 elseif($search_status1=='inactive')
 {
  $db_recruiter_query_raw = "select rl.recruiter_id, concat(r.recruiter_first_name,' ',r.recruiter_last_name) as recruiter_name,jf.inserted, rjf.approved, jf.jobfair_title, jf.id, r.recruiter_featured, rl.recruiter_email_address, r.recruiter_company_name, rl.recruiter_status from " . RECRUITER_JOBFAIR_TABLE." as rjf left join ".JOBFAIR_TABLE." as jf on (rjf.jobfair_id=jf.id and jf.jobfair_status='Yes') left join ".RECRUITER_LOGIN_TABLE . " as rl on (rjf.recruiter_id=rl.recruiter_id) left join " . RECRUITER_TABLE . " as r on ( rl.recruiter_id=r.recruiter_id) where rl.recruiter_status='No' $whereClause order by ".$order_by_clause;
 }
}
else
$db_recruiter_query_raw = "select rl.recruiter_id, concat(r.recruiter_first_name,' ',r.recruiter_last_name) as recruiter_name,jf.inserted, rjf.approved, jf.jobfair_title, jf.id,  r.recruiter_featured, rl.recruiter_email_address, r.recruiter_company_name, rl.recruiter_status from " . RECRUITER_JOBFAIR_TABLE." as rjf left join ".JOBFAIR_TABLE." as jf on (rjf.jobfair_id=jf.id ) left join ".RECRUITER_LOGIN_TABLE . " as rl on (rjf.recruiter_id=rl.recruiter_id) left join " . RECRUITER_TABLE . " as r on ( rl.recruiter_id=r.recruiter_id) where jf.jobfair_status='Yes' $whereClause order by ".$order_by_clause;
//echo $db_recruiter_query_raw;
$db_recruiter_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_recruiter_query_raw, $db_recruiter_query_numrows);
$db_recruiter_query = tep_db_query($db_recruiter_query_raw);
$db_recruiter_num_row = tep_db_num_rows($db_recruiter_query);
if($db_recruiter_num_row > 0)
{
 $alternate=1;
 while ($recruiter = tep_db_fetch_array($db_recruiter_query))
 {
  if($action1!='delete')
  {
   if ( (!isset($_GET['rID']) || (isset($_GET['rID']) && ($_GET['rID'] == $recruiter['recruiter_id']))) && !isset($rInfo) && (substr($action, 0, 3) != 'new'))
   {
    $rInfo = new objectInfo($recruiter);
   }
   if ( (isset($rInfo) && is_object($rInfo)) && ($recruiter['id'] == $rInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    // $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('rID','action','selected_box'))).'&rID='.$recruiter['recruiter_id'] . '\'"';
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('jfID','action','selected_box'))).'&rID='.$recruiter['recruiter_id'].'&jfID='.$recruiter['id'] . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   if ($recruiter['approved'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('jfID','action','selected_box'))).'&rID=' . $recruiter['recruiter_id'] .'&jfID='.$recruiter['id'] .  '&action=recruiter_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_RECRUITER_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_RECRUITER_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_RECRUITER_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('jfID','action','selected_box'))).'&rID=' . $recruiter['recruiter_id'] .'&jfID='.$recruiter['id'] .  '&action=recruiter_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_RECRUITER_ACTIVATE, 28, 22) . '</a>';
   }

  }
  else
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';

///////////////***count no of active  jobs  ***////////
$now=date('Y-m-d H:i:s');
 $njobs_query = tep_db_query("select  distinct job_id from " . JOB_TABLE . " where recruiter_id = '" . $recruiter['recruiter_id'] . "' and job_status='Yes' and expired >='$now' and ( deleted is NULL or deleted='0000-00-00 00:00:00')");
 $no_of_active_jobs= tep_db_num_rows($njobs_query);
 // echo  $recruiter['recruiter_id']. "aa".$no_of_active_jobs;

/***** find no of jobs in this jobfair*****/
$jobfairs_query=tep_db_query("select distinct job_id from " . JOB_JOBFAIR_TABLE." where recruiter_id='".$recruiter['recruiter_id']."' and jobfair_id='".$recruiter['id']."'" );
$no_of_jobs= tep_db_num_rows($jobfairs_query);
/**********************/

//////////*********************************////
  $alternate++;
  $template->assign_block_vars('recruiters', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'check_box' => tep_draw_checkbox_field('recruiter_ids[]',$recruiter['recruiter_id'],($action1=='delete'?true:false)),
   'jobfairtitle' => tep_db_output($recruiter['jobfair_title']),
   'email' => tep_db_output($recruiter['recruiter_email_address']),
   'company' => tep_db_output($recruiter['recruiter_company_name']),
   'no_fairjobs'=>'<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_JOBS,'jfID='.$recruiter['id'].'&rID='.$recruiter['recruiter_id']).'">'.$no_of_jobs.'</a>',
   'status' => $status,
	'no_activejobs'=>$no_of_active_jobs,
   ));
 }
}
/////
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':
  $heading[] = array('text' => '<h4 class="px-4">' . $rInfo->recruiter_name . '</h4>');
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $rInfo->recruiter_name . '</b>');
  $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('page','action','selected_box'))).'&action=confirm_delete' . '"><button class="btn btn-primary">Confirm</button></a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('action','selected_box'))) . '"><button class="btn btn-primary">Cancel</button></a>');
  $contents[] = array('text' => '<br>'.TEXT_DELETE_WARNING.'<br>&nbsp;');
  break;
 default:
 if (isset($rInfo) && is_object($rInfo))
 {
  $heading[] = array('text' => '<h4 class="px-4">'.$rInfo->recruiter_name.'<h4 class="px-4">');
  $contents[] = array('text' => TEXT_COMPANY."<br><div class='px-4 mb-2'>".$rInfo->recruiter_company_name.'</div>');
  $contents[] = array('text' => TEXT_INFO_EDIT_ACCOUNT_INTRO);
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_JOBS, 'rID=' . $rInfo->recruiter_id ).'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.IMAGE_JOBS.'</a>');
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ORDERS, 'rID=' . $rInfo->recruiter_id ).'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.IMAGE_ORDERS.'</a>');
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'plan_for=job_post&rID=' . $rInfo->recruiter_id ).'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.IMAGE_RECRUITER_ACCOUNT_JOB.'</a>');
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'plan_for=resume_search&rID=' . $rInfo->recruiter_id ).'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.IMAGE_RECRUITER_ACCOUNT_RESUME.'</a>');
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION, 'rID=' . $rInfo->recruiter_id ).'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.IMAGE_EDIT.'</a>');
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('rID','selected_box'))).'&rID=' . $rInfo->recruiter_id.'&action=delete'.'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.IMAGE_DELETE.'</a>');
  $contents[] = array('align' => 'left', 'params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"','text' => '<a class="list-group-item list-group-item-action" href="' . tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL, 'rID=' . $rInfo->recruiter_id ).'" class="right_black"><i class="bi bi-chevron-right me-2"></i> '.INFO_TEXT_RECRUITER_CONTROL_PANEL.'</a>');
		$contents[] = array('params'=>'class="dataTableRightRow1"','text' => '<b>'.TEXT_RECRUITER_ID." : ".$rInfo->recruiter_id.'<br>&nbsp;Use this ID to Import job(Indeed)</b>');
  // $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_JOBS, 'rID=' . $rInfo->recruiter_id ) . '">'.tep_image_button(PATH_TO_BUTTON.'button_jobs.gif',IMAGE_JOBS).'</a>  <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ORDERS, 'rID=' . $rInfo->recruiter_id ) . '">'.tep_image_button(PATH_TO_BUTTON.'button_orders.gif',IMAGE_ORDERS).'</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ACCOUNTS, 'rID=' . $rInfo->recruiter_id ) . '">'.tep_image_button(PATH_TO_BUTTON.'button_recruiter_account.gif',IMAGE_RECRUITER_ACCOUNT).'</a><br><br><a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION, 'rID=' . $rInfo->recruiter_id ) . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('rID','selected_box'))).'&rID=' . $rInfo->recruiter_id.'&action=delete' . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a><br><a href="' . tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL, 'rID=' . $rInfo->recruiter_id ) . '">'.tep_db_output(INFO_TEXT_RECRUITER_CONTROL_PANEL).'</a>&nbsp;');
  $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
 }
 break;
}
////
if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='205';
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
$search_status_array=array();
$search_status_array[]=array('id'=>'','text'=>'All');
$search_status_array[]=array('id'=>'active','text'=>'active');
$search_status_array[]=array('id'=>'inactive','text'=>'inactive');

if($action1=='delete')
{
 $check_link='<br>'.TEXT_DELETE_WARNING.'<br><br><a href="#"  onclick="DeleteSelected(\'confirm_bulk_delete\')"><button class="btn btn-primary">Confirm</button></a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR).'"><button class="btn btn-secondary">Cancel</button></a>';
 $template->assign_vars(array(
 'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
 'TABLE_HEADING_EMAIL'=>TABLE_HEADING_EMAIL,
 'TABLE_HEADING_COMPANY'=>TABLE_HEADING_COMPANY,
  ));
}
else
{
 $check_link='<a href="#" onclick="checkall()">Check All</a> / <a class="me-3" href="#"  onclick="uncheckall()">Uncheck All</a> <a class="" href="#"  onclick="DeleteSelected(\'delete\')"><i class="bi bi-trash"></i></a></font>';
 $template->assign_vars(array(
 'TABLE_HEADING_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_EMAIL'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_EMAIL.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_COMPANY'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_COMPANY.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'TABLE_HEADING_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_STATUS.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
//'TABLE_HEADING_FEATURED_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_RECRUITER_PARTICIPATE_JOBFAIR, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_FEATURED_STATUS.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
'TABLE_HEADING_NO_OF_JOBFAIR_JOBS'=>TABLE_HEADING_NO_OF_JOBFAIR_JOBS,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'INFO_TEXT_STATUS'=>INFO_TEXT_STATUS,
 'INFO_TEXT_STATUS1'=>tep_draw_pull_down_menu('search_status', $search_status_array, $search_status1,'onchange="document.disply.submit();" class="form-control form-control-sm"'),
 'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION, 'add=recruiter') . '"><i class="bi bi-plus-lg me-2"></i>' .IMAGE_NEW . '</a>',
  ));
}
$template->assign_vars(array(
 'count_rows'=>$db_recruiter_split->display_count($db_recruiter_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_RECRUITERS),
 'no_of_pages'=>$db_recruiter_split->display_links($db_recruiter_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','rID','action','selected_box'))),
 'check_link'=>($db_recruiter_num_row>0)?$check_link:'',
 'hidden_fields'=>tep_draw_hidden_field('action1',''),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('rec_jobfair');
?>