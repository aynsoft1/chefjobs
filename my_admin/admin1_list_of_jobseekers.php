<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_JOBSEEKERS);
$template->set_filenames(array('jobseekers' => 'admin1_list_of_jobseekers.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$search_status1=tep_db_prepare_input($_GET['search_status']);
$search_query=tep_db_prepare_input($_GET['search_query']);
unset($rInfo); //required

// check if jobseeker exists or not ///
if(isset($_GET['jID']))
{
 $jobseeker_id=(int)tep_db_input($_GET['jID']);
 if(!$row_check_jobseeker=getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$jobseeker_id."'","jobseeker_id"))
 {
  $messageStack->add_session(MESSAGE_JOBSEEKER_ERROR, 'error');
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS));
 }
}
$whereClause='';
if(tep_not_null($action1))
{
 switch ($action1)
 {
  case 'delete':
    if(isset($_POST['jobseeker_ids']))
     $jobseeker_ids= implode(',',tep_db_prepare_input($_POST['jobseeker_ids']));
    if(count($_POST['jobseeker_ids'])>0)
     $whereClause =' and jl.jobseeker_id in ('.tep_db_input($jobseeker_ids).')';
    else
     unset($action1);
   break;
  case 'confirm_bulk_delete':
    if(isset($_POST['jobseeker_ids']) && count($_POST['jobseeker_ids'])>0)
    {
     $total_jobseeker_ids=count($_POST['jobseeker_ids']);
     for($i=0;$i<$total_jobseeker_ids;$i++)
     {
      $jobseeker_id=(int)tep_db_prepare_input($_POST['jobseeker_ids'][$i]);
      /////////////////////////////////////////////////////////////////////////////////////
      $result_job_id=tep_db_query("select id,job_id   from ".APPLY_TABLE." where jobseeker_id='".$jobseeker_id."'");
      while($row=tep_db_fetch_array($result_job_id))
      {
       $job_id=$row['job_id'];
       tep_db_query("update ".JOB_STATISTICS_TABLE." set applications=applications-1 where job_id='".$job_id."'");
      }
      tep_db_free_result($result_job_id);
      tep_db_query("delete from ".APPLY_TABLE." where jobseeker_id='".$jobseeker_id."'");
      //\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\APPLICATION\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
      $result_application=tep_db_query("select id,application_id,resume_name from ".APPLICATION_TABLE." where jobseeker_id='".$jobseeker_id."'");
      while($row11=tep_db_fetch_array($result_application))
      {
       $resume_directory_name=get_file_directory($row11['resume_name']);
       if(is_file(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$resume_directory_name.'/'.$row11['resume_name']))
       {
        @unlink(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$resume_directory_name.'/'.$row11['resume_name']);
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
      /////////////////////////////////////////////////////////////////////////////////////
      tep_db_query("delete from ".COVER_LETTER_TABLE." where jobseeker_id='".$jobseeker_id."'");
      $result_resume_id=tep_db_query("select resume_id  from ".JOBSEEKER_RESUME1_TABLE." where jobseeker_id='".$jobseeker_id."'");
      $resume_id='';
      while($row=tep_db_fetch_array($result_resume_id))
      {
       for($j=2;$j<=6;$j++)
       {
        tep_db_query("delete from ".constant( 'JOBSEEKER_RESUME'.$j.'_TABLE')." where resume_id='".$row['resume_id']."'");
       }
       $result_attachment=tep_db_query("select resume_id,jobseeker_photo,jobseeker_resume   from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$row['resume_id']."'");
       while($row11=tep_db_fetch_array($result_attachment))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$row11['jobseeker_resume']))
        {
         @unlink(PATH_TO_MAIN_PHYSICAL_RESUME.$row11['jobseeker_resume']);
        }
        if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row11['jobseeker_photo']))
        {
         @unlink(PATH_TO_MAIN_PHYSICAL_PHOTO.$row11['jobseeker_photo']);
        }
       }
       tep_db_free_result($result_attachment);
       tep_db_query("delete from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$row['resume_id']."' and jobseeker_id='".$jobseeker_id."'");
       tep_db_query("delete from ".RESUME_STATISTICS_TABLE." where resume_id='".$row['resume_id']."'");
       tep_db_query("delete from ".RESUME_JOB_CATEGORY_TABLE." where resume_id='".$row['resume_id']."'");
       tep_db_query("delete from ".JOBSEEKER_RATING_TABLE." where resume_id='".$row['resume_id']."'");
      }
      tep_db_free_result($result_resume_id);
      /////////////////////////
      $result_tr_id=tep_db_query("select r.topic_id  from ".TOPIC_REPLY_TABLE." as r left outer join ".FORUM_TOPICS_TABLE."  as t on (r.topic_id=t.id) where r.user_type='jobseeker' and  r.user_id='".$jobseeker_id."'");
      while($row=tep_db_fetch_array($result_tr_id))
      {
       $topic_id=$row['topic_id'];
       tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id ='".$topic_id."'");
      }
      tep_db_free_result($result_tr_id);
      tep_db_query("delete from ".TOPIC_REPLY_TABLE." where user_type='jobseeker' and user_id ='".$jobseeker_id."'");
      tep_db_query("delete from ".FORUM_TOPICS_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
      /////////////////////////////////////
      tep_db_query("delete from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." where jobseeker_id ='".$jobseeker_id."'");
      tep_db_query("delete from ".JOBSEEKER_ORDER_TABLE." where jobseeker_id ='".$jobseeker_id."'");
      tep_db_query("delete from ".USER_CONTACT_TABLE." where user_id ='".$jobseeker_id."' and user_type='jobseeker'");
      tep_db_query("delete from ".SAVE_JOB_TABLE." where jobseeker_id='".$jobseeker_id."'");
      tep_db_query("delete from ".SEARCH_JOB_RESULT_TABLE." where jobseeker_id='".$jobseeker_id."'");
      tep_db_query("delete from ".LINKEDIN_USER_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
      tep_db_query("delete from ".TWITTER_USER_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
      tep_db_query("delete from ".VIADEO_USER_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
      tep_db_query("delete from ".JOBSEEKER_LOGIN_TABLE." where jobseeker_id='".$jobseeker_id."'");
      tep_db_query("delete from ".JOBSEEKER_TABLE." where jobseeker_id='".$jobseeker_id."'");
     }
     $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS));
    }
    else
     unset($action1);
   break;
 }
}
/////////////////////////
if(tep_not_null($action))
{
 switch ($action)
 {
  case 'confirm_delete':
   /////////////////////////////////////////////////////////////////////////////////////
   $result_job_id=tep_db_query("select id,job_id   from ".APPLY_TABLE." where jobseeker_id='".$jobseeker_id."'");
   while($row=tep_db_fetch_array($result_job_id))
   {
    $job_id=$row['job_id'];
    tep_db_query("update ".JOB_STATISTICS_TABLE." set applications=applications-1 where job_id='".$job_id."'");
   }
   tep_db_free_result($result_job_id);
   tep_db_query("delete from ".APPLY_TABLE." where jobseeker_id='".$jobseeker_id."'");
   //\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\APPLICATION\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
   $result_application=tep_db_query("select id,application_id,resume_name from ".APPLICATION_TABLE." where jobseeker_id='".$jobseeker_id."'");
   while($row11=tep_db_fetch_array($result_application))
   {
    $resume_directory_name=get_file_directory($row11['resume_name']);
    if(is_file(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$resume_directory_name.'/'.$row11['resume_name']))
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$resume_directory_name.'/'.$row11['resume_name']);
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
   /////////////////////////////////////////////////////////////////////////////////////
   tep_db_query("delete from ".COVER_LETTER_TABLE." where jobseeker_id='".$jobseeker_id."'");
   $result_resume_id=tep_db_query("select resume_id  from ".JOBSEEKER_RESUME1_TABLE." where jobseeker_id='".$jobseeker_id."'");
   $resume_id='';
   while($row=tep_db_fetch_array($result_resume_id))
   {
    for($j=2;$j<=6;$j++)
    {
     tep_db_query("delete from ".constant( 'JOBSEEKER_RESUME'.$j.'_TABLE')." where resume_id='".$row['resume_id']."'");
    }
    $result_attachment=tep_db_query("select resume_id,jobseeker_photo,jobseeker_resume   from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$row['resume_id']."'");
    while($row11=tep_db_fetch_array($result_attachment))
    {
     if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$row11['jobseeker_resume']))
     {
      @unlink(PATH_TO_MAIN_PHYSICAL_RESUME.$row11['jobseeker_resume']);
     }
     if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row11['jobseeker_photo']))
     {
      @unlink(PATH_TO_MAIN_PHYSICAL_PHOTO.$row11['jobseeker_photo']);
     }
    }
    tep_db_free_result($result_attachment);
    tep_db_query("delete from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$row['resume_id']."' and jobseeker_id='".$jobseeker_id."'");
    tep_db_query("delete from ".RESUME_STATISTICS_TABLE." where resume_id='".$row['resume_id']."'");
    tep_db_query("delete from ".RESUME_JOB_CATEGORY_TABLE." where resume_id='".$row['resume_id']."'");
    tep_db_query("delete from ".JOBSEEKER_RATING_TABLE." where resume_id='".$row['resume_id']."'");
   }
   tep_db_free_result($result_resume_id);
   /////////////////////////
   $result_tr_id=tep_db_query("select r.topic_id  from ".TOPIC_REPLY_TABLE." as r left outer join ".FORUM_TOPICS_TABLE."  as t on (r.topic_id=t.id) where r.user_type='jobseeker' and r.user_id='".$jobseeker_id."'");
   while($row=tep_db_fetch_array($result_tr_id))
   {
    $topic_id=$row['topic_id'];
    tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id ='".$topic_id."'");
   }
   tep_db_free_result($result_tr_id);
   tep_db_query("delete from ".TOPIC_REPLY_TABLE." where user_type='jobseeker' and user_id ='".$jobseeker_id."'");
   tep_db_query("delete from ".FORUM_TOPICS_TABLE." where user_type='jobseeker' and user_id ='".$jobseeker_id."'");
   /////////////////////////////////////
   tep_db_query("delete from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." where jobseeker_id ='".$jobseeker_id."'");
   tep_db_query("delete from ".JOBSEEKER_ORDER_TABLE." where jobseeker_id ='".$jobseeker_id."'");
   tep_db_query("delete from ".USER_CONTACT_TABLE." where user_id ='".$jobseeker_id."' and user_type='jobseeker'");
   tep_db_query("delete from ".SAVE_JOB_TABLE." where jobseeker_id='".$jobseeker_id."'");
   tep_db_query("delete from ".SEARCH_JOB_RESULT_TABLE." where jobseeker_id='".$jobseeker_id."'");
   tep_db_query("delete from ".LINKEDIN_USER_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
   tep_db_query("delete from ".TWITTER_USER_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
   tep_db_query("delete from ".VIADEO_USER_TABLE." where user_type='jobseeker' and user_id='".$jobseeker_id."'");
   tep_db_query("delete from ".JOBSEEKER_LOGIN_TABLE." where jobseeker_id='".$jobseeker_id."'");
   tep_db_query("delete from ".JOBSEEKER_TABLE." where jobseeker_id='".$jobseeker_id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS));
   break;
  case 'jobseeker_active':
  case 'jobseeker_inactive':
   tep_db_query("update ".JOBSEEKER_LOGIN_TABLE." set jobseeker_status='".($action=='jobseeker_active'?'Yes':'No')."' where jobseeker_id='".$jobseeker_id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'jobseeker_featured':
  case 'jobseeker_not_featured':
   tep_db_query("update ".JOBSEEKER_TABLE." set jobseeker_featured='".($action=='jobseeker_featured'?'Yes':'No')."' where jobseeker_id='".$jobseeker_id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,tep_get_all_get_params(array('action','selected_box'))));
   break;
 }
}
//////////////////
///only for sorting starts
$sort_array=array("jobseeker_name","jl.jobseeker_email_address","jl.jobseeker_status","j.jobseeker_featured");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause);
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);
///only for sorting ends
if(tep_not_null($search_status1))
{
 if($search_status1=='active')
 {
  $db_jobseeker_query_raw = "select j.jobseeker_id, concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name, j.jobseeker_featured, jl.updated, jl.jobseeker_email_address, jl.inserted, jl.jobseeker_status, jl.ip_address, jl.last_login_time, jl.number_of_logon  from " . JOBSEEKER_LOGIN_TABLE . " as jl, " . JOBSEEKER_TABLE . " as j where jl.jobseeker_id=j.jobseeker_id and jl.jobseeker_email_address!='demo@aynsoft.com' and jobseeker_status='Yes' and j.jobseeker_id NOT IN(select jobseeker_id from ".JOBSEEKER_ORDER_TABLE.") $whereClause order by ".$order_by_clause;
 }
 elseif($search_status1=='inactive')
 {
  $db_jobseeker_query_raw = "select j.jobseeker_id, concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name, j.jobseeker_featured, jl.updated, jl.jobseeker_email_address, jl.inserted, jl.jobseeker_status, jl.ip_address, jl.last_login_time, jl.number_of_logon  from " . JOBSEEKER_LOGIN_TABLE . " as jl, " . JOBSEEKER_TABLE . " as j where jl.jobseeker_id=j.jobseeker_id and jl.jobseeker_email_address!='demo@aynsoft.com' and jobseeker_status='No' and j.jobseeker_id NOT IN(select jobseeker_id from ".JOBSEEKER_ORDER_TABLE.") $whereClause order by ".$order_by_clause;
 }
} elseif (tep_not_null($search_query)) {
              //   $db_recruiter_query_raw = "select * from (
              //     select jl.jobseeker_id,
              //         concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,
              //         jl.updated,
              //         j.jobseeker_featured,
              //         jl.jobseeker_email_address,
              //         jl.jobseeker_status,
              //         jl.ip_address,
              //         jl.last_login_time,
              //         jl.number_of_logon
              //     from ".JOBSEEKER_LOGIN_TABLE." as jl
              //     join ".JOBSEEKER_TABLE." as j
              //     on jl.jobseeker_id=j.jobseeker_id
              //     and jl.jobseeker_email_address!='demo@aynsoft.com'
              //     and j.jobseeker_id NOT IN(select jobseeker_id from ".JOBSEEKER_ORDER_TABLE.")
              // ) as c where c.jobseeker_name like '%$search_query%' or c.jobseeker_email_address like '%$search_query%' order by c.updated";

              $db_jobseeker_query_raw = "select j.jobseeker_id,
                                          concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,
                                          jl.updated,
                                          j.jobseeker_featured,
                                          jl.jobseeker_email_address,
                                          jl.jobseeker_status,
                                          jl.ip_address,
                                          jl.last_login_time,
                                          jl.number_of_logon
                                          from " . JOBSEEKER_LOGIN_TABLE . " as jl, " . JOBSEEKER_TABLE . " as j
                                          where jl.jobseeker_id=j.jobseeker_id
                                          and jl.jobseeker_email_address!='demo@aynsoft.com'
                                          and j.jobseeker_id NOT IN(select jobseeker_id from ".JOBSEEKER_ORDER_TABLE.")
                                          and concat(j.jobseeker_first_name,' ',j.jobseeker_last_name, jl.jobseeker_email_address) LIKE '%$search_query%'
                                          order by ".$order_by_clause;
} else {
  $db_jobseeker_query_raw = "select j.jobseeker_id, concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name, j.jobseeker_featured, jl.updated, jl.jobseeker_email_address, jl.inserted, jl.jobseeker_status, jl.ip_address, jl.last_login_time, jl.number_of_logon  from " . JOBSEEKER_LOGIN_TABLE . " as jl, " . JOBSEEKER_TABLE . " as j where jl.jobseeker_id=j.jobseeker_id and jl.jobseeker_email_address!='demo@aynsoft.com' and j.jobseeker_id NOT IN(select jobseeker_id from ".JOBSEEKER_ORDER_TABLE.") $whereClause order by ".$order_by_clause;
}
//echo $db_jobseeker_query_raw;
$db_jobseeker_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_jobseeker_query_raw, $db_jobseeker_query_numrows);
$db_jobseeker_query = tep_db_query($db_jobseeker_query_raw);
$db_jobseeker_num_row = tep_db_num_rows($db_jobseeker_query);
if($db_jobseeker_num_row > 0)
{
 $alternate=1;
 while ($jobseeker = tep_db_fetch_array($db_jobseeker_query))
 {

  if($action1!='delete')
  {
   $wclause=" jobseeker_id ='".$jobseeker['jobseeker_id'] ."'";
   $job_title='';
   if($check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE ,$wclause,"target_job_titles, resume_id,jobseeker_photo"))
   {
    $target_job_titles= $check['target_job_titles'];
    $job_title = $target_job_titles;
   }
   if ( (!isset($_GET['jID']) || (isset($_GET['jID']) && ($_GET['jID'] == $jobseeker['jobseeker_id']))) && !isset($rInfo) && (substr($action, 0, 3) != 'new'))
   {
    $rInfo = new objectInfo($jobseeker);
   }
   if ( (isset($rInfo) && is_object($rInfo)) && ($jobseeker['jobseeker_id'] == $rInfo->jobseeker_id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);

    $row_selected=' id="defaultSelected" class="dataTableRowSelected table-secondary" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
   }
   else
   {
    //$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('jID','action','selected_box'))).'&jID='.$jobseeker['jobseeker_id'] . '\'"';
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('jID','action','selected_box'))).'&jID='.$jobseeker['jobseeker_id'] . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   if ($jobseeker['jobseeker_status'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('jID','action','selected_box'))).'&jID=' . $jobseeker['jobseeker_id'] . '&action=jobseeker_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_JOBSEEKER_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_JOBSEEKER_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_JOBSEEKER_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('jID','action','selected_box'))).'&jID=' . $jobseeker['jobseeker_id'] . '&action=jobseeker_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_JOBSEEKER_ACTIVATE, 28, 22) . '</a>';
   }
   if ($jobseeker['jobseeker_featured'] == 'Yes')
   {
    $featured_status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('jID','action','selected_box'))).'&jID=' . $jobseeker['jobseeker_id'] . '&action=jobseeker_not_featured' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_JOBSEEKER_NOT_FEATURED, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_JOBSEEKER_FEATURED, 28, 22);
   }
   else
   {
    $featured_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_JOBSEEKER_NOT_FEATURE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('jID','action','selected_box'))).'&jID=' . $jobseeker['jobseeker_id'] . '&action=jobseeker_featured' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_JOBSEEKER_FEATURED, 28, 22) . '</a>';
   }
  }
  else
  {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  }

