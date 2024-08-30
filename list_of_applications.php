<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LIST_OF_APPLICATIONS);
$template->set_filenames(array('application' => 'list_of_applications.htm','application1' => 'change_status.htm','application2' => 'application_rating.htm','email'  =>'send_bulk_email.htm','preview'=>'preview_bulk_email.htm','compair_profile'=>'compair_profile.htm','app_history'=>'show_history.htm','search_applicant'=>'search_job_applicant.htm'));
include_once(FILENAME_BODY);
include_once("general_functions/weight_function.php");
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'list_of_applications.js';


if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_RECRUITER_LOGIN));
}

//$action    = (isset($_POST['action']) ? $_POST['action'] : '');
$action1            = (isset($_POST['action1']) ? $_POST['action1'] : (isset($_GET['action1'])?$_GET['action1']:''));
$application_id     = (isset($_POST['application_id']) ? $_POST['application_id'] : '');
$process_round      = (isset($_POST['process_round']) ? $_POST['process_round'] : '');
$application_status = (isset($_POST['application_status']) ? $_POST['application_status'] : '');
$search_id          = ((isset($_GET['search_id'])  ) ? $_GET['search_id'] : '');
$search             = ((isset($_GET['search'])  ) ?tep_db_input($_GET['search']): '');

if ($search_id == 'create-add-comment') {
  $notes = (isset($_POST['private_notes']) ? $_POST['private_notes'] : '');
  $commentResumeId = (isset($_POST['resume_id']) ? $_POST['resume_id'] : '');
  $currentUrlSearchId = $_GET['q'];

  $sql_data_array1 = array(
    'private_notes' => $notes,
    'resume_id' => $commentResumeId,
    'recruiter_id' => $_SESSION['sess_recruiterid']
  );

  // Your code to handle the received data goes here
  if ($row_rating1 = getAnyTableWhereData(JOBSEEKER_RATING_TABLE, " resume_id='" . $commentResumeId . "'", 'rating_id')) {
    tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array1, 'update', "rating_id='" . $row_rating1['rating_id'] . "'");
    $messageStack->add_session('comment updated successfully', 'success');
  } else {
    $messageStack->add_session('comment added successfully', 'success');
    tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array1);
  }

  tep_redirect(FILENAME_RECRUITER_LIST_OF_APPLICATIONS.'?search_id='.$currentUrlSearchId);

  return true;
}

if($search=='applicant' && isset($_GET['jobID']))
{
 $action1='search_applicant';
}
if(isset($_POST['jobID']))
{
 $job_id         =(int) (isset($_POST['jobID']) ? $_POST['jobID'] : '');
}
elseif(isset($_GET['jobID']))
{
 $job_id         = (int) (isset($_GET['jobID']) ? $_GET['jobID'] : '');
}

/***********///////////////////
if(isset($_GET['query_string3'])  && $_GET['query_string3']!='')
{
 $search_id =check_data1($_GET['query_string3'],"*=*","application","application_id");
}

/************************////////////////////////////

