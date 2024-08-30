<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_LIST_OF_RESUMES);
$template->set_filenames(array('my_resumes' => 'my_resumes.htm'));
include_once(FILENAME_BODY);
if(!check_login("jobseeker"))
{
	$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
//print_r($_POST);print_r($action);die();
if($_GET['data_delete']=="ResultDelete")
{
	$resume_id=explode(",",$_GET['resume_id']);
	for($i=0;$i<count($resume_id);$i++)
	{
  $table_name=JOBSEEKER_RESUME1_TABLE ." as  r1";
  $whereCluse=" resume_id ='".$resume_id[$i] ."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  if($checked=getAnyTableWhereData($table_name,$whereCluse,"r1.resume_id"))
  {
   for($j=2;$j<=6;$j++)
   {
    tep_db_query("delete from ".constant( 'JOBSEEKER_RESUME'.$j.'_TABLE')." where resume_id='".$checked['resume_id']."'");
   }
   ///////////////
   $result_attachment=tep_db_query("select resume_id,jobseeker_photo,jobseeker_resume   from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$checked['resume_id']."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
   while($row11=tep_db_fetch_array($result_attachment))
   {
    $resume_directory=get_file_directory($row11['jobseeker_resume'],6);
    if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.$row11['jobseeker_resume']))
    {
      @unlink(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.$row11['jobseeker_resume']);
    }
    if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row11['jobseeker_photo']))
    {
      @unlink(PATH_TO_MAIN_PHYSICAL_PHOTO.$row11['jobseeker_photo']);
    }
   }
   tep_db_free_result($result_attachment);
   tep_db_query("delete from ". JOBSEEKER_RESUME1_TABLE." where resume_id='".$checked['resume_id']."'");
   //echo("delete from ".constant( JOBSEEKER_RESUME.$j._TABLE)." where resume_id='".$checked['resume_id']."'");
   tep_db_query("delete from ".RESUME_STATISTICS_TABLE." where resume_id='".$checked['resume_id']."'");
   tep_db_query("delete from ".RESUME_JOB_CATEGORY_TABLE." where resume_id='".$checked['resume_id']."'");
   tep_db_query("delete from ".JOBSEEKER_RATING_TABLE." where resume_id='".$checked['resume_id']."'");
  }
	}
  $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_RESUMES);
}
 $action = (isset($_POST['action']) ? $_POST['action'] : '');
 if(isset($_GET['action']) && ($_GET['action']=='search_active' || $_GET['action']=='search_inactive' ||
                               $_GET['action']=='available_active' || $_GET['action']=='available_inactive'))
 {
  $action = $_GET['action'] ;
 }
