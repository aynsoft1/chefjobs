<?php
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RESUME3);
$template->set_filenames(array('resume_step3' => 'jobseeker_resume3.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_resume3.js';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
//print_r($_POST);echo "<br> ";
################# EDUCTION DELETE ##########################
if($_GET['data_delete']=='ResultDelete' && isset($_GET['r3_id']))
{
 $r3_id= explode(",",$_GET['r3_id']);
 for($i=0;$i<count($r3_id);$i++)
	{
	 $table_name=JOBSEEKER_RESUME3_TABLE." as  r3  left outer join ".JOBSEEKER_RESUME1_TABLE ." as r1  on (r1.resume_id =r3.resume_id)";
	 $whereCluse=" r3.r3_id ='".$r3_id[$i] ."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  if($check=getAnyTableWhereData($table_name,$whereCluse,"r1.resume_id,r3_id"))
  {
				$resume_id = $check['resume_id'];
				$r3_id     = $check['r3_id'];
				tep_db_query("delete from ".JOBSEEKER_RESUME3_TABLE." where resume_id='".$check['resume_id']."' and r3_id ='".$r3_id."'");
	 }
 }
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETE,'success');
 tep_redirect(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string);
}
################################################
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (isset($_POST['query_string']))
 $resume_id =check_data($_POST['query_string'],"@@@","resume_id","resume");
elseif (isset($_GET['query_string']))
  $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
elseif (isset($_POST['r3_id']))
{
 if($check=getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE,"r3_id='".$_POST['r3_id']."'","resume_id,r3_id"))
 {
  $resume_id = $check['resume_id'];
  $r3_id     = $check['r3_id'];
 }
 else
 {
  die();
 }
}
$query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
///// Check  Resume  validity///////////
if(!$check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id ='".$resume_id."' and jobseeker_id ='".$_SESSION['sess_jobseekerid']."'",'resume_title'))
{
  $messageStack->add_session(MESSAGE_RESUME_NOT_EXIST,'error');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
}
// print_r($check1);
#######  EDUCATION LIST #########################
$query_list = "select r3_id,resume_id, degree, school, city, state,state_id, country, start_month, start_year, end_month, end_year, related_info  from " .JOBSEEKER_RESUME3_TABLE . " where resume_id ='".$resume_id."' order by r3_id";
//echo $query_list ;
$result_query_list = tep_db_query($query_list);
$list_row = tep_db_num_rows($result_query_list);
$i=1;
while ($row_education = tep_db_fetch_array($result_query_list))
{
	$r_id  = $row_education['r3_id'];
 if($row_education['start_year']!=0 && $row_education['start_month']!=0)
		$start_date  = formate_date(tep_db_output($row_education['start_year']).'-'.tep_db_output($row_education['start_month']).'-01'," M Y ");
 else
		$start_date  = '';
	if($row_education['end_year']!=0 && $row_education['end_month']!=0)
		$end_date  = formate_date(tep_db_output($row_education['end_year']).'-'.tep_db_output($row_education['end_month']).'-01'," M Y ");
 else
		$end_date  = '';
 $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
	$template->assign_block_vars('education', array(
		'row_selected'    => $row_selected,
		'institution_name'=> tep_db_output($row_education['school']),
		'degree'          => get_name_from_table(EDUCATION_LEVEL_TABLE,TEXT_LANGUAGE.'education_level_name', 'id',tep_db_output($row_education['degree'])),
		'start_date'      => $start_date,
		'end_date'        => $end_date,
		'edit'            => "<a href='#' onclick='document.education_list".$i.".submit()'>".TABLE_HEADING_EDIT."</a>",
		'delete'          => "<a href='#' onClick=goRemove('".FILENAME_JOBSEEKER_RESUME3."','r3_id','ResultDelete','$r_id');return false;>". tep_db_output(INFO_TEXT_DELETE)." </a>",
		'list_form'       => tep_draw_form('education_list'.$i, FILENAME_JOBSEEKER_RESUME3, '', 'post','').tep_draw_hidden_field('r3_id',$r_id),
		));
	$i++;
}
tep_db_free_result($result_query_list);
if(tep_not_null($action))
{
 switch($action)
 {
  case 'education_add':
  case 'education_add_next':
  case 'education_edit':
   $resume_id                = $resume_id;
   $r3_id                    = $_POST['r3_id'];
   $degree                   = $_POST['TR_degree'];
   $school                   = $_POST['school'];
   $specialization           = $_POST['specialization'];
   $city                     = $_POST['city'];
   $country                  =(int)tep_db_prepare_input($_POST['TR_country']);
   $start_month              = (int)$_POST['SR_start_month'];
   $start_year               = (int)$_POST['SR_start_year'];
   $end_month                = (int)$_POST['SR_end_month'];
   $end_year                 = (int)$_POST['SR_end_year'];
   $related_info             = $_POST['related_info'];
   $error=false;

   $start_date=formate_date($start_year.'-'.$start_month.'-01',"Ym");
   $end_date=formate_date($end_year.'-'.$end_month.'-01',"Ym");
   if($start_date>$end_date)
   {
    $start_date.'- '.$start_date;
    $error=true;
    $messageStack->add(MESSAGE_DEGREE_DATE_ERROR, 'error');
   }
   if(is_numeric($country) == false)
   {
    $error = true;
    $messageStack->add(ENTRY_COUNTRY_ERROR,'jobseeker_account');
   }
   if(!$error)
			{
				$sql_data_array=array('resume_id'=>tep_db_prepare_input($resume_id),
                          'degree'                   =>tep_db_prepare_input($degree),
                          'school'                   =>tep_db_prepare_input($school),
                          'specialization'           =>tep_db_prepare_input($specialization),
                          'city'                     =>tep_db_prepare_input($city),
                          'country'                  =>tep_db_prepare_input($country),
                          'start_month'              =>tep_db_prepare_input($start_month),
                          'start_year'               =>tep_db_prepare_input($start_year),
                          'end_month'                =>tep_db_prepare_input($end_month),
                          'end_year'                 =>tep_db_prepare_input($end_year),
                          'related_info'             =>tep_db_prepare_input($related_info));

    if($action=='education_edit')
	   {
     $sql_data_array1['updated']='now()';
	    tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
     tep_db_perform(JOBSEEKER_RESUME3_TABLE, $sql_data_array, 'update', "r3_id = '" .$r3_id. "' and  resume_id ='".$resume_id ."'");
     $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
	   }
    elseif($action=='education_add' or $action=='education_add_next')
    {
     tep_db_perform(JOBSEEKER_RESUME3_TABLE, $sql_data_array,'insert');
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
	   }
		  $sql_data_array1['updated']='now()';
		  tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
		  $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
    if($action=='education_add_next')
    {
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME4."?query_string=".$query_string));
    }
    elseif($action=='education_add' or $action=='education_edit')
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string));
			}
			break;
	}
}
//////////////////////////////
if($error)
{
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $add_save_button='<button class="btn btn-primary me-2" type="submit">'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE,"");
 $registration_form=tep_draw_form('defineForm', FILENAME_JOBSEEKER_RESUME3, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','add');

 $education_button='<button class="btn btn-primary me-2" type="submit" onclick=set_action("education_add")>'.ADD_NEW_EDUCATION.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit" onclick=set_action("education_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_education.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('education_add_next')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('education_add_next')");
 $education_form=tep_draw_form('education', FILENAME_JOBSEEKER_RESUME3, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');

	if($_POST['action']=="education_edit" || $_POST['action']=="education_add" || $_POST['action']=="education_add_next")
 {
  if($_POST['action']=="education_edit")
  {
   $resume_id                = $resume_id;
   $resume_name              = $resume_name;
   $organization             = $organization;
   $start_month              = $start_month;
   $start_year               = $start_year;
   $end_month                = $end_month;
   $end_year                 = $end_year;
   $education_button     = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
   $education_form=tep_draw_form('education', FILENAME_JOBSEEKER_RESUME3, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('r3_id',$r3_id).tep_draw_hidden_field('action','education_edit');
  }
  else
  {
   $education_button='<button class="btn btn-primary me-2" type="submit" onclick=set_action("education_add")>'.ADD_NEW_EDUCATION.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit" onclick=set_action("education_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_education.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('education_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('education_add_next')");
   $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
   $education_form=tep_draw_form('education', FILENAME_JOBSEEKER_RESUME3, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
  }
 }
}
else
{
 if(isset($_POST['r3_id']))
 {
  $fields="r3_id,degree, school,specialization, city, state,state_id, country, start_month, start_year, end_month, end_year, related_info ";
  if($row2=getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE,"r3_id='".$r3_id ."' and resume_id ='".$resume_id."'",$fields))
  {
   $r3_id                = tep_db_prepare_input($row2['r3_id']);
   $degree               = tep_db_prepare_input($row2['degree']);
   $school               = tep_db_prepare_input($row2['school']);
   $specialization       = tep_db_prepare_input($row2['specialization']);
   $city                 = tep_db_prepare_input($row2['city']);
   $TR_country           = tep_db_prepare_input($row2['country']);
   $start_month          = tep_db_prepare_input($row2['start_month']);
   $start_year           = tep_db_prepare_input($row2['start_year']);
   $end_month            = tep_db_prepare_input($row2['end_month']);
   $end_year             = tep_db_prepare_input($row2['end_year']);
   $related_info         = tep_db_prepare_input($row2['related_info']);
   $education_button     = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $education_form       = tep_draw_form('education', FILENAME_JOBSEEKER_RESUME3, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('r3_id',$r3_id).tep_draw_hidden_field('action','education_edit');
  }
 }
 else
 {
  $TR_country        = DEFAULT_COUNTRY_ID;
  $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
  $education_button='<button class="btn btn-primary me-2" type="submit" onclick=set_action("education_add")>'.ADD_NEW_EDUCATION.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit" onclick=set_action("education_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_education.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('education_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('education_add_next')");
  $education_form=tep_draw_form('education', FILENAME_JOBSEEKER_RESUME3, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
 }
}
$add_next_button = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME4."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";

 $resume1='<div class="step ms-0"><a class="" href ="#"  onclick="document.resume.submit()">'.INFO_TEXT_LEFT_RESUME.'</a></div>';
		  $resume2='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EXPERIENCE.'</a></div>';
    $resume3='<div class="step current"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EDUCATION.'</a></div>';
		  $resume4='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_SKILLS.'</a></div>';
		  $resume5='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_UPLOAD.'</a></div>';
				$resume6='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_REFERENCE.'</a></div>';
		  $view_resume='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_VIEW_RESUME.'</a></div>';
//////////////Jobseeker resume left start//////
	define('JOBSEEKER_RESUME_LEFT','

  <div class="mb-3">
  <div class="row">
	<div class="">
	<div class="arrow-steps clearfix mx-auto">
    '.$resume1.'
    '.$resume2.'
   '.$resume6.'
  '.$resume3.'
   '.$resume4.'
   '.$resume5.'
   '.$view_resume.'
   </div>
   </div>
   </div>
   </div>

	   <div class="resume-side-menu" style="display:none;">
	   <ul class="resume-side-nav">'.tep_draw_form('resume', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'<li class="resume-left-title-inactive"><i class="fa fa-file-text resume-inactive-icon" aria-hidden="true"></i> '.$resume1.'</li></form>
										'.tep_draw_form('resume1', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
												<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#resume_name" onclick="document.resume1.submit()">'.INFO_TEXT_RESUME_NAME.'</a></li></form>
											'.tep_draw_form('resume2', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
												<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#objective" onclick="document.resume2.submit()">'.INFO_TEXT_OBJECTIVE.'</a></li></form>
											'.tep_draw_form('resume3', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
												<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#target_job" onclick="document.resume3.submit()">'.INFO_TEXT_TARGET_JOB.'</a></li></form>
											</ul>
											<ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-briefcase resume-inactive-icon" aria-hidden="true"></i> '.$resume2.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'#total_experience" >'.INFO_TEXT_TOTAL_WORK_EXP.'</a></li>
											<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'#experience" >'.INFO_TEXT_YOUR_WORK_EXPERIENCE.'</a></li></ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-bookmark resume-inactive-icon" aria-hidden="true"></i>'.$resume6.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-active"><i class="fa fa-graduation-cap resume-active-icon" aria-hidden="true"></i>'.$resume3.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_EDUCATION_DETAILS.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-user resume-inactive-icon" aria-hidden="true"></i>'.$resume4.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'#skill" >'.INFO_TEXT_YOUR_SKILLS.'</a></li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'#language" >'.INFO_TEXT_LANGUAGES.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-upload resume-inactive-icon" aria-hidden="true"></i>'.$resume5.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_RESUME.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-eye resume-inactive-icon" aria-hidden="true"></i>'.$view_resume.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#profile" >'.INFO_TEXT_PERSONAL_PROFILE.'</a></li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#work_experience" >'.INFO_TEXT_EXPERIENCE.'</a></li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#target_job" >'.INFO_TEXT_TARGET_JOB.'</a></li></ul></div></td>');
if($messageStack->size('defineForm') > 0)
 $update_message=$messageStack->output('defineForm');
else
 $update_message=$messageStack->output();
$template->assign_vars(array(
 'HEADING_TITLE'=> HEADING_TITLE,
 'add_save_button'=> $add_save_button,
 'add_next_button'=> $add_next_button,
 'education_button'=> $education_button,
 'education_form'                 => $education_form,
 'TABLE_HEADING_INSTITUTION_NAME'=> TABLE_HEADING_INSTITUTION_NAME,
 'TABLE_HEADING_DEGREE'          => TABLE_HEADING_DEGREE,
 'TABLE_HEADING_DEGREE_OBT_DATE' => TABLE_HEADING_DEGREE_OBT_DATE,
 'TABLE_HEADING_DEGREE_END_DATE' => TABLE_HEADING_DEGREE_END_DATE,
 //'TABLE_HEADING_COUNTRY_NAME'    => TABLE_HEADING_COUNTRY_NAME,
 //'TABLE_HEADING_WORK_STATUS'     => TABLE_HEADING_WORK_STATUS,
 //'TABLE_HEADING_ISSUED_BY'       => TABLE_HEADING_ISSUED_BY,
 'TABLE_HEADING_COUNTRY'         => TABLE_HEADING_COUNTRY,
 'TABLE_HEADING_TYPE'            => TABLE_HEADING_TYPE,
 'TABLE_HEADING_EDIT'            => TABLE_HEADING_EDIT,
 'TABLE_HEADING_DELETE'          => TABLE_HEADING_DELETE,

 'SECTION_ACCOUNT_RESUME_NAME'   => SECTION_ACCOUNT_RESUME_NAME,
 'SECTION_EDUCATION_DETAILS'     => SECTION_EDUCATION_DETAILS,

 'REQUIRED_INFO'                  => REQUIRED_INFO,
 'INFO_TEXT_RESUME_NAME'          => INFO_TEXT_RESUME_NAME,
 'INFO_TEXT_RESUME_NAME1'         => $check1['resume_title'],

 'INFO_TEXT_DEGREE'               => INFO_TEXT_DEGREE,
 'INFO_TEXT_DEGREE1'              => LIST_SET_DATA(EDUCATION_LEVEL_TABLE,"",TEXT_LANGUAGE.'education_level_name','id',TEXT_LANGUAGE."education_level_name",'name="TR_degree" class="form-select required"',TEXT_PLEASE_SELECT."...",'',$degree),

 'INFO_TEXT_INSTITUTION_NAME'     => INFO_TEXT_INSTITUTION_NAME,
 'INFO_TEXT_INSTITUTION_NAME1'    => tep_draw_input_field('school', $school,'class="form-control" size="46"'),

 'INFO_TEXT_SPECIALIZATION'       => INFO_TEXT_SPECIALIZATION,
 'INFO_TEXT_SPECIALIZATION1'      => tep_draw_input_field('specialization', $specialization,'class="form-control" size="46"'),

 'INFO_TEXT_CITY'                 => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'                => tep_draw_input_field('city', $city,'class="form-control" size="46"'),

 'INFO_TEXT_COUNTRY'              => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'             => tep_get_country_list('TR_country',$TR_country, 'class="form-select"'),

 'INFO_TEXT_START_DATE'           => INFO_TEXT_START_DATE,
 'INFO_TEXT_START_DATE1'          => year_month_list("name='SR_start_year' class='form-select required'",'1970',date("Y"),$start_year,"name='SR_start_month' class='form-select required'",$start_month,false,true,true),
 'INFO_TEXT_END_DATE'             => INFO_TEXT_END_DATE,
 'INFO_TEXT_END_DATE1'            => year_month_list("name='SR_end_year' class='form-select required'",'1970',date("Y"),$end_year,"name='SR_end_month' class='form-select required'",$end_month,false,true,true),
 'INFO_TEXT_RELATED_INFO'         => INFO_TEXT_RELATED_INFO,
 'INFO_TEXT_RELATED_INFO1'        => tep_draw_textarea_field('related_info', 'soft', '50', '3', stripslashes($related_info), 'class="form-control h-100"', true, false),
 'INFO_TEXT_JSCRIPT_FILE'         => $jscript_file,

 'LEFT_BOX_WIDTH'                 => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'                => RIGHT_BOX_WIDTH1,
 'JOBSEEKER_RESUME_LEFT'          => JOBSEEKER_RESUME_LEFT,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'                     => RIGHT_HTML,
 'update_message'=> $update_message));
$template -> pparse('resume_step3');
?>