if(isset($_GET['query_string']) && isset($_GET['query_string2']))
{
 $application_id =check_data($_GET['query_string'],"=","application","application_id");
 if($application_id>0)
 $action1 =check_data1($_GET['query_string2'],"*=*","action","action");
}
if(tep_not_null($search_id))
{
 if($row_check_search=getAnyTableWhereData(APPLICATION_TABLE ,"application_id='".tep_db_input($search_id)."'","id"))
  $application_id =$row_check_search['id'];
 else
  $search_id='';
}
$output='';
if(isset($_POST['application_id']) && isset($_POST['application_id']))
{
  $application_id=tep_db_prepare_input($_POST['application_id']);
  if(isset($_POST['output']) && isset($_POST['data_delete']) )
  {
   $output=tep_db_prepare_input($_POST['output']);
   $data_delete=tep_db_prepare_input($_POST['data_delete']);
   if($data_delete=='ResultDelete' && $output=='ajax')
   $action1='applicant_confirm_delete';
  }	
}
if(tep_not_null($application_id))
{
 if($action1=='applicant_confirm_delete')
 {
  if($delete_check=getAnyTableWhereData(APPLICATION_TABLE . " as a left outer join  ".JOBSEEKER_TABLE." as j on(a.jobseeker_id=j.jobseeker_id) left outer join  ".JOB_TABLE. " as jb on (a.job_id=jb.job_id )"," a.id='".tep_db_input($application_id)."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' ","a.id,a.job_id,a.jobseeker_apply_id,a.jobseeker_id,resume_id"))
  {
    /////////////////////////////////////////////////////////////////////////////////////
   tep_db_query("update ".JOB_STATISTICS_TABLE." set applications=applications-1 where job_id='".$delete_check['job_id']."'");
   tep_db_query("delete from ".APPLY_TABLE." where jobseeker_id='".$delete_check['jobseeker_id']."' and  id='".$delete_check['jobseeker_apply_id']."'");
   //\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\APPLICATION\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
   $result_applicant_interaction=tep_db_query("select id,application_id,attachment_file from ".APPLICANT_INTERACTION_TABLE." where application_id='".$delete_check['id']."'");
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
    tep_db_query("delete from ".APPLICANT_INTERACTION_TABLE." where id='".$delete_check['id']."'");
   }
   tep_db_free_result($result_applicant_interaction);
   tep_db_query("delete from ".APPLICANT_STATUS_TABLE." where application_id='".$delete_check['id']."'");
   tep_db_query("delete from ".APPLICATION_RATING_TABLE." where application_id='".$delete_check['id']."'");
   tep_db_query("delete from ".APPLICATION_TABLE." where id='".$delete_check['id']."'");
   if($row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".$delete_check['resume_id']."'",'rating_id'))
   tep_db_query("delete from ".JOBSEEKER_RATING_TABLE." where rating_id='".$row_rating['rating_id']."'");
   /////////////////////////////////////////////////////////////////////////////////////
   if( $output=='ajax')
   die('success');
   $messageStack->add(MESSAGE_SUCCESS_DELETED, 'success');
   unset($application_id);
   $action1='';
  }
  else
  {
   unset($application_id);
   $action1='';
  }
 }
 else
 {
  if(!$row_check=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_TABLE." as j, ".JOB_TABLE. " as jb "," a.id='".$application_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id ","a.id,a.job_id"))
  {
   $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
   tep_redirect(FILENAME_RECRUITER_LIST_OF_APPLICATIONS);
  }
  $job_id=(int)$row_check['job_id'];
 }
}
if(!$row_check_1=getAnyTableWhereData(JOB_TABLE. " as jb "," jb.job_id='".$job_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' ","jb.job_id,jb.job_title"))
{
 $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);
}
$row_company=getAnyTableWhereData(RECRUITER_TABLE." as r" ,"r.recruiter_id ='".$_SESSION['sess_recruiterid']."'","r.recruiter_company_name");
define('APPLICATION_REPLY_MAIL',tep_db_output($row_company['recruiter_company_name']."@".SITE_TITLE));

//print_r($_POST);
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
unset($aInfo);
$whereClause="a.job_id=".tep_db_input($job_id)." and ";
if(tep_not_null($search_id))
{
 $whereClause.=" a.id=".tep_db_input($application_id)." and ";
}

$search_application_id='';
//print_r($row);
$from_list='';
if(!tep_not_null($action1) || ($action1=='applicant_delete' && tep_not_null($application_id)) )
{
 $from_list='Y';

 $first_name    = tep_db_input($_POST['first_name']);
 $last_name     = tep_db_input($_POST['last_name']);
 $email_address = tep_db_input($_POST['TNEF_email_address']);
 $experience=tep_db_input($_POST['experience']);
 $search_application_id = tep_db_input($_POST['TR_application_id']);
 $field=tep_db_prepare_input($_POST['field']);
 $order=tep_db_prepare_input($_POST['order']);
 $lower=(int)tep_db_prepare_input($_POST['lower']);
 $higher=(int)tep_db_prepare_input($_POST['higher']);

 $searchClause='';
 $record_whereClause1='';
 
 if(tep_not_null($search_application_id))
 {
  $display_search_key="application id : ".tep_db_output($search_application_id).",";
  $hidden_fields.=tep_draw_hidden_field('TR_application_id',$search_application_id);
  $searchClause  = " a.application_id ='".tep_db_input($search_application_id)."' and ";
 }
 if(tep_not_null($first_name))
 {
  $display_search_key="First Name: ".tep_db_output($first_name).",";
  $hidden_fields.=tep_draw_hidden_field('first_name',$first_name);
  $searchClause  = " j.jobseeker_first_name like '%".tep_db_input($first_name)."%' and ";
 }
 if(tep_not_null($last_name))
 {
  $display_search_key.=" Last Name: ".tep_db_output($last_name).",";
  $hidden_fields.=tep_draw_hidden_field('last_name',$last_name);
  $searchClause  .= " j.jobseeker_last_name like '%".tep_db_input($last_name)."%' and";
 }

if(tep_not_null($experience))
{
$hidden_fields.=tep_draw_hidden_field('experience_year',$experience_year);
$explode_string=explode("-",$experience);
$work_experince=$explode_string[0];//get_name_from_table(EXPERIENCE_TABLE,'id', 'min_experience',tep_db_input($explode_string[0]));
$work_experince1 = $explode_string[1];
$searchClause.=" ( jr1.experience_year >= '".$work_experince."' and jr1.experience_year <= '".$work_experince1."' ) and ";
}

 if(tep_not_null($email_address))
 {
  $display_search_key.=" Email Address: ".tep_db_output($email_address).",";
  $hidden_fields.=tep_draw_hidden_field('TNEF_email_address',$email_address);
  $searchClause  .= " jl.jobseeker_email_address ='".tep_db_input($email_address)."' and ";
 }
 if(tep_not_null($searchClause))
 {
  $whereClause.=$searchClause;
 }

 if(tep_not_null($process_round))
 {
  if(tep_not_null($searchClause))
  {
   $record_table=APPLICANT_STATUS_TABLE." as aps left join ".APPLICATION_TABLE." as a on (aps.application_id=a.id) left join ".JOBSEEKER_TABLE." as j on (a.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=j.jobseeker_id)";
   $record_whereClause=" aps.id in (select max(ap.id)from ".APPLICANT_STATUS_TABLE."  as ap left outer join ".APPLICATION_TABLE." as a on (a.id=ap.application_id)  left join ".JOBSEEKER_TABLE." as j on (a.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=j.jobseeker_id) where  ".$searchClause." a.job_id='".$job_id."' and process_round='".$process_round."'   GROUP BY ap.application_id,ap.process_round) and  ".$searchClause." process_round='".$process_round."'";
  }
  else
  {
   $record_table=APPLICANT_STATUS_TABLE." as aps left join ".APPLICATION_TABLE." as a on (aps.application_id=a.id)";
   $record_whereClause=" aps.id in (select max(ap.id)from ".APPLICANT_STATUS_TABLE."  as ap left outer join ".APPLICATION_TABLE." as a on (a.id=ap.application_id)  where a.job_id='".$job_id."' and process_round='".$process_round."'  GROUP BY ap.application_id,ap.process_round) and  process_round='".$process_round."' ";
  }
  $no_of_new_application    = no_of_records($record_table,$record_whereClause." and cur_status = 1",'aps.id');
  $no_of_process_application= no_of_records($record_table,$record_whereClause." and cur_status = 2",'aps.id');
  $no_of_select_application = no_of_records($record_table,$record_whereClause." and cur_status = 3",'aps.id');
  $no_of_reject_application = no_of_records($record_table,$record_whereClause." and cur_status = 5",'aps.id');
  $no_of_waiting_application= no_of_records($record_table,$record_whereClause." and cur_status = 4",'aps.id');
  // $no_of_total_application  = no_of_records(APPLICANT_STATUS_TABLE." as aps left join ".APPLICATION_TABLE." as a on (aps.application_id=a.id)",$record_whereClause." ",'id');
  $no_of_total_application= $no_of_new_application+$no_of_process_application+$no_of_select_application + $no_of_reject_application+$no_of_waiting_application;
 }
 if(tep_not_null($process_round) ||  tep_not_null($application_status))
 {
  $whereClause1=$whereClause;
  if(tep_not_null($application_status))
   $whereClause1=" ap.cur_status ='".$application_status."' and ";
  if(tep_not_null($process_round))
   $whereClause1.=" ap.process_round='".$process_round."' and ";

  if(tep_not_null($searchClause))
   $whereClause .=" a.id in (select distinct(ap.application_id) from ".APPLICANT_STATUS_TABLE." as ap left join ".APPLICATION_TABLE." as a on (a.id = ap.application_id) left join ".JOBSEEKER_TABLE." as j on (a.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=j.jobseeker_id) where  $searchClause $whereClause1 ap.id in (select max( ap.id ) from ".APPLICANT_STATUS_TABLE." as ap left join ".APPLICATION_TABLE." as a on ( a.id = ap.application_id ) where job_id='".tep_db_input($job_id)."' group by ap.application_id, ap.process_round ) and job_id='".tep_db_input($job_id)."') and ";
  else
  $whereClause .=" a.id in (select distinct(ap.application_id) from ".APPLICANT_STATUS_TABLE." as ap left join ".APPLICATION_TABLE." as a on (a.id = ap.application_id) where $whereClause1 ap.id in (select max( ap.id ) from ".APPLICANT_STATUS_TABLE." as ap left join ".APPLICATION_TABLE." as a on ( a.id = ap.application_id ) where job_id='".tep_db_input($job_id)."' group by ap.application_id, ap.process_round ) and job_id='".tep_db_input($job_id)."') and ";
 }

 $table_names  = APPLICATION_TABLE." as a  left outer join ".JOBSEEKER_TABLE." as j on (a.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_LOGIN_TABLE. " as jl on (j.jobseeker_id=jl.jobseeker_id ) left outer join ".JOB_TABLE. " as jb on (a.job_id=jb.job_id ) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) left join ".ZONES_TABLE." as z on(j.jobseeker_state_id=z.zone_id or z.zone_id is NULL)" ;
 $table_names1 = APPLICATION_TABLE." as a  left outer join ".JOBSEEKER_TABLE." as j on (a.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_LOGIN_TABLE. " as jl on (j.jobseeker_id=jl.jobseeker_id ) left outer join ".JOB_TABLE. " as jb on (a.job_id=jb.job_id ) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) ";

 $field_names  = "a.*, jb.job_title, jb.job_reference, concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as full_name,j.jobseeker_country_id,jr1.experience_year, jr1.work_experince, jr1.experience_month ,concat(case when j.jobseeker_city='' then '' else concat(j.jobseeker_city,', ') end, if(j.jobseeker_state_id,z.".TEXT_LANGUAGE."zone_name,j.jobseeker_state)) as location,jr1.jobseeker_photo";

 $whereClause_count=$whereClause. "  jb.recruiter_id='".$_SESSION['sess_recruiterid']."'";
 $whereClause.= "  jb.recruiter_id='".$_SESSION['sess_recruiterid']."'";

 $query1 = "select count(distinct(a.application_id)) as x1 from $table_names1 where $whereClause_count";
 //echo "<br>$query1";//exit;   print_r($_POST);
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 //print_r($tt_row);
 $x1=$tt_row['x1'];
 ///only for sorting starts
 $sort_array=array('a.application_id','j.jobseeker_first_name','a.inserted','sum(ap.application_id)','a.applicant_select');
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'a.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 $see_before_page_number_array=see_before_page_number($sort_array,$field,'a.inserted ',$order,'desc',$lower,'0',$higher,MAX_DISPLAY_LIST_OF_APPLICATIONS);//MAX_DISPLAY_LIST_OF_APPLICATIONS
 //print_r($see_before_page_number_array);
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $template->assign_vars(array(
  'TABLE_HEADING_APPLICATION_NO'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_APPLICATION_NO.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_COMMENT'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_COMMENT.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_APPLICANT_SELECT'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][4]."','".$lower."');\"><u>
    ".tep_image(PATH_TO_IMAGE.'icon_correct.gif',IMAGE_SELECTED).'
  </u>'.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
  ));

  $totalpage=ceil($x1/$higher);
  $query = "select $field_names from $table_names where $whereClause ORDER BY $order_by_clause limit $lower,$higher ";
  $result=tep_db_query($query);
  //echo "<br>$query";//exit;
  $x=tep_db_num_rows($result);
  //echo $x;exit;
  $pno= ceil($lower+$higher)/($higher);
  if($application_id)
  {
   $app_info=true;
   $query2="select a.id  from  ".$table_names1."where  $whereClause   ORDER BY $order_by_clause limit $lower,$higher ";
   $result2=tep_db_query($query2);
   $total_ids=tep_db_num_rows($result2);
   //$row_check =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as ap "," application_id='".$application_id."'",'*');
   if($total_ids>0)
   while ($application_1 = tep_db_fetch_array($result2))
   {
    if($application_id==$application_1['id'])
    {
     $app_info=false;
     break;
    }
   }
   tep_db_free_result($result2);
   if($app_info)
    unset($application_id);
  }
  else
   unset($application_id);
  if($x > 0 && $x1 > 0)
  {
   $alternate=1;
   while ($application = tep_db_fetch_array($result))
   {
    $query_string1=encode_string("application_id=".$application['id']."=application_id");
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    if ( (!isset($application_id) || (isset($application_id) && ($application_id == $application['id']))) && !isset($aInfo) )
    {
     $aInfo = new objectInfo($application);
    }
    $row_selected1='';
    if ( (isset($aInfo) && is_object($aInfo)) && ($application['id'] == $aInfo->id) )
    {
     $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
     $row_selected=' class="bg-info dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
     $row_color='class="alert-success"';
     $hidden_fields.=tep_draw_hidden_field('application_id',$aInfo->id).tep_draw_hidden_field('action1','');
    }
    else
    {
     $row_selected1=' onclick="set_action('.$application['id'].')"';
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
     $action_image='<a href="#"  onclick="set_action('.$application['id'].')">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
     $row_color='bgcolor="#fafafa"';
     //$row_color='bgcolor="#999999"';
    }
    if($application['applicant_select']=='Yes')
    //  $applicant_selected=tep_image(PATH_TO_IMAGE.'icon_selected.gif',IMAGE_SELECTED);
     //$applicant_selected='<span class="badge text-bg-success"><i class="bi bi-check"></i> Selected</span>';
     $applicant_selected ='<a class="btn btn-sm btn-text border me-3 rounded-5 btn-ats" href="#" onclick="set_action1(\''.$application['id'].'\',\'applicant_selection_reset\')">'.IMAGE_APPLICANT_SELECTION_RESET.'</a> <div><span class="btn btn-sm btn-text border bg-success me-3 rounded-5 text-white">Selected</span></div> ';
    else
     $applicant_selected ='<a class="btn btn-sm btn-text border me-3 rounded-5 btn-ats" href="#" onclick="set_action1(\''.$application['id'].'\',\'applicant_selected\')">Select candidate</a>';
    //$status=get_status_current_round($application['id'],$process_round);
    //$total_rank=get_application_rating($application['id']);
    $experience_string='';
    if($application['experience_year']>1)
 	   $experience_string=$application['experience_year'].' Years ';
    elseif($application['experience_year']>0)
     $experience_string=$application['experience_year'].' Year ';
    if($application['experience_month']>1)
     $experience_string.=$application['experience_month'].' Months';
    elseif($application['experience_month']>0)
     $experience_string.=$application['experience_month'].' Month';
    $row_experience =getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE." as jr2"," jr2.resume_id='".$application['resume_id']."' ORDER BY r2_id desc limit 0,1 ",'jr2.company,jr2.job_title');
   // $row_education  =getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE." as jr3 left join ".EDUCATION_LEVEL_TABLE." as ed on (ed.id=jr3.degree)"," jr3.resume_id='".$application['resume_id']."' ORDER BY r3_id desc limit 0,1 ",'ed.education_level_name,jr3.specialization ');
    $round=get_current_round_status($application['id'],$process_round);
    $match_percentage=tep_db_output(get_resume_weight($application['resume_id'],$application['job_id']));
    
    if (tep_not_null($application['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$application['jobseeker_photo'])) {
      $seeker_resume_photo = '<a class="mini-profile-img-ats" href="#" 
                              onclick="'.js_popup(PATH_TO_PHOTO.$application['jobseeker_photo'],SITE_TITLE).'">
                              '.tep_image(FILENAME_IMAGE."?size=400&image_name=".PATH_TO_PHOTO.$application['jobseeker_photo'],
                                tep_db_output(SITE_TITLE)
                                ).'</a>';
    }else{
      $seeker_resume_photo = defaultProfilePhotoUrl($application['full_name'],false,100);
    }

    $applicationPrimaryId = $application['id'];
    $query_string3=encode_string("application_id=".$application['resume_id']."=application_id");
    $query_stringc=encode_string("application=".$application['id']."=application_id");

    $template->assign_block_vars('jobs', array( 'row_selected' => $row_selected,
     'row_selected1' => $row_selected1,
	 'row_color'=>$row_color,
     'application_id' => tep_db_output($application['application_id']),
     'd_id' => tep_db_output($application['id']),
     'check_box' => tep_draw_checkbox_field('TR_applicant_id[]',$application['application_id'],'','','lass="form-check-input"'),
     'applicant_selected' =>$applicant_selected,
	  'edit_round'   =>	'<a class="btn btn-sm btn-text border me-3 rounded-5 btn-ats"   href="#"  onclick="set_action1(\''.$application['id'].'\',\'change_status\')" >'.INFO_TEXT_EDIT_ROUND.'</a>' ,
	  'contact'      =>	'<a class="btn btn-sm btn-text border me-3 rounded-5 btn-ats"   href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string=".$query_stringc.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" >'.IMAGE_CONTACT.'</a>' ,
	  'menu_edit_round'=>	'<a class="dropdown-item" href="#" onclick="set_action1(\''.$application['id'].'\',\'change_status\')" >'.INFO_TEXT_M_EDIT_ROUND.'</a>' ,
	  'menu_selection_histor'=>	'<a class="dropdown-item" href="#" onclick="set_action1(\''.$application['id'].'\',\'show_history\')" >'.INFO_TEXT_SELECTION_HISTORY.'</a>' ,
	  'menu_add_comment'=>	'<a class="dropdown-item" href="#"   onclick="addComment('.$application['resume_id'].')"  >'.INFO_TEXT_ADD_COMMENT.'</a>' ,
	  'menu_rate_applicant'=>	'<a class="dropdown-item" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string3).'" target="_blank" >'.IMAGE_RATING.'</a>' ,
	  'menu_delete_applicant'=>	'<a class="dropdown-item"  onclick="delete_record(\''.$application['id'].'\');" >'.INFO_TEXT_DELETE.'</a>' ,
	  'menu_match_detail'=>	'<a class="dropdown-item" onclick="window.open(\''.tep_href_link(FILENAME_RECRUITER_RESUME_MATCH,'jobID='.$job_id.'&application_id='.$application['application_id'] ).'\',\''.date('his').'\',\'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=300,height=300,left = 10,top = 10\');">'.INFO_TEXT_MATCH_DETAIL.'</a>' ,

    //  'name' => tep_db_output($application['full_name']),//"<a href='".tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string)."'>". tep_db_output($application['full_name'])."</a>",
     'name' => "<a href='".tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string3.'&app_num='.$applicationPrimaryId)."'>". tep_db_output($application['full_name'])."</a>",
   //  'inserted' => tep_date_veryshort(tep_db_output($application['inserted'])),
   //  'source'             => (($application['source']=='search_resume')?'Search Resume':'Job Apply'),
   ///  'action' => $action_image,
   //  'experience'=>tep_not_null($experience_string)?$experience_string:'---',
   //  'company_name'=>tep_not_null($row_experience['company'])?tep_db_output($row_experience['company']):'---',
     'job_title'=>tep_not_null($row_experience['job_title'])?tep_db_output($row_experience['job_title']):'---',
    // 'education'=>tep_db_output($row_education[TEXT_LANGUAGE.'education_level_name']).(tep_not_null($row_education['specialization'])?' ('.tep_db_output($row_education['specialization']).')':''),
    // 'location'=>tep_not_null($application['location'])?tep_db_output($application['location']):'---',
     'photo'=> $seeker_resume_photo,
    //  'photo'=>(tep_not_null($application['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$application['jobseeker_photo'])
    //           ?'<a class="mini-profile-img" href="#" onclick="'.js_popup(PATH_TO_PHOTO.$application['jobseeker_photo'],SITE_TITLE).'">
    //           '.tep_image(FILENAME_IMAGE."?size=400&image_name=".PATH_TO_PHOTO.$application['jobseeker_photo'],tep_db_output(SITE_TITLE)).'</a>'
    //           :defaultProfilePhotoUrl($application['full_name'],false,100)),
     'match'=>$match_percentage,
     'round'=>tep_not_null($round) ? $round : 'None',
     ));
     $alternate++;
     $lower = $lower + 1;
   }
   see_page_number();
   $plural=($x1=="1")?INFO_TEXT_APPLICANT:INFO_TEXT_APPLICANTS;
   $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_MATCHED." $x1 ".$plural."."));

  }
  else
  {
    $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br>&nbsp;&nbsp;&nbsp;"));
  }
  tep_db_free_result($result1);
  tep_db_free_result($result);
 //////////////Right Round/////////////
  $db_round_query_raw = "select  sr.".TEXT_LANGUAGE."round_name,sr.id  from ".SELECTION_ROUND_TABLE." as sr  order by  value ";
  //echo $db_round_query_raw;
  $db_round_query = tep_db_query($db_round_query_raw);
  $db_round_num_row = tep_db_num_rows($db_round_query);
  if($db_round_num_row > 0)
  {
   $round_array=array();
   $round_array[]=array('id'=>'','name'=>INFO_TEXT_ALL_APPLICANTS);
   while ($s_round = tep_db_fetch_array($db_round_query))
   {
    $round_array[]=array('id'=>$s_round['id'],'name'=>$s_round[TEXT_LANGUAGE.'round_name']); //.' '.INFO_TEXT_ROUND

   }
  }
  tep_db_free_result($db_round_query);

  $heading2[]  = array('params'=>'','text'  => '<div class="list-group mt-2"><div class="card-header fw-bold" style="padding-left:15px;">'.INFO_TEXT_VIEW_CURRENT_STATUS.'</div></div>');
  $total_round_array=count($round_array);
  for($i=0;$i<$total_round_array;$i++)
  {
   $contents2[] = array('align' => 'left','params'=>'onmouseout="rowOutEffect(this)"', 'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="view_status(\''.$round_array[$i]['id'].'\')" >'.$round_array[$i]['name'].'</a>');

       $template->assign_block_vars('round_link', array( 'round_name' => '<a class="round-name"  href="#" onclick="view_status(\''.$round_array[$i]['id'].'\')">'.$round_array[$i]['name'].'</a>'));

  }
  //////////////Shambhu/////////////
}
/////
if(tep_not_null($action1))
{
 switch($action1)
 {
  case 'applicant_selected':
     $row_select =getAnyTableWhereData(APPLICATION_TABLE," id='".$application_id."'");
     if($row_select['applicant_select']!='Yes')
     {
      tep_db_query("update ".APPLICATION_TABLE." set applicant_select='Yes',selected_date=now()  where id='".$application_id."'");
     }
     $messageStack->add_session(MESSAGE_SUCCESS_APPLICANT_SELECT, 'success');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
   break;
  case 'applicant_selection_reset':
     $row_select =getAnyTableWhereData(APPLICATION_TABLE," id='".$application_id."' ");
     if($row_select['applicant_select']!='No')
     {
      tep_db_query("update ".APPLICATION_TABLE." set applicant_select='No' where id='".$application_id."'");
     }
     $messageStack->add_session(MESSAGE_SUCCESS_APPLICANT_SELECTION_RESET, 'success');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
   break;
  case 'set_status':
   $process_round=(int)tep_db_prepare_input($_POST['round']);
   $row =getAnyTableWhereData(APPLICANT_STATUS_TABLE," application_id='".$application_id."' and process_round='".tep_db_input($process_round)."' order by inserted desc,id desc ",'id,cur_status,process_round');
   //$row =getAnyTableWhereData(APPLICANT_STATUS_TABLE," application_id='".$application_id."' order by inserted desc,id desc ");
   $cur_status=tep_db_prepare_input($_POST['new_status']);
   $pre_status=tep_db_prepare_input($row['cur_status']);
   //$process_round=tep_db_prepare_input($row['process_round']);

   if($pre_status==$cur_status )
   {
	   $messageStack->add_session(ERROR_STATUS_ALREADY_SET, 'error');
   // tep_redirect(FILENAME_RECRUITER_LIST_OF_APPLICATIONS);
   }
   else
   {
    $sql_data_array=array('application_id'=>$application_id,
                         'cur_status'=>$cur_status,
                         'pre_status'=>$pre_status,
                         'process_round'=>$process_round,
                         );
    $sql_data_array['inserted']='now()';
	   $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    tep_db_perform(APPLICANT_STATUS_TABLE, $sql_data_array);
   }
   $query_string=encode_string("application=".$application_id."=application_id");
   $query_string2=encode_string("action*=*change_status*=*action");
   tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'query_string='.$query_string.'&query_string2='.$query_string2.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
  break;
  case 'set_rank':
   $process_round=tep_db_prepare_input($_POST['process_round']);
   $point=tep_db_prepare_input($_POST['rate_it']);
   $application_id=tep_db_prepare_input($_POST['application_id']);
   $sql_data_array=array('application_id'=>$application_id,
                         'point'=>$point,
                         'round_id'=>$process_round,
                         );
   if(!$row1 =getAnyTableWhereData(APPLICATION_RATING_TABLE," application_id='".$application_id."' && round_id='".$process_round."'"))
   {
    $sql_data_array['inserted']='now()';
 	  tep_db_perform(APPLICATION_RATING_TABLE, $sql_data_array);
    $messageStack->add_session(MESSAGE_SUCCESS_RATING_INSERTED, 'success');
   }
   else
   {
    $sql_data_array['updated']='now()';
    tep_db_perform(APPLICATION_RATING_TABLE, $sql_data_array, 'update', "rate_id = '" . $row1['rate_id'] . "'");
    $messageStack->add_session(MESSAGE_SUCCESS_RATING_UPDATED, 'success');
   }
   $query_string=encode_string("application=".$application_id."=application_id");
   $query_string2=encode_string("action*=*rank_it*=*action");
   tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'query_string='.$query_string.'&query_string2='.$query_string2.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
   break;
  case 'preview':
    $total_application= count($_POST['TR_applicant_id']);
    $list_array=array();
    for($i=0;$i<$total_application;$i++)
    {
     $hidden_fields.=tep_draw_hidden_field('TR_applicant_id[]',$_POST['TR_applicant_id'][$i]);
     $list_array[]=$_POST['TR_applicant_id'][$i];
    }
    $list_array1=implode(', ',$list_array);
    $TR_subject=$_POST['TR_subject'];
    $email_text=stripslashes($_POST['message1']);
    $hidden_fields.=tep_draw_hidden_field('TR_subject',$TR_subject);
    $hidden_fields.=tep_draw_hidden_field('message1',$email_text);
    $error=false;
    if(tep_validate_email($TREF_from) == false)
    {
     //$error = true;
     //$messageStack->add(EMAIL_ADDRESS1_INVALID_ERROR,'error');
    }
    if (strlen($TR_subject) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_SUBJECT_ERROR,'error');
    }
    if (strlen($email_text) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_MESSAGE_ERROR,'error');
    }
    if($total_application <=0)
    {
     $error = true;
     $messageStack->add_session(ERROR_ATLEAST_ONE_SELECT,'error');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
    }
    if(!$error)
    {
     //////// file upload Attachment starts //////
     if(tep_not_null($_FILES['attachment']['name']))
     {
      if($obj_resume = new upload('attachment', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('doc','pdf','txt','jpg','gif','png')))
      {
       $attachment_file_name=tep_db_input($obj_resume->filename);
      }
      else
      {
       $error=false;
       $messageStack->add(ERROR_ATTACHMENT_FILE, 'error');
      }
     }
     //////// file upload ends //////
     ////////////////   Attachment ///////////////
     if($attachment_file_name!='')
     {
      $hidden_fields.=tep_draw_hidden_field('attachment',stripslashes($attachment_file_name));
     }
    }
    else
     $action1='send_mail';

    break;
   case 'send':
   case 'back':
    $total_application= count($_POST['TR_applicant_id']);
    if($total_application<=0)
    {
     $error=true;
     $messageStack->add(ERROR_ATLEAST_ONE_CHECKED,'error');
    }
    //$TREF_from    = tep_db_prepare_input($_POST['TREF_from']);
    $TR_subject   = tep_db_prepare_input($_POST['TR_subject']);
    $email_text   = stripslashes($_POST['message1']);
    $attachment   = $_POST['attachment'];
    $error=false;
    //if(tep_validate_email($TREF_from) == false)
    /// {
    //$error = true;
    //$messageStack->add(EMAIL_ADDRESS1_INVALID_ERROR,'error');
    //}
    if (strlen($TR_subject) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_SUBJECT_ERROR,'error');
    }
    if (strlen($email_text) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_MESSAGE_ERROR,'error');
    }
    if(!$error)
    {
     if($action1=='back')
     {
      if($attachment!='')
      if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment))
      @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment);
     }
     else
     {
      if($attachment!='')
      if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment))
      {
       $file_directory=get_file_directory($attachment);
       if(check_directory(PATH_TO_RECRUITER_EMAIL_ATTACHMENT.$file_directory))
       {
        $target_file_name=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$attachment;
        copy(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment,$target_file_name);
        @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment);
        chmod($target_file_name, 0644);
       }
      }
      //$text = strip_tags($email_text);
       if (SEND_EMAILS == 'true')
       {
        //$message = new email();
        if(tep_not_null($_POST['attachment']))
        {
          $file_directory=get_file_directory($_POST['attachment']);
          $destination=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$_POST['attachment'];
          $file_name = basename($destination);
          //$handle    = fopen($destination, "r");
          //$contents = fread($handle, filesize($destination));
          //fclose($handle);
          //$message->add_attachment($contents,substr($file_name,14));
        }/*
        if (EMAIL_USE_HTML == 'true')
        {
         $message->add_html($email_text);
        }
        else
        {
         $message->add_text($text);
        }
        // Send message
        $message->build_message();*/
        $total_send_mail=0;
        ini_set('max_execution_time','0');
        for($i=0;$i<$total_application;$i++)
        {
         if($row_check_mail=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_LOGIN_TABLE." as jl, ".JOB_TABLE. " as jb left join  ".RECRUITER_TABLE." as r on ( r.recruiter_id ='".$_SESSION['sess_recruiterid']."' ) "," a.application_id='".$_POST['TR_applicant_id'][$i]."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=jl.jobseeker_id  ","a.id, jl.jobseeker_email_address,r.recruiter_company_name"))
         {
          $email_address=tep_db_output($row_check_mail['jobseeker_email_address']);
          $sql_data_array=array('application_id'=>$row_check_mail['id'],
                                'subject'=>$TR_subject,
                                'message'=>$email_text,
                                //'email_address'=>$TREF_from,
                                'sender_id'=>$_SESSION['sess_recruiterid'],
                                'attachment_file'=>$attachment,
                                'inserted'=>'now()',
                               );
          //$message->send('',$email_address,tep_db_output($row_check_mail['recruiter_company_name']),APPLICATION_REPLY_MAIL, $TR_subject);
          tep_new_mail('',$email_address, $TR_subject, $email_text,APPLICATION_REPLY_MAIL, $row_check_mail['recruiter_company_name'],$destination,substr($file_name,14)) ;
          tep_db_perform(APPLICANT_INTERACTION_TABLE,$sql_data_array);
          $total_send_mail++;
         }
        }
       }
       $messageStack->add_session(sprintf(MESSAGE_SUCCESS_SENT_APPLICANT,$total_send_mail), 'success');
       tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
     }
    }
    else
     $action1='send_mail';
    break;
 }
}
$row_1=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_TABLE." as j, ".JOB_TABLE. " as jb "," a.id='".$application_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id ","a.*,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as full_name");
if ((isset($application_id) && $application_id!='' ) && !isset($aInfo) )
{
 $aInfo = new objectInfo($row_1);
}
if (is_object($aInfo))
{
 if($process_round)
 {
  $heading1[]  = array('text'  => '<div class="list-group"><div class="card-header fw-bold">'.INFO_TEST_HEADER_SUMMERY.'</div></div>');
  $contents1[] = array('align' => 'left', 'text' => "<div class='px-3 py-2 fw-bold text-success'>" .INFO_TEST_SUMMERY_NEW." : ".$no_of_new_application."</div>");
  $contents1[] = array('align' => 'left', 'text' => "<div class='px-3 py-2 fw-bold text-success'>" .INFO_TEST_SUMMERY_SELECTED." : ".$no_of_select_application."</div>");
  //$contents1[] = array('align' => 'left', 'text' => INFO_TEST_SUMMERY_WAITING." : ".$no_of_waiting_application);
  $contents1[] = array('align' => 'left', 'text' => "<div class='px-3 py-2 fw-bold text-success'>" .INFO_TEST_SUMMERY_REJECTED." : ".$no_of_reject_application."</div>");
  $contents1[] = array('align' => 'left', 'text' => "<div class='px-3 py-2 fw-bold text-success'>" .INFO_TEST_SUMMERY_PROCESS." : ".$no_of_process_application."</div>");
  $contents1[] = array('align' => 'left', 'text' => "<div class='px-3 py-2 fw-bold text-success'>".INFO_TEST_SUMMERY_TOTAL."</b> : ".$no_of_total_application."</div>"."");
 }

 $query_string=encode_string("application=".$aInfo->id."=application_id");
 $query_string2=encode_string("application_id=".$aInfo->resume_id."=application_id");
 $rightSideApplicationPrimaryId = $aInfo->id;

 $heading[] = array('params'=>'','text' => '<div class="card-header"><div class="list-group"><h4 class="m-0">' . tep_db_output($aInfo->full_name) . '</h4><div class="text-danger"><small>'.TEXT_INFO_APPLICANT_OPRATION.'</small></div></div></div>');
//  $contents[] = array('align' => 'center', 'text' => TEXT_INFO_APPLICANT_OPRATION);
 $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string2.'&app_num='.$rightSideApplicationPrimaryId).'" target="_blank"  >'.IMAGE_PROFILE.'</a>');
 //$contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"', 'text' => '<img src="img/red_rec.gif"> <a href="#" onclick="set_action1(\''.$aInfo->id.'\',\'rank_it\')" class="right_black">'.IMAGE_RATING.'</a>');
 $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action1(\''.$aInfo->id.'\',\'change_status\')" >'.IMAGE_CHANGE_STATUS.'</a>');
 $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action1(\''.$aInfo->id.'\',\'show_history\')" >'.INFO_TEXT_SELECTION_HISTORY.'</a>');
  $contents[] = array('align' => 'left','params'=>'', 'text' => (($aInfo->applicant_select=='No')
  ?'<a class="list-group-item list-group-item-action px-3 py-1 d-flex" href="#" onclick="set_action1(\''.$aInfo->id.'\',\'applicant_selected\')" >
  '.IMAGE_SELECTED.' <span class="badge text-bg-success d-flex ms-auto  align-items-center"><i class="bi bi-check"></i> Select</span></a>'
  :'<a class="list-group-item list-group-item-action px-3 py-1 d-flex" href="#" onclick="set_action1(\''.$aInfo->id.'\',\'applicant_selection_reset\')" >'.IMAGE_APPLICANT_SELECTION_RESET.'
  <span class="badge text-bg-danger d-flex ms-auto  align-items-center"><i class="bi bi-check"></i> Unselect</span></a>'));
 $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string=".$query_string.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" >'.IMAGE_CONTACT.'</a>');
//  $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,"query_string=".$query_string2).'"  target="_blank" >'.INFO_TEXT_ADD_COMMENT.'</a>');
$contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#add_comment" onclick="addComment('.$aInfo->resume_id.')" >'.INFO_TEXT_ADD_COMMENT.'</a>');
 $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string2).'" target="_blank" >'.IMAGE_RATING.'</a>');
 if($from_list=='Y')
 {
  if($action1=='applicant_delete')
  {
  // $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' => '<img src="img/red_rec.gif"> <a href="#" onclick="set_action1(\''.$aInfo->id.'\',\'\')" class="right_black" >'.IMAGE_CANCEL.'</a>');
   $contents[] = array('align' => 'left', 'text' => '<div class="text-danger px-3 py-2">'.TEXT_DELETE_INTRO.'</div>');
   $contents[] = array('align' => 'left','params'=>'',  'text' => '
                        <a class="btn btn-sm btn-text border-secondary text-secondary mx-3"
                          href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"&search_id=".$search_id).'">
                        Cancel
                        </a>
                        <a class="btn btn-sm btn-danger text-white" 
                            href="#" 
                            onclick="set_action1(\''.$aInfo->id.'\',\'applicant_confirm_delete\')"  >
                            '.INFO_TEXT_DELETE_CONFIRM.'
                        </a>
                      ');
  // $contents[] = array('align' => 'left', 'text' => TEXT_DELETE_WARNING);

  }
  else
   $contents[] = array('align' => 'left','params'=>'',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action1(\''.$aInfo->id.'\',\'applicant_delete\')"  >'.INFO_TEXT_DELETE.'</a>');
 }
 $contents[] = array('align' => 'left',
                    'params'=>'',
                    'text' => '<a class="list-group-item list-group-item-action px-3 py-1"
                                  href="#"  onclick="window.open(\''.tep_href_link(FILENAME_RECRUITER_RESUME_MATCH,
                                  'jobID='.$job_id.'&application_id='.$aInfo->application_id).'\',\''.date('his').'\',\'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=300,height=300,left = 10,top = 10\');"
                                  >'.INFO_TEXT_MATCH_DETAIL.'</a>');
}