#######################################################################
if(tep_not_null($action))
{

 switch($action)
 {
  ////////////////////////////////////////////
  case 'duplicate':
   set_time_limit(0);
   $temp_resume_no=no_of_records(JOBSEEKER_RESUME1_TABLE," jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
   if(($temp_resume_no+1)>MAX_NUM_OF_RESUMES)
   {
    $messageStack->add_session(sprintf(ERROR_EXCEED_MAX_NO_RESUME,$temp_resume_no), 'error');
    tep_redirect(FILENAME_JOBSEEKER_LIST_OF_RESUMES);
   }

  //////////////////////////////
   for($j=1;$j<=6;$j++)
   {
    $sql_data_array=array();
    $result=tep_db_query("show fields from ".constant( 'JOBSEEKER_RESUME'.$j.'_TABLE'));
    $i=0;
    while ($a_row = tep_db_fetch_array($result, MYSQLI_ASSOC))
    {
     $field[$i]="$a_row[Field]";
     $i++;
    }
    tep_db_free_result($result);
    if($j==1)
    $duplicate_query="select * from ".constant('JOBSEEKER_RESUME'.$j.'_TABLE')." where resume_id='".$_POST['resume_id']."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
     else
    $duplicate_query="select * from ".constant('JOBSEEKER_RESUME'.$j.'_TABLE')." where resume_id='".$_POST['resume_id']."' ";
    $duplicate_query_result = tep_db_query($duplicate_query);
    while ($row= tep_db_fetch_array($duplicate_query_result))
    {
     for($i=0;$i<count($row);$i++)
     {
      $sql_data_array["$field[$i]"]=$row["$field[$i]"];
     }
     if($j==1)
     {
      $sql_data_array["resume_id"]='';
      $sql_data_array["inserted"]='now()';
      $sql_data_array["updated"]='null';
      $makeResumeName = $row['resume_title'].' 1';
      if($row['jobseeker_photo']!='')
      {
       if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo']))
       {
        $target_file_name=PATH_TO_MAIN_PHYSICAL_PHOTO.date("YmdHis").substr($row['jobseeker_photo'],14);
        $target_file_name1=date("YmdHis").substr($row['jobseeker_photo'],14);
        copy(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo'],$target_file_name);
        chmod($target_file_name, 0644);
        $sql_data_array["jobseeker_photo"]=$target_file_name1;
       }
       else
       {
        $sql_data_array["jobseeker_photo"]='';
       }
      }
      if($row['jobseeker_resume']!='')
      {
       $resume_directory=get_file_directory($row['jobseeker_resume'],6);
       if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.$row['jobseeker_resume']))
       {
        $target_file_name=PATH_TO_MAIN_PHYSICAL_RESUME.date("Ym").'/'.date("YmdHis").substr($row['jobseeker_resume'],14);
        $target_file_name1=date("YmdHis").substr($row['jobseeker_resume'],14);
        $resume_directory_new=get_file_directory($target_file_name1,6);
        if(check_directory(PATH_TO_RESUME.$resume_directory_new))
        copy(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.$row['jobseeker_resume'],$target_file_name);
        chmod($target_file_name, 0644);
        $sql_data_array["jobseeker_resume"]=$target_file_name1;
       }
       else
       {
        $sql_data_array["jobseeker_resume"]='';
       }
      }
     }
     else
     {
      $sql_data_array["r".$j."_id"]="";
      $sql_data_array["resume_id"]=$new_resume_id;
     }
     if($j==1)
     {
      $count =2;
      while($row_check=getAnyTableWhereData(constant('JOBSEEKER_RESUME'.$j.'_TABLE')," jobseeker_id='".$_SESSION['sess_jobseekerid']."' and resume_title='".tep_db_input($makeResumeName)."'"))
      {
        $makeResumeName = $row['resume_title'].' '.$count;
      }
      $sql_data_array["resume_title"]= $makeResumeName;
      tep_db_perform(constant('JOBSEEKER_RESUME'.$j.'_TABLE'), $sql_data_array);
      $new_row = getAnyTableWhereData (constant('JOBSEEKER_RESUME'.$j.'_TABLE')," jobseeker_id='".$_SESSION['sess_jobseekerid']."' and  resume_title='".tep_db_input($makeResumeName)."' and inserted =".$sql_data_array["inserted"]."","resume_id");
      $new_resume_id=$new_row["resume_id"];
     }
     else
       tep_db_perform(constant( 'JOBSEEKER_RESUME'.$j.'_TABLE'), $sql_data_array);
    }
    tep_db_free_result($duplicate_query_result);
   }
   //////////////////////////////////////////////////////////////////
   $sql_data_array=array();
   $result=tep_db_query("show fields from ".RESUME_JOB_CATEGORY_TABLE);
   $i=0;
   while ($a_row = tep_db_fetch_array($result, MYSQLI_ASSOC))
   {
    $field[$i]="$a_row[Field]";
    $i++;
   }
   tep_db_free_result($result);
   $duplicate_query="select * from ".RESUME_JOB_CATEGORY_TABLE." where resume_id='".$_POST['resume_id']."' ";
   $duplicate_query_result = tep_db_query($duplicate_query);
   while ($row= tep_db_fetch_array($duplicate_query_result))
   {
    for($i=0;$i<count($row);$i++)
    {
     $sql_data_array["$field[$i]"]=$row["$field[$i]"];
    }
    $sql_data_array["resume_id"]=$new_resume_id;
    tep_db_perform(RESUME_JOB_CATEGORY_TABLE, $sql_data_array);
   }
   tep_db_free_result($duplicate_query_result);
   $messageStack->add_session(MESSAGE_SUCCESS_DUPLICATED, 'success');
   tep_redirect(FILENAME_JOBSEEKER_LIST_OF_RESUMES);
   break;
  ////////////////////////////////////////////////////////////////

  case 'search_active':
  case 'search_inactive':
   if(tep_not_null($_GET['resume']))
   {
   if(get_name_from_table(JOBSEEKER_TABLE,'jobseeker_cv_searchable', 'jobseeker_id',$_SESSION['sess_jobseekerid'])=='Yes')
     {
      tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set search_status='".($action=='search_active'?'Yes':'No')."' where resume_id='".$_GET['resume']."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
     }
     else
      $messageStack->add_session(NOT_ACTIVATION_ERROR,'error');
   }
   tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
   break;
  case 'available_active':
  case 'available_inactive':
   if(tep_not_null($_GET['resume']))
   {
    if($action=='available_active')
    {
     tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set availability_date=now() where resume_id='".$_GET['resume']."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
    }
    else
     tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set availability_date=NULL where resume_id='".$_GET['resume']."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
   }
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
   break;
 }
}
############### RESUME LISTING ###############

