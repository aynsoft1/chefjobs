<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT);
$template->set_filenames(array('application' => 'list_of_selectd_applicant.htm','email'  =>'send_bulk_email.htm','preview'=>'preview_bulk_email.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
//print_r($_POST);
//die();
$action    = (isset($_POST['action']) ? $_POST['action'] : '');
$action1   = (isset($_POST['action1']) ? $_POST['action1'] : '');
if(isset($_POST['jobID']))
{
 $job_id         =(int) (isset($_POST['jobID']) ? $_POST['jobID'] : '');
}
elseif(isset($_GET['jobID']))
{
 $job_id         = (int) (isset($_GET['jobID']) ? $_GET['jobID'] : '');
}
$application_id     = (isset($_POST['application_id']) ? $_POST['application_id'] : '');
unset($aInfo);
$app_join_status=array('new','joined','declined');
if(tep_not_null($_POST['join_status']) && in_array($_POST['join_status'],$app_join_status))
{
 $join_status1=tep_db_prepare_input($_POST['join_status']);
}
elseif(tep_not_null($_GET['join_status']) && in_array($_GET['join_status'],$app_join_status))
{
 $join_status1=tep_db_prepare_input($_GET['join_status']);
}
else
$join_status1='';

if(!$row_check_1=getAnyTableWhereData(JOB_TABLE. " as jb left join ".RECRUITER_TABLE." as r on (jb.recruiter_id=r.recruiter_id)"," jb.job_id='".$job_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' ","jb.job_id,jb.job_title,r.recruiter_company_name"))
{
 $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);
}
define('APPLICATION_REPLY_MAIL',tep_db_output($row_check_1['recruiter_company_name']."@".SITE_TITLE));

//print_r($_POST);
//$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$whereClause="a.job_id=".tep_db_input($job_id)." and ";
if(tep_not_null($application_id))
{
 if(!$row_check=getAnyTableWhereData(APPLICATION_TABLE." as a " ," a.application_id='".$application_id."'  and a.job_id='".$job_id."'","a.id,a.applicant_join_status"))
 {
   $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
   tep_redirect(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT);
 }
}

//print_r($row);
if(tep_not_null($action1))
{
 $lower=(int)tep_db_prepare_input($_POST['lower']);
 $higher=(int)tep_db_prepare_input($_POST['higher']);
 $page_string='';
 $hidden_field1='';
 if($lower >0)
 {
  $page_string='lower='.$lower; 
  $hidden_field1.=tep_draw_hidden_field('lower',$lower);
 }
 if($higher >0)
 {
  $page_string.=(($page_string=='')?'':'&').'higher='.$higher; 
  $hidden_field1.=tep_draw_hidden_field('higher',$higher);
 }
 if(tep_not_null($join_status1))
 {
  $page_string.=(($page_string=='')?'':'&').'join_status='.$join_status1; 
  $hidden_field1.=tep_draw_hidden_field('join_status',$join_status1);
 }
}
 if(tep_not_null($action1) )
 {
  switch($action1)
  {
   case 'candidate_join':
    if($row_check['applicant_join_status']=='')
    {  
      tep_db_query("update ".APPLICATION_TABLE." set applicant_join_status='joined' where application_id='".$application_id."'");
    }
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,tep_get_all_get_params(array('lower','higher','join_status')).$page_string));
    //die();
    break;
   case 'candidate_decline':
    if($row_check['applicant_join_status']=='')
    {  
      tep_db_query("update ".APPLICATION_TABLE." set applicant_join_status='declined' where application_id='".$application_id."'");
    }
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,tep_get_all_get_params(array('lower','higher','join_status')).$page_string));
    break;
   case 'remove_dicline':
   case 'candidate_disjoin':
    tep_db_query("update ".APPLICATION_TABLE." set applicant_join_status='' where application_id='".$application_id."'");
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,tep_get_all_get_params(array('lower','higher','join_status')).$page_string));
    break;
  case 'preview':
    $total_application= count($_POST['ch_applicant_id']);
    $list_array=array();
    for($i=0;$i<$total_application;$i++)
    {
     $hidden_fields.=tep_draw_hidden_field('ch_applicant_id[]',$_POST['ch_applicant_id'][$i]);
     $list_array[]=$_POST['ch_applicant_id'][$i];
    }
    $list_array1=implode(', ',$list_array);
    //$TREF_from=$_POST['TREF_from'];
    $TR_subject=$_POST['TR_subject'];
    $email_text=stripslashes($_POST['message1']);
    //$hidden_fields.=tep_draw_hidden_field('TREF_from',$TREF_from);
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
    $total_application= count($_POST['ch_applicant_id']);
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
      $text = strip_tags($email_text);
       if (SEND_EMAILS == 'true') 
       {
        $message = new email();
        if(tep_not_null($_POST['attachment']))
        {
          $file_directory=get_file_directory($_POST['attachment']);
          $destination=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$_POST['attachment'];
          $file_name = basename($destination);  
          $handle    = fopen($destination, "r");
          $contents = fread($handle, filesize($destination));
          fclose($handle);
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
        $total_send_mail=0;
        ini_set('max_execution_time','0');
        for($i=0;$i<$total_application;$i++)
        {
         if($row_check_mail=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_LOGIN_TABLE." as jl, ".JOB_TABLE. " as jb left join  ".RECRUITER_TABLE." as r on ( r.recruiter_id ='".$_SESSION['sess_recruiterid']."' ) "," a.application_id='".$_POST['ch_applicant_id'][$i]."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=jl.jobseeker_id  ","a.id, jl.jobseeker_email_address,r.recruiter_company_name"))
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
          $message->send('',$email_address,tep_db_output($row_check_mail['recruiter_company_name']),APPLICATION_REPLY_MAIL, $TR_subject);
          tep_db_perform(APPLICANT_INTERACTION_TABLE,$sql_data_array); 
          $total_send_mail++;
         }
        }
       }
       $messageStack->add_session(sprintf(MESSAGE_SUCCESS_SENT_APPLICANT,$total_send_mail), 'success');
       tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,"jobID=".$job_id."&".$page_string));
     }
    }
    else
     $action1='send_mail';
    break;
  }
 }

 if(!tep_not_null($action1) ||$action1=='excel_report' )
 {
  if(tep_not_null($join_status1))
  {
   if($join_status1=='New')
   {
    $whereClause.=" a.applicant_join_status='' and ";
   }
   else
   {
    $whereClause.= " a.applicant_join_status='".tep_db_input($join_status1)."' and ";
   }
  }


  $table_names  = APPLICATION_TABLE." as a ,".JOBSEEKER_TABLE." as j, ".JOB_TABLE. " as jb ";
  $field_names  = "a.id,a.application_id,a.jobseeker_id,a.applicant_join_status,a.selected_date,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as full_name,j.jobseeker_country_id";
  $whereClause.= "jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id  and a.applicant_select='Yes' ";
  $query1 = "select count(a.id) as x1 from $table_names where $whereClause";
  //echo "<br>$query1";//exit;   print_r($_POST);
  $result1=tep_db_query($query1);
  $tt_row=tep_db_fetch_array($result1);
  //print_r($tt_row);
  $x1=$tt_row['x1'];
  ///only for sorting starts
  $sort_array=array('a.application_id','j.jobseeker_first_name','a.selected_date','a.applicant_join_status');
  include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
  $obj_sort_by_clause=new sort_by_clause($sort_array,'a.selected_date desc');
  $order_by_clause=$obj_sort_by_clause->return_value;
  $see_before_page_number_array=see_before_page_number1($sort_array,$field,'a.selected_date',$order,'desc',$lower,'0',$higher,MAX_DISPLAY_LIST_OF_APPLICATIONS);//MAX_DISPLAY_LIST_OF_APPLICATIONS
  //print_r($see_before_page_number_array);
  $lower=$see_before_page_number_array['lower'];
  $higher=$see_before_page_number_array['higher'];
  $field=$see_before_page_number_array['field'];
  $order=$see_before_page_number_array['order'];
  $hidden_fields.=tep_draw_hidden_field('sort',$sort);
  $template->assign_vars(array(
   'TABLE_HEADING_APPLICATION_NO'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\">".TABLE_HEADING_APPLICATION_NO.''.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
   'TABLE_HEADING_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\">".TABLE_HEADING_NAME.''.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
   'TABLE_HEADING_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\">".TABLE_HEADING_INSERTED.''.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
   'TABLE_HEADING_STATUS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\">".TABLE_HEADING_STATUS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  ));

  $totalpage=ceil($x1/$higher);
  if($action1=='excel_report')
  {
   if(tep_not_null($_POST['ch_applicant_id'][0]))
   {
    $total_application=count($_POST['ch_applicant_id']);
    $application_array =array(); 
    for($i=0;$i<$total_application;$i++)
    $application_array[]="'".tep_db_input($_POST['ch_applicant_id'][$i])."'";
    $excel_applicant=implode(",",$application_array);
    $whereClause2 =" a.application_id in (".$excel_applicant.") and a.job_id='".tep_db_input($job_id)."'" ;
    $table_names2  = APPLICATION_TABLE." as a left join ".JOBSEEKER_TABLE." as j  on (a.jobseeker_id=j.jobseeker_id) left join ".JOBSEEKER_LOGIN_TABLE." as  jl on(j.jobseeker_id=jl.jobseeker_id) left join ".COUNTRIES_TABLE." as c on (j.jobseeker_country_id=c.id)" ;
    $field_names2="a.id ,a.application_id as 'Applicant ID',concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as 'Full Name',jl.jobseeker_email_address as 'E-Mail Address',j.jobseeker_address1 as Address1, j.jobseeker_address2 as Address2, c.country_name as Country, j.jobseeker_city as City";
    $query2 = "select $field_names2 from $table_names2 where $whereClause2 ORDER BY $order_by_clause limit $lower,$higher ";
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'mysql_to_excel.php');
    $obj_excel_create=new mysql_to_excel($query2,"List of Select Applicant","excel");
   }
  }
  unset($action1);
  $query = "select $field_names from $table_names where $whereClause ORDER BY $order_by_clause limit $lower,$higher ";
  $result=tep_db_query($query);
  //echo "<br>$query";//exit;
  $x=tep_db_num_rows($result);
  //echo $x;exit;
  $pno= ceil($lower+$higher)/($higher);
  if($x > 0 && $x1 > 0)
  {
   if($application_id)
   {
    $app_info=true;
    $query2="select a.id,a.application_id  from  ".$table_names."where  $whereClause   ORDER BY $order_by_clause limit $lower,$higher ";
    $result2=tep_db_query($query2);
    $total_ids=tep_db_num_rows($result2);
    //$row_check =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as ap "," application_id='".$application_id."'",'*');
    if($total_ids>0)
    while ($application_1 = tep_db_fetch_array($result2)) 
    {
     if($application_id==$application_1['application_id'])
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

   $alternate=1;
   while ($application = tep_db_fetch_array($result)) 
   {
    if ( (!tep_not_null($application_id) || (isset($application_id) && ($application_id == $application['application_id'])))  && !isset($aInfo) ) 
    {
     $aInfo = new objectInfo($application);
    }
    if ( (isset($aInfo) && is_object($aInfo)) && ($application['application_id'] == $aInfo->application_id) ) 
    {
     $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_CURRENT_APPLICANT); 
     $row_selected=' class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
     $hidden_fields.=tep_draw_hidden_field('application_id',$aInfo->application_id).tep_draw_hidden_field('action1','');
    } 
    else 
    {
     //$row_selected1=' onclick="set_action('.$application['id'].')"';
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
     $action_image='<a href="#"  onclick="set_action(\''.$application['application_id'].'\')">'.tep_image(PATH_TO_IMAGE.'icon_info.gif','').'</a>'; 
    }
    //$query_string1=encode_string("application_id=".$application['id']."=application_id");

    if($row_duplicate_check=getAnyTableWhereData(APPLICATION_TABLE ," job_id='".$job_id."' and  jobseeker_id='".$application['jobseeker_id']."'  and id!='".$application['id']."' and  applicant_select ='Yes'","id"))
    $row_selected=' class="dataTableRow4" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';

    $applicant_join_status='';
    if($application['applicant_join_status']=='joined')
    {
    //  $applicant_join_status=tep_image(PATH_TO_IMAGE.'applicant_join.gif',INFO_TEXT_CANDIDATE_JOIN); 
     $applicant_join_status='<span class="badge text-success"><i class="fa fa-thumbs-up" aria-hidden="true"></i> Joined</span>'; 
    }
    elseif($application['applicant_join_status']=='declined')
    {
    //  $applicant_join_status=tep_image(PATH_TO_IMAGE.'icon_declined.gif',INFO_TEXT_CANDIDATE_DECLINED); 
     $applicant_join_status='<span class="badge text-danger"><i class="fa fa-thumbs-down" aria-hidden="true"></i> Declined</span>'; 
    }
    $template->assign_block_vars('applicant', array( 'row_selected' => $row_selected,
     'check_box' => tep_draw_checkbox_field('ch_applicant_id[]',$application['application_id']),
     'application_id' =>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'search_id='.$application['application_id'])."'>". tep_db_output($application['application_id'])."</a>",// tep_db_output($application['application_id']),
     'name' => tep_db_output($application['full_name']),
     'status' => $applicant_join_status,
     'inserted' => tep_date_short(tep_db_output($application['selected_date'])),
     'action_image'=>$action_image, 
     ));
     $alternate++;
     $lower = $lower + 1;
   }
   see_page_number();
   $plural=($x1=="1")?INFO_TEXT_APPLICANT:INFO_TEXT_APPLICANTS;
   $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_SELECTED." $x1 ".$plural."."));

  }
  else
  {
    $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_SELECTED." <br>&nbsp;&nbsp;&nbsp;"));
  }
  tep_db_free_result($result1);
  tep_db_free_result($result);
  $lower_value= ($_POST['lower']!='')?'document.page.lower.value='.$_POST['lower'].';':'';
  $higher_value= ($_POST['lower']!='')?'document.page.lower.value='.$_POST['lower'].';':'';
  if(!isset($_POST['lower']) &&  $_GET['lower'] >0 )
   $lower_value= ($_GET['lower']!='')?'document.page.lower.value='.(int)$_GET['lower'].';':'';
  if(!isset($_POST['higher']) &&  $_GET['higher'] >0 )
   $higher_value= ($_GET['higher']!='')?'document.page.higher.value='.(int)$_GET['higher'].';':'';
}
/////
if (is_object($aInfo)) 
{
 $heading[] = array('params'=>'background="img/emp_left_bar_bg.gif"','text' => '<div class="list-group"><div class="card-header"><h4 class="m-0">' . tep_db_output($aInfo->full_name) . '</h4><div class="text-danger"><small>'.TEXT_INFO_APPLICANT_OPRATION.'</div></small></div></div>');
//  $contents[] = array('align' => 'center', 'text' => TEXT_INFO_APPLICANT_OPRATION);
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow"  onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action2(\''.$aInfo->application_id.'\',\'send_to_mail\')"   class="style27">'.INFO_TEXT_SEND_MAIL.'</a>');
 if($aInfo->applicant_join_status=='joined')
 {
  $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1"  onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action2(\''.$aInfo->application_id.'\',\'candidate_disjoin\')"   class="style27">'.INFO_TEXT_REMOVE_JOIN.'<span class="badge text-danger text-end"><i class="fa fa-thumbs-down" aria-hidden="true"></i> Remove</span></a>');
 }
 elseif($aInfo->applicant_join_status=='declined')
 {
  $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1"  onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action2(\''.$aInfo->application_id.'\',\'remove_dicline\')"   class="style27">'.INFO_TEXT_REMOVE_DECLINED.'<span class="badge text-danger text-end"><i class="fa fa-thumbs-down" aria-hidden="true"></i> Remove</span></a>');
 }
 else
 {
  $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1"  onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action2(\''.$aInfo->application_id.'\',\'candidate_join\')" ">'. INFO_TEXT_JOIN .' <span class="badge text-success text-end"><i class="fa fa-thumbs-up" aria-hidden="true"></i> Joins</span></a> ');
  
  $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' => '<a class="list-group-item list-group-item-action px-3 py-1" href="#" onclick="set_action2(\''.$aInfo->application_id.'\',\'candidate_decline\')" ">'.INFO_TEXT_DECLINED .'  <span class="badge text-danger text-end"><i class="fa fa-thumbs-down" aria-hidden="true"></i> Declines</span></a> ');
 }
}
if((tep_not_null($heading)) && (tep_not_null($contents))) 
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='160';
 $RIGHT_HTML= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='';
}

