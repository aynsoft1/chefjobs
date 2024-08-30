<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_APPLICANTS_LIST);
$template->set_filenames(array('applicants' => 'admin1_list_of_applicants.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
//$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$search_status1=tep_db_prepare_input($_GET['search_status']);
$search_query=tep_db_prepare_input($_GET['search_query']);
unset($aInfo); //required

// check if applicant exists or not ///
if(isset($_GET['aID']))
{
 $id=(int)tep_db_input($_GET['aID']);
//echo "id=".$id;
 if(!$row_check_applicant=getAnyTableWhereData(APPLICATION_TABLE,"id='".$id."'","id"))
 {
  $messageStack->add_session(MESSAGE_APPLICANT_ERROR, 'error');
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANTS_LIST));
 }
}

/////////////////////////
///only for sorting starts
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $sort_array=array("jobseeker_name",'jl.jobseeker_email_address',"r.recruiter_company_name",'jb.job_title','a.inserted',"a.applicant_select");
 $obj_sort_by_clause=new sort_by_clause($sort_array,'a.inserted desc');
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause);
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);
///only for sorting ends

 $table_names=APPLICATION_TABLE . " as a left outer join  ".JOBSEEKER_TABLE." as j on(a.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (a.resume_id=jr1.resume_id) left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (a.jobseeker_id=jl.jobseeker_id) left outer join ".JOB_TABLE. " as jb on (a.job_id=jb.job_id ) left outer join ".RECRUITER_TABLE." as r on (r.recruiter_id=jb.recruiter_id)";


 $field_names="a.id,a.job_id,jr1.resume_id, j.jobseeker_image,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,jr1.jobseeker_photo, jl.jobseeker_email_address,r.recruiter_id, r.recruiter_company_name,jb.job_title, a.inserted, a.application_id, a.applicant_select";
 $query1 = "select count(a.id) as x1 from $table_names ";

if (tep_not_null($search_query)) {
    $db_applicant_query_raw = "select $field_names from $table_names 
                                where concat(j.jobseeker_first_name,' ',j.jobseeker_last_name, jl.jobseeker_email_address) LIKE '%$search_query%'
                                ORDER BY ". $order_by_clause;
} else {
  $db_applicant_query_raw = "select $field_names from $table_names ORDER BY ". $order_by_clause;
}

//echo $db_applicant_query_raw;
$db_applicant_split = new splitPageResults($_GET['page'], '20', $db_applicant_query_raw, $db_applicant_query_numrows);
$db_applicant_query = tep_db_query($db_applicant_query_raw);
$db_applicant_num_row = tep_db_num_rows($db_applicant_query);
if($db_applicant_num_row > 0)
{
 $alternate=1;
 while ($applicant = tep_db_fetch_array($db_applicant_query))
 {
  if($action1!='delete')
  {
   $wclause=" id ='".$applicant['id'] ."'";
   if ( (!isset($_GET['aID']) || (isset($_GET['aID']) && ($_GET['aID'] == $applicant['id']))) && !isset($aInfo) && (substr($action, 0, 3) != 'new'))
   {
    $aInfo = new objectInfo($applicant);
   }
   if ( (isset($aInfo) && is_object($aInfo)) && ($applicant['id'] == $aInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
   }
   else
   {
    //$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANTS_LIST, tep_get_all_get_params(array('aID','action','selected_box'))).'&aID='.$applicant['jobseeker_id'] . '\'"';
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANTS_LIST, tep_get_all_get_params(array('aID','action','selected_box'))).'&aID='.$applicant['id'].'">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }

  }
  else
  {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  }

/////////////photo display////////////////
if(tep_not_null($applicant['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$applicant['jobseeker_photo']))
	 $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$applicant['jobseeker_photo'].'&size=40','','','');
else
	$photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_IMAGE.'no_pic.gif&size=40','','','');
////////////////////////////////////////////
$round_st   = getAnyTableWhereData(APPLICATION_STATUS_TABLE.' as apn_st left outer join '.APPLICANT_STATUS_TABLE.' as ap_st on (apn_st.id=ap_st.cur_status) left outer join '.SELECTION_ROUND_TABLE.' as sel_rd on (ap_st.process_round=sel_rd.id)'," ap_st.application_id='".$applicant['id']."' order by inserted desc limit 0,1",'apn_st.application_status,sel_rd.round_name,ap_st.cur_status');

//////////*********************************////
$job_id=$applicant['job_id'];
$aplication_id=$applicant['aplication_id'];
$rec_id=$applicant['recruiter_id'];
$applicant_select=$applicant['applicant_select'];
  $alternate++;
   $template->assign_block_vars('applicant',array(
	 'row_selected' => $row_selected,
     'check_box' => tep_draw_checkbox_field('id[]',$applicant['id']),
     'app_pic' => $photo,
     'app_id' => tep_db_output($applicant['application_id']),
     'app_name'  => tep_db_output($applicant['jobseeker_name']),
     'app_email' => tep_db_output($applicant['jobseeker_email_address']),
     'comp_applied'=> tep_db_output($applicant['recruiter_company_name']),
     'job_applied'=> tep_db_output($applicant['job_title']),
     'date_of_application'  => date('M d Y',strtotime($applicant['inserted'])),
     'applicant_select'=> ($applicant['applicant_select']=='Yes'?'Shortlisted <br>('.$round_st['round_name'].' Round - '.$round_st['application_status'].' )':'Resume Submitted'),
     'action'=> $action_image,
     ));
 }
}
/////
$ADMIN_RIGHT_HTML="";
//echo $aInfo->resume_id;
$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':
  $heading[] = array('text' => '<b>' . $aInfo->jobseeker_name .  $aInfo->job_id.'</b>');
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $aInfo->jobseeker_name . '</b>');
  $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANTS_LIST, tep_get_all_get_params(array('page','action','selected_box'))).'&action=confirm_delete' . '">'.tep_button('Confirm','class="btn btn-delete"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANTS_LIST, tep_get_all_get_params(array('action','selected_box'))) . '">'.tep_button('Cancel','class="btn btn-cancel"').'</a>');
  $contents[] = array('text' => '<br>'.TEXT_DELETE_WARNING.'<br>&nbsp;');
 break;
 default:
 if (isset($aInfo) && is_object($aInfo))
 {
  $heading[] = array('text' => '
                      <div class=""><h4 class="mb-0">'.$aInfo->jobseeker_name.'</h4>
                      <div class="my-2">'.TEXT_INFO_EDIT_ACCOUNT_INTRO.'</div>
                      </div>');
  // $contents[] = array('text' => TEXT_INFO_EDIT_ACCOUNT_INTRO);
  $contents[]= $contentvar;
if ($aInfo->applicant_select=='Yes')
{
  $contents[] = array('align' => 'left', 
                        'params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',
                        'text' => '
                        <a class="" href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS, 
                          'jobID=' . $job_id . '&action1=change_status'.'&aID=' . $aInfo->id.'&rID=' . $aInfo->recruiter_id).'" 
                          class="btn btn-primary">Edit Status
                          </a>'
                    );
}
else
{
  $contents[] = array('align' => 'left', 
                        'params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',
                        'text' => '
                        <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS, 'jobID=' . $job_id .'&rID=' . $aInfo->recruiter_id).'" 
                        class="btn btn-primary mb-3">Edit Status
                        </a>'
                  );
}

  $contents[] = array('align' => 'left', 
                        'params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',
                        'text' => '
                        <a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.encode_string("application_id=".$aInfo->resume_id."=application_id")).'" \
                        class="btn btn-secondary">View Resume
                        </a>'
                  );
 }
 break;
}
////
if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='150';
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////