if((tep_not_null($heading)) && (tep_not_null($contents)))
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='160';
 if(!tep_not_null($search_id) && (tep_not_null($heading1)))
 $RIGHT_HTML1.= $box->infoBox($heading1, $contents1);
 $RIGHT_HTML.= $box->infoBox($heading, $contents);
 if((tep_not_null($heading2)) && (tep_not_null($contents2)))
	{
		$RIGHT_HTML.='';
 $RIGHT_HTML.= $box->infoBox($heading2, $contents2);
	}
}
elseif((tep_not_null($heading2)) && (tep_not_null($contents2)))
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='160';
 $RIGHT_HTML.= $box->infoBox($heading2, $contents2);
}
else
{
	$RIGHT_BOX_WIDTH='';
}
////////////////////////////////////////////////////////////////////////////////
$template->assign_vars(array(
  'INFO_TEXT_JOB_TITLE1'=>tep_db_output($row_check_1['job_title']),
  'INFO_TEXT_ALL_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id).'">'.INFO_TEXT_ALL_APPLICANT.'</a>',
  'INFO_TEXT_SELECTED_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1"  href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,'jobID='.$job_id).'" ><i class="bi bi-person-check me-1"></i> '.INFO_TEXT_SELECTED_APPLICANT.'</a>',
  'INFO_TEXT_SEARCH_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id."&search=applicant").'" > <i class="bi bi-search"></i> '.INFO_TEXT_SEARCH_APPLICANT.'</a>',
  'INFO_TEXT_JOB_DETAIL'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB,'jobID='.$job_id).'" target="_blank">'.INFO_TEXT_JOB_DETAIL.'</a>',
  'INFO_TEXT_REPORT_PIPELINE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#Pipeline ".'">'.INFO_TEXT_REPORT_PIPELINE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise".'">'.INFO_TEXT_REPORT_ROUNDWISE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE_SUMMARY'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise_summary".'">'.INFO_TEXT_REPORT_ROUNDWISE_SUMMARY.'</a>',
  'INFO_TEXT_VIEW_DATE_REPORT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id."#date_report").'">'.INFO_TEXT_VIEW_DATE_REPORT.'</a>',
  'INFO_TEXT_ADD_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'" target="_blank">'.INFO_TEXT_ADD_APPLICANT.'</a>',
  'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
  'AJAX_URL'=>tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id.(tep_not_null($search_id)?"&search_id=".$search_id:"")),
 'search_form'                  => tep_draw_form('search',FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id,'post',' class="d-flex ms-auto" role="search" onsubmit="return ValidateForm(this)" '),
 'INFO_TEXT_APPLICATION1'=> tep_draw_input_field('TR_application_id',$search_application_id, 'class="form-control" style="border-color:#dee2e6;border-top-right-radius:0px!important;border-bottom-right-radius:0px!important;" placeholder="Application ID"', true ),
  'INFO_TEXT_SELECTED_APPLICANT1'=>'<a class="me-4 btn btn-sm btn-text border btn-ats"  href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,'jobID='.$job_id).'" ><i class="bi bi-person-check me-1"></i> '.INFO_TEXT_SELECTED_APPLICANT.'</a>',
  'INFO_TEXT_SEARCH_APPLICANT1'=>'<a class="btn btn-sm btn-text border btn-ats"  href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id."&search=applicant").'" > <i class="bi bi-search"></i> '.INFO_TEXT_SEARCH_APPLICANT.'</a>',

));

