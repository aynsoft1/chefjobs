<?
/*
***********************************************************
**********# Name          : Shamhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("../include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_APPLICANT_REPORTS);
$template->set_filenames(array('app_search' => 'admin1_applicant_search.htm',
                               'app_search_result'=>'admin1_applicant_reports.htm',
                               'email'=>'admin1_send_email.htm',
                               'preview'=>'admin1_preview_email.htm'));
include_once(FILENAME_ADMIN_BODY);
//print_r($_POST);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$email = (isset($_POST['email']) ? $_POST['email'] : '');
$excel = (isset($_POST['excel']) ? $_POST['excel'] : '');
$preview = (isset($_POST['preview']) ? $_POST['preview'] : '');
////// send email ///
if(isset($_POST['send_mail_x']) && tep_not_null($_POST['send_mail_x']))
{
 $email_address=$_POST['email_address1'];
 if($email_address['0']=='-1')
 {
  array_shift($email_address);
 }
 $email_address=implode(", ",$email_address);
 $subject=tep_db_output($_POST['TR_subject']);
 $email_text=stripslashes($_POST['TR_message']);
	$text = strip_tags($email_text);
 if (SEND_EMAILS == 'true')
 {
  $message = new email();
  if(tep_not_null($_POST['attachment']))
  {
    $destination=PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment'];
    $file_name = basename($destination);
    $handle    = fopen($destination, "r");
    $contents = fread($handle, filesize($destination));
    fclose($handle);
    if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']))
    @unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']);
    $message->add_attachment($contents,substr($file_name,14));
  }
  if (EMAIL_USE_HTML == 'true')
  {
   $message->add_html($email_text);
  }
  else
  {
   $message->add_text($text);
  }
		// Send message
			$message->build_message();
			$message->send('', $email_address, SITE_OWNER, ADMIN_EMAIL, $subject);
 }
//tep_mail('' , $email_address, $subject, $email_text, SITE_OWNER, ADMIN_EMAIL);
/*
 echo "To: ".$email_address;
 echo "<br>Subject: ".$subject;
 echo "<br>Message: ".$email_text;
 */
 $messageStack->add_session(MESSAGE_SUCCESS_SENT, 'success');
 tep_redirect(FILENAME_ADMIN1_APPLICANT_REPORTS);
}
//////
if($preview=='preview')
{
 $preview=true;
}
else
{
 $preview=false;
}
if($email=='email')
{
 $email=true;
}
elseif($email=='back')
{
 $email='back';
}
else
{
 $email=false;
}
if($excel=='excel')
{
 $excel=true;
}
else
{
 $excel=false;
}

