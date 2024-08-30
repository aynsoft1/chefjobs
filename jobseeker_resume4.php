<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RESUME4);
$template->set_filenames(array('resume_step4' => 'jobseeker_resume4.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_resume4.js';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}

//print_r($_GET);die();

if(isset($_POST['action']))
{
// print_r($_POST);
// exit;
}

################# SKILLS DELETE ##########################
if($_GET['data_delete']=='ResultDelete' && isset($_GET['r4_id']))
{
 $r4_id= explode(",",$_GET['r4_id']);
	for($i=0;$i<count((array)$r4_id);$i++)
	{
  $table_name=JOBSEEKER_RESUME4_TABLE." as  r6  left outer join ".JOBSEEKER_RESUME1_TABLE ." as r1  on (r1.resume_id =r6.resume_id)";
  $whereCluse=" r6.r4_id ='".$r4_id[$i] ."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  if($check=getAnyTableWhereData($table_name,$whereCluse,"r1.resume_id,r4_id"))
  {
   $resume_id = $check['resume_id'];
   $r4_id     = $check['r4_id'];
   tep_db_query("delete from ".JOBSEEKER_RESUME4_TABLE." where resume_id='".$check['resume_id']."' and r4_id ='".$r4_id ."'");
	 }
 }
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETE,'success');
 tep_redirect(FILENAME_JOBSEEKER_RESUME4."?query_string=".$query_string);
}
################################################

################# LANGUAGES DELETE ##############
if($_GET['data_delete']=='ResultDelete'&& isset($_GET['r5_id']))
{
 $r5_id= explode(",",$_GET['r5_id']);
	for($i=0;$i<count((array) $r5_id);$i++)
	{
  $table_name=JOBSEEKER_RESUME5_TABLE." as  r7  left outer join ".JOBSEEKER_RESUME1_TABLE ." as r1  on (r1.resume_id =r7.resume_id)";
  $whereCluse=" r7.r5_id ='".$r5_id[$i] ."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  if($check=getAnyTableWhereData($table_name,$whereCluse,"r1.resume_id,r5_id"))
  {
   $resume_id = $check['resume_id'];
   $r5_id     = $check['r5_id'];
   tep_db_query("delete from ".JOBSEEKER_RESUME5_TABLE." where resume_id='".$check['resume_id']."' and r5_id ='".$r5_id ."'");
	 }
 }
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETE,'success');
 tep_redirect(FILENAME_JOBSEEKER_RESUME4."?query_string=".$query_string);
}
###############################################
//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (isset($_POST['query_string']))
  $resume_id =check_data($_POST['query_string'],"@@@","resume_id","resume");
elseif (isset($_GET['query_string']))
   $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