if(tep_not_null($RIGHT_HTML))
{
	$right_design='';
	$right_design1 = $RIGHT_HTML;
}
else
{
	$right_design='';
	$right_design1='';
}
/////////////////////////////////////////////////////////////////
$template->assign_vars(array(
  'INFO_TEXT_JOB_TITLE1'=>tep_db_output($row_check_1['job_title']),
  'INFO_TEXT_ALL_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id).'">'.INFO_TEXT_ALL_APPLICANT.'</a>',
  'INFO_TEXT_SELECTED_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,'jobID='.$job_id).'" >'.INFO_TEXT_SELECTED_APPLICANT.'</a>',
  'INFO_TEXT_SEARCH_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id."&search=applicant").'" >'.INFO_TEXT_SEARCH_APPLICANT.'</a>',
  'INFO_TEXT_JOB_DETAIL'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB,'jobID='.$job_id).'" target="_blank">'.INFO_TEXT_JOB_DETAIL.'</a>',
  'INFO_TEXT_REPORT_PIPELINE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#Pipeline ".'">'.INFO_TEXT_REPORT_PIPELINE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise".'">'.INFO_TEXT_REPORT_ROUNDWISE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE_SUMMARY'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise_summary".'">'.INFO_TEXT_REPORT_ROUNDWISE_SUMMARY.'</a>',
  'INFO_TEXT_VIEW_DATE_REPORT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id."#date_report").'">'.INFO_TEXT_VIEW_DATE_REPORT.'</a>',
  'INFO_TEXT_ADD_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'" target="_blank">'.INFO_TEXT_ADD_APPLICANT.'</a>',
 ));
