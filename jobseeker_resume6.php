<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RESUME6);
$template->set_filenames(array('resume_step6' => 'jobseeker_resume6.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_resume6.js';

if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
################# REFERENCE DELETE ##########################
if($_GET['data_delete']=='ResultDelete' && isset($_GET['r6_id']))
{
 $r6_id= explode(",",$_GET['r6_id']);
 for($i=0;$i<count($r6_id);$i++)
	{
	 $table_name=JOBSEEKER_RESUME6_TABLE." as  r6  left outer join ".JOBSEEKER_RESUME1_TABLE ." as r1  on (r1.resume_id =r6.resume_id)";
	 $whereCluse=" r6.r6_id ='".$r6_id[$i] ."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  if($check=getAnyTableWhereData($table_name,$whereCluse,"r1.resume_id,r6_id"))
  {
				$resume_id = $check['resume_id'];
				$r6_id     = $check['r6_id'];
				tep_db_query("delete from ".JOBSEEKER_RESUME6_TABLE." where resume_id='".$check['resume_id']."' and r6_id ='".$r6_id."'");
	 }
 }
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETE,'success');
 tep_redirect(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string);
}
################################################
//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (isset($_POST['query_string']))
  $resume_id =check_data($_POST['query_string'],"@@@","resume_id","resume");
elseif (isset($_GET['query_string']))
   $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
elseif (isset($_POST['r6_id']))
{
 if($check=getAnyTableWhereData(JOBSEEKER_RESUME6_TABLE,"r6_id='".$_POST['r6_id']."'","resume_id,r6_id"))
 {
  $resume_id = $check['resume_id'];
  $r6_id     = $check['r6_id'];
 }
 else
 {
  die();
 }
}

$query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
///// Check  Resume  validity///////////
 if(!$check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id ='".$resume_id."' and jobseeker_id ='".$_SESSION['sess_jobseekerid']."'",'resume_title,experience_year,experience_month'))
 {
  $messageStack->add_session(MESSAGE_RESUME_NOT_EXIST,'error');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
 }
// print_r($check1);
///////////////////////////
#######  REFERENCE LIST START#########################
$query_list = "select *  from " .JOBSEEKER_RESUME6_TABLE . " where resume_id ='".$resume_id."' order by r6_id";
//echo $query_list ;
$result_query_list = tep_db_query($query_list);
$list_row = tep_db_num_rows($result_query_list);
$i=1;
while ($row_reference = tep_db_fetch_array($result_query_list))
{
	$r_id  = $row_reference['r6_id'];
 $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
	$template->assign_block_vars('reference', array(
		'row_selected'    => $row_selected,
		'refname'=> tep_db_output($row_reference['name']),
		'company_name'=> tep_db_output($row_reference['company_name']),
		'email_address'=> tep_db_output($row_reference['email_address']),
		'edit'            => "<a href='#' onclick='document.reference_list".$i.".submit()'>".TABLE_HEADING_EDIT."</a>",
		'delete'          => "<a href='#' onClick=goRemove('".FILENAME_JOBSEEKER_RESUME6."','r6_id','ResultDelete','$r_id');return false;>". tep_db_output(INFO_TEXT_DELETE)." </a>",
		'list_form'       => tep_draw_form('reference_list'.$i, FILENAME_JOBSEEKER_RESUME6, '', 'post','').tep_draw_hidden_field('r6_id',$r_id),
		));
	$i++;
}
tep_db_free_result($result_query_list);
#######  REFERENCE LIST END#########################

//$resume_id=1;//$_POST['resume_id'];
// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
 	case 'reference_add':
  case 'reference_add_next':
  case 'reference_edit':
   $resume_id      = tep_db_prepare_input($resume_id);
   $r6_id          = tep_db_prepare_input($_POST['r6_id']);
   $refname           = tep_db_prepare_input($_POST['TR_refname']);
   $company_name   = tep_db_prepare_input($_POST['company_name']);
   $ref_country    = (tep_not_null($_POST['country'])?tep_db_prepare_input($_POST['country']):0);
   $position_title = tep_db_prepare_input($_POST['position_title']);
   $contact_no     = tep_db_prepare_input($_POST['contact_no']);
   $email_address  = tep_db_prepare_input($_POST['TNEF_email_address']);
   $relationship   = tep_db_prepare_input($_POST['relationship']);
   $error=false;

   if(!tep_not_null($refname))
   {
    $error=true;
    $messageStack->add(ENTER_NAME_ERROR,'error');
   }

			if(!$error)
			{
				$sql_data_array=array('resume_id'     =>$resume_id,
                          'name'          =>$refname,
                          'company_name'  =>$company_name,
                          'country'       =>$ref_country,
				          'position_title'=>$position_title,
                          'contact_no'    =>$contact_no,
                          'email_address' =>$email_address,
                          'relationship'  =>$relationship,
                         );
				if($action=='reference_edit')
	   {
     $sql_data_array1['updated']='now()';
					$sql_data_array['updated']='now()';
	    tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
     tep_db_perform(JOBSEEKER_RESUME6_TABLE, $sql_data_array, 'update', "r6_id = '" .$r6_id. "' and  resume_id ='".$resume_id ."'");
     $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
	   }
    elseif($action=='reference_add' or $action=='reference_add_next')
    {
     $sql_data_array['inserted']='now()';
     tep_db_perform(JOBSEEKER_RESUME6_TABLE, $sql_data_array,'insert');
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
	   }
		  $sql_data_array1['updated']='now()';
		  tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
		  $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
    if($action=='reference_add_next')
    {
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string));
    }
    elseif($action=='reference_add' or $action=='reference_edit')
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string));
			}
			break;
  }
}
//////////////////////////////
if($error)
{
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $reference_button='<button class="btn btn-primary mr-2" type="submit" onclick=set_action1("reference_add")>'.ADD_NEW_REF.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit" onclick=set_action1("reference_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_reference.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action1('reference_add')")."".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action1('reference_add_next')");
 $reference_form=tep_draw_form('reference', FILENAME_JOBSEEKER_RESUME6, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','reference_add');
 if($_POST['action']=="reference_edit" || $_POST['action']=="reference_add" || $_POST['action']=="reference_add_next")
 {
  if($_POST['action']=="reference_edit")
  {
   $resume_id       = $resume_id;
   $resume_name     = $resume_name;
   $refname            = $refname;
   $company_name    = $company_name;
   $reference_button= '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $query_string    = encode_string("resume_id@@@".$resume_id."@@@resume");
			$add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
   $reference_form  = tep_draw_form('reference', FILENAME_JOBSEEKER_RESUME6, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('r6_id',$r6_id).tep_draw_hidden_field('action','reference_edit');
  }
  else
  {
   $reference_button='<button class="btn btn-primary me-2" type="submit" onclick=set_action1("reference_add")>'.ADD_NEW_REF.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit" onclick=set_action1("reference_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_reference.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action1('reference_add')")."".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action1('reference_add_next')");
   $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
			$add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
   $reference_form=tep_draw_form('reference', FILENAME_JOBSEEKER_RESUME6, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
  }
 }
}
else
{
	if(isset($_POST['r6_id']))
 {
  $fields="r6_id,name, company_name,position_title, contact_no, email_address,relationship,country ";
  if($row2=getAnyTableWhereData(JOBSEEKER_RESUME6_TABLE,"r6_id='".$r6_id ."' and resume_id ='".$resume_id."'",$fields))
  {
   $r6_id                = tep_db_prepare_input($row2['r6_id']);
   $refname= tep_db_prepare_input($row2['name']);
   $company_name= tep_db_prepare_input($row2['company_name']);
   $ref_country= tep_db_prepare_input($row2['country']);
   $position_title= tep_db_prepare_input($row2['position_title']);
   $contact_no= tep_db_prepare_input($row2['contact_no']);
   $email_address= tep_db_prepare_input($row2['email_address']);
   $relationship= tep_db_prepare_input($row2['relationship']);
   $reference_button     = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
$add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
   $reference_form       = tep_draw_form('reference', FILENAME_JOBSEEKER_RESUME6, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('r6_id',$r6_id).tep_draw_hidden_field('action','reference_edit');
  }
 }
 else
 {
  $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
  $reference_button='<button class="btn btn-primary me-2" type="submit" onclick=set_action1("reference_add")>'.ADD_NEW_REF.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit" onclick=set_action1("reference_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_reference.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action1('reference_add')")."".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action1('reference_add_next')");
		$add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME3."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
  $reference_form=tep_draw_form('reference', FILENAME_JOBSEEKER_RESUME6, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
 }
}
 $resume1='<div class="step ms-0"><a class="" href ="#"  onclick="document.resume.submit()">'.INFO_TEXT_LEFT_RESUME.'</a></div>';
		  $resume2='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EXPERIENCE.'</a></div>';
    $resume3='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EDUCATION.'</a></div>';
		  $resume4='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_SKILLS.'</a></div>';
		  $resume5='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_UPLOAD.'</a></div>';
				$resume6='<div class="step current"><a class=" " href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_REFERENCE.'</a></div>';
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


	<td width="19%">
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
	<li class="resume-left-title-active"><i class="fa fa-bookmark resume-active-icon" aria-hidden="true"></i>'.$resume6.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-graduation-cap resume-inactive-icon" aria-hidden="true"></i>'.$resume3.'</li>
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

/*if($state_error)
{
 $zones_array=tep_get_country_zones($TR_country);
 if(sizeof($zones_array) > 1)
 {
  define('INFO_TEXT_STATE1',LIST_SET_DATA(ZONES_TABLE,"",TEXT_LANGUAGE.'zone_name','zone_id',TEXT_LANGUAGE."zone_name",'name="state"',"state",'',$state_value)." ".tep_draw_input_field('state1',is_numeric($state_value)?'': $state_value,'size="25"',true));
 }
 else
 {
  define('INFO_TEXT_STATE1',LIST_SET_DATA(ZONES_TABLE,"",TEXT_LANGUAGE.'zone_name','zone_id',TEXT_LANGUAGE."zone_name",'name="state"',"state",'',$state_value)." ".tep_draw_input_field('state1', is_numeric($state_value)?'': $state_value,'size="25"',true));
 }
}
else
{
 define('INFO_TEXT_STATE1',LIST_SET_DATA(ZONES_TABLE,"",TEXT_LANGUAGE.'zone_name','zone_id',TEXT_LANGUAGE."zone_name",'name="state"',"state",'',$state_value)." ".tep_draw_input_field('state1',is_numeric($state_value)?'': $state_value,'size="25"',true));
}*/
if($messageStack->size('work_history') > 0)
 $update_message=$messageStack->output('work_history');
else
 $update_message=$messageStack->output();
$template->assign_vars(array(
 'HEADING_TITLE'                => HEADING_TITLE,
 'add_save_button'              => $add_save_button,
 'add_next_button'              => $add_next_button,
 'work_history_form'            => $work_history_form,
 'work_experience_form'         => tep_draw_form('work_experience', FILENAME_JOBSEEKER_RESUME6,'query_string='.$query_string, 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('action','add_experience'),
	'INFO_TEXT_EXPERIENCE'         => INFO_TEXT_EXPERIENCE,
	'INFO_TEXT_EXPERIENCE1'        => year_month_experience_drop("name='experience_year'",$check1['experience_year'],"name='experience_month'",$check1['experience_month'],$required=false,$show_name=true ,$change_order=false),
 //'TABLE_HEADING_START_DATE'     => TABLE_HEADING_START_DATE,
 //'TABLE_HEADING_END_DATE'       => TABLE_HEADING_END_DATE,
 //'TABLE_HEADING_JOB_TITLE'      => TABLE_HEADING_JOB_TITLE,
 'TABLE_HEADING_EDIT'           => TABLE_HEADING_EDIT,
 'TABLE_HEADING_DELETE'         => TABLE_HEADING_DELETE,
 'SECTION_ACCOUNT_RESUME_NAME'  => SECTION_ACCOUNT_RESUME_NAME,
 //'SECTION_WORK_HISTORY_DETAIL'  => SECTION_WORK_HISTORY_DETAIL,
 //'SECTION_WORK_EXPERIENCE'      => SECTION_WORK_EXPERIENCE,
 'REQUIRED_INFO'                => REQUIRED_INFO,
 'INFO_TEXT_RESUME_NAME'        => INFO_TEXT_RESUME_NAME,
 'INFO_TEXT_RESUME_NAME1'       => $check1['resume_title'],
 //'INFO_TEXT_COMPANY'            => INFO_TEXT_COMPANY,
 'INFO_TEXT_COMPANY1'           => tep_draw_input_field('TR_company', $company,'size="45"',true),
 //'INFO_TEXT_CITY'               => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'              => tep_draw_input_field('city', $city,'size="45"'),

 //'INFO_TEXT_COUNTRY'            => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'           => tep_get_country_list('TR_country',$TR_country),
 //'INFO_TEXT_STATE'              => INFO_TEXT_STATE,
 //'INFO_TEXT_STATE1'             => INFO_TEXT_STATE1,
 'COUNTRY_STATE_SCRIPT'         => country_state($c_name='TR_country',$c_d_value=INFO_TEXT_PLEASE_SELECT_COUNTRY.'...',$s_name='state',$s_d_value='state','zone_id',$state_value),

 //'INFO_TEXT_JOB_TITLE'          => INFO_TEXT_JOB_TITLE,
 'INFO_TEXT_JOB_TITLE1'         => tep_draw_input_field('TR_job_title', $job_title,'size="45"',true),
 //'INFO_TEXT_SALARY'             => INFO_TEXT_SALARY,
 'INFO_TEXT_SALARY1'            => LIST_SET_DATA(CURRENCY_TABLE,"",'code','currencies_id',"code",'name="currency" ',TEXT_PLEASE_SELECT,'',$currency)." ".tep_draw_input_field('salary', $salary,'size="10"',false).'&nbsp;&nbsp;Per&nbsp;'.tep_draw_radio_field('salary_per', 'Year', '', $salary_per, 'id="salary_per1"').'&nbsp;<label for="salary_per1">'.INFO_TEXT_YEAR.'</label>&nbsp;'.tep_draw_radio_field('salary_per', 'Month', '', $salary_per, 'id="salary_per2"').'&nbsp;<label for="salary_per2">'.INFO_TEXT_MONTH.'</label>&nbsp;'.tep_draw_radio_field('salary_per', 'Hour', '', $salary_per, 'id="salary_per3"').'&nbsp;<label for="salary_per3">'.INFO_TEXT_HOUR.'</label>',
 //'INFO_TEXT_COMPANY_INDUSTRY'   => INFO_TEXT_COMPANY_INDUSTRY,
 'INFO_TEXT_COMPANY_INDUSTRY1'  => LIST_SET_DATA(JOB_CATEGORY_TABLE,"",TEXT_LANGUAGE.'category_name','id',TEXT_LANGUAGE."category_name",'name="company_industry" ',INFO_TEXT_PLEASE_SELECT."...",'',$company_industry),
 //'INFO_TEXT_START_DATE'         => INFO_TEXT_START_DATE,
 'INFO_TEXT_START_DATE1'        => year_month_list("name='TR_start_year'",'1970',date("Y"),$start_year,"name='TR_start_month'",$start_month,true,true,true),
 'INFO_TEXT_TILL_DATE1'         => tep_draw_checkbox_field('current', 'Yes', '',$still_work,'id="checkbox_current" onclick="set_current_emp()"')."&nbsp;<span class='small'><label for='checkbox_current'>".'INFO_TEXT_TILL_DATE'."</label></div>",
// 'INFO_TEXT_END_DATE'           => INFO_TEXT_END_DATE,
 'INFO_TEXT_END_DATE1'          => year_month_list("name='TR_end_year'",'1970',date("Y"),$end_year,"name='TR_end_month'",$end_month,true,true,true),

 //'INFO_TEXT_DESCRIPTION'        => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'       => tep_draw_textarea_field('description', 'soft', '40', '3', stripslashes($description), '', true, false),

	'reference_button'             => $reference_button,
 'reference_form'               => $reference_form,
 'TABLE_HEADING_NAME'           => TABLE_HEADING_NAME,
 'TABLE_HEADING_COMPANY_NAME'   => TABLE_HEADING_COMPANY_NAME,
 'TABLE_HEADING_EMAIL_ADDRESS'  => TABLE_HEADING_EMAIL_ADDRESS,
 'TABLE_HEADING_EDIT'           => TABLE_HEADING_EDIT,
 'TABLE_HEADING_DELETE'         => TABLE_HEADING_DELETE,

 'SECTION_ACCOUNT_RESUME_NAME'  => SECTION_ACCOUNT_RESUME_NAME,
 'SECTION_REFERENCE_DETAILS'    => SECTION_REFERENCE_DETAILS,

 'INFO_TEXT_RESUME_NAME'        => INFO_TEXT_RESUME_NAME,
 'INFO_TEXT_RESUME_NAME1'       => $check1['resume_title'],

 'INFO_TEXT_NAME'               => INFO_TEXT_NAME,
 'INFO_TEXT_NAME1'              => tep_draw_input_field('TR_refname', $refname,'class="form-control required" size="46"',true),

 'INFO_TEXT_COMPANY_NAME'       => INFO_TEXT_COMPANY_NAME,
 'INFO_TEXT_COMPANY_NAME1'      => tep_draw_input_field('company_name', $company_name,'class="form-control"size="46"',false),

 'INFO_TEXT_REF_COUNTRY'        => INFO_TEXT_REF_COUNTRY,
 'INFO_TEXT_REF_COUNTRY1'       => LIST_SET_DATA(COUNTRIES_TABLE,"",TEXT_LANGUAGE.'country_name','id',TEXT_LANGUAGE."country_name",'class="form-select" name="country"',INFO_TEXT_PLEASE_SELECT,"",$ref_country),

 'INFO_TEXT_POSITION_TITLE'     => INFO_TEXT_POSITION_TITLE,
 'INFO_TEXT_POSITION_TITLE1'    => tep_draw_input_field('position_title', $position_title,'class="form-control"size="46"',false),

 'INFO_TEXT_CONTACT_NO'         => INFO_TEXT_CONTACT_NO,
 'INFO_TEXT_CONTACT_NO1'        => tep_draw_input_field('contact_no', $contact_no,'class="form-control"size="46"',false),

 'INFO_TEXT_EMAIL_ADDRESS'      => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'     => tep_draw_input_field('TNEF_email_address', $email_address,'class="form-control"size="30"',false),

 'INFO_TEXT_RELATIONSHIP'       => INFO_TEXT_RELATIONSHIP,
 'INFO_TEXT_RELATIONSHIP1'      => tep_draw_input_field('relationship', $relationship,'class="form-control"size="46"',false),
 'INFO_TEXT_JSCRIPT_FILE'       => $jscript_file,

 'LEFT_BOX_WIDTH'               => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'              => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'JOBSEEKER_RESUME_LEFT'        => JOBSEEKER_RESUME_LEFT,
 'RIGHT_HTML'                   => RIGHT_HTML,
 'update_message'               => $update_message));
$template                       -> pparse('resume_step6');
?>