$search_status_array=array();
$search_status_array[]=array('id'=>'','text'=>'All');
$search_status_array[]=array('id'=>'selected','text'=>'selected');
$search_status_array[]=array('id'=>'joined','text'=>'joined');

if($action1=='delete')
{
 $check_link='<br>'.TEXT_DELETE_WARNING.'<br><br><a href="#"   onclick="DeleteSelected(\'confirm_bulk_delete\')">'.tep_button('Confirm','class="btn btn-primary"').'</a>
 <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_APPLICANTS_LIST).'">'.tep_button('Cancel','class="btn btn-primary"').'</a>';
 $template->assign_vars(array(
  'TABLE_HEADING_APPLICANT_PIC'=>TABLE_HEADING_APPLICANT_PIC,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,

  ));
}
else
{
 $check_link='<a href="#"  onclick="checkall()">Check All</a> / <a href="#"   onclick="uncheckall()">Uncheck All</a> <b>With Selected <a href="#"   onclick="DeleteSelected(\'delete\')">Delete</a></b></font>';
 $template->assign_vars(array(
    'TABLE_HEADING_APPLICANT_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\">".TABLE_HEADING_APPLICANT_NAME.''.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
    'TABLE_HEADING_APPLICANT_EMAIL' =>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\">".TABLE_HEADING_APPLICANT_EMAIL.''.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
    'TABLE_HEADING_APPLICANT_COMP_APPLIED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\">".TABLE_HEADING_APPLICANT_COMP_APPLIED.''.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
    'TABLE_HEADING_APPLICANT_JOB_APPLIED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\">".TABLE_HEADING_APPLICANT_JOB_APPLIED.''.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
    'TABLE_HEADING_DATE_OF_APPLICATION'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][4]."','".$lower."');\">".TABLE_HEADING_DATE_OF_APPLICATION.''.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
    'TABLE_HEADING_APPLICANT_SELECT'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][5]."','".$lower."');\">".TABLE_HEADING_APPLICANT_SELECT.''.$obj_sort_by_clause->return_sort_array['image'][5]."</a>",
    'search_box'=> tep_draw_form('search_query', PATH_TO_ADMIN . FILENAME_ADMIN1_APPLICANTS_LIST, '', 'get', ' enctype="multipart/form-data"').'
                               <input type="search" class="form-control form-control-sm" style="width:250px;" name="search_query" autocomplete="off" placeholder="search..."  />
                            </form>   
                          ',
  ));
}

$template->assign_vars(array(
 'check_link'=>($db_applicant_num_row>0)?$check_link:'',
  'TABLE_HEADING_APPLICANT_PIC'=>TABLE_HEADING_APPLICANT_PIC,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'hidden_fields'=>tep_draw_hidden_field('action1',''),
 'count_rows'=>$db_applicant_split->display_count($db_applicant_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_APPLICANTS),
 'no_of_pages'=>$db_applicant_split->display_links($db_applicant_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','aID','action','selected_box'))),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('applicants');
?>