if($action1=='send_mail' ||  $action1=='back' || ($action1=='send_to_mail' && tep_not_null($application_id)))
{
 //die();
 $list_application='';
 $list_array=array();
 if($action1=='send_to_mail')
 {
  $total_application= 1;
  $list_application.='\''.tep_db_input($_POST['application_id']).'\',';
  $list_array[]=$_POST['application_id'];
 }
 else
 {
  $total_application= (int)count($_POST['ch_applicant_id']);
  if($total_application<=0)
  {
   $messageStack->add_session(ERROR_ATLEAST_ONE_CHECKED,'error');
   tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,tep_get_all_get_params(array('lower','higher','join_status')).$page_string));
  }
  for($i=0;$i<$total_application;$i++)
  {
   $list_application.='\''.tep_db_input($_POST['ch_applicant_id'][$i]).'\',';
   $list_array[]=$_POST['ch_applicant_id'][$i];
  }
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
 // 'INFO_TEXT_TO1'    =>LIST_TABLE2($flag=0,APPLICATION_TABLE." as a left join ".JOBSEEKER_LOGIN_TABLE." as jl on (jl.jobseeker_id=a.jobseeker_id)"," where  a.application_id in (".$list_application.")",'application_id','jobseeker_email_address','application_id',$order_by='application_id',$addoption_value="",$addstart="" ,$addmiddle=" - ",$addend="", $query="",$parameters=' name="ch_applicant_id[]" multiple','','',$list_array1),