//echo $jID;
// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);

   $first_name=tep_db_prepare_input($_POST['first_name']);
   $last_name=tep_db_prepare_input($_POST['last_name']);
   $email_address=tep_db_prepare_input($_POST['email_address']);


   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $sort=tep_db_prepare_input($_POST['sort']);


   //// first name last name starts///
   if(tep_not_null($first_name) && tep_not_null($last_name))
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.="( j.jobseeker_first_name like '%".tep_db_input($first_name)."%' &&  j.jobseeker_last_name like '%".tep_db_input($last_name)."%' )";
    $hidden_fields.=tep_draw_hidden_field('first_name',$first_name);
    $hidden_fields.=tep_draw_hidden_field('last_name',$last_name);
   }
   else if(tep_not_null($first_name))
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.="( j.jobseeker_first_name like '%".tep_db_input($first_name)."%' )";
    $hidden_fields.=tep_draw_hidden_field('first_name',$first_name);
   }
   else if(tep_not_null($last_name))
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.="( j.jobseeker_last_name like '%".tep_db_input($last_name)."%' )";
    $hidden_fields.=tep_draw_hidden_field('last_name',$last_name);
   }
   //// first name last name ends ///
   //// email-address starts///
   if(tep_not_null($email_address))
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.="( jl.jobseeker_email_address like '%".tep_db_input($email_address)."%' )";
    $hidden_fields.=tep_draw_hidden_field('email_address',$email_address);
   }
   //// email-address ends ///
   //// country starts///
   if($country > 0)
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.="( j.jobseeker_country_id='".tep_db_input($country)."' )";
    $hidden_fields.=tep_draw_hidden_field('country',$country);
   }
   //// country ends ///
   //// state starts///
   if(tep_not_null($state))
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $temp_result=tep_db_query("select zone_id from ".ZONES_TABLE." where zone_name like '%".tep_db_input($state)."%'");
    if(tep_db_num_rows($temp_result) > 0)
    {
     $zone_array=array();
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $zone_array[]=$temp_row['zone_id'];
     }
     $whereClause.="(j.jobseeker_state_id in (".implode(",",$zone_array)."))";
    }
    else
    {
     $whereClause.="(j.jobseeker_state like '%".tep_db_input($state)."%')";
    }
    $hidden_fields.=tep_draw_hidden_field('state',$state);
   }
   //// state ends ///
   //// city starts///
   if(tep_not_null($city))
   {
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.="( j.jobseeker_city like '%".tep_db_input($city)."%' )";
    $hidden_fields.=tep_draw_hidden_field('city',$city);
   }
   //// email-address ends ///

   if(!tep_not_null($whereClause))
   {
    $whereClause='1';
   }
   $now=date('Y-m-d H:i:s');
   $table_names1=APPLICATION_TABLE." as a left join  ".JOB_TABLE." as jb  on (a.job_id =jb.job_id) left outer join ".JOBSEEKER_TABLE." as j on (j.jobseeker_id=a.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) left join ".JOBSEEKER_RESUME2_TABLE." as jr2 on (jr1.resume_id=jr2.resume_id) left  join ".JOBSEEKER_RESUME3_TABLE." as jr3 on (jr1.resume_id=jr3.resume_id) left join ".JOBSEEKER_LOGIN_TABLE." as jl on (j.jobseeker_id=jl.jobseeker_id) ";
   $query2 = "select distinct(jl.jobseeker_id) from $table_names1 where $whereClause ";
   $table_names=APPLICATION_TABLE." as a left join  ".JOB_TABLE." as jb  on (a.job_id =jb.job_id) left  join ".JOBSEEKER_TABLE." as j on (j.jobseeker_id=a.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) left join ".JOBSEEKER_LOGIN_TABLE." as jl on (j.jobseeker_id=jl.jobseeker_id)  ";//JOBSEEKER_LOGIN_TABLE." as jl  left outer join ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) ";
   $whereClause1="jl.jobseeker_id in ($query2)   ";
   $field_names="jl.jobseeker_id,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as name,jl.jobseeker_email_address,jb.recruiter_id,jl.inserted,jl.updated, jb.job_title, jl.jobseeker_status, jl.ip_address, jl.last_login_time, jl.number_of_logon";
   $query1 = "select count(a.id) as x1 from $table_names where $whereClause1 ";
   //echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];
   //echo $x1;
   //////////////////
  ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("j.jobseeker_first_name",'jl.jobseeker_email_address','jl.inserted');
   $obj_sort_by_clause=new sort_by_clause($sort_array,'jl.inserted desc');
   $order_by_clause=$obj_sort_by_clause->return_value;
   //print_r($obj_sort_by_clause->return_sort_array['name']);
   //print_r($obj_sort_by_clause->return_sort_array['image']);
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'jl.inserted',$order,'desc',$lower,'0',$higher,'20');
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort',$sort);

   $template->assign_vars(array('TABLE_HEADING_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
    'TABLE_HEADING_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
    'TABLE_HEADING_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
    'TABLE_HEADING_COMPANY'=>TABLE_HEADING_COMPANY,
    'TABLE_HEADING_POSITION'=>TABLE_HEADING_POSITION,
    'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION));
  ///only for sorting ends
   $totalpage=ceil($x1/$higher);
   $query = "select $field_names from $table_names where $whereClause1 ORDER BY ".$order_by_clause.($email || $excel?'':" limit $lower,$higher");
   if($excel)
   {
   // $table_names=JOBSEEKER_LOGIN_TABLE." as jl left outer join ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left join ".COUNTRIES_TABLE." as c on (j.jobseeker_country_id=c.id) left join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (jl.jobseeker_id=jr1.jobseeker_id or jr1.jobseeker_id is NULL)";
    $table_names=APPLICATION_TABLE." as a left join  ".JOB_TABLE." as jb  on (a.job_id =jb.job_id) left outer join ".RECRUITER_TABLE." as r on (r.recruiter_id=jb.recruiter_id) left outer join ".JOBSEEKER_TABLE." as j on (j.jobseeker_id=a.jobseeker_id) left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=j.jobseeker_id) left join ".COUNTRIES_TABLE." as c on (j.jobseeker_country_id=c.id) left join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (jl.jobseeker_id=jr1.jobseeker_id or jr1.jobseeker_id is NULL)";

    $field_names="distinct(jl.jobseeker_id), concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as 'Applicant Name',jl.jobseeker_email_address as 'E-Mail Address',j.jobseeker_address1 as Address1, j.jobseeker_address2 as Address2, c.country_name as Country, j.jobseeker_city as City, jb.job_title as Post, r.recruiter_company_name as Company";
    $query = "select $field_names from $table_names where $whereClause1 ORDER BY ".$order_by_clause;
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'mysql_to_excel.php');
    $obj_excel_create=new mysql_to_excel($query,"List of Applicants","excel");
   }
   $result=tep_db_query($query);
   //echo "<br>$query";//exit;
   $x=tep_db_num_rows($result);
   //echo $x;exit;
   $pno= ceil($lower+$higher)/($higher);
   $email_array=array();
   $email_array[]=array("id"=>'-1','text'=>'All Applicants');
   if($x > 0 && $x1 > 0)
   {
    $alternate=1;
    while($row = tep_db_fetch_array($result))
    {
/////////////////////////

   if($rowc=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$row['recruiter_id']."'","recruiter_company_name"))
   {
    $company_applied=$rowc['recruiter_company_name'];
   }
     //////////////
     if($email)
     {
      $email_array[]=array('id'=>$row['jobseeker_email_address'],'text'=>$row['jobseeker_email_address']);
     }
     if ((!tep_not_null($_POST['jID']) || (isset($_POST['jID']) && ($_POST['jID'] == $row['jobseeker_id']))) && !isset($rInfo) && (substr($action, 0, 3) != 'new'))
     {
      $rInfo = new objectInfo($row);
     }
     if ( (isset($rInfo) && is_object($rInfo)) && ($row['jobseeker_id'] == $rInfo->jobseeker_id) )
     {
      $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
      $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
     }
     else
     {
      $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick=list_submit("'.$row['jobseeker_id'].'")';
      $action_image='<a href="#" onclick=list_submit("'.$row['jobseeker_id'].'")>'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
     }
     $alternate++;
     $template->assign_block_vars('search_result', array(
      'row_selected' => $row_selected,
      'action' => $action_image,
      'name_email' => tep_db_output($row['name']).'<br>'.tep_db_output($row['jobseeker_email_address']),
      'company_pos' => $company_applied.'<br>'.tep_db_output($row['job_title']),
      'inserted' => tep_date_long($row['inserted']),
      ));
     $lower = $lower + 1;
    }
    $hidden_fields.=tep_draw_hidden_field('jID');
    $plural=($x1=="1")?"Applicant":"Applicants";
    $template->assign_vars(array('total'=>SITE_TITLE." has matched <font color='red'><b>$x1</b></font> ".$plural." to your search criteria."));
   }
   else
   {
    $template->assign_vars(array('total'=>SITE_TITLE." has not matched any Applicant to your Search Criteria."));
   }
   see_page_number();
   tep_db_free_result($result1);
  }
}
$start_date=date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));
$end_date=date("Y-m-d");
/////
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 default:
 if (isset($rInfo) && is_object($rInfo))
 {
  // $heading[] = array('text' => '<b>'.$rInfo->name.'</b>');
  // $contents[] = array('text' => TEXT_INFO_EDIT_ACCOUNT_INTRO);
  // $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1, 'jID=' . $rInfo->jobseeker_id ) . '">'
  // .tep_button('Edit','class="btn btn-secondary"').'</a>');
  // $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
  // $contents[] = array('text' => '<br><b>'.TEXT_INFO_IP_ADDRESS.'</b><br>'.$rInfo->ip_address);
  // $contents[] = array('text' => '<br><b>'.TEXT_INFO_UPDATED.'</b><br>'.$rInfo->updated);
  // $contents[] = array('text' => '<br><b>'.TEXT_INFO_LAST_LOGIN.'</b><br>'.$rInfo->last_login_time);
  // $contents[] = array('text' => '<br><b>'.TEXT_INFO_NUMBER_OF_LOGON.'</b><br>'.$rInfo->number_of_logon);
  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">'.$rInfo->name.'</div></div>');
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1">'.TEXT_INFO_EDIT_ACCOUNT_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1, 'jID=' . $rInfo->jobseeker_id ) . '">Edit</a>
  <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
  </div>');
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
$hidden_fields.=tep_draw_hidden_field('email');
$hidden_fields.=tep_draw_hidden_field('preview');
$hidden_fields.=tep_draw_hidden_field('excel');
$template->assign_vars(array(
 'hidden_fields' => $hidden_fields,
//  'button' => tep_draw_submit_button_field('','Search','class="btn btn-secondary"'),//tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
'button' => tep_button_submit('btn btn-primary',IMAGE_SEARCH),
 'new_button' => ($x > 0 && $x1 > 0?'<a href="#" onclick="page_submit(\'email\');">'.tep_button('Email','class="btn btn-primary"').'</a>
 <a class="btn btn-secondary" href="#" onclick="page_submit(\'excel\');">'.IMAGE_EXCEL.'</a>':''),
 'form' => tep_draw_form('search', PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANT_REPORTS,'','post').tep_draw_hidden_field('action','search'),
 'HEADING_TITLE' => HEADING_TITLE,
 'INFO_TEXT_KEYWORD' => INFO_TEXT_KEYWORD,
 'INFO_TEXT_KEYWORD1' => tep_draw_input_field('keyword', $keyword,'size="50"',false).INFO_TEXT_KEYWORD_EXAMPLE,
 'INFO_TEXT_KEYWORD3' => INFO_TEXT_KEYWORD_CRITERIA.tep_draw_radio_field('word1', 'Yes', '', true,'id=radio_word1').'<label for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label>'.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2').'<label for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label>',
 'INFO_TEXT_START_DATE' => INFO_TEXT_START_DATE,
 'INFO_TEXT_START_DATE1' => datelisting($start_date, 'name="Date"', 'name="Month"', 'name="Year"', 2006, date("Y"),false),
 'INFO_TEXT_END_DATE' => INFO_TEXT_END_DATE,
 'INFO_TEXT_END_DATE1' => datelisting($end_date, 'name="date"', 'name="month"', 'name="year"', 2006, date("Y"),false),
 'INFO_TEXT_FIRST_NAME' => INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1' => tep_draw_input_field('first_name', $first_name,'class="form-control form-control-sm"',false),
 'INFO_TEXT_LAST_NAME' => INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1' => tep_draw_input_field('last_name', $last_name,'class="form-control form-control-sm"',false),
 'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1' => tep_draw_input_field('email_address', $email_address,'class="form-control form-control-sm"',false),
 'INFO_TEXT_COUNTRY' => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1' => LIST_TABLE(COUNTRIES_TABLE,"country_name"," priority ,country_name","name='country' ","Please select a country..",""),
 'INFO_TEXT_STATE' => INFO_TEXT_STATE,
 'INFO_TEXT_STATE1' => tep_draw_input_field('state1', $state,'size="30"',false),
 //'INFO_TEXT_STATE1' => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_name',"zone_name",'name="state"',"state",'',$state)." ".tep_draw_input_field('state1', $state,'size="30"',false),
 'INFO_TEXT_CITY' => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1' => tep_draw_input_field('city', $city,'size="30"',false),
 'INFO_TEXT_INDUSTRY_SECTOR' => INFO_TEXT_INDUSTRY_SECTOR,
 'INFO_TEXT_INDUSTRY_SECTOR1' => get_drop_down_list(JOB_CATEGORY_TABLE,"name='job_category[]' size='6' multiple","All Job Categorys","0"),
 ////'SCRIPT'                  => country_state($c_name='country',$c_d_value='All countries',$s_name='state',$s_d_value='state','zone_name',$state),
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()
));
if($preview)
{
 $TR_email_address=$_POST['TR_email_address'];
 for($i=0;$i<count($TR_email_address);$i++)
  $hidden_fields.=tep_draw_hidden_field('email_address1[]',$TR_email_address[$i]);
 $TREF_to=implode(", ",$TR_email_address);
 if($TR_email_address['0']=='-1')
 {
  $TREF_to='All Applicants';
 }
 $TREF_from=$_POST['TREF_from'];
 $hidden_fields.=tep_draw_hidden_field('TREF_from',$TREF_from);
 $TR_subject=$_POST['TR_subject'];
 $hidden_fields.=tep_draw_hidden_field('TR_subject',$TR_subject);
 $TR_message=$_POST['TR_message'];
 $hidden_fields.=tep_draw_hidden_field('TR_message',stripslashes($TR_message));
 //////// file upload Attachment starts //////
 if(tep_not_null($_FILES['attachment']['name']))
 {
  if($obj_resume = new upload('attachment', PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT,'644',array('doc','pdf','txt','jpg','gif','png')))
  {
   $attachment_file_name=tep_db_input($obj_resume->filename);
  }
  else
  {
   $messageStack->add(ERROR_ATTACHMENT_FILE, 'error');
  }
 }
 //////// file upload ends //////
 ////////////////   Attachment ///////////////
 if($attachment_file_name!='')
 {
  $hidden_fields.=tep_draw_hidden_field('attachment',stripslashes($attachment_file_name));
 }

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TEXT_TO'=>TEXT_TO,
  'TEXT_TO1'=>tep_db_output($TREF_to),
  'TEXT_FROM'=>TEXT_FROM,
  'TEXT_FROM1'=>tep_db_output($TREF_from),
  'TEXT_SUBJECT'=>TEXT_SUBJECT,
  'TEXT_SUBJECT1'=>tep_db_output($TR_subject).(($attachment_file_name!='')?"<span class='small'> <br>Attachment : ".tep_db_output(substr($attachment_file_name,14))."</span>":''),
  'TEXT_MESSAGE'=>TEXT_MESSAGE,
  'TEXT_MESSAGE1'=>stripslashes($TR_message),
  'buttons'=>'<a href="#" onclick="javascript: submitform();">'.tep_button('Back','class="btn btn-secondary"').'</a>&nbsp;&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_send_mail.gif', IMAGE_SEND_MAIL, 'name="send_mail"'),
  'form'=>tep_draw_form('preview_mail', PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANT_REPORTS, '', 'post', 'onsubmit="return ValidateForm(this)"'),
  'hidden_fields'=>$hidden_fields
  ));
 $template->pparse('preview');
}
else if($email ||$email=='back')
{
 if($email=='back')
 {
  if($_POST['attachment']!='')
   if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']))
   @unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']);
 }
 if(isset($_POST['TREF_from']))
 {
  $email_address1=$_POST['email_address1'];
  $TREF_from=$_POST['TREF_from'];
  $TR_subject=$_POST['TR_subject'];
  $TR_message=stripslashes($_POST['TR_message']);
 }
 else
 {
  $TREF_from=ADMIN_EMAIL;
 }
 $template->assign_vars(array(
  'TEXT_TO'=>TEXT_TO,
  'TEXT_TO1'=>tep_draw_pull_down_menu('TR_email_address[]', $email_array, $email_address1, 'size="5" multiple', true),
  'TEXT_FROM'=>TEXT_FROM,
  'TEXT_FROM1'=>tep_draw_input_field('TREF_from',$TREF_from, 'size="35"', true ),
  'TEXT_SUBJECT'=>TEXT_SUBJECT,
  'TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'size="35"', true ),
  'MAIL_ATTACHMENT'=>MAIL_ATTACHMENT,
  'MAIL_ATTACHMENT1'=>tep_draw_file_field('attachment', false),
  'TEXT_MESSAGE'=>TEXT_MESSAGE,
  'TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '80%', '10', $TR_message, '', true, true),
  'buttons'=>'<a href="#" onclick="page_submit(\'preview\');">'.tep_image(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW_MAIL).'</a>',
  'form'=>tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANT_REPORTS, '', 'post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"')
  ));
 $template->pparse('email');
}
else if($action=='search')
{
 $template->pparse('app_search_result');
}
else
{
 $template->pparse('app_search');
}
?>