/////

if($action1 =='compair_profile')
{
 if(count($_POST['TR_applicant_id'])<2)
 {
  die("");
 }
 $hidden_fields=tep_draw_hidden_field('TR_applicant_id[]',$_POST['TR_applicant_id'][0]);
 $hidden_fields.=tep_draw_hidden_field('TR_applicant_id[]',$_POST['TR_applicant_id'][1]);
 if($_POST['order']=='reverce')
 {
  $change_order='<a class="btn btn-sm btn-outline-secondary" href="#" onclick="document.compair_profile.submit();"><i class="bi bi-arrow-left-right h5"></i></a>';
  $hidden_fields.=tep_draw_hidden_field('order','');
  $applicant_id1=tep_db_input($_POST['TR_applicant_id'][1]);
  $applicant_id2=tep_db_input($_POST['TR_applicant_id'][0]);
 }
 else
 {
  $change_order='<a class="btn btn-sm btn-outline-secondary" href="#" onclick="document.compair_profile.submit();"><i class="bi bi-arrow-left-right h5"></i></a>';
  $hidden_fields.=tep_draw_hidden_field('order','reverce');
  $applicant_id1=tep_db_input($_POST['TR_applicant_id'][0]);
  $applicant_id2=tep_db_input($_POST['TR_applicant_id'][1]);
 }
 $resume_id1='';
 $resume_id2='';
 $hidden=MESSAGE_JOBSEEKER_PRIVACY;
 if($resume_check=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_TABLE." as j, ".JOB_TABLE. " as jb "," a.application_id='".$applicant_id1."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id ","a.resume_id"))
  $resume_id1=(int)$resume_check['resume_id'];
 if($resume_check=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_TABLE." as j, ".JOB_TABLE. " as jb "," a.application_id='".$applicant_id2."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id ","a.resume_id"))
  $resume_id2=(int)$resume_check['resume_id'];
 $add_sec_header='

 <table border="0" width="100%" cellspacing="3" cellpadding="0" >
                  <tr>
                   <td valign="top">
                    <table border="0" width="1100" cellspacing="15" cellpadding="0" >
                     <tr>
                      <td class="sectionHeading">';
 $add_sec_footer='</td>
                 </tr>
                </table>
               </td>
               </tr>
             </table>
             <table class="tabled small">
              <tr class="background">';
  $data1_not_available='
           <td valign="top" class="mr-3 border" width="50%">
            <table>
             <tr>
              <td width="130" align="right" class="black_verdana">'.INFO_TEXT_NOT_AVAILABLE.' </td>
             </tr>
            </table>
           </td>';
  $data2_not_available='
           <td valign="top"  class="ml-3 border" width="50%">
            <table>
             <tr>
              <td width="130" align="right" class="black_verdana">'.INFO_TEXT_NOT_AVAILABLE.' </td>
             </tr>
            </table>
           </td>';


 for($j=1;$j<=2;$j++)
 {
  $a='resume_id'.$j;
  $b='applicant_id'.$j;
  $resume_id=$$a;
  if($resume_id>0)
  {
   $table_name   = JOBSEEKER_LOGIN_TABLE." as jl left outer join  ".JOBSEEKER_TABLE."  as j  on (jl.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr  on (j.jobseeker_id=jr.jobseeker_id)";
   $fields= "jl.jobseeker_email_address,jobseeker_first_name,jobseeker_middle_name,jobseeker_last_name,j.jobseeker_address1,j.jobseeker_address2,j.jobseeker_country_id,j.jobseeker_state,j.jobseeker_state_id,j.jobseeker_city,j.jobseeker_zip,j.jobseeker_phone,j.jobseeker_mobile,j.jobseeker_work_phone,jr.objective ,jr.job_type_id ,jr.currency,jr.expected_salary ,jr.expected_salary_per ,jr.target_job_titles ,jr.relocate ,jr.jobseeker_resume,jr.jobseeker_resume_text,jr.jobseeker_photo,j.jobseeker_privacy,jobseeker_video,jr.experience_year,jr.experience_month,jr.facebook_url,jr.google_url,jr.linkedin_url,jr.twitter_url";
   $row=getAnyTableWhereData($table_name,"  jr.resume_id='".$resume_id."'",$fields);
   $show_detail=(($row['jobseeker_privacy']==2 || $row['jobseeker_privacy']==3)?true:false);
   
   $fullNameOfApplicant = tep_db_output($row['jobseeker_first_name'].' '.$row['jobseeker_middle_name'].' '.$row['jobseeker_last_name']);
   
   if (tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo'])) {
    $photo = '<a href="#" onclick="'.js_popup(PATH_TO_PHOTO.$row['jobseeker_photo'],SITE_TITLE).'">'
                .tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_PHOTO.$row['jobseeker_photo'],tep_db_output(SITE_TITLE),'','','class="mini-profile-img-ats me-3"').
              '</a>';
   } else {
    $photo = defaultProfilePhotoUrl($fullNameOfApplicant,false,100,' class="mini-profile-img-ats me-3"');
   }
   
            $j_profile='jobseeker'.$j.'_profile';
   $$j_profile='
            <td valign="top" class="jobseeker'.$j.'_profile" width="50%">
             <table border="0"  class="jobseeker'.$j.'_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">
              <tr>
               <td width="130" align="right" class="black_verdana">'.INFO_TEXT_APPLICANT_ID.'</td>
               <td class="gray_verdana"><b>'.tep_db_output($$b).'</b></td>
               <td valign="top" align="right" rowspan="6"><div class="mini-profile-img-ats">'.$photo.'</div></td>
              </tr>
              <tr>
               <td width="130" align="right" class="black_verdana">'.INFO_TEXT_NAME.'</td>
               <td class="gray_verdana">'.$fullNameOfApplicant.'</td>
              </tr>
              <tr>
               <td width="130" align="right" class="black_verdana" valign="top">'.INFO_TEXT_ADDRESS.'</td>
               <td class="gray_verdana">'.((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':tep_db_output($row['jobseeker_address1'].(tep_not_null($row['jobseeker_address2'])?', '.$row['jobseeker_address2']:'').(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_city']:'').(tep_not_null($row['jobseeker_zip'])?', '.$row['jobseeker_zip']:'').($row['jobseeker_state_id'] > 0?', '.get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',$row['jobseeker_state_id']):(tep_not_null($row['jobseeker_state'])?', '.$row['jobseeker_state']:'')).(tep_not_null($row['jobseeker_country_id'])?', '.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$row['jobseeker_country_id']):''))).'</td>
              </tr>
              <tr>
               <td width="130" align="right" class="black_verdana">'.INFO_TEXT_HOME_PHONE.'</td>
               <td class="gray_verdana">'.((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':tep_db_output($row['jobseeker_phone'])).'</td>
              </tr>
              <tr>
               <td width="130" align="right" class="black_verdana">'.INFO_TEXT_MOBILE.'</td>
               <td class="gray_verdana">'.((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':(($row['jobseeker_mobile']!='')?$row["jobseeker_mobile"]:'-')).'</td>
              </tr>
              <tr>
               <td width="130" align="right" class="black_verdana">'.INFO_TEXT_EMAIL_ADDRESS.'</td>
               <td class="gray_verdana">'.((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':'<a style="color:blue;" href="mailto:'.tep_db_output($row['jobseeker_email_address']).'">'.tep_db_output($row['jobseeker_email_address']).'</a>').'</td>
              </tr>
             </table>
            </td>';
   $j_objective='jobseeker'.$j.'_objective';
   if($row['objective']!='')
   $$j_objective='
            <td valign="top" class="jobseeker.$j._profile" width="50%">
             <table border="0"  class="jobseeker'.$j.'_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">
             <tr>
              <td class="gray_verdana" align="left">'.tep_db_output($row['objective']).'</td>
             </tr>
             </table>
            </td>';

   $j_social='jobseeker'.$j.'_social';
   if($row['facebook_url']!='' || $row['google_url']!='' || $row['linkedin_url']!='' || $row['twitter_url']!='' )
		 {
				$$j_social='';
    if($row['facebook_url'])
    $$j_social.='
       <tr>
        <td class="black_verdana" align="right"  width="17%">'.INFO_TEXT_FACEBOOK_URL.'</td>
      		<td class="gray_verdana"><a href="'.($row['facebook_url']).'" target="_blank">'.($row['facebook_url']).'</a></td>
   	   </tr>';
	   if($row['google_url'])
    $$j_social.='
         <tr>
          <td class="black_verdana" align="right" width="17%" >'.INFO_TEXT_GOOGLE_URL.'</td>
        		<td class="gray_verdana"><a href="'.($row['google_url']).'" target="_blank">'.($row['google_url']).'</a></td>
   	     </tr>';
    if($row['linkedin_url'])
    $$j_social.='
         <tr>
          <td class="black_verdana" align="right" width="17%" >'.INFO_TEXT_LINKEDIN_URL.'</td>
      		  <td class="gray_verdana"><a href="'.($row['linkedin_url']).'" target="_blank">'.($row['linkedin_url']).'</a></td>
   	     </tr>';

    if($row['twitter_url'])
    $$j_social.='
         <tr>
          <td class="black_verdana" align="right"  width="17%">'.INFO_TEXT_TWITTER_URL.'</td>
      	  	<td class="gray_verdana"><a href="'.($row['twitter_url']).'" target="_blank">'.($row['twitter_url']).'</a></td>
   	     </tr>';
		 }

   //////Total Work Experience///////////////////////////
   $j_experience='jobseeker'.$j.'_experience';
   $$j_experience='';
   if($row['experience_year']>0 || $row['experience_month']>0)
			{
    $experience_string='';
    if($row['experience_year']>1)
     $experience_string=$row['experience_year'].' Years ';
    elseif($row['experience_year']>0)
     $experience_string=$row['experience_year'].' Year ';
    if($row['experience_month']>1)
     $experience_string.=$row['experience_month'].' Months';
    elseif($row['experience_year']>0)
     $experience_string.=$row['experience_year'].' Month';
    $$j_experience='
             <tr bgcolor="#fffefe">
              <td class="black_verdana" width="23%" align="right">'.tep_db_output(INFO_TEXT_WORK_EXPERIENCE).'</td>
              <td class="gray_verdana" align="left" colspan="3">'.tep_db_output($experience_string).'</td>
             </tr>';
			}

   /////////////////////////////////////////////////////////////////////////////////
   $target_category=get_category_name_with_parent(get_name_from_table(RESUME_JOB_CATEGORY_TABLE,'job_category_id','resume_id',tep_db_output($resume_id)));

   $j_target_job='jobseeker'.$j.'_target_job';
   $$j_target_job.='<td valign="top" class="jobseeker.$j._profile" width="50%">
                     <table border="0"  class="jobseeker'.$j.'_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">
                      <tr>
                        <td class="black_verdana" align="right" width="27%">'.INFO_TEXT_TARGET_JOB_TITLES.'</td>
                        <td colspan="2" class="gray_verdana">'.tep_db_output($row['target_job_titles']).'</td>
                        </tr>
                        <tr>
                        <td class="black_verdana" align="right" width="15%" valign="top">'.INFO_TEXT_JOB_TYPE.'</td>
                        <td class="gray_verdana" colspan="2"> '.(tep_not_null($row['job_type_id'])?get_name_from_table(JOB_TYPE_TABLE,TEXT_LANGUAGE.'type_name', 'id',$row['job_type_id']):"Any Type").'</td>
                        </tr>
                       <tr>
                        <td class="black_verdana" align="right" width="12%" valign="top">'.INFO_TEXT_INDUSTRY.'</td>
                        <td colspan="2" class="gray_verdana"> '.tep_db_output($target_category).'</td>
                       </tr>
                       <tr>
                        <td class="black_verdana" align="right">'.INFO_TEXT_DESIRED_SALARY.'</td>
                        <td class="gray_verdana"> '.(tep_not_null($row['expected_salary'])?get_name_from_table(CURRENCY_TABLE,'code', 'currencies_id',$row['currency']).' '.tep_db_output($row['expected_salary'].'/'.$row['expected_salary_per']):"--").'</td>
                        <td class="black_verdana" align="left" width="40%">'.INFO_TEXT_RELOCATE.'<span class="gray_verdana"> '.tep_db_output($row['relocate']).'</span></td>
                       <tr>
                     </table>
                    </td>';

   ///////////////////////    Work History  /////////////////////////////////////////////////////
   $work_history_query="select * from ".JOBSEEKER_RESUME2_TABLE." where resume_id='".$resume_id."' order by start_year desc ,start_month desc";
   $work_history_result = tep_db_query($work_history_query);
   $rows=tep_db_num_rows($work_history_result);
   $j_work_history='jobseeker'.$j.'_work_history';
   $$j_work_history='';
   $r_no=1;
   while ($row1= tep_db_fetch_array($work_history_result))
   {
    if($row1['start_month'] >0 and  $row1['start_year']>0  )
     $start_date=formate_date($row1['start_year'].'-'.$row1['start_month'].'-1',"M Y");
    else
     $start_date='-';

    if($row1['end_month']>0 and  $row1['end_year']>0  )
     $end_date=formate_date($row1['end_year'].'-'.$row1['end_month'].'-1',"M Y");
    elseif($row1['still_work']=='Yes'  )
     $end_date='still working ';
    else
     $end_date='-';
    $description='';
    if($row1['description']!='')
       $description='<tr>
                     <td class="black_verdana" align="right" valign="top">'.INFO_TEXT_RELATED_INFO.' </td>
                     <TD class="gray_verdana" colspan="4">'.nl2br(tep_db_output($row1['description'])).'</span></TD>
                    </tr>';
    if(tep_db_output($row1['state_id']) > 0)
     $state_display=get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name', 'zone_id',tep_db_output($row1['state_id']));
    else
     $state_display=tep_db_output($row1['state']);

    $$j_work_history.='<tr>
																								<td class="black_verdana" align="right" valign="top" width="20%" >'.INFO_TEXT_COMPANY.'</td>
																								<td class="gray_verdana" align="left" colspan="3"> '.tep_db_output($row1['company']).'</td>
																							</tr>
																							<tr>
																								<td class="black_verdana" align="right" valign="top" width="20%" >'.INFO_TEXT_JOB_TITLE.' </td>
																								<td class="gray_verdana" align="left" width="30%"> '.tep_db_output($row1['job_title']).'</td>
																								<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_SALARY.'</td>
																								<td class="gray_verdana" align="left" width="30%">'.(tep_not_null($row1['salary'])?get_name_from_table(CURRENCY_TABLE,'code', 'currencies_id',$row['currency']).' '.tep_db_output($row1['salary'].' / '.$row1['salary_per']):'---').'</td>
																							</tr>
																							<tr>
																								<td class="black_verdana" align="right" valign="top" width="20%"> '.INFO_TEXT_INDUSTRY.' </td>
																								<td class="gray_verdana" align="left" colspan="3">'.get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name', 'id',tep_db_output($row1['company_industry'])).'</td>
																							</tr>
																							<tr>
																								<td class="black_verdana" align="right" width="20%" valign="top" >'.INFO_TEXT_COUNTRY.'</td>
																								<td class="gray_verdana" align="left" width="30%"> '.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name', 'id',tep_db_output($row1['country'])).'</td>
																								<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_CITY.'</td>
																								<td class="gray_verdana" align="left" width="30%"> '.tep_db_output($row1['city']).'</td>
																							</tr>
																							<tr>
																								<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_FROM.'</td>
																								<td class="gray_verdana" align="left" width="30%"> '.$start_date.'</td>
																								<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_TO.'</td>
																								<td class="gray_verdana" align="left" width="30%">'.$end_date.'</td>
																							</tr>
																							<tr>
																								<TD class="black_verdana" align="left" colspan="4">'.$description.'</TD>
																							</tr>';

   if($rows!=$r_no)
   $$j_work_history.='<tr><td colspan="4"><hr></td></tr>';
   $r_no++;
   }
   tep_db_free_result($work_history_result);
  }
  ///////////////////////////////////// education_details //////////////////////////////////////////
  $education_query="select * from ".JOBSEEKER_RESUME3_TABLE." where resume_id='".$resume_id."' ";
  $education_result = tep_db_query($education_query);
  $rows=tep_db_num_rows($education_result);
  $j_education='jobseeker'.$j.'_education';
  $$j_education='';
  $r_no=1;
  while ($row1= tep_db_fetch_array($education_result))
  {
   if($row1['start_year']>0 && $row1['start_month']>0)
    $start_date  = formate_date(tep_db_output($row1['start_year']).'-'.tep_db_output($row1['start_month']).'-01'," M Y ");
   if($row1['end_year']>0 && $row1['end_month']>0)
    $end_date  = formate_date(tep_db_output($row1['end_year']).'-'.tep_db_output($row1['end_month']).'-01'," M Y ");
   $$j_education .= '<tr>
																						<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_DEGREE.'</td>
																						<td class="gray_verdana" align="left" colspan="3">'.get_name_from_table(EDUCATION_LEVEL_TABLE,TEXT_LANGUAGE.'education_level_name', 'id',tep_db_output($row1['degree'])).(tep_not_null($row1['specialization'])?' ('.tep_db_output($row1['specialization']).')':'').'</td>
																					</tr>
																					<tr>
																						<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_INSTITUTION_NAME.'</td>
																						<td class="gray_verdana" align="left" colspan="3" >'.(tep_not_null($row1['school'])?tep_db_output($row1['school']):'---').'</td>
																					</tr>
																					<tr>
																						<td class="black_verdana" align="right" width="20%">'.INFO_TEXT_COUNTRY.'</td>
																						<td class="gray_verdana" align="left"  width="30%" > '.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name', 'id',tep_db_output($row1['country'])).'</td>
																						<td class="black_verdana" align="right" width="20%">'.INFO_TEXT_CITY.'</td>
																						<td class="gray_verdana" align="left" width="30%">'.tep_db_output($row1['city']).'</td>
																					</tr>
																					<tr>
																						<td class="black_verdana" align="right" width="20%">'.INFO_TEXT_START_DATE.' </TD>
																						<td class="gray_verdana" align="left" width="30%">'.$start_date.'</td>
																						<td class="black_verdana" align="right" width="20%">'.INFO_TEXT_END_DATE.'</TD>
																						<td class="gray_verdana" align="left" width="30%">'.$end_date.'</td>
																					</tr>
																					<tr>
																						<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_RELATED_INFO.' </td>
																						<td class="gray_verdana" align="left" colspan="3">'.tep_db_output($row1['related_info']).'</span></td>
																					</tr>';
  if($rows!=$r_no)
  $$j_education.='<tr><td colspan="4"><hr></td></tr>';
  $r_no++;
  }
  tep_db_free_result($education_result);
  ///////////////////////////////////// end education_details ////////////////////////////////////////////