///////////////***count no of resumes added  ***////////
$now=date('Y-m-d H:i:s');
 $nresume_query = tep_db_query("select  distinct resume_id from " . JOBSEEKER_RESUME1_TABLE . " where jobseeker_id = '" . $jobseeker['jobseeker_id'] . "'");
 $total_resumes= tep_db_num_rows($nresume_query);
    // echo  $jobseeker['jobseeker_id']. "aa".$total_resumes;

    ///jobseeker photo////////////
    $photo = defaultProfilePhotoUrl($jobseeker['jobseeker_name'], true, 50, 'class="no-pic" id="seeker-img"'); //tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_IMAGE.'no_pic.gif&size=40','','','');
    if ($checkdd = getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE, "jobseeker_id ='" . $jobseeker['jobseeker_id'] . "'", "jobseeker_photo")) {
      if (tep_not_null($checkdd['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO . $checkdd['jobseeker_photo'])) {
        $photo = tep_image(FILENAME_IMAGE . '?image_name=' . PATH_TO_PHOTO . $checkdd['jobseeker_photo'] . '&size=40', '', '', '');
      }
    }
//////////*********************************////

  $alternate++;
  $template->assign_block_vars('jobseekers', array( 'row_selected' => $row_selected,
   'action'    => $action_image,
   'check_box' => tep_draw_checkbox_field('jobseeker_ids[]',$jobseeker['jobseeker_id'],($action1=='delete'?true:false)),
   'photo'   => $photo,
   'total_resumes'=>$total_resumes,
   'name'      => tep_db_output($jobseeker['jobseeker_name']),
   'inserted'      => tep_date_short($jobseeker['inserted']),
   'email'     => tep_db_output($jobseeker['jobseeker_email_address']),
   'status'    => $status,
   'featured_status' => $featured_status,
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
  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">' . $rInfo->jobseeker_name . '
                      <div class="h6">'.TEXT_DELETE_INTRO.'</div>
                      </div></div>
                      ');
  // $contents[] = array('text' => TEXT_DELETE_INTRO);
  // $contents[] = array('text' => '<div class="py-2">' . $rInfo->jobseeker_name . '</div>');
  $contents[] = array('align' => 'left', 'text' => '
                      <div class="py-2">
                      <a class="btn btn-primary" href="
                        ' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,
                        tep_get_all_get_params(array('page','action','selected_box'))).'&action=confirm_delete' . '">
                        Confirm</a>
                        <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('action','selected_box'))) . '">
                        Cancel
                        </a>
                      </div>');
  $contents[] = array('text' => '<div class="py-2">'.TEXT_DELETE_WARNING.'</div>');
 break;
 default:
 if (isset($rInfo) && is_object($rInfo))
 {
  $heading[] = array('text' => '<div class="list-group"><h4 class="mb-0">'.$rInfo->jobseeker_name.'</h4></div>');
  // $contents[] = array('text' => TEXT_INFO_EDIT_ACCOUNT_INTRO);
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1">'.TEXT_INFO_EDIT_ACCOUNT_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1, 'jID=' . $rInfo->jobseeker_id ) . '">Edit</a>
  <a class="btn btn-primary" href="' . tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL, 'jID=' . $rInfo->jobseeker_id ) . '">Dashboard</a>
  <a class="btn btn-secondary" href="
        ' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,
        tep_get_all_get_params(array('jID','selected_box'))).'&jID=' . $rInfo->jobseeker_id.'&action=delete' . '">Delete
  </a>
  <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
  </div>');
  // $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
  $contents[] = array('text' => '
                        <div class="py-2">
                        <div class="row">
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_IP_ADDRESS.'
                        </label>
                        <h5 class="mt-0">
                        '.$rInfo->ip_address.'
                        </h5>
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_UPDATED.'
                        </label>
                        <h5 class="mt-0">
                        '.$rInfo->updated.'
                        </h5>
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_LAST_LOGIN.'
                        </label>
                        <h5 class="mt-0">
                        '.$rInfo->last_login_time.'
                        </h5>
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_NUMBER_OF_LOGON.'
                        </label>
                        <h5 class="mt-0">
                        '.$rInfo->number_of_logon.'
                        </h5>
                        </div>
                        </div>
                        </div>');
  // $contents[] = array('text' => '<div class="py-2"><label class="font-weight-bolder">
  //                       '.TEXT_INFO_UPDATED.'</label><h5>'.$rInfo->updated.'
  //                       </h5></div>
  //                     ');
  // $contents[] = array('text' => '<div class="py-2"><label class="font-weight-bolder">
  //                       '.TEXT_INFO_LAST_LOGIN.'</label><h5>'.$rInfo->last_login_time.'
  //                       </h5></div>
  //                     ');
  // $contents[] = array('text' => '<div class="py-2"><label class="font-weight-bolder">
  //                       '.TEXT_INFO_NUMBER_OF_LOGON.'</label><h5>'.$rInfo->number_of_logon.'
  //                       </h5></div>
  //                     ');
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
 $check_link='<br>'.TEXT_DELETE_WARNING.'<br><br><a class="text-danger" href="#" onclick="DeleteSelected(\'confirm_bulk_delete\')">'.tep_button('Confirm','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS).'">'.tep_button('Cancel','class="btn btn-primary"').'</a>';
 $template->assign_vars(array(
  'TABLE_HEADING_NAME'=>TABLE_HEADING_NAME,
  'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
  'TABLE_HEADING_EMAIL'=>TABLE_HEADING_EMAIL,
  ));
}
else
{
 $check_link='<a href="#" onclick="checkall()">Check All</a> / <a class="me-3" href="#"   onclick="uncheckall()">Uncheck All</a><a class="" href="#"   onclick="DeleteSelected(\'delete\')"><i class="bi bi-trash"></i></a></font>';
 $template->assign_vars(array(
 'TABLE_HEADING_NAME'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('sort','jID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_NAME.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_EMAIL'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('sort','jID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_EMAIL.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('sort','jID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_STATUS.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'TABLE_HEADING_FEATURED_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS, tep_get_all_get_params(array('sort','jID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_FEATURED_STATUS.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
 'TABLE_HEADING_TOTAL_RESUMES'=>TABLE_HEADING_TOTAL_RESUMES,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
 'INFO_TEXT_STATUS'=>INFO_TEXT_STATUS,
 'INFO_TEXT_STATUS1'=>tep_draw_pull_down_menu('search_status', $search_status_array, $search_status1,'onchange="document.disply.submit();" class="form-control form-control-sm form-control form-control-sm-sm form-select"'),
 'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1, 'add=jobseeker') . '">
 <i class="bi bi-plus-lg me-2"></i>Add New</a>',
 'search_box'=> tep_draw_form('search_query', PATH_TO_ADMIN . FILENAME_ADMIN1_JOBSEEKERS, '', 'get', ' enctype="multipart/form-data"').'
                  <input type="search" class="form-control form-control-sm" style="width:250px;" name="search_query" autocomplete="off" placeholder="search..."  />
                </form>
              ',
  ));
}

$template->assign_vars(array(
 'check_link'=>($db_jobseeker_num_row>0)?$check_link:'',
 'hidden_fields'=>tep_draw_hidden_field('action1',''),
 'count_rows'=>$db_jobseeker_split->display_count($db_jobseeker_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBSEEKERS),
 'no_of_pages'=>$db_jobseeker_split->display_links($db_jobseeker_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jID','action','selected_box'))),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('jobseekers');
?>