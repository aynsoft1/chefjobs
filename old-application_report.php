<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_APPLICATION_REPORT);
$template->set_filenames(array('application' => 'application_report.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
//$action    = (isset($_POST['action']) ? $_POST['action'] : '');
$action1   = (isset($_POST['action1']) ? $_POST['action1'] : '');
if(isset($_POST['jobID']))
{
 $job_id         =(int) (isset($_POST['jobID']) ? $_POST['jobID'] : '');
}
elseif(isset($_GET['jobID']))
{
 $job_id         = (int) (isset($_GET['jobID']) ? $_GET['jobID'] : '');
}
if(!$row_check_1=getAnyTableWhereData(JOB_TABLE. " as jb "," jb.job_id='".$job_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' ","jb.job_id,jb.job_title"))
{
 $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);
}
//print_r($_POST);
//$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');

//$report_type = tep_db_prepare_input($_GET['report_type']);

$whereClause="a.job_id=".tep_db_input($job_id)." and ";
//print_r($row);
//if($report_type=='date')
{
 $month=tep_db_prepare_input($_POST['TR_from_month']);
 $year=tep_db_prepare_input($_POST['TR_from_year']);
 if(!@checkdate($month,'1',$year))
 {
  if($row=getAnyTableWhereData(APPLICATION_TABLE,"job_id='".$job_id."'order by id asc limit 0,1","date_format(inserted,'%Y') as start_year ,date_format(inserted,'%m') as start_month"))
  {
   $month=tep_db_prepare_input($row['start_month']);
   $year=tep_db_prepare_input($row['start_year']);
  }
  else
  {
   $month=date('m');
   $year=date('Y');
  }
 }
 $start_date=1;
 $end_date=@date("t", mktime(0, 0, 0,$month, 1,$year));
 /*
 $to_date='';
 $from_date='';
 $date=tep_db_prepare_input($_POST['TR_from_date']);
 $month=tep_db_prepare_input($_POST['TR_from_month']);
 $year=tep_db_prepare_input($_POST['TR_from_year']);
 $from_date=$year."-".$month."-".$date;
 if(!@checkdate($month,$date,$year))
 $from_date='';
 $date=tep_db_prepare_input($_POST['TR_to_date']);
 $month=tep_db_prepare_input($_POST['TR_to_month']);
 $year=tep_db_prepare_input($_POST['TR_to_year']);
 $to_date=$year."-".$month."-".$date;
 if(!@checkdate($month,$date,$year))
 $to_date='';
 if($to_date!='' && $from_date!=''  &&  $to_date >= $from_date)
 {
  $end_date = $to_date;
  $date     = $from_date;
 }
 else
 {
  $row=getAnyTableWhereData(APPLICATION_TABLE,"job_id='".$job_id."'","date_format(min(inserted),'%Y-%m-%d') as inserted ,date_format(max(selected_date),'%Y-%m-%d') as last_selected,date_format(max(inserted),'%Y-%m-%d') as last_inserted");
  $date=$row['inserted'];
  if($date!='')
  {
   $last_selected=$row['last_selected'];
   $last_inserted=$row['last_inserted'];
   if($last_selected > $last_inserted)
    $end_date=$last_selected;
   else
    $end_date=$last_inserted;
    $end_date;
   }
   else
   {
    $date=date('Y-m-d');
    $end_date= date('Y-m-d');
   }
 }
 $date_array=explode('-',$date);
 $alternate=1;
 $no_of_inserted=0;
 $no_of_selected=0;
 $round_report=array();
 */
 $date=$year.'-'.$month.'-'.$start_date;
 $date_array=explode('-',$date);
 for($i=$start_date;$i<=$end_date;$i++)
 {

  $cur_date= @date("Y-m-d", mktime(0, 0, 0, $date_array[1],($i), $date_array[0]));
  $next_date= @date("Y-m-d", mktime(0, 0, 0, $date_array[1],(1+$i), $date_array[0]));
  $cur_date1= @date("d-M-Y", mktime(0, 0, 0, $date_array[1],($i), $date_array[0]));
  $map_date1= @date("d M", mktime(0, 0, 0, $date_array[1],($i), $date_array[0]));

  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $inserted    = no_of_records(APPLICATION_TABLE,"job_id='".$job_id."' and  inserted <'".$next_date."' and inserted  >='".$cur_date."'",'id');
  $selected    = no_of_records(APPLICATION_TABLE,"job_id='".$job_id."' and applicant_select ='Yes'  and selected_date <'".$next_date."' and selected_date >='".$cur_date."'",'id');
  $no_of_inserted=$no_of_inserted+$inserted;
  $no_of_selected=$no_of_selected+$selected;
  //if($cur_date>$end_date)
  // break;
  $date_report['name'][]=$map_date1;
  $date_report['value'][]= $inserted;
   if($inserted ==0 && $selected==0)
  continue;

   $template->assign_block_vars('applicant', array( 'row_selected' => $row_selected,
   'date' => tep_db_output($cur_date1),
   'inserted' => tep_db_output($inserted),
   'selected' => tep_db_output($selected),
   ));
   $alternate++;
 }
 ///////////////////////////////////////
 $total_date_report=count($date_report['name']);
 $total_applicant=array_sum($date_report['value']);
 for($i=0;$i<$total_date_report;$i++)
 {
  $image_height    =(($total_applicant>0)?round(($date_report['value'][$i]/ $total_applicant) * 200, 1):0);
  $template->assign_block_vars('date_report',array('name'   => tep_db_output($date_report['name'][$i]),
                                                   'image'   => '<img style="border: 1 solid #000000" src="img/bar7.gif"  height="'.$image_height.'"   alt="'.$date_report['value'][$i].'"   width="10">',
                                                   'value'   =>  tep_db_output($date_report['value'][$i])
                                                 ));
 }
 ///////////////////////////////////////////////////////////////

}
//else
{
 /////////////////////////////////////////////////////////////////
                  /*  ROUND\STATUS REPORT    */
 //\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 $query=" select *  from ".SELECTION_ROUND_TABLE." order by value";
 $result_query_list = tep_db_query($query);
 $list_row = tep_db_num_rows($result_query_list);
 $round_report=array();
 $round_report['name'][0]='Total';
 $round_report['value'][0]= no_of_records(APPLICATION_TABLE,"job_id='".$job_id."'",'id');
 if($list_row > 0)
 {
  $alternate=1;
  while ($row= tep_db_fetch_array($result_query_list))
  {
   $whereClause="a.job_id=".tep_db_input($job_id)." and ";
   $whereClause.="ap.process_round='".$row['id']."' and ";
   $query_app_ids= "select max(ap.id) as id from ".APPLICANT_STATUS_TABLE." as ap  left join   " .APPLICATION_TABLE." as a  on (ap.application_id=a.id) left join ".JOB_TABLE. " as jb on (a.job_id=jb.job_id),".JOBSEEKER_TABLE." as j "." where   $whereClause jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id  group by ap.application_id";
   $no_of_applicant=array();
   for($j=1;$j<6;$j++)
   {
    $whereClause2=" ap.id in (".$query_app_ids.") and  ap.cur_status = '".$j."' and  job_id='".$job_id."'";
    $no_of_applicant[$j]    = no_of_records(APPLICANT_STATUS_TABLE." as ap  left join   " .APPLICATION_TABLE." as a  on (ap.application_id=a.id)",$whereClause2,'ap.id');
   }
   $no_of_applicant['0']  = array_sum($no_of_applicant);
   $no_of_new_application    = (int)$no_of_applicant['1'];
   $no_of_process_application= (int)$no_of_applicant['2'];
   $no_of_select_application = (int)$no_of_applicant['3'];
   $no_of_reject_application = (int)$no_of_applicant['5'];
   $no_of_waiting_application= (int)$no_of_applicant['4'];
   $no_of_total_application  = (int)$no_of_applicant['0'];
   $round_report['name'][]  = $row[TEXT_LANGUAGE.'round_name'].' '.INFO_TEXT_ROUND;
   $round_report['value'][] = $no_of_total_application;
   ///////////////////////////////////////////////////////////////////////////////
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $template->assign_block_vars('applicant_round', array( 'row_selected' => $row_selected,
    'round'   => tep_db_output($row[TEXT_LANGUAGE.'round_name']." ".INFO_TEXT_ROUND),
    'total'   => tep_db_output($no_of_total_application),
    'new'     => tep_db_output($no_of_new_application),
    'process' => tep_db_output($no_of_process_application),
    'select'  => tep_db_output($no_of_select_application),
    'reject'  => tep_db_output($no_of_reject_application),
    'waiting' => tep_db_output($no_of_waiting_application),
   ));
   ///////////////////BAR REPORT//////////////////////////////////
   if($no_of_total_application==0)
   {
    $bar_total  = $bar_new = $bar_process=$bar_select=$bar_reject =$bar_waiting=0;
   }
   else
   {
    $bar_total  =200;
    $bar_new    =(($no_of_new_application>0)?round(($no_of_new_application/ $no_of_total_application) * 200, 1):0);
    $bar_process=(($no_of_process_application>0)?round(($no_of_process_application/ $no_of_total_application) * 200, 1):0);
    $bar_select=(($no_of_select_application>0)?round(($no_of_select_application / $no_of_total_application) * 200, 1):0);
    $bar_reject =(($no_of_reject_application>0)?round(($no_of_reject_application / $no_of_total_application) * 200, 1):0);
    $bar_waiting=(($no_of_waiting_application>0)?round(($no_of_waiting_application / $no_of_total_application) * 200, 1):0);
   }
   $start_row=$end_row='';
   $alternate;
   if(($alternate-1)%3==0)
    $start_row="<tr>";
   if($alternate%3==0)
    $end_row="</tr>";
   $template->assign_block_vars('report_bar', array(
    'start_row'   => $start_row,
    'end_row'  => $end_row,
    'round'   => tep_db_output($row[TEXT_LANGUAGE.'round_name']." ".INFO_TEXT_ROUND),
    'bar_total'   => tep_db_output($bar_total),
    'total'   => tep_db_output($no_of_total_application),
    'new'     => tep_db_output($no_of_new_application),
    'bar_new'     => tep_db_output($bar_new),
    'process' => tep_db_output($no_of_process_application),
    'bar_process' => tep_db_output($bar_process),
    'select'  => tep_db_output($no_of_select_application),
    'bar_select'  => tep_db_output($bar_select),
    'reject'  => tep_db_output($no_of_reject_application),
    'bar_reject'  => tep_db_output($bar_reject),
    'waiting' => tep_db_output($no_of_waiting_application),
    'bar_waiting' => tep_db_output($bar_waiting),
   ));
   ////////////////////////////////////////////////////
   $alternate++;
  }
  //end while loop
  tep_db_free_result($result_query_list );
 }
 $round_report['name'][]=INFO_TEXT_SELECTED;
 $round_report['value'][]= no_of_records(APPLICATION_TABLE,"job_id='".$job_id."' and applicant_select='Yes'",'id');
 $round_report['name'][]=INFO_TEXT_JOINED;
 $round_report['value'][]= no_of_records(APPLICATION_TABLE,"job_id='".$job_id."' and applicant_select='Yes' and applicant_join_status ='joined'",'id');
 $round_report['name'][]=INFO_TEXT_DECLINED;
 $round_report['value'][]= no_of_records(APPLICATION_TABLE,"job_id='".$job_id."' and applicant_select='Yes' and applicant_join_status ='declined'",'id');

 ///////////////////////////////////////
 $total_round_report=count($round_report['name']);
 for($i=0;$i<$total_round_report;$i++)
 {
  $image_height    =(($round_report['value']['0']>0)?round(($round_report['value'][$i]/ $round_report['value']['0']) * 200, 1):0);
  $template->assign_block_vars('round_report',array('name'   => tep_db_output($round_report['name'][$i]),
                                                   'image'   => '<img style="border: 1 solid #000000" src="img/bar'.($i+1).'.gif"  height="'.$image_height.'"   alt="'.$round_report['value'][$i].'"   width="20">',
                                                   'value'   =>  tep_db_output($round_report['value'][$i])
                                                 ));
 }
 ///////////////////////////////////////////////////////////////
}
$template->assign_vars(array(
  'HEADING_TITLE' => HEADING_TITLE,
  'HEADING_TITLE_PIPELINE'=>HEADING_TITLE_PIPELINE,
  'HEADING_TITLE_ROUNDWISE'=>HEADING_TITLE_ROUNDWISE,
  'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
  'TABLE_HEADING_TITLE1'=>TABLE_HEADING_TITLE1,
  'TABLE_HEADING_DATE'=> TABLE_HEADING_DATE,
  'TABLE_HEADING_APPLICATION'=> TABLE_HEADING_APPLICATION,

  'TABLE_HEADING_SELECTED_APPLICATION'=> TABLE_HEADING_SELECTED_APPLICATION,
  'TABLE_HEADING_ROUND_STATUS'=>TABLE_HEADING_ROUND_STATUS,
  'TABLE_HEADING_TOTAL'=>TABLE_HEADING_TOTAL,
  'TABLE_HEADING_NEW'=>TABLE_HEADING_NEW,
  'TABLE_HEADING_PROCESS'=>TABLE_HEADING_PROCESS,
  'TABLE_HEADING_REJECT'=>TABLE_HEADING_REJECT,
  'TABLE_HEADING_SELECT'=>TABLE_HEADING_SELECT,
  'TABLE_HEADING_WAITING'=>TABLE_HEADING_WAITING,

  'INFO_TEXT_JOB_TITLE'=>tep_db_output($row_check_1['job_title']),
  'INFO_TEXT_ALL_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id).'">'.INFO_TEXT_ALL_APPLICANT.'</a>',
  'INFO_TEXT_SELECTED_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,'jobID='.$job_id).'" >'.INFO_TEXT_SELECTED_APPLICANT.'</a>',
  'INFO_TEXT_SEARCH_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id."&search=applicant").'" >'.INFO_TEXT_SEARCH_APPLICANT.'</a>',
  'INFO_TEXT_JOB_DETAIL'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB,'jobID='.$job_id).'" target="_blank">'.INFO_TEXT_JOB_DETAIL.'</a>',
  'INFO_TEXT_REPORT_PIPELINE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#Pipeline ".'">'.INFO_TEXT_REPORT_PIPELINE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise".'">'.INFO_TEXT_REPORT_ROUNDWISE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE_SUMMARY'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise_summary".'">'.INFO_TEXT_REPORT_ROUNDWISE_SUMMARY.'</a>',
  'INFO_TEXT_VIEW_DATE_REPORT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id."#date_report").'">'.INFO_TEXT_VIEW_DATE_REPORT.'</a>',
  'INFO_TEXT_ADD_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'" target="_blank">'.INFO_TEXT_ADD_APPLICANT.'</a>',

  'INFO_TEXT_BACK'=>tep_draw_form('search_applicant',FILENAME_RECRUITER_SEARCH_APPLICANT,'','post').tep_draw_hidden_field('action1','search').'<button class="btn btn-outline-secondary btn-sm" type="submit">'.INFO_TEXT_BACK.'</button>',//'<a class="btn btn-outline-secondary btn-sm" href="#" onclick="javascript:history.back();">'.INFO_TEXT_BACK.'</a>',
  'INFO_TEXT_JOB_LISTING'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS).'">'.INFO_TEXT_JOB_LISTING.'</a>',
  'INFO_TEXT_APPLICATION'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"jobID=".$job_id).'">'.INFO_TEXT_APPLICATION.'</a>',

  'INFO_TEXT_FROM_DATE'=>INFO_TEXT_FROM_DATE,
  'INFO_TEXT_FROM_DATE1'=>year_month_list("name='TR_from_year' class='form-control'",'2007',date("Y"),$year,"name='TR_from_month' class='form-control'",$month,false,true,true),
  //datelisting($date, 'name="TR_from_date"', 'name="TR_from_month"', 'name="TR_from_year"', 2006, date("Y"),'true'),
  //'INFO_TEXT_TO_DATE'=>INFO_TEXT_TO_DATE,
  //'INFO_TEXT_TO_DATE1'=>datelisting($end_date, 'name="TR_to_date"', 'name="TR_to_month"', 'name="TR_to_year"', 2006, date("Y"),'true'),
  'INFO_TEXT_SUBMIT'=>'<button class="btn btn-primary btn-sm" type="submit">Go</button>',//tep_image_submit(PATH_TO_BUTTON.'go.gif', IMAGE_GO),
  'form'=>tep_draw_form('page',FILENAME_RECRUITER_APPLICATION_REPORT, 'jobID='.$job_id."#date_report", 'post', 'onsubmit="return ValidateForm(this)"'),
  'no_of_inserted'=>$no_of_inserted,
  'no_of_selected'=>$no_of_selected,
  'bar_total'   =>   $bar_total,
  'bar_new'     =>   $bar_new,
  'bar_process' =>   $bar_process,
  'bar_select'  =>   $bar_select,
  'bar_reject'  =>   $bar_reject,
  'bar_waiting' =>   $bar_waiting,
  'INFO_TEXT_TOTAL' => INFO_TEXT_TOTAL,
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>RIGHT_HTML,
  'update_message'=>$messageStack->output()));
  $template->pparse('application');
?>