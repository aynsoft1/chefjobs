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
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LIST_OF_JOBS);
$template->set_filenames(array('jobs' => 'list_of_jobs.htm','screener'=>'screener.htm','re_adv'=>'readvertise.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'list_of_job.js';

if(!check_login("recruiter"))
{
  $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
  tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$screener_file=false;
$re_adv_file=false;
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$_GET['j_status'] = (in_array($_GET['j_status'],array('active','expired','deleted','other')) ? $_GET['j_status'] : 'active');
unset($jInfo);
//////////////// Screener Area //////////////////////////////
if($action=='add_screener' || $action=='edit_screener')
{
  $screener_file=true;
  if($_POST['Submit']=='Save')
  {
    $error=false;
    for($i=1;$i<=NO_OF_SCREENERS;$i++)
    {
      $ques1='question_number'.$i;
      $ques2='question_number'.($i+1);
      $$ques1=$_POST[$ques1];
      $$ques2=$_POST[$ques2];
      if($$ques1=="" && $$ques2!="")
      {
        $error=true;
      break;
    }
  }
  if($error)
  {
    $messageStack->add(sprintf(ERROR_QUESTION,$i),'screener');
    for($i=1;$i<=NO_OF_SCREENERS;$i++)
    {
      $ques='question_number'.$i;
      $$ques=$_POST[$ques];
    }
  }
  else
  {
    $job_id=tep_db_input($_GET['jobID']);
    $recruiter_id=$_SESSION['sess_recruiterid'];
    $sql_data_array['job_id']=$job_id;
    $sql_data_array['recruiter_id']=$recruiter_id;
    for($i=1;$i<=NO_OF_SCREENERS;$i++)
    {
      $ques='question_number'.$i;
      $ques1='q'.$i;
      $sql_data_array[$ques1]=$_POST[$ques];
    }
    if($action=='edit_screener' && $check_row=getAnyTableWhereData(SCREENER_TABLE,"job_id='".$job_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'"))
    {
      tep_db_perform(SCREENER_TABLE, $sql_data_array,'update',"job_id='".$job_id."'");
      $messageStack->add_session(MESSAGE_SUCCESS_SCREENER_UPDATED, 'success');
    }
    else
    {
      tep_db_perform(SCREENER_TABLE, $sql_data_array);
      $messageStack->add_session(MESSAGE_SUCCESS_SCREENER_INSERTED, 'success');
    }
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS));
  }
}
$edit=false;
$job_id=tep_db_prepare_input($_GET['jobID']);
if($action=='add_screener' && $check_row=getAnyTableWhereData(JOB_TABLE,"job_id='".$job_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'",'job_id'))
{
  for($i=1;$i<=NO_OF_SCREENERS;$i++)
  {
    $ques1='question_number'.$i;
    $$ques1='';
  }
}
else if($action=='edit_screener' && $check_row=getAnyTableWhereData(SCREENER_TABLE,"job_id='".$job_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'"))
{
  $screener_file=true;
  $edit=true;
  for($i=1;$i<=NO_OF_SCREENERS;$i++)
  {
    $ques1='question_number'.$i;
    $$ques1=$check_row['q'.$i];
  }
}
else
{
  /// Something wrong
  $matter=sprintf(HACKING_ATTEMPT,$_SERVER['REMOTE_ADDR'],'add/edit screener in '.FILENAME_LIST_OF_JOBS,date('Y/m/d H:i:s'));
  sendMail($admin_email,'Someone is trying to hack',$matter,'info@erecruitmentsoftware.com');
  header('location:'.FILENAME_LIST_OF_JOBS);
  exit;
}
$question_answer_string='';
for($i=1;$i<=NO_OF_SCREENERS;$i++)
{
  $ques1='question_number'.$i;
  $question_answer_string.='
  <!--
  <tr>
  <td align="right"><b>'.INFO_TEXT_QUESTION.$i.' :</b></td>
  <td>'.tep_draw_input_field($ques1, $$ques1,'size="60"',false).'</td>
  </tr>
  -->
  <div class="form-group">
  <div class="row">
    <div class="col-md-3 col-form-label">
      <label for="my-input">'.INFO_TEXT_QUESTION.$i.'</label>
    </div>
    <div class="col-md-9">
      '.tep_draw_input_field($ques1, $$ques1,'size="60" class="form-control"',false).'
    </div>
  </div>
</div>
  ';
}
}
else
{
  ////////////////////////////
  $whereClause="";

  $today=date("Y-m-d H:i:s");
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
  if(isset($_SESSION['sess_recruiteruserid']))
  $whereClause.=" and j.recruiter_user_id='".$_SESSION['sess_recruiteruserid']."'";
  else
  $whereClause.=" and j.recruiter_id='".$_SESSION['sess_recruiterid']."'";
  ////////////////
  $different_jobs='<a class="me-2" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=active" class="hm_color">'.INFO_TEXT_ACTIVE_JOBS.'</a>';

  $different_jobs.='<a class="me-2" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=expired" class="hm_color">'.INFO_TEXT_EXPIRED_JOBS.'</a>';

  $different_jobs.='<a class="me-2" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=deleted" class="hm_color">'.INFO_TEXT_DELETED_JOBS.'</a>';

  $different_jobs.='<a class="" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=other" class="hm_color">'.INFO_TEXT_OTHER_JOBS.'</a>';
  /////////////
  if(tep_not_null($action))
  {
    switch ($action)
    {
      case 'confirm_screener_delete':
        $job_id=$_GET['jobID'];
        if($check_row=getAnyTableWhereData(SCREENER_TABLE,"job_id='".$job_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'"))
        {
          tep_db_query("delete from ".SCREENER_TABLE." where job_id='".$job_id."'");
          $messageStack->add_session(MESSAGE_SUCCESS_SCREENER_DELETED, 'success');
        }
        else
        {
          $messageStack->add_session(MESSAGE_UNSUCCESS_SCREENER_DELETED, 'error');
        }
        tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,tep_get_all_get_params(array('action','sort','jobID'))));
      break;
      case 'undelete_job':
        $job_id=$_GET['jobID'];
        if($check_row=getAnyTableWhereData(JOB_TABLE,"job_id='".$job_id."' and deleted IS NOT NULL"))
        {
           tep_db_query("update ".JOB_TABLE." set deleted=NULL,updated='".$today."' where job_id='".$job_id."'");
          $messageStack->add_session(MESSAGE_SUCCESS_JOB_UNDELETED, 'success');
        }
        else
        {
          $messageStack->add_session(MESSAGE_UNSUCCESS_SCREENER_DELETED, 'error');
        }
        tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,tep_get_all_get_params(array('action','sort','jobID'))));

	break;
      case 'confirm_delete':
        if($check_row=getAnyTableWhereData(JOB_TABLE," job_id='".(int)$_GET['jobID']."' and recruiter_id='".$_SESSION['sess_recruiterid']."'",'job_id'))
        {
          $job_id=(int)$_GET['jobID'];
          if($_GET['j_status']=='deleted') // physically delete all data.
          {
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
                  if(is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$row12['attachment_file']))
                  {
                    @unlink(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$row12['attachment_file']);
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
            tep_db_query("delete from ".JOB_POINT_HISTORY_TABLE." where job_id='".$job_id."'");
            tep_db_query("delete from ".SCREENER_TABLE." where job_id='".$job_id."'");
            tep_db_query("delete from ".INDEED_JOB_TABLE." where job_id='".$job_id."'");
			tep_db_query("delete from ".USAJOBS_JOB_TABLE." where job_id='".$job_id."'");
            tep_db_query("delete from ".JOB_TABLE." where job_id='".$job_id."'");
          }
          else //delete flag set.
          {
            tep_db_query("update ".JOB_TABLE." set deleted='$today' where job_id='".$job_id."'");
          }
        }
        $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
        tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,tep_get_all_get_params(array('action','sort','jobID'))));
      break;
      case 'job_active':
        case 'job_inactive':
          tep_db_query("update ".JOB_TABLE." set job_status='".($action=='job_active'?'Yes':'No')."' where job_id='".$_GET['jobID']."'");
          $messageStack->add_session(MESSAGE_SUCCESS_STATUS_UPDATED, 'success');
          tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,tep_get_all_get_params(array('action'))));
        break;
        case 'readv_job':
          $job_id=tep_db_prepare_input($_GET['jobID']);
          if(!$check_job_row=getAnyTableWhereData(JOB_TABLE,"job_id='".$job_id."' and expired <= '".date("Y-m-d H:i:s")."' and recruiter_id='".$_SESSION['sess_recruiterid']."'",'job_id'))
          {
            $messageStack->add_session(MESSAGE_JOB_ERROR, 'error');
            tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS));
          }
          $re_adv_file=true;
        break;
        case 'confirm_readv_job':
          $job_id=tep_db_prepare_input($_GET['jobID']);
          if(!$check_job_row=getAnyTableWhereData(JOB_TABLE,"job_id='".$job_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'",'job_id'))
          {
            $messageStack->add_session(MESSAGE_JOB_ERROR, 'error');
            tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS));
          }
          $re_adv_file=true;
          include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
          $obj_account=new recruiter_accounts();
          $total_job=$obj_account->allocated_amount['job'];
          $total_point_enjoyed=$obj_account->enjoyed_amount['job'];
          if($total_job!="Unlimited")
          if($total_job >= $total_point_enjoyed)
          {
            if($total_job == $total_point_enjoyed)
            {
              $messageStack->add_session(sprintf(MESSAGE_JOB_UNSUCCESS_READVERTISED2,($total_job-$total_point_enjoyed)), 'error');
              tep_redirect(tep_href_link(FILENAME_SUBSCRIPTION_ERROR));
            }
            //$total_point_enjoyed1=$total_point_enjoyed+points($_POST['TR_vacancy_period']);
            $total_point_enjoyed1=$total_point_enjoyed+1;
            if($total_job < $total_point_enjoyed1)
            {
              $messageStack->add_session(sprintf(MESSAGE_JOB_UNSUCCESS_READVERTISED1,($total_job-$total_point_enjoyed)), 'error');
              tep_redirect(tep_href_link(FILENAME_SUBSCRIPTION_ERROR));
            }
          }
          if($obj_account->check_subscription($_GET['jobID'])) //if true
          {
            $vacancy_period=tep_db_prepare_input($_POST['TR_vacancy_period']);
            $vacancy_added_date=tep_db_prepare_input($_POST['TR_year'].'-'.$_POST['TR_month'].'-'.$_POST['TR_date']);
            if($obj_account->re_advertise($_GET['jobID'],$vacancy_period,$vacancy_added_date)) //if true
            {
              $messageStack->add_session(MESSAGE_JOB_SUCCESS_READVERTISED, 'success');
            }
            else
            {
              $messageStack->add_session(MESSAGE_JOB_UNSUCCESS_READVERTISED, 'error');
              tep_redirect(tep_href_link(FILENAME_SUBSCRIPTION_ERROR));
            }
          }
          else
          {
            $messageStack->add_session(MESSAGE_JOB_UNSUCCESS_READVERTISED, 'error');
            tep_redirect(tep_href_link(FILENAME_SUBSCRIPTION_ERROR));
          }
          tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS));
        break;
      }
    }
    //////////////////
    ///only for sorting starts
    $sort_array=array("j.job_reference","j.job_title","j.re_adv","j.expired", "j.job_status",'s.viewed','s.clicked','s.applications');
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
    $obj_sort_by_clause=new sort_by_clause($sort_array,'j.re_adv desc');
    $order_by_clause=$obj_sort_by_clause->return_value;
    //print_r($obj_sort_by_clause->return_sort_array['name']);
    //print_r($obj_sort_by_clause->return_sort_array['image']);

    ///only for sorting ends

    $db_job_query_raw = "select j.job_id, j.recruiter_id as rec_id, j.job_reference, j.deleted, j.job_title, j.re_adv, j.expired, j.job_status, s.viewed, s.clicked, s.applications from " . JOB_TABLE . " as j left join ".JOB_STATISTICS_TABLE." as s on ( j.job_id=s.job_id or s.job_id is NULL ) where $whereClause order by ".$order_by_clause;
    //echo $db_job_query_raw;
    $db_job_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LIST_OF_JOBS, $db_job_query_raw, $db_job_query_numrows);
    $db_job_query = tep_db_query($db_job_query_raw);
    $db_job_num_row = tep_db_num_rows($db_job_query);
    if($db_job_num_row > 0)
    {
      $alternate=1;
      while ($job = tep_db_fetch_array($db_job_query))
      {
        if ( (!isset($_GET['jobID']) || (isset($_GET['jobID']) && ($_GET['jobID'] == $job['job_id']))) && !isset($jInfo) && (substr($action, 0, 3) != 'new'))
        {
          $jInfo = new objectInfo($job);
        }
        if ( (isset($jInfo) && is_object($jInfo)) && ($job['job_id'] == $jInfo->job_id) )
        {
          $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);

          $row_selected=' class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
        }
        else
        {
          $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$job['job_id'] . '\'"';
          $action_image='<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$job['job_id']. '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
        }
        $alternate++;
        if ($job['job_status'] == 'Yes')
        {
          $status='<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$job['job_id'].'&action=job_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_JOB_INACTIVATE, 32, 20) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_JOB_ACTIVE, 32, 20);
        }
        else
        {
          $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_JOB_INACTIVE, 32, 20) . '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$job['job_id'] . '&action=job_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_JOB_ACTIVATE, 32, 20) . '</a>';
        }
     //if($job['deleted']!=NULL)
	//echo 'deleted';
        $template->assign_block_vars('jobs', array( 'row_selected' => $row_selected,
        'reference' => tep_db_output($job['job_reference']),
        'title' => tep_db_output($job['job_title']),
        'inserted' => tep_date_veryshort(tep_db_output($job['re_adv'])),
        'expired' => tep_date_veryshort(tep_db_output($job['expired'])),
        'status' => $status,
        'viewed' => tep_db_output($job['viewed']),
        'clicked' => tep_db_output($job['clicked']),
        'applications' =>'<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS, 'jobID='.$job['job_id']).'">'.tep_db_output($job['applications']).'</a>',
        'action' => $action_image,
        'row_selected' => $row_selected
      ));
    }
  }
  /////
}
$RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($action)
{
  case 'delete_screener':
    /* $heading[] = array('text' => '<b>Delete Screener ?</b>');
    $contents[] = array('text' => TEXT_SCREENER_DELETE_INTRO);
    $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('page','action'))).'&action=confirm_screener_delete' . '">'.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'</a><br><a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
    $contents[] = array('text' => TEXT_DELETE_SCREENER_WARNING);  */
    $JOB_RIGHT='

	<div class="">
	  <a href="#" class="">
		'.ucfirst(tep_db_output($jInfo->job_title)).'
	  </a>
	  <div class="">
	  <div class="text-danger small mb-2">'.TEXT_SCREENER_DELETE_INTRO.'</div>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('page','action'))).'&action=confirm_screener_delete' . '"><button class="btn btn-primary w-100 mb-3">Confirm</button></a> <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('action'))) . '"><button class="btn btn-outline-secondary w-100">Cancel</button></a>
	  <div class="small text-muted mt-3">'.TEXT_DELETE_SCREENER_WARNING.'</div>
	  </div>
	</div>
