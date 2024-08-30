<?php
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik				#********
**********# Company       : Aynsoft										#**********
**********# Copyright (c) www.aynsoft.com 2004				#**********
**********# Modified in Nov 2017										#**********
**********# Version Jobboard Software :Version 4.1			#**********
***********************************************************
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_SEARCH_APPLICANT);
$template->set_filenames(array('search_applicant' => 'search_applicant.htm','search_applicant1' =>'search_applicant1.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'search_applicant.js';
include_once(FILENAME_BODY);
include_once("general_functions/weight_function.php");

$action1            = (isset($_POST['action1']) ? $_POST['action1'] : '');
//print_r($_POST);

///*****check whether apply without login is true or not*/
	if($row_check_login=getAnyTableWhereData(RECRUITER_TABLE ,"recruiter_id='".$_SESSION['sess_recruiterid']."'","recruiter_applywithoutlogin"))
		$direct_login=($row_check_login['recruiter_applywithoutlogin']=='Yes'?'Yes':'No');
///*****check whether apply without login is true or not*/

if(tep_not_null($action1))
{
 switch($action1)
 {
  case 'search':
   if(tep_not_null($_POST['application_id']))
   {
    $application_id=tep_db_input($_POST['application_id']);
    if($row_check_search=getAnyTableWhereData(APPLICATION_TABLE ,"application_id='".$application_id."' order by id desc ","id"))
     tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,(tep_not_null($application_id)?"&search_id=".$application_id:"")));
    else
    {
     $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_SEARCH_APPLICANT));
    }
   }
   else
   {
    $first_name    = tep_db_input($_POST['first_name']);
    $last_name     = tep_db_input($_POST['last']);
    $email_address = tep_db_input($_POST['TNEF_email_address']);
	$experience=tep_db_input($_POST['experience']);

    $action1       = tep_db_prepare_input($_POST['action1']);

    $hidden_fields = tep_draw_hidden_field('action1',$action1);
    $field         = tep_db_prepare_input($_POST['field']);
    $order         = tep_db_prepare_input($_POST['order']);
    $lower         = (int)tep_db_prepare_input($_POST['lower']);
    $higher        = (int)tep_db_prepare_input($_POST['higher']);
    $whereClause   = '';
    if(tep_not_null($first_name))
    {
     $whereClause   = (tep_not_null($whereClause)?$whereClause.' and ':'');
     $hidden_fields.= tep_draw_hidden_field('first_name',$first_name);
     $whereClause  .= "j.jobseeker_first_name like '%".tep_db_input($first_name)."%'";
    }
    if(tep_not_null($last_name))
    {
     $whereClause   = (tep_not_null($whereClause)?$whereClause.' and ':'');
     $hidden_fields.= tep_draw_hidden_field('last_name',$last_name);
     $whereClause  .= "j.jobseeker_last_name like '%".tep_db_input($last_name)."%'";
    }
    if(tep_not_null($experience))
    {
    $hidden_fields.=tep_draw_hidden_field('experience',$experience);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $explode_string=explode("-",$experience);
    $work_experince=get_name_from_table(EXPERIENCE_TABLE,'id', 'min_experience',tep_db_input($explode_string[0]));
    $whereClause.=" ( jr1.work_experince = '".(int)tep_db_input($work_experince)."' ) ";
    }

    if(tep_not_null($email_address))
    {
     $whereClause   = (tep_not_null($whereClause)?$whereClause.' and ':'');
     $hidden_fields.= tep_draw_hidden_field('email_address',$email_address);
     $whereClause  .= "jl.jobseeker_email_address ='".tep_db_input($email_address)."'";
    }
    $whereClause   = (tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause  .= "jb.recruiter_id ='".tep_db_input($_SESSION['sess_recruiterid'])."'";
    $table_names   = APPLICATION_TABLE." as a left join  ".JOB_TABLE." as jb  on (a.job_id =jb.job_id) left  join ".JOBSEEKER_TABLE." as j on (j.jobseeker_id=a.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) left join ".JOBSEEKER_LOGIN_TABLE." as jl on (j.jobseeker_id=jl.jobseeker_id)  ";
    $field_names   = 'a.id as application_primary_id, a.job_id, a.application_id,j.jobseeker_first_name,j.jobseeker_last_name,jr1.experience_year, jl.jobseeker_email_address,j.jobseeker_privacy,j.jobseeker_city,jb.job_title,a.inserted, jr1.jobseeker_photo, a.resume_id';
    $query1 = "select count(a.id) as x1 from $table_names where $whereClause";
    //echo$query1;
    $result1=tep_db_query($query1);
    $tt_row=tep_db_fetch_array($result1);
    $x1=$tt_row['x1'];
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
    $sort_array=array('a.application_id','j.jobseeker_first_name','jl.jobseeker_email_address','jb.job_title','a.inserted',);
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
     'TABLE_HEADING_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
     'TABLE_HEADING_JOB_TITLE'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_JOB_TITLE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
     'TABLE_HEADING_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][4]."','".$lower."');\"><u>".TABLE_HEADING_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
     ));
     $totalpage=ceil($x1/$higher);
     $query = "select $field_names from $table_names where $whereClause ORDER BY $order_by_clause limit $lower,$higher ";
     $result=tep_db_query($query);
     //echo "<br>$query";//exit;
     $x=tep_db_num_rows($result);
     //echo $x;exit;
     $pno= ceil($lower+$higher)/($higher);
     if($x > 0 && $x1 > 0)
     {
      $alternate=1;
      while($row = tep_db_fetch_array($result))
      {
        $application_primary_id = $row['application_primary_id'];
       $ide=$row["application_id"];
		$job_id=$row['job_id'];
		$resume_id=$row['resume_id'];
		$query_string=encode_string("application_id=".$resume_id."=application_id");
       $query_string3=encode_string("application*=*".$ide."*=*application_id");
$experience_row=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE.' as ex ',"resume_id='".$ide."' order by start_year desc ,start_month desc","ex.company,ex.job_title");

       $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'3').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
       $application_id='<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"&search_id=".$ide).'" target="_blank">'.tep_db_output($ide).'</a>';
