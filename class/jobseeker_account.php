<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class jobseeker_account 
{
 var $jobseeker_id;
 function __construct($jobseeker_id)
 {
  global $rInfo;
  global $row_check_jobseeker;
  $this->jobseeker_id=$jobseeker_id;
  $now=date("Y-m-d");
  if($row=getAnyTableWhereData(JOBSEEKER_ACCOUNT_HISTORY_TABLE,"jobseeker_id='".tep_db_input($this->jobseeker_id)."' and start_date <= '$now' and end_date >='$now'"))
  {
   $rInfo=array_merge($row_check_jobseeker,$row);
   $rInfo = new objectInfo($rInfo);
  }
 }
 function check_status()
 {
  $now=date("Y-m-d");
  if($row=getAnyTableWhereData(JOBSEEKER_ACCOUNT_HISTORY_TABLE,"jobseeker_id='".tep_db_input($this->jobseeker_id)."' and start_date <= '$now' and end_date >='$now'","id"))
   return true;
  else
   return false;
 }
}
?>