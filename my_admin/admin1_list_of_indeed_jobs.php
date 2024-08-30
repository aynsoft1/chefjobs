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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS);
$template->set_filenames(array('jobs' => 'admin1_list_of_indeed_jobs.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$_GET['j_status'] = (in_array($_GET['j_status'],array('active','expired','deleted','other')) ? $_GET['j_status'] : 'imported');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
unset($jInfo);
$whereClause="";
// check if recruiter exists or not ///
if(isset($_GET['rID']))
{
 if($row_check_recruiter=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".tep_db_input($_GET['rID'])."'","recruiter_id"))
 {
  $whereClause.=" j.recruiter_id='".tep_db_input($_GET['rID'])."'";
 }
 else
 {
  $whereClause.="";
 }
}
else
{
 $whereClause.="";
}
if(tep_not_null($action1))
{
 switch ($action1)
 {
  case 'delete':
    if(isset($_POST['job_ids']))
     $job_ids= implode(',',tep_db_prepare_input($_POST['job_ids']));
    if(count($_POST['job_ids'])>0)
    {
     $whereClause=($whereClause!=""?$whereClause." and":"");
     $whereClause .='j.job_id in ('.tep_db_input($job_ids).')';
    }
    else
     unset($action1);
   break;
  case 'confirm_bulk_delete':
   if(isset($_POST['job_ids']) && count($_POST['job_ids'])>0)
   {
    $job_ids= tep_db_prepare_input($_POST['job_ids']);
    $job_ids= implode(',',$job_ids);
    $today=date("Y-m-d H:i:s",mktime(date("H"),date("i"), date("s"), date("m"), date("d"), date("Y")));
    tep_db_query("update ".JOB_TABLE." set deleted='$today' where job_id in (".$job_ids.")");
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS,tep_get_all_get_params(array('action','sort','jobID','selected_box'))));
   }
  else
   unset($action1);
   break;
  case 'confirm_bulk_c_delete':
   if(isset($_POST['job_ids']) && count($_POST['job_ids'])>0)
   {
    $total_job_ids=count($_POST['job_ids']);
    for($i=0;$i<$total_job_ids;$i++)
    {
     $job_id= tep_db_prepare_input($_POST['job_ids'][$i]);
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
     tep_db_query("delete from ".INDEED_JOB_TABLE." where job_id='".$job_id."'");
     tep_db_query("delete from ".SIMPLYHIRED_JOB_TABLE." where job_id='".$job_id."'");
     tep_db_query("delete from ".JOB_CSV_TABLE." where job_id='".$job_id."'");
     tep_db_query("delete from ".JOB_TABLE." where job_id='".$job_id."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS,tep_get_all_get_params(array('action','sort','jobID','selected_box'))));
   }
  else
   unset($action1);
   break;
 }
}
$whereClause=($whereClause!=""?$whereClause." and":"");
$today=date("Y-m-d H:i:s",mktime(date("H"),date("i"), date("s"), date("m"), date("d"), date("Y")));
switch($_GET['j_status'])
{
 case 'active':
  $whereClause.=" j.re_adv <= '".$today."' and j.expired >= '".$today."' and j.deleted is NULL";
  break;
 case 'expired':
  $whereClause.=" re_adv <= '".$today."' and expired <= '".$today."' and deleted is NULL";
  break;
 case 'deleted':
  $whereClause.=" re_adv <= '".$today."' and deleted <= '".$today."'";
  break;
  case 'other':
   $whereClause.=" j.re_adv > '".$today."' and j.expired >= '".$today."' and j.deleted is NULL";
   break;
}
////////////////
$different_jobs='<a class="btn btn-text text-primary border mr-2" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort','selected_box'))).'&j_status=active">Active Jobs</a>';
$different_jobs.='<a class="btn btn-text text-primary border mr-2" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort','selected_box'))).'&j_status=expired">Expired Jobs</a>';
$different_jobs.='<a class="btn btn-text text-primary border mr-2" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort','selected_box'))).'&j_status=deleted">Deleted Jobs</a>';
$different_jobs.='<a class="btn btn-text text-primary border" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort','selected_box'))).'&j_status=other">Other Jobs</a>';
/////////////
if(tep_not_null($action))
{
 switch ($action)
 {
  case 'confirm_delete':
   if($_GET['j_status']=='deleted') // physically delete all data.
   {
    $job_id=tep_db_prepare_input($_GET['jobID']);
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
    tep_db_query("delete from ".JOB_STATISTICS_TABLE." where job_id='".$_GET['jobID']."'");
    tep_db_query("delete from ".JOB_JOB_CATEGORY_TABLE." where job_id='".$_GET['jobID']."'");
    tep_db_query("delete from ".RESUME_WEIGHT_TABLE." where job_id='".$_GET['jobID']."'");
    tep_db_query("delete from ".INDEED_JOB_TABLE." where job_id='".$_GET['jobID']."'");
    tep_db_query("delete from ".SIMPLYHIRED_JOB_TABLE." where job_id='".$_GET['jobID']."'");
    tep_db_query("delete from ".JOB_CSV_TABLE." where job_id='".$_GET['jobID']."'");
    tep_db_query("delete from ".JOB_TABLE." where job_id='".$_GET['jobID']."'");
   }
   else //delete flag set.
   {
    tep_db_query("update ".JOB_TABLE." set deleted='$today' where job_id='".$_GET['jobID']."'");
   }
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS,tep_get_all_get_params(array('action','sort','jobID','selected_box'))));
   break;
  case 'job_active':
  case 'job_inactive':
   tep_db_query("update ".JOB_TABLE." set job_status='".($action=='job_active'?'Yes':'No')."' where job_id='".$_GET['jobID']."'");
   $messageStack->add_session(MESSAGE_SUCCESS_STATUS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'job_featured':
  case 'job_not_featured':
   tep_db_query("update ".JOB_TABLE." set job_featured='".($action=='job_featured'?'Yes':'No')."' where job_id='".$_GET['jobID']."'");
   $messageStack->add_session(MESSAGE_SUCCESS_STATUS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS,tep_get_all_get_params(array('action','selected_box'))));
   break;
 }
}
//////////////////
///only for sorting starts
$sort_array=array("r.recruiter_company_name","j.job_title","j.re_adv","j.job_status","j.job_featured",'s.viewed','s.clicked','s.applications');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'j.re_adv desc');
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);