elseif (isset($_POST['r4_id']))
{
 if($check=getAnyTableWhereData(JOBSEEKER_RESUME4_TABLE,"r4_id='".$_POST['r4_id']."'","resume_id,r4_id"))
 {
  $resume_id = $check['resume_id'];
  $r4_id     = $check['r4_id'];
 }
 else
 {
 }
}
elseif (isset($_POST['r5_id']))
{
 if($check=getAnyTableWhereData(JOBSEEKER_RESUME5_TABLE,"r5_id='".$_POST['r5_id']."'","resume_id,r5_id"))
 {
  $resume_id = $check['resume_id'];
  $r5_id     = $check['r5_id'];
 }
 else
 {
 }
}
$query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
///// Check  Resume  validity///////////
if(!$check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id ='".$resume_id."' and jobseeker_id ='".$_SESSION['sess_jobseekerid']."'",'resume_title'))
 {
  $messageStack->add_session(MESSAGE_RESUME_NOT_EXIST,'error');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
 }

 //print_r($check1);
///////////////////////////

#######  SKILLS LIST #########################
$query_list = "select r4_id,skill, skill_level, last_used, years_of_exp  from " .JOBSEEKER_RESUME4_TABLE . " where resume_id ='".$resume_id."' order by r4_id";
//echo $query_list ;
$result_query_list = tep_db_query($query_list);
$list_row = tep_db_num_rows($result_query_list);
 $i=1;
 while ($row_jobseeker_certification = tep_db_fetch_array($result_query_list))
 {
  $r_id  = $row_jobseeker_certification['r4_id'];
$skill_level=(tep_not_null($row_jobseeker_certification['skill_level'])?tep_db_output($row_jobseeker_certification['skill_level']):3);
  $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $template->assign_block_vars('skills', array(
   'row_selected'   => $row_selected,
   'skill'          => tep_db_output($row_jobseeker_certification['skill']),
   'skill_level'    => tep_db_get_field(SKILL_LEVEL_TABLE,TEXT_LANGUAGE.'skill_name',"id=".$skill_level),
   'last_used'      => tep_db_get_field(SKILL_LAST_USED_TABLE,TEXT_LANGUAGE.'skill_last_used','id='.tep_db_output($row_jobseeker_certification['last_used'])),
   'years_of_exp'   => tep_db_output($row_jobseeker_certification['years_of_exp']),
   'edit_skills'    => "<a href='#' onclick='document.skills_list".$i.".submit()'>".tep_db_output(INFO_TEXT_EDIT)."</a>",
   'delete'         => "<a href='#' onClick=goRemove('".FILENAME_JOBSEEKER_RESUME4."','r4_id','ResultDelete','$r_id');return false;>". tep_db_output(INFO_TEXT_DELETE)." </a>",
   'list_form'      => tep_draw_form('skills_list'.$i, FILENAME_JOBSEEKER_RESUME4, '', 'post','').tep_draw_hidden_field('r4_id',$r_id)
   ));
  $i++;
 }
tep_db_free_result($result_query_list );

//////////////////////
########### END SKILLS LIST ####################

#######  LANGUAGES LIST #########################
///////////////////////
$query_list = "select r5_id,language,proficiency from " .JOBSEEKER_RESUME5_TABLE . " where resume_id ='".$resume_id."' order by r5_id";
//echo $query_list ;
$result_query_list = tep_db_query($query_list);
$list_row = tep_db_num_rows($result_query_list);
 $i=1;
 while ($row_jobseeker_certification = tep_db_fetch_array($result_query_list))
 {
  $r_id  = $row_jobseeker_certification['r5_id'];
  $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $template->assign_block_vars('languages', array(
   'row_selected'   => $row_selected,
   'languages'      => tep_db_get_field(JOBSEEKER_LANGUAGE_TABLE,TEXT_LANGUAGE.'name','languages_id='.tep_db_output($row_jobseeker_certification['language'])),
   'proficiency'    => tep_db_get_field(LANGUAGE_PROFICIENCY_TABLE,TEXT_LANGUAGE.'language_proficiency','id='.tep_db_output($row_jobseeker_certification['proficiency'])),
   'edit_languages' => "<a href='#' onclick='document.languages_list".$i.".submit()'>".tep_db_output(INFO_TEXT_EDIT)."</a>",
   'delete'         => "<a href='#' onClick=goRemove('".FILENAME_JOBSEEKER_RESUME4."','r5_id','ResultDelete','$r_id');return false;>". tep_db_output(INFO_TEXT_DELETE)." </a>",
   'list_form'      => tep_draw_form('languages_list'.$i, FILENAME_JOBSEEKER_RESUME4, '', 'post','').tep_draw_hidden_field('r5_id',$r_id)
   ));
  $i++;
 }
tep_db_free_result($result_query_list );
//////////////////////
########### END LANGUAGES LIST ####################
if(tep_not_null($action))
{
 switch($action)
 {
  case 'add_skills':
  case 'edit_skills':
   $resume_id  = $resume_id;
   $r4_id      = tep_db_prepare_input($_POST['r4_id']);
   $skill      = tep_db_prepare_input($_POST['TR_skill']);
   $skill_level= tep_db_prepare_input($_POST['skill_level']);
   $last_used  = tep_db_prepare_input($_POST['last_used']);
   $years_of_exp= tep_db_prepare_input($_POST['years_of_exp']);
   $error=false;
   if(!$error)
			{
				$sql_data_array=array('resume_id'  => $resume_id,
                          'skill'      => $skill ,
                          'skill_level'=> $skill_level,
                          'last_used'  => $last_used,
                          'years_of_exp'=> $years_of_exp);
    if($action=='edit_skills')
				{
     $sql_data_array1['updated']='now()';
		   tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
     tep_db_perform(JOBSEEKER_RESUME4_TABLE, $sql_data_array, 'update', "r4_id = '" .$r4_id. "' and  resume_id ='".$resume_id ."'");
     $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
		  }
    elseif($action=='add_skills')
    {
    	tep_db_perform(JOBSEEKER_RESUME4_TABLE, $sql_data_array,'insert');
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    }
 			$sql_data_array1['updated']='now()';
				tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
			 $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME4."?query_string=".$query_string));
			}
		break;
  case 'add_languages':
  case 'edit_languages':
   $resume_id           = $resume_id;
   $r5_id               = tep_db_prepare_input($_POST['r5_id']);
   $languages           = tep_db_prepare_input($_POST['TR_languages']);
   $proficiency         = tep_db_prepare_input($_POST['TR_proficiency']);
   $error=false;
   if(!$error)
			{
				$sql_data_array=array('resume_id'           => $resume_id,
                          'language'            => $languages,
                          'proficiency'         => $proficiency);
    if($action=='edit_languages')
				{
     $sql_data_array1['updated']='now()';
		   tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
     tep_db_perform(JOBSEEKER_RESUME5_TABLE, $sql_data_array, 'update', "r5_id = '" .$r5_id. "' and  resume_id ='".$resume_id ."'");
     $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
		  }
    elseif($action=='add_languages')
    {
    	tep_db_perform(JOBSEEKER_RESUME5_TABLE, $sql_data_array,'insert');
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    }
 			$sql_data_array1['updated']='now()';
				tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
			 $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME4."?query_string=".$query_string));
			}
			break;
 }
}
if($error)
{
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $skills_button='<button class="btn btn-primary me-2" type="submit">'.IMAGE_SAVE_ADD_NEW.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE,"");
 $skills_form=tep_draw_form('skills', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','add_skills');

 $languages_button='<button class="btn btn-primary me-2" type="submit">'.IMAGE_SAVE_ADD_NEW.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE,"");
 $languages_form=tep_draw_form('languages', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','add_languages');

 if($_POST['action']=="edit_skills" || $_POST['action']=="add_skills")
 {
  if($_POST['action']=="edit_skills")
  {
   $resume_id   = $resume_id;
   $resume_name = $resume_name;
   $skill       = $skill;
   $skill_level = $skill_level;
   $last_used   = $last_used;
   $years_of_exp= $years_of_exp;
   $query_string=encode_string("resume_id@@@".$_POST['resume_id']."@@@resume");
   $skills_button     = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $skills_form=tep_draw_form('skills', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('r4_id',$r4_id).tep_draw_hidden_field('action','edit_skills');
  }
  else
  {
  $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
  $skills_button = '<button class="btn btn-primary me-2" type="submit" onclick=set_action("add_skills")>'.IMAGE_SAVE_ADD_NEW.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('add_skills')");
  $skills_form=tep_draw_form('skills', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','add_skills');
  }
 }
 elseif($_POST['action']=="edit_languages" || $_POST['action']=="add_languages")
 {
  if($_POST['action']=="edit_languages")
  {
   $resume_id           = $resume_id;
   $resume_name         = $resume_name;
   $languages           = $languages;
   $proficiency         = $proficiency;
   $query_string=encode_string("resume_id@@@".$_POST['resume_id']."@@@resume");
   $languages_button     = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $languages_form=tep_draw_form('languages', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('r5_id',$r5_id).tep_draw_hidden_field('action','edit_languages');
  }
  else
  {
   $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
   $languages_button = '<button class="btn btn-primary me-2" type="submit" onclick=set_action1("add_languages")>'.IMAGE_SAVE_ADD_NEW.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action1('add_languages')");
   $languages_form=tep_draw_form('languages', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','add_languages');
  }
 }
}
else
{
 if(isset($_POST['r4_id']))
 {
  $fields="r4_id,skill, skill_level, last_used, years_of_exp ";
  if($row2=getAnyTableWhereData(JOBSEEKER_RESUME4_TABLE,"r4_id='".$r4_id ."' and resume_id ='".$resume_id."'",$fields))
  {
   $r4_id      = $row2['r4_id'];
   $skill      = tep_db_prepare_input($row2['skill']);
   $skill_level= tep_db_prepare_input($row2['skill_level']);
   $last_used  = tep_db_prepare_input($row2['last_used']);
   $years_of_exp  = tep_db_prepare_input($row2['years_of_exp']);
   $skills_button    = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $skills_form= tep_draw_form('skills', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('r4_id',$row2['r4_id']).tep_draw_hidden_field('action','edit_skills');
  }
 }
 else
 {
  $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
  $skills_button= '<button class="btn btn-primary me-2" type="submit" onclick=set_action("add_skills")>'.IMAGE_SAVE_ADD_NEW.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('add_skills')");
  $skills_form  =tep_draw_form('skills', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
  $r4_id      = "";
  $skill      = "";
  $skill_level= "";
  $last_used  = "";
  $years_of_exp  = "";
 }
 if(isset($_POST['r5_id']))
 {
  $fields="r5_id,language,proficiency";
  if($row2=getAnyTableWhereData(JOBSEEKER_RESUME5_TABLE,"r5_id='".$r5_id ."' and resume_id ='".$resume_id."'",$fields))
  {
   $r5_id              = $row2['r5_id'];
   $languages          = tep_db_prepare_input($row2['language']);
   $proficiency        = tep_db_prepare_input($row2['proficiency']);
   $languages_button    = '<button class="btn btn-outline-secondary px-4 me-2 mmt-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $languages_form= tep_draw_form('languages', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('r5_id',$row2['r5_id']).tep_draw_hidden_field('action','edit_languages');
  }
 }
 else
 {
  $query_string       = encode_string("resume_id@@@".$resume_id."@@@resume");
  $languages_button    = '<button class="btn btn-primary me-2" type="submit" onclick=set_action1("add_languages")>'.IMAGE_SAVE_ADD_NEW.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action1('add_languages')");
  $languages_form=tep_draw_form('languages', FILENAME_JOBSEEKER_RESUME4, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
  $r5_id              = "";
  $languages          = "";
  $proficiency        = "";
 }
}
 /*********************************************************/
$query_string       = encode_string("resume_id@@@".$resume_id."@@@resume");
$add_next_button    = "<a href='".tep_href_link(FILENAME_JOBSEEKER_RESUME5."?query_string=".$query_string)."' class='btn btn-outline-secondary mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
//$add_next_button1    = "<a  href='#' onclick='document.languages.reset()' >".tep_image(PATH_TO_BUTTON.'button_reset.gif', IMAGE_RESET)."</a>".'&nbsp;&nbsp;&nbsp;'."<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME5."?query_string=".$query_string)."' >".tep_image(PATH_TO_BUTTON.'button_next_page.gif', IMAGE_NEXT)."</a>";
$add_next_button1    = "<a class='btn btn-outline-secondary px-4 me-2 mmt-15' href='".tep_href_link(FILENAME_JOBSEEKER_RESUME5."?query_string=".$query_string)."' >".NEXT_PAGE."</a>";

$resume1='<div class="step ms-0"><a class="" href ="#"  onclick="document.resume.submit()">'.INFO_TEXT_LEFT_RESUME.'</a></div>';
		  $resume2='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EXPERIENCE.'</a></div>';
    $resume3='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EDUCATION.'</a></div>';
		  $resume4='<div class="step current"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_SKILLS.'</a></div>';
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
	<li class="resume-left-title-inactive"><i class="fa fa-bookmark resume-inactive-icon" aria-hidden="true"></i>'.$resume6.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-graduation-cap resume-inactive-icon" aria-hidden="true"></i>'.$resume3.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_EDUCATION_DETAILS.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-active"><i class="fa fa-user resume-active-icon" aria-hidden="true"></i>'.$resume4.'</li>
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

$update_message=$messageStack->output();

if(!tep_not_null($jobseeker_video))
 $jobseeker_video='http://www.youtube.com/watch?v=';

$template->assign_vars(array(
 'HEADING_TITLE'                   => HEADING_TITLE,
 'add_next_button'                 => $add_next_button,
 'add_next_button1'                 => $add_next_button1,
 'skills_button'                   => $skills_button,
 'skills_form'                     => $skills_form,
 'languages_button'                => $languages_button,
 'languages_form'                  => $languages_form,

 'TABLE_HEADING_SKILL'             => TABLE_HEADING_SKILL,
 'TABLE_HEADING_SKILL_LEVEL'       => TABLE_HEADING_SKILL_LEVEL,
 'TABLE_HEADING_LAST_USED'         => TABLE_HEADING_LAST_USED,
 'TABLE_HEADING_YEARS_OF_EXP'      => TABLE_HEADING_YEARS_OF_EXP,

'TABLE_HEADING_LANGUAGE'           => TABLE_HEADING_LANGUAGE,
'TABLE_HEADING_PROFICIENCY'        => TABLE_HEADING_PROFICIENCY,


 'TABLE_HEADING_SKILL'             => TABLE_HEADING_SKILL,
 'TABLE_HEADING_EDIT'              => TABLE_HEADING_EDIT,
 'TABLE_HEADING_DELETE'            => TABLE_HEADING_DELETE,

 'SECTION_ACCOUNT_RESUME_NAME'     => SECTION_ACCOUNT_RESUME_NAME,
 'SECTION_SKILLS'                  => SECTION_SKILLS,
 'SECTION_LANGUAGES'               => SECTION_LANGUAGES,

 'REQUIRED_INFO'                   => REQUIRED_INFO,
 'INFO_TEXT_RESUME_NAME'           => INFO_TEXT_RESUME_NAME,
 'INFO_TEXT_RESUME_NAME1'          => $check1['resume_title'],

 'INFO_TEXT_SKILL'                 => INFO_TEXT_SKILL,
 'INFO_TEXT_SKILL1'                => tep_draw_input_field('TR_skill', $skill,'class="form-control required" size="46"',true),
 'INFO_TEXT_SKILL_LEVEL'           => INFO_TEXT_SKILL_LEVEL,
 'INFO_TEXT_SKILL_LEVEL1'          => LIST_SET_DATA(SKILL_LEVEL_TABLE,"",TEXT_LANGUAGE.'skill_name','id',"priority",'name="skill_level" class="form-select" ',"Please select ...",'',$skill_level),
 'INFO_TEXT_LAST_USED'             => INFO_TEXT_LAST_USED,
 'INFO_TEXT_LAST_USED1'            => LIST_SET_DATA(SKILL_LAST_USED_TABLE,"",TEXT_LANGUAGE.'skill_last_used','id',"priority",'name="last_used" class="form-select" ',"Please select ...",'',$last_used),
 'INFO_TEXT_YEARS_OF_EXP'          => INFO_TEXT_YEARS_OF_EXP,
 'INFO_TEXT_YEARS_OF_EXP1'         => tep_draw_input_field('years_of_exp', $years_of_exp,'class="form-control" size="46"'),

 'INFO_TEXT_LANGUAGE'              => INFO_TEXT_LANGUAGE,
 'INFO_TEXT_LANGUAGE1'             => LIST_SET_DATA(JOBSEEKER_LANGUAGE_TABLE,"",TEXT_LANGUAGE.'name','languages_id',"name",'name="TR_languages" class="form-select" ',"Please select ...",'',$languages),
 'INFO_TEXT_PROFICIENCY'           => INFO_TEXT_PROFICIENCY,
 'INFO_TEXT_PROFICIENCY1'          => LIST_SET_DATA(LANGUAGE_PROFICIENCY_TABLE,"",TEXT_LANGUAGE.'language_proficiency','id',"priority",'name="TR_proficiency" class="form-select" ',"Please select ...",'',$proficiency),
 'INFO_TEXT_JSCRIPT_FILE'          => $jscript_file,

 'LEFT_BOX_WIDTH'                  => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'                 => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
	'JOBSEEKER_RESUME_LEFT'           => JOBSEEKER_RESUME_LEFT,
 'RIGHT_HTML'                      => RIGHT_HTML,
 'update_message'                  => $update_message));
$template -> pparse('resume_step4');
?>