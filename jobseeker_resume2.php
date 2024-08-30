<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RESUME2);
$template->set_filenames(array('resume_step2' => 'jobseeker_resume2.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_resume2.js';

if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
//print_r($_GET);die();
//print_r($_POST);die();

################# Experience DELETE ##########################
if($_GET['data_delete']=='ResultDelete' && isset($_GET['r2_id']))
{
 $r2_id= explode(",",$_GET['r2_id']);

	for($i=0;$i<count((array)$r2_id);$i++)
	{
  $table_name=JOBSEEKER_RESUME2_TABLE." as  r3  left outer join ".JOBSEEKER_RESUME1_TABLE ." as r1  on (r1.resume_id =r3.resume_id)";
  $whereCluse=" r3.r2_id ='".$r2_id[$i] ."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  if($check=getAnyTableWhereData($table_name,$whereCluse,"r1.resume_id,r2_id"))
  {
   $resume_id = $check['resume_id'];
   $r2_id     = $check['r2_id'];
   //echo "delete from ".JOBSEEKER_RESUME2_TABLE." where resume_id='".$check['resume_id']."' and r2_id ='".$r2_id ."'";
   tep_db_query("delete from ".JOBSEEKER_RESUME2_TABLE." where resume_id='".$check['resume_id']."' and r2_id ='".$r2_id."'");
   set_work_experience($resume_id);
	 }
 }
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETE,'success');
 tep_redirect(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string);
}
################################################
//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (isset($_POST['query_string']))
  $resume_id =check_data($_POST['query_string'],"@@@","resume_id","resume");
elseif (isset($_GET['query_string']))
   $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
elseif (isset($_POST['r2_id']))
{
 if($check=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE,"r2_id='".$_POST['r2_id']."'","resume_id,r2_id"))
 {
  $resume_id = $check['resume_id'];
  $r2_id     = $check['r2_id'];
 }
 else
 {
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
#######  jobseeker work_history LIST #########################
///////////////////////
$query_list = "select r2_id,resume_id,start_month, start_year, end_month, end_year,still_work,company,job_title from " .JOBSEEKER_RESUME2_TABLE . " where resume_id ='".$resume_id."' order by start_year desc ,start_month desc ";
//echo $query_list ;
$result_query_list = tep_db_query($query_list);
$list_row = tep_db_num_rows($result_query_list);
 $i=1;
 while ($row_work_history = tep_db_fetch_array($result_query_list))
 {
  $r_id  = $row_work_history['r2_id'];
  $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $template->assign_block_vars('work_history', array(
   'row_selected'   => $row_selected,
   'start_date'     => formate_date(tep_db_output($row_work_history['start_year']).'-'.tep_db_output($row_work_history['start_month']).'-01',"M-Y"),
   'end_date'       => ($row_work_history['still_work']!='Yes')?formate_date(tep_db_output($row_work_history['end_year']).'-'.tep_db_output($row_work_history['end_month']).'-01',"M-Y"):INFO_TEXT_STILL_WORK,
   'job_title'      => $row_work_history['job_title'],
   'company_name'   => $row_work_history['company'],
   'edit'           => "<a href='#' onclick='document.work_history_list".$i.".submit()'>".TABLE_HEADING_EDIT."</a>",
   'delete'         => "<a href='#' onClick=goRemove('".FILENAME_JOBSEEKER_RESUME2."','r2_id','ResultDelete','$r_id');return false;>". tep_db_output(INFO_TEXT_DELETE)." </a>",
   'list_form'      => tep_draw_form('work_history_list'.$i, FILENAME_JOBSEEKER_RESUME2, '', 'post','').tep_draw_hidden_field('r2_id',$r_id),
   ));
  $i++;
 }
tep_db_free_result($result_query_list );

//////////////////////
########### END work_history LIST ####################
//$resume_id=1;//$_POST['resume_id'];
// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
  case 'add_experience':
   $sql_data_array['updated']='now()';
   $experience_year    = tep_db_prepare_input($_POST['experience_year']);
   $experience_month   = tep_db_prepare_input($_POST['experience_month']);
			$sql_data_array['experience_year']=$experience_year;
			$sql_data_array['experience_month']=$experience_month;
   tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
   $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string);
	  break;
		case 'experience_add_next':
  case 'experience_add':
  case 'edit':
   $resume_id    = $resume_id;
   $r2_id        = tep_db_prepare_input($_POST['r2_id']);
   $company      = tep_db_prepare_input($_POST['TR_company']);
   $city         = tep_db_prepare_input($_POST['city']);

   $country=(int)tep_db_prepare_input($_POST['TR_country']);

   if(isset($_POST['state']) && $_POST['state']!='')
   $state_value=tep_db_prepare_input($_POST['state']);
   elseif(isset($_POST['state1']))
   $state_value  = tep_db_prepare_input($_POST['state1']);
   $job_title    = tep_db_prepare_input($_POST['TR_job_title']);
	if(tep_not_null($_POST['currency']))
		$currency                 = tep_db_prepare_input($_POST['currency']);
	else
		$currency=0;
   $salary       = tep_db_prepare_input($_POST['salary']);
   $salary_per   = tep_db_prepare_input($_POST['salary_per']);
   $company_industry= (tep_not_null($_POST['company_industry'])?tep_db_prepare_input($_POST['company_industry']):0);
   $start_year   = tep_db_prepare_input($_POST['TR_start_year']);
   $start_month  = tep_db_prepare_input($_POST['TR_start_month']);
   $end_year     = tep_db_prepare_input($_POST['TR_end_year']);
   $end_month    = tep_db_prepare_input($_POST['TR_end_month']);
   $description  = tep_db_prepare_input($_POST['description']);
   $still_work   = tep_db_prepare_input($_POST['current']);
   $error=false;
   $start_date=formate_date($start_year.'-'.$start_month.'-01',"Ym");
   $end_date=formate_date($end_year.'-'.$end_month.'-01',"Ym");
   if(!@checkdate($start_month,1,$start_year))
   {
    $error=true;
    $messageStack->add(MESSAGE_START_DATE_ERROR, 'error');
   }
   if(!@checkdate($end_month,1,$end_year) && ($_POST['current']!='Yes'))
   {
    $error=true;
    $messageStack->add(MESSAGE_END_DATE_ERROR, 'error');
   }
   if(($start_date>$end_date ) && ($still_work!='Yes'))
   {
    $error=true;
    $messageStack->add(MESSAGE_DATE_ERROR, 'error');
   }
   if(($start_date==$end_date ) && ($still_work!='Yes'))
   {
    $error=true;
    $messageStack->add(MESSAGE_SAME_DATE_ERROR, 'error');
   }
   if($company=='')
   {
    $error=true;
    $messageStack->add(MESSAGE_COMPANY_ERROR, 'error');
   }
   if(is_numeric($country) == false)
   {
    $error = true;
    $messageStack->add(ENTRY_COUNTRY_ERROR,'jobseeker_account');
   }
   /////////// check state //
 /*  if(is_numeric($state_value))
   {
    $zone_id = 0;//echo $state_value;
    if($check_query = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
    {
     $zone_query = tep_db_query("select distinct zone_id from " . ZONES_TABLE . " where zone_country_id = '" . tep_db_input($country) . "' and (zone_id ='" . tep_db_input($state_value) . "' )");
     if (tep_db_num_rows($zone_query) == 1)
     {
      $zone = tep_db_fetch_array($zone_query);
      $zone_id = $zone['zone_id'];
     }
     else
     {
      $state_error=true;
      $error = true;
      $messageStack->add(ENTRY_STATE_ERROR_SELECT,'jobseeker_account');
     }
    }
    else
    {
     $state_error=true;
     $error = true;
     $messageStack->add(ENTRY_STATE_ERROR_SELECT,'jobseeker_account');
    }
   }
   else
   {
    if($row11 = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
    {
     $state_error=true;
     $error = true;
     $messageStack->add(ENTRY_STATE_ERROR_SELECT,'jobseeker_account');
    }
    elseif (strlen($state_value) <= 0)
    {
     //$state_error=true;
     //$error = true;
     //$messageStack->add(ENTRY_STATE_ERROR,'jobseeker_account');
    }
   }*/
   /////////  /////////// end check state ///////////////////////
   if($job_title =='')
   {
    $error=true;
    $messageStack->add(MESSAGE_JOB_TITLE_ERROR, 'error');
   }
   if($still_work=='Yes')
   {
    $still_work= $still_work;
    $end_year= 0;
    $end_month= 0;
   }
   else
   {
    $still_work = 'No';
   }

   if(!$error)
			{
				$sql_data_array=array('resume_id'    => $resume_id,
                          'company'      => $company,
                          'city'         => $city,
                          'country'      => $country,
                          'job_title'    => $job_title,
                          'currency'     => $currency,
                          'salary'       => $salary,
                          'salary_per'   => $salary_per,
                          'company_industry'=> $company_industry,
                          'start_year'   => $start_year,
                          'start_month'  => $start_month,
                          'end_year'     => $end_year,
                          'end_month'    => $end_month,
                          'still_work'    => $still_work,
                          'description'  => $description);
   /* if($zone_id > 0)
    {
     $sql_data_array['state']=NULL;
     $sql_data_array['state_id']=$zone_id;
    }
    else
    {
     $sql_data_array['state']=$state_value;
     $sql_data_array['state_id']=0;
    }*/

    if($action=='edit')
				{
     $sql_data_array1['updated']='now()';
		   tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
     tep_db_perform(JOBSEEKER_RESUME2_TABLE, $sql_data_array, 'update', "r2_id = '" .$r2_id. "' and  resume_id ='".$resume_id ."'");
     set_work_experience($resume_id);
     $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
		  }
    elseif($action=='experience_add_next' or $action=='experience_add')
    {
    	tep_db_perform(JOBSEEKER_RESUME2_TABLE, $sql_data_array,'insert');
     set_work_experience($resume_id);
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    }
    $sql_data_array1['updated']='now()';
				tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
			 $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");

				if($action=='experience_add_next')
    {
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string));
    }

				elseif($action=='experience_add' or $action=='edit')
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string));
			}
			break;
 }
}
//////////////////////////////
/**********/
if($_SESSION['sess_new_jobseeker']=='y')
{
 $add_save_button='<button class="btn btn-primary me-2 mmb-15" type="submit" onclick=set_action("experience_add")>'.ADD_NEW_EXPERIENCE.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit" onclick=set_action("experience_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_experience.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('experience_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('experience_add_next')");
 $add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2' >".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
}
else
{
 $add_save_button='<button class="btn btn-primary me-2 mmb-15" type="submit" onclick=set_action("experience_add")>'.ADD_NEW_EXPERIENCE.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit" onclick=set_action("experience_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_experience.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('experience_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('experience_add_next')");
 $add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmb-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
}
/****************/
if($error)
{
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 $add_save_button='<button class="btn btn-primary me-2 mmb-15" type="submit" onclick=set_action("experience_add")>'.ADD_NEW_EXPERIENCE.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit" onclick=set_action("experience_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_experience.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('experience_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('experience_add_next')");
 $work_history_form=tep_draw_form('work_history', FILENAME_JOBSEEKER_RESUME2, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','experience_add');

 $reference_button='<button class="btn btn-primary me-2 mmb-15" type="submit" onclick=set_action1("reference_add")>'.ADD_NEW_EXPERIENCE.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit" onclick=set_action1("reference_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action1('reference_add_next')")." ".tep_image_submit(PATH_TO_BUTTON.'button_save_add_new.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action1('reference_add')");
 $reference_form=tep_draw_form('reference', FILENAME_JOBSEEKER_RESUME2, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','reference_add');
 if($_POST['action']=="edit" || $_POST['action']=="experience_add_next")
	{
  if($_POST['action']=="edit")
  {
			$company      = $company;
			$city         = $city;
			$TR_country   = $country;
		//	$state_value  = $state;
			$job_title    = $job_title;
			$salary       = $salary;
   $currency     = $currency;
			$salary_per   = $salary_per;
			$company_industry= $company_industry;
			$start_year   = $start_year;
			$start_month  = $start_month;
			$end_year     = $end_year;
			$end_month    = $end_month;
			$description  = $description;
			$still_work   =$still_work;
   $add_save_button     = '<button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
   $add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmb-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
   $work_history_form   = tep_draw_form('work_history', FILENAME_JOBSEEKER_RESUME2, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('r2_id',$r2_id).tep_draw_hidden_field('action','edit');
  }
  else
  {
   $add_save_button='<button class="btn btn-primary me-2 mmb-15" type="submit" onclick=set_action("experience_add")>'.ADD_NEW_EXPERIENCE.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit" onclick=set_action("experience_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_experience.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('experience_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('experience_add_next')");
   $add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmb-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
   $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
   $work_history_form=tep_draw_form('work_history', FILENAME_JOBSEEKER_RESUME2, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
  }
	}
}
else
{
	if(isset($_POST['r2_id']))
 {
  $fields="r2_id,company, city, state, state_id, country, company_industry, job_title, start_month, start_year, end_month, end_year,still_work, description,currency,salary,salary_per";
  if($row2=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE,"r2_id='".$r2_id ."' and resume_id ='".$resume_id."'",$fields))
 {
  $add_save_button    = '<button class="btn btn-outline-secondary px-4 me-2" type="submit">Update</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
  $add_next_button    = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmb-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
  $work_history_form  = tep_draw_form('work_history', FILENAME_JOBSEEKER_RESUME2, '', 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('r2_id',$row2['r2_id']).tep_draw_hidden_field('action','edit');
  $r2_id              = $row2['r2_id'];
  $company            = tep_db_prepare_input($row2['company']);
  $city               = tep_db_prepare_input($row2['city']);
  $TR_country         = tep_db_prepare_input($row2['country']);
  $state_value        =(int)tep_db_prepare_input($row2['state_id']);
  if($state_value > 0 and is_int($state_value) )
  {
   $state_value=$state_value;//get_name_from_table(ZONES_TABLE,'zone_name', 'zone_id',$state_value);
  }
  else
  {
   $state_value=tep_db_prepare_input($row2['state']);
  }
  $job_title          = tep_db_prepare_input($row2['job_title']);
		$currency           = tep_db_prepare_input($row2['currency']);
  $salary             = tep_db_prepare_input($row2['salary']);
  $salary_per         = tep_db_prepare_input($row2['salary_per']);
  $company_industry   = tep_db_prepare_input($row2['company_industry']);
  $start_year         = tep_db_prepare_input($row2['start_year']);
  $start_month        = tep_db_prepare_input($row2['start_month']);
  $end_year           = tep_db_prepare_input($row2['end_year']);
  $end_month          = tep_db_prepare_input($row2['end_month']);
  $still_work         = tep_db_prepare_input($row2['still_work']);
  $description        = tep_db_prepare_input($row2['description']);
 }
	}
 else
 {
  $add_save_button='<button class="btn btn-primary me-2 mmb-15" type="submit" onclick=set_action("experience_add")>'.ADD_NEW_EXPERIENCE.'</button> <button class="btn btn-outline-secondary px-4 me-2 mmb-15" type="submit" onclick=set_action("experience_add_next")>'.SAVE_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'add_new_experience.gif', IMAGE_SAVE_ADD_NEW," onclick=set_action('experience_add')")."&nbsp;&nbsp;&nbsp;".tep_image_submit(PATH_TO_BUTTON.'button_save_next.gif', IMAGE_SAVE_NEXT,"onclick=set_action('experience_add_next')");
  $add_next_button   = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME6."?query_string=".$query_string)."' class='btn btn-outline-secondary px-4 me-2 mmb-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
  $query_string      = encode_string("resume_id@@@".$resume_id."@@@resume");
  $work_history_form = tep_draw_form('work_history', FILENAME_JOBSEEKER_RESUME2, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('query_string',$query_string).tep_draw_hidden_field('action','');
  $r2_id             = "";
  $company           = "";
  $city              = "";
  $TR_country        = DEFAULT_COUNTRY_ID;
 // $state_value       = "";
  $job_title         = "";
  $company_industry  = "";
  $start_year        = '';
  $start_month       = '';
  $end_year          = '';
  $end_month         = '';
  $description       = "";
  $still_work        = "";
  $salary_per        = "Year";
 }
}
 $resume1='<div class="step ms-0"><a class="" href ="#"  onclick="document.resume.submit()">'.INFO_TEXT_LEFT_RESUME.'</a></div>';
		  $resume2='<div class="step current"><a class=" active" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EXPERIENCE.'</a></div>';
    $resume3='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EDUCATION.'</a></div>';
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

	<td width="19%">
	<div class="resume-side-menu" style="display:none;">
	<ul class="resume-side-nav">'.tep_draw_form('resume', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'<li class="resume-left-title-inactive"><i class="fa fa-file-text resume-inactive-icon" aria-hidden="true"></i> '.$resume1.'</li></form>'.tep_draw_form('resume1', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#resume_name" onclick="document.resume1.submit()">'.INFO_TEXT_RESUME_NAME.'</a></li></form>'.tep_draw_form('resume2', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#objective" onclick="document.resume2.submit()">'.INFO_TEXT_OBJECTIVE.'</a></li></form>'.tep_draw_form('resume3', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#target_job" onclick="document.resume3.submit()">'.INFO_TEXT_TARGET_JOB.'</a></li></form></ul><ul class="resume-side-nav"><li class="resume-left-title-active"><i class="fa fa-briefcase resume-active-icon" aria-hidden="true"></i> '.$resume2.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'#total_experience" >'.INFO_TEXT_TOTAL_WORK_EXP.'</a></li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'#experience" >'.INFO_TEXT_YOUR_WORK_EXPERIENCE.'</a></li></ul><ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-bookmark resume-inactive-icon" aria-hidden="true"></i> '.$resume6.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></li></ul><ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-graduation-cap resume-inactive-icon" aria-hidden="true"></i> '.$resume3.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_EDUCATION_DETAILS.'</a></li></ul><ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-user resume-inactive-icon" aria-hidden="true"></i> '.$resume4.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).' #skill">'.INFO_TEXT_YOUR_SKILLS.'</a></li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'#language">'.INFO_TEXT_LANGUAGES.'</a></li></ul><ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-upload resume-inactive-icon" aria-hidden="true"></i> '.$resume5.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_RESUME.'</a></li></ul><ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-eye resume-inactive-icon" aria-hidden="true"></i> '.$view_resume.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#profile" >'.INFO_TEXT_PERSONAL_PROFILE.'</a></li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#work_experience" >'.INFO_TEXT_EXPERIENCE.'</a></li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#target_job" >'.INFO_TEXT_TARGET_JOB.'</a></li></ul></div></td>
			');

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

/* $total_experience='<tr>'.tep_draw_form('work_experience', 'ajax/'.FILENAME_TOTAL_EXPERIENCE,'query_string='.$query_string, 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('action','add_experience').'
																					<td>&nbsp;</td>
																					<div id="content">
																					<td><div align="right" class="style27">'.INFO_TEXT_EXPERIENCE.'}</div></td>
																					<td>&nbsp;</td>
																					<td>'.year_month_experience_drop("name='experience_year'",$check1['experience_year'],"name='experience_month'",$check1['experience_month'],$required=false,$show_name=true ,$change_order=false).'&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Save"></td>
																					</div>
																				</tr></form>
																				<tr>
																					<td colspan="4">&nbsp;</td>
																				</tr>';*/
if($messageStack->size('work_history') > 0)
 $update_message=$messageStack->output('work_history');
else
 $update_message=$messageStack->output();
$template->assign_vars(array(
 'HEADING_TITLE'                => HEADING_TITLE,
 'add_save_button'              => $add_save_button,
 'add_next_button'              => $add_next_button,
 'work_history_form'            => $work_history_form,
	'total_experience'             => $total_experience,
 'work_experience_form'         => tep_draw_form('work_experience', FILENAME_JOBSEEKER_RESUME2,'query_string='.$query_string, 'post', 'onsubmit="return ValidateForm(this)" ').tep_draw_hidden_field('action','add_experience'),
 'INFO_TEXT_EXPERIENCE'         => INFO_TEXT_EXPERIENCE,
	'INFO_TEXT_EXPERIENCE1'        => year_month_experience_drop("name='experience_year' class='form-select'",$check1['experience_year'],"name='experience_month' class='form-select'",$check1['experience_month'],$required=false,$show_name=true ,$change_order=false),
 'TABLE_HEADING_START_DATE'     => TABLE_HEADING_START_DATE,
 'TABLE_HEADING_END_DATE'       => TABLE_HEADING_END_DATE,
 'TABLE_HEADING_JOB_TITLE'      => TABLE_HEADING_JOB_TITLE,
 'TABLE_HEADING_EDIT'           => TABLE_HEADING_EDIT,
 'TABLE_HEADING_DELETE'         => TABLE_HEADING_DELETE,
 'SECTION_ACCOUNT_RESUME_NAME'  => SECTION_ACCOUNT_RESUME_NAME,
 'TABLE_HEADING_COMPANY_NAME'   => TABLE_HEADING_COMPANY_NAME,
 'SECTION_WORK_HISTORY_DETAIL'  => SECTION_WORK_HISTORY_DETAIL,
 'SECTION_WORK_EXPERIENCE'      => SECTION_WORK_EXPERIENCE,
 'REQUIRED_INFO'                => REQUIRED_INFO,
 'INFO_TEXT_RESUME_NAME'        => INFO_TEXT_RESUME_NAME,
 'BTN_SAVE'        => BTN_SAVE,


 'INFO_TEXT_RESUME_NAME1'       => $check1['resume_title'],
 'INFO_TEXT_COMPANY'            => INFO_TEXT_COMPANY,
 'INFO_TEXT_COMPANY1'           => tep_draw_input_field('TR_company', $company,'class="form-control required" size="45"',true),
 'INFO_TEXT_CITY'               => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'              => tep_draw_input_field('city', $city,'class="form-control" size="45"'),

 'INFO_TEXT_COUNTRY'            => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'           => tep_get_country_list('TR_country',$TR_country,'class="form-select"'),
 'INFO_TEXT_STATE'              => INFO_TEXT_STATE,
 //'INFO_TEXT_STATE1'             => INFO_TEXT_STATE1,
 'COUNTRY_STATE_SCRIPT'         => country_state($c_name='TR_country',$c_d_value=INFO_TEXT_PLEASE_SELECT_COUNTRY.'...',$s_name='state',$s_d_value='state','zone_id',$state_value),

 'INFO_TEXT_JOB_TITLE'          => INFO_TEXT_JOB_TITLE,
 'INFO_TEXT_JOB_TITLE1'         => tep_draw_input_field('TR_job_title', $job_title,'class="form-control required" size="45"',true),
 'INFO_TEXT_SALARY'             => INFO_TEXT_SALARY,
//  'INFO_TEXT_SALARY1'            => LIST_SET_DATA(CURRENCY_TABLE,"",'code','currencies_id',"code",'name="currency" class="form-control" ',TEXT_PLEASE_SELECT,'',$currency)." ".tep_draw_input_field('salary', $salary,'size="10" class="form-control my-1"',false).'&nbsp;&nbsp;Per&nbsp;'.tep_draw_radio_field('salary_per', 'Year', '', $salary_per, 'id="salary_per1"').'&nbsp;<label for="salary_per1">'.INFO_TEXT_YEAR.'</label>&nbsp;'.tep_draw_radio_field('salary_per', 'Month', '', $salary_per, 'id="salary_per2"').'&nbsp;<label for="salary_per2">'.INFO_TEXT_MONTH.'</label>&nbsp;'.tep_draw_radio_field('salary_per', 'Hour', '', $salary_per, 'id="salary_per3"').'&nbsp;<label for="salary_per3">'.INFO_TEXT_HOUR.'</label>',
 'INFO_TEXT_SALARY1'            => "<div class='col'>".LIST_SET_DATA(CURRENCY_TABLE,"",'code','currencies_id',"code",'name="currency" class="form-select" ',TEXT_PLEASE_SELECT,'',$currency)."</div><div class='col'>".tep_draw_input_field('salary', $salary,'size="10" class="form-control"',false).'</div><div class="form-check2 mt-2 d-flex">'.tep_draw_radio_field('salary_per', 'Year', '', $salary_per, 'id="salary_per1" class="form-check-input me-1"').'<label class="form-check-label me-3" for="salary_per1">Year</label>'.tep_draw_radio_field('salary_per', 'Month', '', $salary_per, 'id="salary_per2" class="form-check-input me-1"').'<label class="form-check-label me-3" for="salary_per2">Month</label>'.tep_draw_radio_field('salary_per', 'Hour', '', $salary_per, 'id="salary_per3" class="form-check-input me-1"').'<label class="form-check-label me-3" for="salary_per3">'."Hour".'</label></div>',
 'INFO_TEXT_COMPANY_INDUSTRY'   => INFO_TEXT_COMPANY_INDUSTRY,
 'INFO_TEXT_COMPANY_INDUSTRY1'  => LIST_SET_DATA(JOB_CATEGORY_TABLE,"",TEXT_LANGUAGE.'category_name','id',TEXT_LANGUAGE."category_name",'class="form-select" name="company_industry" ',INFO_TEXT_PLEASE_SELECT."...",'',$company_industry),
 'INFO_TEXT_START_DATE'         => INFO_TEXT_START_DATE,
 'INFO_TEXT_START_DATE1'        => year_month_list("name='TR_start_year' class='form-select required' ",'1970',date("Y"),$start_year,"name='TR_start_month' class='form-select required'",$start_month,true,true,true),
 'INFO_TEXT_TILL_DATE1'         => tep_draw_checkbox_field('current', 'Yes', '',$still_work,'class="form-check-input" id="checkbox_current" onclick="set_current_emp()"')."&nbsp;<span class='small'><label class='form-check-label' for='checkbox_current'>".INFO_TEXT_TILL_DATE."</label></div>",
 'INFO_TEXT_END_DATE'           => INFO_TEXT_END_DATE,
 'INFO_TEXT_END_DATE1'          => year_month_list("name='TR_end_year' class='form-select required' ",'1970',date("Y"),$end_year,"name='TR_end_month' class='form-select required'",$end_month,true,true,true),

 'INFO_TEXT_DESCRIPTION'        => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'       => tep_draw_textarea_field('description', 'soft', '', '3', stripslashes($description), 'class="form-control"', true, false),
 'INFO_TEXT_JSCRIPT_FILE'       => $jscript_file,

 'LEFT_BOX_WIDTH'               => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'              => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'JOBSEEKER_RESUME_LEFT'        => JOBSEEKER_RESUME_LEFT,
 'RIGHT_HTML'                   => RIGHT_HTML,
 'update_message'               => $update_message));
$template                       -> pparse('resume_step2');
?>