///only for sorting ends

/////////indeed jobs
$whereClause=($whereClause!=""?$whereClause." and":"");
$whereClause.=" job_source != 'jobsite'";
//////////////

$db_job_query_raw = "select r.recruiter_company_name, j.job_id, j.recruiter_id as rec_id, j.job_title, j.re_adv, j.job_status, j.job_featured, s.viewed, s.clicked, s.applications from " . JOB_TABLE . " as j left join ".JOB_STATISTICS_TABLE." as s on ( j.job_id=s.job_id or s.job_id is NULL ), ".RECRUITER_TABLE." as r where j.recruiter_id=r.recruiter_id and  $whereClause order by ".$order_by_clause;
//echo $db_job_query_raw;
$db_job_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_job_query_raw, $db_job_query_numrows);
$db_job_query = tep_db_query($db_job_query_raw);
$db_job_num_row = tep_db_num_rows($db_job_query);
if($db_job_num_row > 0)
{
 $alternate=1;
 while ($job = tep_db_fetch_array($db_job_query))
 {
  if($action1!='delete')
  {
   if ( (!isset($_GET['jobID']) || (isset($_GET['jobID']) && ($_GET['jobID'] == $job['job_id']))) && !isset($jInfo) && (substr($action, 0, 3) != 'new'))
   {
    $jInfo = new objectInfo($job);
   }
   if ( (isset($jInfo) && is_object($jInfo)) && ($job['job_id'] == $jInfo->job_id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
    $row_selected=' class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('jobID','action','selected_box'))).'&jobID='.$job['job_id']. '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   if ($job['job_status'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('jobID','action','selected_box'))).'&jobID='.$job['job_id'].'&action=job_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_JOB_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_JOB_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_JOB_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('jobID','action','selected_box'))).'&jobID='.$job['job_id'] . '&action=job_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_JOB_ACTIVATE, 28, 22) . '</a>';
   }
   if ($job['job_featured'] == 'Yes')
   {
    $featured='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('jobID','action','selected_box'))).'&jobID=' . $job['job_id'] . '&action=job_not_featured' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_JOB_NOT_FEATURED, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_JOB_FEATURED, 28, 22);
   }
   else
   {
    $featured=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_JOB_NOT_FEATURE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('jobID','action','selected_box'))).'&jobID=' . $job['job_id'] . '&action=job_featured' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_JOB_FEATURED, 28, 22) . '</a>';
   }
  }
  else
  {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  }
  $alternate++;
  $template->assign_block_vars('jobs', array( 'row_selected' => $row_selected,
   'check_box' => tep_draw_checkbox_field('job_ids[]',$job['job_id'],($action1=='delete'?true:false)),
   'action' => $action_image,
   'company_name' => tep_db_output($job['recruiter_company_name']),
   'title' => tep_db_output($job['job_title']),
   'inserted' => tep_date_short(tep_db_output($job['re_adv'])),
   'status' => $status,
   'featured' => $featured,
   'viewed' => ($action1=='delete')?'':tep_db_output($job['viewed']),
   'clicked' =>($action1=='delete')?'': tep_db_output($job['clicked']),
   'applications' => ($action1=='delete')?'':tep_db_output($job['applications']),
   ));
 }
}
/////
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'del_job':
  $heading[] = array('text' => '<div class="list-group"><h4 class="mb-0">
                                    ' . tep_db_output($jInfo->job_title) . '</h4>
                                    <div><small class="h5">'.TEXT_DELETE_INTRO.'</small></div>
                                </div>
                    ');
  // $contents[] = array('text' => TEXT_DELETE_INTRO);
$contents[] = array('align' => 'center', 
'text' => '<div class="py-2">
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, 
              tep_get_all_get_params(array('page','action','selected_box'))).'&action=confirm_delete' . '">
          Confirm
  </a>
  <a class="btn btn-danger" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('action','selected_box'))) . '">
    Cancel
  </a>
  <div class="mt-2">
  '.TEXT_DELETE_WARNING.'
  </div>
  </div>');
  // $contents[] = array('text' => TEXT_DELETE_WARNING);
  break;
 default:
  if (is_object($jInfo))
  {
   $heading[] = array('text' => '<div class="list-group"><h4 class="mb-0">
                                  ' . tep_db_output($jInfo->job_title) . '</h4>
                                    <div>'.TEXT_INFO_EDIT_JOB_INTRO.'</div> 
                                    </div>
                                  
                      ');
  //  $contents[] = array('align' => 'center', 'text' => TEXT_INFO_EDIT_JOB_INTRO);
   $contents[] = array('align' => 'left', 'text' => '
              <div class="py-2">
                  <a class="btn btn-primary me-2" href="' . tep_href_link(FILENAME_RECRUITER_POST_JOB, 'rID=' . $jInfo->rec_id .'&jobID=' . $jInfo->job_id ) . '">
                          Edit
                  </a> 
                  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, 
                          tep_get_all_get_params(array('jobID','selected_box'))).'&jobID='.$jInfo->job_id.'&action=del_job' . '">
                          Delete
                  </a>
              </div>
            ');
  }
}
////
if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
if($action1=='delete')
{
 if($_GET['j_status']=='deleted')
  $delete_action='confirm_bulk_c_delete';
 else
  $delete_action='confirm_bulk_delete';
 $check_link='<br>'.TEXT_DELETE_WARNING.'<br><br><a href="#"  style="color:#0000ff" onclick="DeleteSelected(\''.$delete_action.'\')">'.tep_button('Confirm','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS).'">' . tep_button('Cancel','class="btn btn-primary"') . '</a>';
 $template->assign_vars(array(
  'TABLE_HEADING_COMPANY_NAME'=>TABLE_HEADING_COMPANY_NAME,
  'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
  'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
  ));
}
else
{
 $check_link='<a href="#" onclick="checkall()">Check All</a> / <a class="me-3" href="#" onclick="uncheckall()">Uncheck All</a><a href="#" onclick="DeleteSelected(\'delete\')"><i class="bi bi-trash"></i></a></font>';
 $template->assign_vars(array(
  'TABLE_HEADING_COMPANY_NAME'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_COMPANY_NAME.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_TITLE.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_STATUS.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_JOB_FEATURED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_JOB_FEATURED.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
  'TABLE_HEADING_VIEWED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][5]."' class='white'>".TABLE_HEADING_VIEWED.$obj_sort_by_clause->return_sort_array['image'][5]."</a>",
  'TABLE_HEADING_CLICKED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][6]."' class='white'>".TABLE_HEADING_CLICKED.$obj_sort_by_clause->return_sort_array['image'][6]."</a>",
  'TABLE_HEADING_APPLICATIONS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INDEED_JOBS, tep_get_all_get_params(array('sort','jobID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][7]."' class='white'>".TABLE_HEADING_APPLICATIONS.$obj_sort_by_clause->return_sort_array['image'][7]."</a>",
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 ));
}
$template->assign_vars(array(
 'HEADING_TITLE'=>sprintf(HEADING_TITLE,ucfirst($_GET['j_status'])),
 'check_link'=>($db_job_num_row>0)?$check_link:'',
 'hidden_fields'=>tep_draw_hidden_field('action1',''),
 'different_jobs'=>$different_jobs,
 'count_rows'=>$db_job_split->display_count($db_job_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBS),
 'no_of_pages'=>$db_job_split->display_links($db_job_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jobID','action','selected_box'))),
 'new_button'=>'',
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('jobs');
?>