//echo "ss=".get_resume_weight($row["resume_id"],$row['job_id']);die;
       $template->assign_block_vars('search_applicant', array(
          'application_id'=>$application_id,
    'match_percentage'=>(get_resume_weight($row["resume_id"],$row['job_id'])==0 ? '0%' :tep_db_output(get_resume_weight($row["resume_id"],$row['job_id']))."%"),

		  'applicant_pipeline'=>'<a class="btn btn-sm btn-text border text-muted me-2 m-dblock" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id).'#Pipeline">'.APPLICANT_PIPELINE.'</a>',
		  'contact'=>'<a class="btn btn-sm btn-text border text-muted me-2 m-dblock" href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,'query_string3='.$query_string3).'">'.TEXT_CONTACT.'</a>',
		  'view_resume'=>'<a class="btn btn-sm btn-text border text-muted me-2 m-dblock" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string.'&app_num='.$application_primary_id).'">'.VIEW_RESUME.'</a>',
  		  'photo'=>(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo'])?tep_image(FILENAME_IMAGE."?size=200&image_name=".PATH_TO_PHOTO.$row['jobseeker_photo'],tep_db_output(SITE_TITLE),'','','class="mini-profile-img rounded"'):'<img src="'.HOST_NAME.'/img/nopic.jpg" class="mini-profile-img rounded">'),
          'name' =>'<a class="text-dark" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"&search_id=".$ide).'" target="_blank">'. tep_db_output($row['jobseeker_first_name'].' '.$row['jobseeker_last_name']).'</a>',
          'email_address' =>($row['jobseeker_privacy']==3?tep_db_output($row['jobseeker_email_address']):'*****'),//tep_db_output($row['jobseeker_email_address']),
		  'experience' =>(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :''),
		'totalexp'=>$row['experience_year'],
          'inserted' => tep_date_long($row['inserted']),
          'job_title' => tep_db_output($row['job_title']),
          'city' => tep_db_output($row['jobseeker_city']),
          'row_selected'=>$row_selected
          ));
       $alternate++;
       $lower = $lower + 1;
      }
      $plural=($x1=="1")?INFO_TEXT_APPLICATION:INFO_TEXT_APPLICATIONS;
      $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE).INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH));
     }
     else
     {
      $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED));
     }
     see_page_number();
     tep_db_free_result($result1);
     tep_db_free_result($result);
   }
   break;
 }
}
if(tep_not_null($action1))
{
 $template->assign_vars(array(
  'HEADING_TITLE'         => HEADING_TITLE,
'HEADING_SEARCH'=>HEADING_SEARCH,
'no_of_applicants'=>$x1,
'INFO_TEXT_CR_APP'=>INFO_TEXT_CR_APP,

'INFO_TEXT_MATCHED'=>INFO_TEXT_MATCHED,
'INFO_ALL_APPLICANTS'=>INFO_ALL_APPLICANTS,
'search_resume_form'=>tep_draw_form('search_applicant', FILENAME_RECRUITER_SEARCH_APPLICANT,'','post').tep_draw_hidden_field('action1','search'),
  'INFO_TEXT_APPLICATION1'=> tep_draw_input_field('application_id','', 'class="form-control mb-2" placeholder="'.INFO_TEXT_APPLICATION.'"', false ),
  'INFO_TEXT_FIRST_NAME1' => tep_draw_input_field('first_name',$first_name, 'class="form-control mb-2" placeholder="'.INFO_FIRST_NAME.'"',false),
  'INFO_TEXT_LAST_NAME1'  => tep_draw_input_field('last_name',$last_name, 'class="form-control mb-2" placeholder="'.INFO_LAST_NAME.'"',false),
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'  => experience_drop_down('name="experience" class="form-select mb-2"', ''.INFO_ANY_EXP.'', '', $experience),
  'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('TNEF_email_address',$email_address, 'class="form-control mb-2" placeholder="'.TABLE_HEADING_EMAIL_ADDRESS.'"', false),
  'buttons'  => tep_draw_submit_button_field('',''.TEXT_SEARCH.'','class="btn btn-primary btn-block"'),
'applicant_tracking'=>'<li>'.tep_draw_form('search_app',FILENAME_RECRUITER_SEARCH_APPLICANT,'','post','').tep_draw_hidden_field('action1','search').'<button type="submit" class="ats-left-bar-btn"> >  All Applicants</button></form></li>
<li><button onclick="location.href=\''.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'?jobID=\''.$JOB_ID.'"  class="ats-left-bar-btn"> > '.APPLICANT_PIPELINE.'</button></li>
<li><a href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT).'&search_id='.$JOB_ID.'"  class="ats-left-bar-btn"> > '.APPLICANT_PIPELINE.'</a></li>
<li><button href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_APPLICANT).'" type="button" class="ats-left-bar-btn"> > '.INFO_APP_SEARCH.'</button></li>
<li>'.tep_draw_form('search_app',FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,'','post','onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('action1','search').'<button type="submit" class="ats-left-bar-btn"> > Selected Applicants</button></form></li>',
'direct_applicants'=>($direct_login=='Yes'?'<a class="btn btn-text text-dark border" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_UNREGISTERED_RESUMES).'" title="'.INFO_TEXT_UNREGISTERED_RESUMES.'">'.INFO_TEXT_UNREGISTERED_RESUMES.'</a>':''),
  'INFO_TEXT_SEARCH_APPLICANTS'=>INFO_TEXT_SEARCH_APPLICANTS,
  'hidden_fields' => $hidden_fields,
  'LEFT_BOX_WIDTH'   => LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
  'LEFT_HTML'        => LEFT_HTML,
  'RIGHT_HTML'       => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('search_applicant1');
}
else
{
 $template->assign_vars(array(
  'HEADING_TITLE'         => HEADING_TITLE,
  'hidden_fields' => $hidden_fields,
  'form'                  => tep_draw_form('search',FILENAME_RECRUITER_SEARCH_APPLICANT,'','post','onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('action1','search'),
  'buttons'               => tep_draw_submit_button_field('',''.TEXT_SEARCH.'','class="btn btn-primary"'),
  'INFO_TEXT_SEARCH_BY_ID'=>INFO_TEXT_SEARCH_BY_ID,
  'INFO_TEXT_ADVANCE_SEARCH'=>INFO_TEXT_ADVANCE_SEARCH,
  'INFO_TEXT_APPLICATION' => INFO_TEXT_APPLICATION,
  'INFO_TEXT_APPLICATION1'=> tep_draw_input_field('application_id','', 'placeholder="'.INFO_APPLICATION_ID.'" class="form-control mb-2"', false ),
  'INFO_TEXT_FIRST_NAME'  => INFO_TEXT_FIRST_NAME,
  'INFO_TEXT_FIRST_NAME1' => tep_draw_input_field('first_name',$first_name, 'placeholder="'.INFO_FIRST_NAME.'" class="form-control mb-2" ',false),
  'INFO_TEXT_LAST_NAME'   => INFO_TEXT_LAST_NAME,
  'INFO_TEXT_LAST_NAME1'  => tep_draw_input_field('last_name',$last_name, 'placeholder="'.INFO_LAST_NAME.'" class="form-control mb-2" ',false),
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'  => experience_drop_down('name="experience" class="form-select mb-2"', INFO_ANY_EXP, '', $experience),

  'INFO_TEXT_EMAIL_ADDRESS'=> INFO_TEXT_EMAIL_ADDRESS,
  'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('TNEF_email_address',$email_address, 'class="form-control mb-2" placeholder="'.INFO_TEXT_EMAIL_ADDRESS.'"', false),

  'INFO_TEXT_SEARCH_APPLICANTS'=>INFO_TEXT_SEARCH_APPLICANTS,
  'INFO_TEXT_ALL_APPLICANTS'=>INFO_TEXT_ALL_APPLICANTS,
  'APPLICATION_ID2'=>APPLICATION_ID2,
  'INFO_TEXT_JSCRIPT_FILE'  =>$jscript_file,
  'LEFT_BOX_WIDTH'   => LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
  'LEFT_HTML'        => LEFT_HTML,
  'RIGHT_HTML'       => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('search_applicant');
}
?>