///////////////////////////////////// reference_details //////////////////////////////////////////
$reference_query="select * from ".JOBSEEKER_RESUME6_TABLE." where resume_id='".$resume_id."' ";
$reference_result = tep_db_query($reference_query);
$rows=tep_db_num_rows($reference_result);
$j_reference='jobseeker'.$j.'_reference';
$$j_reference='';
$r_no=1;
while ($row1= tep_db_fetch_array($reference_result))
{
 $$j_reference.='<tr>
															<td class="black_verdana" align="right" valign="top" width="20%">'.INFO_TEXT_REFERENCE_NAME.'</td>
															<td class="gray_verdana" width="30%" align="left">'.tep_db_output($row1['name']).'</td>
															<td class="black_verdana" align="right" width="20%">'.INFO_TEXT_COMPANY_NAME.'</td>
															<td class="gray_verdana" align="left">'.tep_db_output($row1['company_name']).'</td>
														</tr>
														<tr>
															<td class="black_verdana" align="right" width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.INFO_TEXT_COUNTRY.'
															<td class="gray_verdana" align="left" width="30%"> '.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name', 'id',tep_db_output($row1['country'])).'</td>
															<td class="black_verdana" align="right" width="20%">'.INFO_TEXT_POSITION_TITLE.'</td>
															<td class="gray_verdana" width="30%">'.tep_db_output($row1['position_title']).'</td>
              </tr>
														<tr>
															<td class="black_verdana" align="right" width="20%">&nbsp;&nbsp;&nbsp;&nbsp;'.INFO_TEXT_CONTACT_NO.' </TD>
															<TD class="gray_verdana" width="30%" align="left">'.tep_db_output($row1['contact_no']).'</td>
															<td class="black_verdana" align="right" width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.INFO_TEXT_EMAIL_ADDRESS.'</TD>
															<TD class="gray_verdana" align="left" width="30%">'.tep_db_output($row1['email_address']).'</td>
														</tr>
														<tr>
															<td class="black_verdana" align="right" valign="top">'.INFO_TEXT_RELATIONSHIP.' </td>
															<td class="gray_verdana" colspan="4" align="left">'.tep_db_output($row1['relationship']).'</span></td>
														</tr>';