';
  break;
  case 'del_job':
    /* $heading[] = array('text' => '<b>' . tep_db_output($jInfo->job_title) . '</b>');
    $contents[] = array('text' => TEXT_DELETE_INTRO);
    $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('page','action'))).'&action=confirm_delete' . '">'.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'</a><br><a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
    $contents[] = array('text' => TEXT_DELETE_WARNING);  */
    $JOB_RIGHT='

    <div class="">
	  <div class="">
    <h4 class="m-0">'.ucfirst(tep_db_output($jInfo->job_title)).'</h4>
	  <div class="small mb-2 fw-bold">'.TEXT_DELETE_INTRO.'</div>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('page','action'))).'&action=confirm_delete' . '"><button class="btn btn-primary w-100 mb-3">Confirm</button></a> <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('action'))) . '"><button class="btn btn-outline-secondary w-100">Cancel</button></a>
	  <div class="small mt-2">'.TEXT_DELETE_WARNING.'</div>
	  </div>
	</div>
';
  break;
}

if($jInfo->expired <= date("Y-m-d H:i:s"))
$re_adv='

<div class="">
  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID'))).'&jobID='.$jInfo->job_id.'&action=readv_job' . '" class=" ">'.IMAGE_READVERTISE.'</a>
</div>';
if($row_2=getAnyTableWhereData(SCREENER_TABLE,"job_id='".$jInfo->job_id."'"))
{
  $add_edit_screener=	'

<div class="">
  <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID'))).'&jobID='.$jInfo->job_id.'&action=edit_screener'.'" class="list-group-item list-group-item-action"><i class="bi bi-pencil-fill"></i> '.IMAGE_EDIT_SCREENER.' <span class="float-right"><i class="fa fa-edit text-muted" aria-hidden="true"></i></span></a>
</div>

<div class="">
  <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID'))).'&jobID='.$jInfo->job_id.'&action=delete_screener'.'" class="list-group-item list-group-item-action"><i class="bi bi-trash3"></i> '. IMAGE_DELETE_SCREENER.' <span class="float-right"><i class="fa fa-trash text-muted" aria-hidden="true"></i></span></a>
</div>
';
}
else
{
  $add_edit_screener='

  <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID'))).'&jobID='.$jInfo->job_id.'&action=add_screener'.'" class="list-group-item list-group-item-action"><i class="bi bi-person-badge-fill me-2"></i> <span class="">'.IMAGE_ADD_SCREENER.'</span></a>';
}
if ((tep_not_null($jInfo->job_id)) && !(tep_not_null($action)))
{
  $JOB_RIGHT='


  <div class="list-group list-group-item list-group-item-action" style="border-bottom-left-radius:0;border-bottom-right-radius:0;border-bottom:0px;">
  <h6 class="m-0 text-dark">'.ucfirst(tep_db_output($jInfo->job_title)).'</h6>
  <div class="small text-danger">'.TEXT_INFO_EDIT_JOB_INTRO.'</div>
  </div>

  <div class="list-group" style="border-top-left-radius:0;border-top-right-radius:0;">
	  <a href="' . tep_href_link(FILENAME_RECRUITER_VIEW_JOB,'jobID='.$jInfo->job_id).'" class="list-group-item list-group-item-action"><i class="bi bi-eye-fill me-2"></i> <span class="">'.IMAGE_VIEW_JOB.'</span></a>
	  '.$re_adv.
	  (tep_not_null($jInfo->deleted)?'<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$jInfo->job_id.'&action=undelete_job' . '" class="list-group-item list-group-item-action"><i class="bi bi-pencil-square"></i> <span class="">'.IMAGE_UNDELETE_JOB.' </a>':'<a href="' . tep_href_link(FILENAME_RECRUITER_POST_JOB,'jobID='.$jInfo->job_id).'" class="list-group-item list-group-item-action"><i class="bi bi-pencil-square me-2"></i> <span class="">'.IMAGE_EDIT_JOB.'</span></a>').'
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$jInfo->job_id.'&action=del_job' . '" class="list-group-item list-group-item-action"><i class="bi bi-trash3-fill me-2"></i> <span class="">'.IMAGE_DELETE_JOB.'</span></a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS, 'jobID='.$jInfo->job_id).'" class="list-group-item list-group-item-action" tabindex="-1" aria-disabled="true"><i class="bi bi-file-earmark-text-fill me-2"></i> <span class="">'.IMAGE_APPLICATIONS.'</span></a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT, 'jobID='.$jInfo->job_id).'" class="list-group-item list-group-item-action" tabindex="-1" aria-disabled="true"><i class="bi bi-person-check-fill me-2"></i> <span class="">'.IMAGE_SELECTED_APPLICATIONS.'</span></a>

	  '.$add_edit_screener.'

	  <a href="' . tep_href_link(FILENAME_RECRUITER_RESUME_WEIGHT, 'jobID='.$jInfo->job_id).'" class="list-group-item list-group-item-action" tabindex="-1" aria-disabled="true"><i class="bi bi-percent me-2"></i> <span class="">'.INFO_TEXT_RESUME_WEIGHT.'</span></a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT, 'jobID='.$jInfo->job_id).'" class="list-group-item list-group-item-action" tabindex="-1" aria-disabled="true"><i class="bi bi-bar-chart-fill me-2"></i> <span class="">'.IMAGE_REPORT.'</span></a>
	</div>


  <!-- below commented code
  <div class="list-group list-group-horizontal">
	  <span class=" font-weight-bold info-bg">
		'.ucfirst(tep_db_output($jInfo->job_title)).'
	  </span>
	  <div class="small text-muted">'.TEXT_INFO_EDIT_JOB_INTRO.'</div>
	  <a href="dddd" class="">ddd</a>
	  '.$re_adv.'
	  <a href="' . tep_href_link(FILENAME_RECRUITER_VIEW_JOB,'jobID='.$jInfo->job_id).'" class="">'.IMAGE_VIEW_JOB.'</a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_POST_JOB,'jobID='.$jInfo->job_id).'" class="">'.IMAGE_EDIT_JOB.'dd</a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('jobID','action'))).'&jobID='.$jInfo->job_id.'&action=del_job' . '" class="">'.IMAGE_DELETE_JOB.'</a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS, 'jobID='.$jInfo->job_id).'" class="">'.IMAGE_APPLICATIONS.'</a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT, 'jobID='.$jInfo->job_id).'" class="">'.IMAGE_SELECTED_APPLICATIONS.'</a>
	  '.$add_edit_screener.'
	  <a href="' . tep_href_link(FILENAME_RECRUITER_RESUME_WEIGHT, 'jobID='.$jInfo->job_id).'" class="">'.INFO_TEXT_RESUME_WEIGHT.'</a>
	  <a href="' . tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT, 'jobID='.$jInfo->job_id).'" class="">'.IMAGE_REPORT.'</a>
	</div>
 -->
  ';
}


