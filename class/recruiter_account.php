<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class recruiter_account 
{
 var $recruiter_id;
 var $plan_for;
 function __construct($recruiter_id,$plan_for='job_post')
 {
  global $rInfo;
  global $row_check_recruiter;
  $this->recruiter_id=$recruiter_id;
  $this->plan_for=$plan_for;
  $now=date("Y-m-d");
  if($row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE," recruiter_id='".tep_db_input($this->recruiter_id)."' and  plan_for='".tep_db_input($this->plan_for)."'  and start_date <= '$now' and end_date >='$now'"))
  {
   $rInfo=array_merge($row_check_recruiter,$row);
   $rInfo = new objectInfo($rInfo);
  }
 }
 function check_status()
 {
  $now=date("Y-m-d");
  if($row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".tep_db_input($this->recruiter_id)."' and  plan_for='".tep_db_input($this->plan_for)."'  and start_date <= '$now' and end_date >='$now'","id"))
   return true;
  else
   return false;
 }
}
?>