if($rows!=$r_no)
$$j_reference.='<tr><td colspan="4"><hr></td></tr>';
$r_no++;
}
tep_db_free_result($reference_result);
///////////////////////////////////// end reference_details ////////////////////////////////////////////


  ///////////////////////////////////// skills_details///////////
  $skills_query="select * from ".JOBSEEKER_RESUME4_TABLE." where resume_id='".$resume_id."' ";
  $skills_result = tep_db_query($skills_query);
  $rows=tep_db_num_rows($skills_result);
  $j_skills='jobseeker'.$j.'_skills';
  $$j_skills='';
  $r_no=1;
  while ($row1= tep_db_fetch_array($skills_result))
  {
   $$j_skills.='
      <tr>
       <td class="black_verdana" align="right" width="20%">'.INFO_TEXT_SKILL.' </td>
       <td class="gray_verdana" >'.tep_db_output($row1['skill']).'</td>
       <td class="black_verdana" align="right">'.INFO_TEXT_SKILL_LEVEL.'</td>
       <td class="gray_verdana">'.get_name_from_table(SKILL_LEVEL_TABLE,TEXT_LANGUAGE.'skill_name', 'id',tep_db_output($row1['skill_level'])).'</td>
        </tr>
        <tr>
          <td class="black_verdana" align="right">'.INFO_TEXT_LAST_USED.'</td>
       <td class="gray_verdana"> '.get_name_from_table(SKILL_LAST_USED_TABLE,'skill_last_used', 'id',tep_db_output($row1['last_used'])).'</td>
       <td class="black_verdana" align="right">'.INFO_TEXT_YEARS_OF_EXP.'
          <td class="gray_verdana">'.tep_db_output($row1['years_of_exp']).'</td>
        </tr>
                    ';
  if($rows!=$r_no)
  $j_skills.='<tr><td colspan="4"><hr></td></tr>';
  $r_no++;
  }
  tep_db_free_result($skills_result);
  ///////////////////////////////////// end skills_details ////////////////////////////////////////////
  ///////////////////////////////////// language_details ////////////////////////////////////////////
  $language_query="select * from ".JOBSEEKER_RESUME5_TABLE." where resume_id='".$resume_id."' ";
  $language_result = tep_db_query($language_query);
  $rows=tep_db_num_rows($language_result);
  $j_language='jobseeker'.$j.'_language';
  $$j_language='';
  $r_no=1;
  if($rows>0)
  {
   $$j_language.='
               <tr bgcolor="#ffffff">
                <td class="black_verdana" align="left" valign="top">&nbsp;&nbsp;&nbsp;'.INFO_TEXT_LANGUAGE.'</td>
                <td class="black_verdana" align="left" valign="top">&nbsp;&nbsp;&nbsp;'.INFO_TEXT_PROFICIENCY.'</td>
               </tr>
                     ';

   while ($row1= tep_db_fetch_array($language_result))
   {
    $$j_language.='
                     <tr>
                      <td class="black_verdana" align="left" valign="top"><span class="gray_verdana">'.get_name_from_table(JOBSEEKER_LANGUAGE_TABLE,TEXT_LANGUAGE.'name', 'languages_id',tep_db_output($row1['language'])).'</span></td>
                      <td class="black_verdana" align="left" valign="top"><span class="gray_verdana">'.get_name_from_table(LANGUAGE_PROFICIENCY_TABLE,TEXT_LANGUAGE.'language_proficiency', 'id',tep_db_output($row1['proficiency'])).'</span></td>
                     </tr>
                     ';
   $r_no++;
   }
  }
  tep_db_free_result($language_result);
  ///////////////////////////////////// end language_details ////////////////////////////////////////////
  $j_attachment='resume_attachment'.$j;
  $$j_attachment='';
  $resume_directory=get_file_directory($row['jobseeker_resume'],6);
  if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.stripslashes($row['jobseeker_resume'])))
  {
   $query_string3 = encode_string("resume_id@@@".$resume_id."@@@resume");
   $$j_attachment="<a href='".tep_href_link(FILENAME_JOBSEEKER_RESUME_DOWNLOAD,(tep_not_null($resume_id)?'query_string='.$query_string3:''))."'>".stripslashes(stripslashes(substr($row['jobseeker_resume'],14)))."</a>";
  }
   $j_resume='jobseeker'.$j.'_resume';
   if($row['jobseeker_resume_text']!='' || $$j_attachment!='')
   $$j_resume='
            <td valign="top" class="jobseeker.$j._profile" width="50%">
             <table border="0"  class="jobseeker'.$j.'_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">
             <tr>
              <td class="gray_verdana" align="left">'.(($$j_attachment!='')?" <span class='red'><u>".INFO_MAIL_ATTACHMENT."</u> :- ".$$j_attachment."</span>":"").nl2br(stripslashes($row['jobseeker_resume_text'])).'</td>
             </tr>
             </table>
            </td>';
  ////////VIDEO///////////////
   $j_video='jobseeker'.$j.'_video';
   $$j_video='';
   if($row['jobseeker_video']!='')
   {
  	 $jobseeker_video_link=$row['jobseeker_video'];
    $photo_arr=(explode("watch?v=",(basename($jobseeker_video_link))));
    $photo ='http://img.youtube.com/vi/'.trim($photo_arr[1]).'/2.jpg';
    $vquery_string=encode_string("video_dispaly===".$resume_id."===videoid");
    $video ='<a href="#" onclick=\'popUp1("'.tep_href_link(FILENAME_DISPLAY_VIDEO,"query_string1=".$vquery_string).'")\' ><img style="border:2  solid #a0a0a0;" src="'.$photo.'" alt="" ></a>';
    $$j_video='
            <td valign="top" class="jobseeker.$j._profile" width="50%">
             <table border="0"  class="jobseeker'.$j.'_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">
             <tr>
              <td class="gray_verdana" align="left">'.$video.'</td>
             </tr>
             </table>
            </td>';
   }
  ////////VIDEO///////////////
 }
 /////////////////////////////////////////////////////////////////////////////////////////
 if(tep_not_null($jobseeker1_profile) or tep_not_null($jobseeker2_profile) )
 {
  $jobseeker_profile.=$add_sec_header.SECTION_PERSONAL_PROFILE.$add_sec_footer;
  if(tep_not_null($jobseeker1_profile))
   $jobseeker_profile.=$jobseeker1_profile;
  else
     $jobseeker_profile.=$data1_not_available;
  if(tep_not_null($jobseeker2_profile))
   $jobseeker_profile.=$jobseeker2_profile;
  else
     $jobseeker_profile.=$data2_not_available;
   $jobseeker_profile.="</tr></table><br&nbsp;> ";
 }
 if(tep_not_null($jobseeker1_objective) or tep_not_null($jobseeker2_objective) )
 {
  $jobseeker_objective.=$add_sec_header.SECTION_OBJECTIVE.$add_sec_footer;
  if(tep_not_null($jobseeker1_objective))
   $jobseeker_objective.=$jobseeker1_objective;
  else
     $jobseeker_objective.=$data1_not_available;
  if(tep_not_null($jobseeker2_objective))
   $jobseeker_objective.=$jobseeker2_objective;
  else
     $jobseeker_objective.=$data2_not_available;
   $jobseeker_objective.="</tr></table><br&nbsp;> ";
 }

	$jobseeker1_work_history=$jobseeker1_experience.$jobseeker1_work_history;
	$jobseeker2_work_history=$jobseeker2_experience.$jobseeker2_work_history;
 $array_list1 = array('jobseeker1_work_history','jobseeker1_reference','jobseeker1_education','jobseeker1_skills','jobseeker1_language','jobseeker1_target_job','jobseeker1_video','jobseeker1_resume','jobseeker1_social');
 $array_list2 = array('jobseeker2_work_history','jobseeker2_reference','jobseeker2_education','jobseeker2_skills','jobseeker2_language','jobseeker2_target_job','jobseeker2_video','jobseeker2_resume','jobseeker2_social');
 $array_list  = array('jobseeker_work_history','jobseeker_reference','jobseeker_education','jobseeker_skills','jobseeker_language','jobseeker_target_job','jobseeker_video','jobseeker_resume','jobseeker_social');
 $array_list3 = array(SECTION_WORK_HISTORY_DETAIL,SECTION_REFERENCE_DETAILS,SECTION_EDUCATION_DETAILS,SECTION_SKILLS,SECTION_LANGUAGES,SECTION_TARGET_JOB,SECTION_DOCUMENT_VIDEO,SECTION_RESUME,SECTION_SOCIAL_ACCOUNT);
 for ($i=0;$i<count($array_list);$i++)
 {
  if(tep_not_null($$array_list1[$i]) or tep_not_null($$array_list2[$i]) )
  {
   $$array_list[$i]=$add_sec_header.$array_list3[$i].$add_sec_footer;
   if(tep_not_null($$array_list1[$i]))
    $$array_list[$i].='<td valign="top" class="jobseeker1_profile" width="50%"><table border="0"  class="jobseeker1_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">'.$$array_list1[$i].'</td></table>';
   else
      $$array_list[$i].=$data1_not_available;
   if(tep_not_null($$array_list2[$i]))
    $$array_list[$i].='<td valign="top" class="jobseeker2_profile" width="50%"><table border="0"  class="jobseeker2_profile"width="100%" cellspacing="2" cellpadding="2" align="left" bgcolor="#FFFFFF">'.$$array_list2[$i].'</td></table>';
   else
      $$array_list[$i].=$data2_not_available;
   $$array_list[$i].="</tr></table><br&nbsp;> ";
  }
 }
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE_PROFILE,
 'SECTION_JOBSEEKER_PROFILE'=>$jobseeker_profile,
 'SECTION_OBJECTIVE'        =>$jobseeker_objective,
 'SECTION_WORK_HISTORY_DETAIL'=> $jobseeker_work_history,
 'SECTION_REFERENCE_DETAILS' => $jobseeker_reference,
	'SECTION_EDUCATION_DETAILS' => $jobseeker_education,
 'SECTION_SKILLS'            => $jobseeker_skills,
 'SECTION_LANGUAGES'         => $jobseeker_language,
 'SECTION_TARGET_JOB'        => $jobseeker_target_job,
 'SECTION_DOCUMENT_VIDEO'    => $jobseeker_video,
 'SECTION_RESUME'            => $jobseeker_resume,
 'SECTION_SOCIAL_ACCOUNT'    => $jobseeker_social,
 'form'=>tep_draw_form('compair_profile', FILENAME_RECRUITER_LIST_OF_APPLICATIONS,tep_get_all_get_params(array('query_string','query_string2')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('action1','compair_profile'),
 'hidden_fields'             => $hidden_fields,
 'change_order'              => $change_order,
//  'back_buttons'              =>'<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id).'"><button class="btn btn-sm btn-outline-secondary">Back</button></a>',
 'back_buttons'              =>'<a class="btn btn-sm btn-outline-secondary float-right" href="#" onclick="javascript:history.back();">Back</a>',
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'LEFT_HTML'=>'',
 'RIGHT_HTML'=>$RIGHT_HTML,
 'update_message'=>$messageStack->output()));
 $template->pparse('compair_profile');
}
elseif($action1 =='change_status' || $action1 =='show_history' )
{
  ///only for sorting starts
  $db_applicant_query_raw = "select  apt.id,sr.".TEXT_LANGUAGE."round_name,apt.inserted,ap.application_status as pre_status,ap1.application_status as cur_status from ".APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.pre_status=ap.id) left join ".APPLICATION_STATUS_TABLE." as ap1 on (apt.cur_status=ap1.id)  left join ".SELECTION_ROUND_TABLE." as sr on (apt.process_round=sr.id)  where application_id='".$application_id."' order by  inserted desc,apt.id desc";
  //echo $db_applicant_query_raw ;
  $db_applicant_query = tep_db_query($db_applicant_query_raw );
  $db_applicant_num_row = tep_db_num_rows($db_applicant_query);
  if($db_applicant_num_row > 0)
  {
   $alternate=1;
   while ($application = tep_db_fetch_array($db_applicant_query))
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $template->assign_block_vars('application_status',array( 'row_selected' => $row_selected,
     'round'        => tep_db_output($application[TEXT_LANGUAGE.'round_name']),
     'pre_status'   => tep_not_null($application['pre_status'])?tep_db_output($application['pre_status']):'---',
     'change_status'=> tep_not_null($application['cur_status'])?tep_db_output($application['cur_status']):'---',//"<a href='".tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string)."'>". tep_db_output($application['full_name'])."</a>",
     'inserted'     => tep_date_veryshort(tep_db_output($application['inserted'])),
     ));
     $alternate++;
   }
  }
  tep_db_free_result($db_applicant_query);