// HEADING_TITLE_JOINED
 $template->assign_vars(array(
  'HEADING_TITLE'    => HEADING_TITLE1,
  'TABLE_HEADING_INTERACTION_SUBJECT'=>TABLE_HEADING_INTERACTION_SUBJECT,
  'TABLE_HEADING_INTERACTION_INSERTED'=>TABLE_HEADING_INTERACTION_INSERTED,
  'TABLE_HEADING_INTERACTION_FEADBACK'=>TABLE_HEADING_INTERACTION_FEADBACK,
  'INFO_TEXT_TO'     => INFO_TEXT_TO,
  'INFO_TEXT_TO1'    =>LIST_TABLE2($flag=0,'',"",'application_id','jobseeker_email_address','application_id',$order_by='application_id',$addoption_value="",$addstart="" ,$addmiddle=" - ",$addend="", $list_query,$parameters=' name="ch_applicant_id[]" class="form-control" multiple','','',$list_array1),
  'INFO_TEXT_FROM'   => INFO_TEXT_FROM,
  //'INFO_TEXT_FROM1'  => tep_draw_input_field('TREF_from',$TREF_from, 'size="35"', true ),
  'INFO_TEXT_FROM1'  => tep_db_output(APPLICATION_REPLY_MAIL),
  'INFO_TEXT_SUBJECT'=> INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'size="35" class="form-control mt-2 required" placeholder="subject" required', true ),
  'INFO_MAIL_ATTACHMENT'=>INFO_MAIL_ATTACHMENT,
  'INFO_MAIL_ATTACHMENT1'=>tep_draw_file_field('attachment', false),
  'INFO_TEXT_MESSAGE' => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=> tep_draw_textarea_field('message1', 'soft', '80%', '10', $email_text, '', true, true,'class="form-control h-100"'),
  // 'buttons'           => tep_image_submit(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW_MAIL).' <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,"jobID=".$job_id."&".$page_string).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif',IMAGE_CANCEL).'</a>',
  'buttons'           => tep_button_submit('btn btn-primary btn-sm', IMAGE_PREVIEW_MAIL).'
  <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,"jobID=".$job_id."&".$page_string).'" class="btn btn-outline-secondary btn-sm">'.IMAGE_CANCEL.'</a>',
  'form'              => tep_draw_form('email',FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,"jobID=".$job_id,'post','onsubmit="return ValidateForm(this)" enctype="multipart/form-data"').tep_draw_hidden_field('action1','preview').$hidden_field1,
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
  'HEADING_TITLE'=>HEADING_TITLE1,
  'INFO_TEXT_TO'=>INFO_TEXT_TO,
  'INFO_TEXT_TO1'=>tep_db_output($list_array1),
  'INFO_TEXT_FROM'=>INFO_TEXT_FROM,
  //'INFO_TEXT_FROM1'=>tep_db_output($TREF_from),
  'INFO_TEXT_FROM1'=>tep_db_output(APPLICATION_REPLY_MAIL),
  'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_db_output($TR_subject).(($attachment_file_name!='')?'<span class="small"><a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,"query_string=".$query_string5).'" title="'.tep_db_output(substr($attachment_file_name,14)).'">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' :'.tep_db_output(substr($attachment_file_name,14))).' :'.tep_db_output(substr($attachment_file_name,14)).'</a></span>':''),  
  'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=>stripslashes($_POST['message1']),
  // 'buttons'=>'<a href="#" onclick="javascript: set_action(\'back\');">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_send_mail.gif', IMAGE_SEND_MAIL, 'name="send_mail"'),
  'buttons'=>'<a href="#" onclick="javascript: set_action(\'back\');" class="btn btn-outline-secondary">'.IMAGE_BACK.'</a>&nbsp;&nbsp;
  '.tep_button_submit('btn btn-primary', IMAGE_SEND_MAIL, 'name="send_mail"'),
  'form'=>tep_draw_form('preview_mail',FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT, "jobID=".$job_id, 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','send').$hidden_field1,
  'hidden_fields'=>$hidden_fields,
  'update_message'=>$messageStack->output()));
 $template->pparse('preview');
}
else
{
 $check_link='<a href="#" onclick="checkall()">'.INFO_TEXT_CHECK_ALL.'</a> / <a href="#" onclick="uncheckall()">'.INFO_TEXT_UNCHECK_ALL.'</a>';
 /*$check_link1='<b>With Selected</b> 
              <select name="select_action"  onchange="select_action2();">
                 <option value="" selected="selected">With selected:</option>
                 <option value="send_mail" >Send Mail</option>
              </select>';
 */
 $quick_action='<select class="form-select" name="quick_action"  onchange="select_action1();">
                 <option value="" selected="selected">'.INFO_TEXT_QUICK_ACTION.'</option>
                 <option value="excel_report" >'.INFO_TEXT_CREATE_EXCEL_REPORT.'</option>
                 <option value="send_mail" >'.INFO_TEXT_SEND_MAIL.'</option>
              </select>';
  
$join_status_array=array();
$join_status_array[]=array('id'=>'','text'=>INFO_TEXT_ALL);
$join_status_array[]=array('id'=>'new','text'=>INFO_TEXT_NEW);
$join_status_array[]=array('id'=>'joined','text'=>INFO_TEXT_JOINED);
$join_status_array[]=array('id'=>'declined','text'=>INFO_TEXT_DECLINED);

if($join_status1=='joined')
 $heading_title=HEADING_TITLE_JOINED;
elseif($join_status1=='declined')
 $heading_title=HEADING_TITLE_DECLINED;
elseif($join_status1=='new')
 $heading_title=HEADING_TITLE_NEW;
else
 $heading_title=HEADING_TITLE;

 $template->assign_vars(array(
  'HEADING_TITLE'=>$heading_title,
  'INFO_TEXT_BACK'=>'<a class="btn btn-outline-secondary btn-sm float-right" href="#" onclick="javascript:history.back();">'.INFO_TEXT_BACK.'</a>',
  'INFO_TEXT_JOB_LISTING'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS).'">'.INFO_TEXT_JOB_LISTING.'</a>',
  'INFO_TEXT_APPLICATION'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id).'">'.INFO_TEXT_APPLICATION.'</a>',
  'hidden_fields'=>$hidden_fields,
  'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_REPORTS'  =>INFO_TEXT_REPORTS,
  'lower_value'=>$lower_value,
  'higher_value'=>$higher_value,
  'check_link'=>($x>0)?$check_link:'',
  'check_link1'=>($x>0)?$check_link1:'',
  'quick_action'=>($x>0)?$quick_action:'',  
  'INFO_TEXT_JOIN_STATUS'=>INFO_TEXT_JOIN_STATUS,
  'INFO_TEXT_JOIN_STATUS1'=>tep_draw_pull_down_menu('join_status', $join_status_array, $join_status1,'onchange="document.page.submit();" class="form-select"'),
  'new_button'=>'',
  'form'=>tep_draw_form('page',FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT, "jobID=".$job_id, 'post', 'onsubmit="return ValidateForm(this)"'),
		'RIGHT_DESIGN'     =>$right_design,
		'RIGHT_DESIGN1'    =>$right_design1,
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>$RIGHT_HTML1.$RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('application');
}
?>