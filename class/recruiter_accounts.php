<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class recruiter_accounts 
{
 var $allocated_amount=array();
 var $enjoyed_amount=array();
 var $remained_amount=array();
 
 function __construct($recruiter_id="",$plan_for='job_post')
 {
  $now=date("Y-m-d");
  if(!tep_not_null($recruiter_id))
  {
   $recruiter_id=$_SESSION['sess_recruiterid'];
  }
  if(!$row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."'  and plan_for='".tep_db_input($plan_for)."'  and start_date <= '$now' and end_date >='$now'"))
  {
   $this->allocated_amount=array('job'=>'0',
                                 'cv'=>'0',
                                 'sms'=>'0',
                                 'from'=>'',
                                 'to'=>'',
			                              'featured_job'=>'No');
   $this->enjoyed_amount=array('job'=>0,
                               'cv'=>0,
                               'sms'=>0);
   $this->remained_amount=array('job'=>0,
                               'cv'=>0,
                               'sms'=>0);
  }
  else
  {
   $this->allocated_amount=array('job'=>($row['recruiter_job_status']=='Yes'?($row['recruiter_job']=='2147483647'?'Unlimited':$row['recruiter_job']):'0'),
                                 'cv'=>($row['recruiter_cv_status']=='Yes'?($row['recruiter_cv']=='2147483647'?'Unlimited':$row['recruiter_cv']):'0'),
                                 'sms'=>($row['recruiter_sms_status']=='Yes'?($row['recruiter_sms']=='2147483647'?'Unlimited':$row['recruiter_sms']):'0'),
                                 'from'=>tep_date_long($row['start_date']),
                                 'to'=>tep_date_long($row['end_date']),
                                 'from1'=>$row['start_date'],
                                 'to1'=>$row['end_date'],
                                 'plan'=>$row['plan_type_name'],
                                 'featured_job'=>$row['featured_job']);
   $this->enjoyed_amount=array('job'=>$row['job_enjoyed'],
                               'cv'=>$row['cv_enjoyed'],
                               'sms'=>$row['sms_enjoyed']);
  }
 }
 function check_subscription($job_id)
 {
  global $messageStack;
  if(!$row=getAnyTableWhereData(JOB_TABLE,"job_id='$job_id' and recruiter_id='".$_SESSION['sess_recruiterid']."'"))
  {
   $messageStack->add_session(MESSAGE_JOB_ERROR, 'error');
   tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);   
  }
  $job=$this->allocated_amount['job'];
  if($job=="0")
   return false;
  else
   return true;
 }
 function re_advertise($job_id,$point,$from_date)
 {
  global $messageStack;
  if(!$row=getAnyTableWhereData(JOB_TABLE,"job_id='$job_id' and recruiter_id='".$_SESSION['sess_recruiterid']."'"))
  {
   $messageStack->add_session(MESSAGE_JOB_ERROR, 'error');
   tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);   
  }
  $job=$this->allocated_amount['job'];
  $total_point_enjoyed=$this->enjoyed_amount['job'];
  $now=date("Y-m-d");
  $row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE," recruiter_id='".$_SESSION['sess_recruiterid']."' and plan_for ='job_post' and start_date <= '$now' and end_date >='$now'","id,job_enjoyed");
  if($job >= $total_point_enjoyed || $job=="Unlimited")
  {
   //$sql_data_array=array('job_enjoyed'=>points($point)+$row['job_enjoyed']);
   $sql_data_array=array('job_enjoyed'=>1+$row['job_enjoyed']);
   tep_db_perform(RECRUITER_ACCOUNT_HISTORY_TABLE, $sql_data_array, 'update', "id = '" . $row['id'] . "'");
   $deleted='null';
   $re_adv=$from_date;
   $expired=datetime($point,$from_date);
   $sql_data_array=array('deleted'=>$deleted,'re_adv'=>$from_date,'expired'=>$expired);
   $check_featured=$this->allocated_amount['featured_job'];
   if($check_featured=='Yes')
 			$sql_data_array['job_featured']='Yes';
			else
 			$sql_data_array['job_featured']='No';
	  tep_db_perform(JOB_TABLE, $sql_data_array, 'update', "job_id = '" . $job_id . "'");
   $sql_data_array=array("job_id"=>$job_id,"re_advertised_from"=>$from_date,"re_advertised_to"=>$expired);
   tep_db_perform(JOB_POINT_HISTORY_TABLE, $sql_data_array);
   return true;
  }
  else
  {
   return false;
  }
 }
}
?>