//////////////////////////////////////////////////
 if($action1 =='change_status')
 {
 $db_applicant_query_raw = "select  sr.id,sr.".TEXT_LANGUAGE."round_name from ".SELECTION_ROUND_TABLE."  as sr order by  value ";
 //echo $db_applicant_query_raw ;
 $db_applicant_query = tep_db_query($db_applicant_query_raw );
 $db_applicant_num_row = tep_db_num_rows($db_applicant_query);
 if($db_applicant_num_row > 0)
 {
  $alternate=1;
  while ($application = tep_db_fetch_array($db_applicant_query))
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $latest_status =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."'  and  apt.process_round ='".$application['id']."'order by inserted desc,apt.id desc","ap.id,apt.inserted,ap.application_status as cur_status");

   //if(!$latest_status =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."'  and  apt.process_round ='".$application['id']."'order by inserted desc,apt.id desc","apt.inserted,ap.application_status as cur_status"))
   // $status_drop_down=LIST_SET_DATA(APPLICATION_STATUS_TABLE,"where id = 1 ",'application_status','id',"priority ",'name="new_status"',"",'',$application_status);
   //else
    $status_drop_down=LIST_SET_DATA(APPLICATION_STATUS_TABLE,"",'application_status','id',"priority desc,id ",'class="form-select form-select-sm radius-0 mw-100" name="new_status"',"",'',$latest_status['id']);
   $template->assign_block_vars('application_set_status',array( 'row_selected' => $row_selected,
    'round'         => tep_db_output($application[TEXT_LANGUAGE.'round_name'].' Round').tep_draw_hidden_field('round',$application['id']),
    'current_status'=> tep_not_null($latest_status['cur_status'])?tep_db_output($latest_status['cur_status']):'---',
    'change_status' => tep_not_null($application['cur_status'])?tep_db_output($application['cur_status']):'---',//"<a href='".tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string)."'>". tep_db_output($application['full_name'])."</a>",
    'updated'       => tep_date_short(tep_db_output($latest_status['inserted'])),
    'status_drop_down'=>$status_drop_down,
    ));
    $alternate++;
  }
 }
 tep_db_free_result($db_applicant_query);
 }

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE1,
  'TABLE_HEADING_ROUND'=>TABLE_HEADING_ROUND,
  'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_REPORTS'  =>INFO_TEXT_REPORTS,
  'TABLE_HEADING_PRE_STATUS'=>TABLE_HEADING_PRE_STATUS,
  'TABLE_HEADING_CHANGE_STATUS'=>TABLE_HEADING_CHANGE_STATUS,
  'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED1,
  'TABLE_HEADING_UPDATED'  => TABLE_HEADING_UPDATED,
  'TABLE_HEADING_CURRENT_STATUS' =>TABLE_HEADING_CURRENT_STATUS,
  'INFO_TEXT_LATEST_STATUS'=> INFO_TEXT_LATEST_STATUS,
  'INFO_TEXT_STATUS_HISTORY'=> INFO_TEXT_STATUS_HISTORY,
  'INFO_TEXT_PRE_STATUS1'=>tep_db_output($row['cur_status']),
  'TABLE_HEADING_SET_STATUS'=>TABLE_HEADING_SET_STATUS,
  'form'=>tep_draw_form('page', FILENAME_RECRUITER_LIST_OF_APPLICATIONS,tep_get_all_get_params(array('query_string','query_string2')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('action1','set_status'),
  'form1'=>tep_draw_form('page1', FILENAME_RECRUITER_LIST_OF_APPLICATIONS,tep_get_all_get_params(array('query_string','query_string2')),'post', '').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('action1','set_status'),
  'button'=>'<button class="btn btn-sm btn-primary" style="font-size:14px;" type="submit">Update</button>',//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE),
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>$RIGHT_HTML,
  'update_message'=>$messageStack->output()));
  if($action1 =='show_history')
  $template->pparse('app_history');
  else
  $template->pparse('application1');
}
elseif($action1=='rank_it')
{
 ///only for sorting starts
 $row =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' order by inserted desc,apt.id desc","apt.process_round");
 $db_app_status_query_raw = "select  *  from ".SELECTION_ROUND_TABLE." as ap  where value <='".$row['process_round']."' order by value";
 $db_app_status_query_raw ;
 $db_app_status_query = tep_db_query($db_app_status_query_raw );
 $db_app_status_num_row = tep_db_num_rows($db_app_status_query);
 if($db_app_status_num_row > 0)
 {
  $alternate=1;
  $rate_it_array=array();
  for($i=1;$i<=10;$i++)
  {
    $rate_it_array[]=array("id"=>$i,"text"=>$i);
  }
  $rate_it_string=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'5', '', false);
  while ($application_status = tep_db_fetch_array($db_app_status_query))
  {//print_r($application_status);die();
   $row_rating=getAnyTableWhereData(APPLICATION_RATING_TABLE," application_id='".$application_id."' and round_id ='".$application_status['id']."'","point,rate_id");
   $rate_it_string=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'', '', false);
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $template->assign_block_vars('application_status',array( 'row_selected' => $row_selected,
    'form'         => tep_draw_form('page1', FILENAME_RECRUITER_LIST_OF_APPLICATIONS,tep_get_all_get_params(array('query_string','query_string2')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('process_round',$application_status['id']).tep_draw_hidden_field('action1','set_rank'),
    'round'        => get_name_from_table(SELECTION_ROUND_TABLE,TEXT_LANGUAGE.'round_name', 'id', ($application_status['id'])),
    'rating'       => $rate_it_string,
    'rate_button'  => '<button class="btn btn-primary" type="submit">Rate</button>',//tep_image_submit(PATH_TO_BUTTON.'button_rate.gif',IMAGE_RATE),
    ));
    $alternate++;
  }
 }
 ////////////////////////////////////////////////

 $db_app_rate_query_raw = "select  *  from ".APPLICATION_RATING_TABLE." as ar left join ".SELECTION_ROUND_TABLE." as sr on (sr.id=ar.round_id)  where ar.application_id ='".$application_id."' order by value";
 $db_app_rate_query_raw ;
 $db_app_rate_query = tep_db_query($db_app_rate_query_raw );
 $db_app_rate_num_row = tep_db_num_rows($db_app_rate_query);
 if($db_app_rate_num_row > 0)
 {
  $alternate=1;
  while ($application_rate = tep_db_fetch_array($db_app_rate_query))
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $template->assign_block_vars('application_status1',array( 'row_selected' => $row_selected,
    'round'        => tep_db_output($application_rate[TEXT_LANGUAGE.'round_name']),
    'rating'       => tep_db_output($application_rate['point']),
    'inserted'     => tep_db_output($application_rate['inserted']),
    'updated'      => ($application_rate['updated']=='0000-00-00 00:00:00')?'-':tep_db_output($application_rate['updated']),
    ));
    $alternate++;
  }
 }
 //$row =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' order by inserted desc,apt.id desc","apt.id,apt.process_round,apt.inserted,ap.application_status as cur_status");
 // print_r($row);die();
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE_RATING,
  'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_REPORTS'  =>INFO_TEXT_REPORTS,
  'TABLE_HEADING_RANK_ROUND'=>TABLE_HEADING_RANK_ROUND,
  'TABLE_HEADING_RANK_RATING'=>TABLE_HEADING_RANK_RATING,
  'TABLE_HEADING_RANK_INSERTED'=>TABLE_HEADING_RANK_INSERTED,
  'TABLE_HEADING_RANK_UPDATED'=>TABLE_HEADING_RANK_UPDATED,
  'form'=>tep_draw_form('page', FILENAME_RECRUITER_LIST_OF_APPLICATIONS,tep_get_all_get_params(array('query_string','query_string2')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('action1','set_rank'),
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>$RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('application2');
}
elseif($action1=='send_mail' ||  $action1=='back')
{
//  $list_application=
 $total_application= (int)count($_POST['TR_applicant_id']);
 if($total_application<=0)
 {
  $messageStack->add_session(ERROR_ATLEAST_ONE_CHECKED,'error');
  tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
 }
 $list_application='';
 $list_array=array();
 for($i=0;$i<$total_application;$i++)
 {
  $list_application.='\''.tep_db_input($_POST['TR_applicant_id'][$i]).'\',';
  $list_array[]=$_POST['TR_applicant_id'][$i];
 }
 $list_array1= implode(',',$list_array);
 $list_application=substr($list_application,0,-1);
 /*
 if($action1=='send_mail')
 {
  $row1=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_LOGIN_TABLE." as jl, ".JOB_TABLE. " as jb left join  ".RECRUITER_TABLE." as r on ( r.recruiter_id ='".$_SESSION['sess_recruiterid']."' ) "," a.id='".$application_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=jl.jobseeker_id  ","a.id, jl.jobseeker_email_address,jb.recruiter_user_id");
  if($row1['recruiter_user_id']!='' && ($row_email=getAnyTableWhereData(RECRUITER_USERS_TABLE,"id='".$row1['recruiter_user_id']."' and status='Yes'","email_address,name")))
  {
	 	$TREF_from=tep_db_output($row_email['email_address']);
  }
  else
  {
   $row_email=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id ='".$_SESSION['sess_recruiterid']."'",'recruiter_email_address');
   $TREF_from=tep_db_output($row_email['recruiter_email_address']);
  }
 }
 if($_POST['TREF_from']!='')
  $TREF_from=$_POST['TREF_from'];*/
 $list_query="SELECT case when j.jobseeker_privacy=1 then ''  else  jobseeker_email_address end as jobseeker_email_address, a.application_id  from ".APPLICATION_TABLE." as a left join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=a.jobseeker_id) left join ".JOBSEEKER_TABLE ." as j on (j.jobseeker_id =jl.jobseeker_id)   where  a.application_id in (".$list_application.")  order by application_id";
 // 'INFO_TEXT_TO1'    =>LIST_TABLE2($flag=0,APPLICATION_TABLE." as a left join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=a.jobseeker_id)"," where  a.application_id in (".$list_application.")",'application_id','jobseeker_email_address','application_id',$order_by='application_id',$addoption_value="",$addstart="" ,$addmiddle=" - ",$addend="", $query="",$parameters=' name="TR_applicant_id[]" multiple','','',$list_array1),
 $template->assign_vars(array(
  'HEADING_TITLE'    => HEADING_TITLE_SEND_MAIL,
  'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_REPORTS'  =>INFO_TEXT_REPORTS,
  'TABLE_HEADING_INTERACTION_SUBJECT'=>TABLE_HEADING_INTERACTION_SUBJECT,
  'TABLE_HEADING_INTERACTION_INSERTED'=>TABLE_HEADING_INTERACTION_INSERTED,
  'TABLE_HEADING_INTERACTION_FEADBACK'=>TABLE_HEADING_INTERACTION_FEADBACK,
  'INFO_TEXT_TO'     => INFO_TEXT_TO,
  'INFO_TEXT_TO1'    =>LIST_TABLE2($flag=0,'',"",'application_id','jobseeker_email_address','application_id',$order_by='application_id',$addoption_value="",$addstart="" ,$addmiddle=" - ",$addend="", $list_query,$parameters=' name="TR_applicant_id[]" class="form-control" multiple','','',$list_array1),
  'INFO_TEXT_FROM'   => INFO_TEXT_FROM,
  //'INFO_TEXT_FROM1'  => tep_draw_input_field('TREF_from',$TREF_from, 'size="35"', true ),
  'INFO_TEXT_FROM1'  => tep_db_output(APPLICATION_REPLY_MAIL),
  'INFO_TEXT_SUBJECT'=> INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'size="35" class="form-control required mt-3" placeholder="Subject"', true ),
  'INFO_MAIL_ATTACHMENT'=>INFO_MAIL_ATTACHMENT,
  'INFO_MAIL_ATTACHMENT1'=>tep_draw_file_field('attachment', false),
  'INFO_TEXT_MESSAGE' => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=> tep_draw_textarea_field('message1', 'soft', '80%', '10', $email_text, 'class="form-control required"', true, true),
  'buttons'           => '<button class="btn btn-primary mr-2" type="submit">Preview</button><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id).'"><button class="btn btn-outline-secondary">Cancel</button></a>',
//  'buttons'           => tep_image_submit(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW_MAIL).' <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif',IMAGE_CANCEL).'</a>',
  'form'              => tep_draw_form('email',FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'','post','onsubmit="return ValidateForm(this)" enctype="multipart/form-data"').tep_draw_hidden_field('jobID',$job_id).tep_draw_hidden_field('action1','preview'),
 'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
  'update_message'=>$messageStack->output()));
 $template->pparse('email');
}
elseif($action1=='preview')
{
 if($attachment_file_name!='')
 if(!is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment_file_name))
 $attachment_file_name='';
 $query_string5=encode_string("temp_attachment@#^#@".$attachment_file_name."@#^#@attachment");
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE_SEND_MAIL,
  'INFO_TEXT_TO'=>INFO_TEXT_TO,
  'INFO_TEXT_REPORTS'=>INFO_TEXT_REPORTS,
  'INFO_TEXT_TO1'=>tep_db_output($list_array1),
  'INFO_TEXT_FROM'=>INFO_TEXT_FROM,
  // 'INFO_TEXT_FROM1'=>tep_db_output($TREF_from),
  'INFO_TEXT_FROM1'=>tep_db_output(APPLICATION_REPLY_MAIL),
  'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_db_output($TR_subject).(($attachment_file_name!='')
    ?'<span class="small">
      <a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,"query_string=".$query_string5).'" title="'.tep_db_output(substr($attachment_file_name,14)).'">
      '.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' :'.tep_db_output(substr($attachment_file_name,14))).'
      :'.tep_db_output(substr($attachment_file_name,14)).'
      </a>
      </span>'
    :''),
  'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=>stripslashes($_POST['message1']),
  'buttons'=>'<button class="btn btn-primary mr-2" type="submit" name="send_mail">Send Mail</button> <a href="#" onclick="javascript: set_action(\'back\');"><button class="btn btn-outline-secondary">Back</button></a>',//tep_image_submit(PATH_TO_BUTTON.'button_send_mail.gif', IMAGE_SEND_MAIL, 'name="send_mail"'),
  'form'=>tep_draw_form('preview_mail',FILENAME_RECRUITER_LIST_OF_APPLICATIONS, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('jobID',$job_id).tep_draw_hidden_field('action1','send'),
  'hidden_fields'=>$hidden_fields,
  'update_message'=>$messageStack->output()));
 $template->pparse('preview');
}
elseif($action1=='search_applicant')
{
 $template->assign_vars(array(
 'HEADING_TITLE'         => HEADING_TITLE_SEARCH,
 'form'                  => tep_draw_form('search',FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id,'post','onsubmit="return ValidateForm(this)" '),
 'buttons'               => '<button class="btn btn-primary" type="submit">Search</button>',//tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
 'INFO_TEXT_SEARCH_BY_ROUND'=>INFO_TEXT_SEARCH_BY_ROUND,
 'INFO_TEXT_JOB_TITLE'   =>INFO_TEXT_JOB_TITLE,
 'INFO_TEXT_SEARCH_BY_ID'=>INFO_TEXT_SEARCH_BY_ID,
 'INFO_TEXT_REPORTS'     =>INFO_TEXT_REPORTS,
 'INFO_TEXT_ADVANCE_SEARCH'=>INFO_TEXT_ADVANCE_SEARCH,
 'INFO_TEXT_TRACKING_ROUND1' => LIST_SET_DATA(SELECTION_ROUND_TABLE,"",TEXT_LANGUAGE.'round_name','id',"value",'name="process_round" class="form-select form-select-sm radius-0 mw-100"',INFO_TEXT_ALL,'',$process_round),
 'INFO_TEXT_APPLICATION_STATUS1' => LIST_SET_DATA(APPLICATION_STATUS_TABLE,"",'application_status','id',"priority",'name="application_status" class="form-select form-select-sm radius-0 mw-100" ',INFO_TEXT_ALL,'',$application_status),

 'INFO_TEXT_APPLICATION' => INFO_TEXT_APPLICATION,
 'INFO_TEXT_APPLICATION1'=> tep_draw_input_field('TR_application_id','', 'class="form-control required" placeholder="Application ID"', true ),
 'INFO_TEXT_FIRST_NAME'  => INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1' => tep_draw_input_field('first_name',$first_name, 'class="form-control" placeholder="First name"'),
 'INFO_TEXT_LAST_NAME'   => INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1'  => tep_draw_input_field('last_name',$last_name, 'class="form-control" placeholder="Last name"'),
 'INFO_TEXT_EMAIL_ADDRESS'=> INFO_TEXT_SEARCH_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('TNEF_email_address',$email_address, 'class="form-control" placeholder="Email address"'),
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'  => '<select name="experience" class="form-select form-select form-select-sm radius-0 mw-100">
							<option value="">Any Experience</option>
							<option value="0-1">Less than 1 year</option>
							<option value="2-5">2 year - 5 year</option>
							<option value="6-9">6 years -9 years</option>
							<option value="10-100">More than 10 years</option>
						</select>',//experience_drop_down('name="experience" class="form-control"', 'Any Experience', '', $experience),
 'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
     'match'=>$match_percentage,

 'LEFT_BOX_WIDTH'   => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'        => LEFT_HTML,
 'RIGHT_HTML'       => RIGHT_HTML,
 'update_message'=>$messageStack->output()));

 $template->pparse('search_applicant');
}
else
{
 $check_link='<a href="#" onclick="checkall()">'.INFO_TEXT_CHECK_ALL.'</a> &nbsp;/&nbsp; <a href="#" onclick="uncheckall();">'.INFO_TEXT_UN_CHECK_ALL.'</a>';
 $check_link1='
              
        <!--<label for="validationCustom01">'.INFO_TEXT_WITH_SELECTED.'</label>-->
				<select name="select_action"  class="form-select form-select-sm radius-0 mw-100" onchange="select_action2();">
                 <option value="" selected="selected">'.INFO_TEXT_WITH_SELECTED.':</option>
                 <option value="send_mail" >'.HEADING_TITLE_SEND_MAIL.'</option>
                 <option value="compair_profile" >'.INFO_TEXT_COMPARE_PROFILE.'</option>
              </select>
				  
			  ';
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  //'TABLE_HEADING_RESUME'=>TABLE_HEADING_RESUME,
  'INFO_TEXT_JOB_TITLE' =>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_REPORTS'   =>INFO_TEXT_REPORTS,
  'HEADING_TITLE_SEARCH'=>HEADING_TITLE_SEARCH,
  'INFO_TEXT_RESUME_MATCH'=>INFO_TEXT_RESUME_MATCH,
  'INFO_TEXT_CURRENT_COMPANY'=>INFO_TEXT_CURRENT_COMPANY,
  'INFO_TEXT_CURRENT_JOB_TITLE'=>INFO_TEXT_CURRENT_JOB_TITLE,
  'INFO_TEXT_LOCATION_CITY' =>INFO_TEXT_LOCATION_CITY,
  'INFO_TEXT_EDUCATION_DEGREE_LEVLE'=>INFO_TEXT_EDUCATION_DEGREE_LEVLE,
  'INFO_TEXT_TOTAL_EXPERIENCE'=>INFO_TEXT_TOTAL_EXPERIENCE,
  'INFO_TEXT_SOURCE'          =>INFO_TEXT_SOURCE,
  'INFO_TEXT_APPLIED_ON'      =>INFO_TEXT_APPLIED_ON,
  'INFO_TEXT_TRACKING_ROUND'  =>INFO_TEXT_TRACKING_ROUND,
  //'TABLE_HEADING_VIEW_RESUME' =>TABLE_HEADING_VIEW_RESUME,
  'TABLE_HEADING_SOURCE'=>TABLE_HEADING_SOURCE,
  //'INFO_TEXT_ATS_HELP_LINK'=>'<a href="#" onclick="window.open(\''.FILENAME_ATS_HELP.'\',\'new\',\'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=300,left = 0,top = 0\')" >'.INFO_TEXT_ATS_HELP_LINK."</a>",
  "INFO_TEXT_APPLICATION_STATUS" => INFO_TEXT_APPLICATION_STATUS,
  "INFO_TEXT_APPLICATION_STATUS1" => LIST_SET_DATA(APPLICATION_STATUS_TABLE,"",'application_status','id',"priority",'name="application_status" class="form-select form-select-sm radius-0 mw-100 m-status" onchange="document.page.submit()"',INFO_TEXT_ALL,'',$application_status),
  "INFO_TEXT_TRACKING_ROUND" => INFO_TEXT_TRACKING_ROUND,
  "INFO_TEXT_TRACKING_ROUND1" => LIST_SET_DATA(SELECTION_ROUND_TABLE,"",TEXT_LANGUAGE.'round_name','id',"value",'name="process_round" class="form-select form-select-sm radius-0 mw-100 m-status" onchange="document.page.submit()"',INFO_TEXT_ALL,'',$process_round),
  'new_button'=>'',
  'display_search_key'=>$display_search_key,
  'hidden_fields'=>$hidden_fields,
  'lower_value'=>($_POST['lower']!='')?'document.page.lower.value='.$_POST['lower'].';':'',
  'higher_value'=>($_POST['higher']!='')?'document.page.higher.value='.$_POST['higher'].';':'',
  'check_link'=>($x>0)?$check_link:'',
  'check_link1'=>($x>0)?$check_link1:'',
  'quick_action'=>($x>0)?$quick_action:'',
  'new_button'=>'',
  // 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'ADD_COMMENT_API_URL' => tep_href_link('api/add-comment.php', 'resumeId='),
  'ADD_COMMENT_POST_URL' => tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'search_id=create-add-comment&q='.$search_id),
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>$RIGHT_HTML1.$RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('application');
}

?>