$table_names=JOBSEEKER_RESUME1_TABLE." as jr1 ";
$whereClause.="jr1.jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by jr1.inserted desc";
$field_names="jr1.resume_id,jr1.jobseeker_photo,jr1.resume_title,jr1.inserted,jr1.updated,jr1.availability_date,jr1.search_status ";//;,sum(rs.viewed) as viewed";

$resume_query_raw="select $field_names from $table_names where $whereClause";
//echo $resume_query_raw;
$resume_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $resume_query_raw, $resume_query_numrows);
$resume_query = tep_db_query($resume_query_raw);
$resume_query_numrows=tep_db_num_rows($resume_query);
$resume_number=$resume_query_numrows+1;
if($resume_query_numrows > 0)
{
 $alternate=1;
 while ($resume = tep_db_fetch_array($resume_query))
 {
  $ide=$resume['resume_id'];
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $edit_form= tep_draw_form("edit_resume".$alternate,FILENAME_JOBSEEKER_RESUME1);
  $hidden_fields1=tep_draw_hidden_field('resume_id',$resume['resume_id']);
  $view_form = tep_draw_form("view_resume".$alternate,FILENAME_JOBSEEKER_VIEW_RESUME);
  $duplicate_form= tep_draw_form("duplicate_resume".$alternate,FILENAME_JOBSEEKER_LIST_OF_RESUMES).tep_draw_hidden_field('action','duplicate');
  $edit= $edit_form."<a class='text-info' href='#' onclick=\"document.edit_resume".$alternate.".submit()\">".tep_db_output(INFO_TEXT_EDIT)."</a>".$hidden_fields1."</form>";
  $delete="<a class='text-danger' href='#' onClick=goRemove('".FILENAME_JOBSEEKER_LIST_OF_RESUMES."','resume_id','ResultDelete','$ide');return false;>".tep_db_output(INFO_TEXT_DELETE)."</a>".$hidden_fields1."</form>";
  $view = $view_form .
          "<a class='btn btn-primary mw-100 w-100' href='#' onclick=\"document.view_resume".$alternate.".submit()\" >".tep_db_output(INFO_TEXT_VIEW)."</a>"
          .$hidden_fields1.
          "</form>";
  $duplicate=$duplicate_form."<a class='text-success' href='#' onclick=\"document.duplicate_resume".$alternate.".submit()\">".tep_db_output(INFO_TEXT_DUPLICATE)."</a>".$hidden_fields1."</form>";
  if ($resume['search_status'] == 'Yes')
  {
   $search_status='<a href="' . tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES, tep_get_all_get_params(array('action','resume'))).'&resume=' . $resume['resume_id'] . '&action=search_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_RESUME_NOT_SEARCHABLE, 30, 17) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_RESUME_SEARCH, 30, 17);
  }
  else
  {
   $search_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_RESUME_NOT_SEARCHABLE, 30, 17) . '&nbsp;<a href="' . tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES, tep_get_all_get_params(array('action','resume'))).'&resume='. $resume['resume_id'] . '&action=search_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_RESUME_SEARCHABLE, 30, 17) . '</a>';
  }
  if(tep_not_null($resume['availability_date']))
  {
   $available_status='<a href="' . tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES, tep_get_all_get_params(array('action','resume'))).'&resume=' . $resume['resume_id'] . '&action=available_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_NOT_AVAILABLITY, 30, 17) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 17);
  }
  else
  {
   $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLITY, 30, 17) . '&nbsp;<a href="' . tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES, tep_get_all_get_params(array('action','resume'))).'&resume='. $resume['resume_id'] . '&action=available_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_AVAILABLITY, 30, 17) . '</a>';
  }
  $row_viewed=getAnyTableWhereData(RESUME_STATISTICS_TABLE,"resume_id='".$ide."' group by resume_id",'sum(viewed) as viewed');

  if (str_contains(tep_db_output($resume['resume_title']), "Resume :")) {
    $resumeName = str_replace("Resume :","",tep_db_output($resume['resume_title']));
  } else if(str_contains(tep_db_output($resume['resume_title']), "Resume:")) {
    $resumeName = str_replace("Resume:","",tep_db_output($resume['resume_title']));
  }
  else {
    $resumeName = tep_db_output($resume['resume_title']);
  }

  /* link added to img and title start wrapped under form tag */
  $profile_logo = $resume['jobseeker_photo'];
  if (tep_not_null($profile_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_PHOTO.$profile_logo)) {
    // $profile = tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_PHOTO.$profile_logo.'','','','" class="my-resume-pic mb-3"');
    $profile = $view_form .
                    "<a class='mw-100 w-100' href='#' onclick=\"document.view_resume".$alternate.".submit()\" >
                    ".tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_PHOTO.$profile_logo.'','','','" class="my-resume-pic mb-3"')."
                    </a>"
                    .$hidden_fields1;
  }else{
    // $profile = defaultProfilePhotoUrl($resumeName,false,112,'class="my-resume-pic mb-3" id=""');
    $profile = $view_form .
                  "<a class='mw-100 w-100' href='#' onclick=\"document.view_resume".$alternate.".submit()\" >
                    ".defaultProfilePhotoUrl($resumeName,false,112,'class="my-resume-pic mb-3" id=""')."
                    </a>"
                  .$hidden_fields1;
  }

  $resumeTitle = "<a class='mw-100 w-100' href='#' onclick=\"document.view_resume".$alternate.".submit()\" >".limitString($resumeName, 17)."</a>"
                .$hidden_fields1.
                "</form>";

  /* link added to img and title end */

  $template->assign_block_vars('resume', array( 'row_selected' => $row_selected,
  //  'resume_name'=> tep_db_output($resume['resume_title']),
  //  'resume_name'=> $resumeName,
   'resume_name'=> $resumeTitle,
   'inserted'   => tep_date_veryshort($resume['inserted']),
   'updated'    => tep_date_veryshort($resume['updated']),
   'viewed'     => tep_db_output($row_viewed['viewed']),
   'search_status'     => $search_status,
   'available_status'     => $available_status,
   'edit' => $edit,
   'delete'=> $delete,
   'view' => $view,
   'duplicate' => $duplicate,
   'profile_img' => $profile,
   ));
  $alternate++;
 }
}
else
{
 $_GET['page']=1;
}
############### RESUME LISTING ###############
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_MAX_RESUME'=>sprintf(INFO_TEXT_MAX_RESUME,MAX_NUM_OF_RESUMES),
 'TABLE_HEADING_RESUME_NAME'=>TABLE_HEADING_RESUME_NAME,
 'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
 'TABLE_HEADING_UPDATED'=>TABLE_HEADING_UPDATED,
 'TABLE_HEADINGVIEWED'=>TABLE_HEADINGVIEWED,
 'TABLE_HEADING_SEARCHABLE'=>TABLE_HEADING_SEARCHABLE,
 'TABLE_HEADING_AVAILABILITY'=>TABLE_HEADING_AVAILABILITY,
 'TABLE_HEADING_EDIT'=>TABLE_HEADING_EDIT,
	'TABLE_HEADING_DELETE'=>TABLE_HEADING_DELETE,
	'TABLE_HEADING_VIEW'=>TABLE_HEADING_VIEW,

	'TEXT_CREATED'=>TEXT_CREATED,
	'TEXT_UPDATED'=>TEXT_UPDATED,
	'TEXT_VIEWS'=>TEXT_VIEWS,
	'TEXT_SEARCHABLE'=>TEXT_SEARCHABLE,
	'TEXT_AVAILABLE'=>TEXT_AVAILABLE,





  
	'TABLE_HEADING_DUPLICATE'=>TABLE_HEADING_DUPLICATE,
 'count_rows'=>$resume_split->display_count($resume_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_RESUMES),
 'no_of_pages'=>$resume_split->display_links($resume_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'add_new'=>"<a class='btn btn-sm btn-primary mmt-15' href='".tep_href_link(FILENAME_JOBSEEKER_RESUME1)."'>".ADD_RESUME." <i class='bi bi-box-arrow-in-up-right ms-2'></i></a>",
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>'',
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
	'JOB_SEARCH_LEFT' => JOB_SEARCH_LEFT,
 'update_message'=>$messageStack->output()));
$template->pparse('my_resumes');

function limitString($str, $limit) {
  if (strlen($str) <= $limit) {
      return $str; // If the string is already shorter than the limit, return the original string
  } else {
      return substr($str, 0, $limit) . "..."; // Otherwise, limit the string and append "..."
  }
}
?>