if($screener_file)
{
  $template->assign_vars(array(
    'HEADING_TITLE'=>sprintf(HEADING_TITLE,$j_status),
    'hidden_fields'=>$hidden_fields.tep_draw_hidden_field('Submit','Save'),
    'question_answer_string'=>$question_answer_string,
    'button'=>'<button class="btn btn-primary" type="submit">Update</button>',//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE),
    'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
    'INFO_TEXT_SCREENER_QUESTION'=>INFO_TEXT_SCREENER_QUESTION,
    'INFO_TEXT_ADD_UPTO_FIVE'  => INFO_TEXT_ADD_UPTO_FIVE,
    'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
    'LEFT_HTML'=>'',
    'RIGHT_HTML'=>$JOB_RIGHT,
	'LEFT_HTML'=>LEFT_HTML,
    'update_message'=>$messageStack->output()));
    $template->pparse('screener');
  }
  else if($re_adv_file)
  {
    $vacancy_period_array[]=array('id'=>'',
    'text'=>INFO_TEXT_SPECIFY_VACANCY_PERIOD,
  );
  $vacancy_period_array[]=array('id'=>'One week',
  'text'=>INFO_TEXT_ONE_WEEK,
);
$vacancy_period_array[]=array('id'=>'Two weeks',
'text'=>INFO_TEXT_TWO_WEEKS,
);
$vacancy_period_array[]=array('id'=>'Three weeks',
'text'=>INFO_TEXT_THREE_WEEKS,
);
$vacancy_period_array[]=array('id'=>'One month',
'text'=>INFO_TEXT_ONE_MONTH,
);
$template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE_READV,
  'INFO_TEXT_READVERTISE'=>INFO_TEXT_READVERTISE,
  'INFO_TEXT_READVERTISE1'=>datelisting(date("Y-m-d"),"name='TR_date'","name='TR_month'","name='TR_year'",$startYear,$endYear,true),
  'INFO_TEXT_ADVERTISE_WEEKS'=>INFO_TEXT_ADVERTISE_WEEKS,
  'INFO_TEXT_ADVERTISE_WEEKS1'=>tep_draw_pull_down_menu('TR_vacancy_period', $vacancy_period_array, $TR_vacancy_period,'',true),
  // 'button'=>tep_image_submit(PATH_TO_BUTTON.'button_re_advertise.gif', IMAGE_READVERTISE).'&nbsp;<a href="javascript:window.history.back();">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>',//'<input type="button" name="re_adv" value="Back" onclick="javascript:window.history.back();">&nbsp;&nbsp;<input type="submit" name="re_adv" value="Re-advertise">',
  'button'=>'<button class="btn btn-primary" type="submit">Re-Advertise</button>&nbsp;<a href="javascript:window.history.back();"><button class="btn btn-outline-secondary">Back</button></a>',
  'form'=>tep_draw_form('re_adv', FILENAME_RECRUITER_LIST_OF_JOBS, 'jobID='.$_GET['jobID'].'&action=confirm_readv_job', 'post', 'onsubmit="return ValidateForm(this)"'),
  'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,tep_draw_pull_down_menu('TR_vacancy_period', $vacancy_period_array, $TR_vacancy_period),
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>$JOB_RIGHT,
  'LEFT_HTML'=>LEFT_HTML,
  'update_message'=>$messageStack->output()));
  $template->pparse('re_adv');
}
else
{
  // echo MAX_DISPLAY_LIST_OF_JOBS;die();
  $template->assign_vars(array(
    'HEADING_TITLE'=>sprintf(HEADING_TITLE,$_GET['j_status']),
    'QUESTION1'=>tep_draw_input_field('TR_question_number1', $TR_question_number1,'size="60" class="form-control"',true),
    'ANSWER1'=>tep_draw_radio_field('answer1', 'Yes', '', $answer1, 'id="answer1"').'&nbsp;<label for="answer1">'.INFO_TEXT_YES.'</label>&nbsp;'.tep_draw_radio_field('answer1', 'No', '', $answer1, 'id="answer11"').'&nbsp;<label for="answer11">'.INFO_TEXT_NO.'</label>&nbsp;',
    'question_answer_string'=>$question_answer_string,
    'different_jobs'=>$different_jobs,
    'TABLE_HEADING_REFERENCE'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_REFERENCE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
    'TABLE_HEADING_TITLE'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_TITLE.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
    'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
    'TABLE_HEADING_EXPIRED'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_EXPIRED.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
    'TABLE_HEADING_STATUS'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_STATUS.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
    'TABLE_HEADING_VIEWED'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][5]."' class='white'>".TABLE_HEADING_VIEWED.$obj_sort_by_clause->return_sort_array['image'][5]."</a>",
    'TABLE_HEADING_CLICKED'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][6]."' class='white'>".TABLE_HEADING_CLICKED.$obj_sort_by_clause->return_sort_array['image'][6]."</a>",
    'TABLE_HEADING_APPLICATIONS'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][7]."' class='white'>".TABLE_HEADING_APPLICATIONS.$obj_sort_by_clause->return_sort_array['image'][7]."</a>",
    'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
    'count_rows'=>$db_job_split->display_count($db_job_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBS),
    'no_of_pages'=>$db_job_split->display_links($db_job_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jobID','action'))),
    'new_button'=>'',
    'hidden_fields'=>$hidden_fields,
    'new_button'=>'',
    'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
    'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
    'LEFT_HTML'=>'',
	'LEFT_HTML'=>LEFT_HTML,
    'RIGHT_HTML'=>$JOB_RIGHT,
    'update_message'=>$messageStack->output()));
    $template->pparse('jobs');
  }
  ?>