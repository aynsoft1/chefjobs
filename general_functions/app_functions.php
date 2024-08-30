<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
**********************************************************/
function get_access_key($user_id,$ip_addres)
{
 $key =date('YmdHis').randomize(3).$ip_addres.'#'.$user_id;
	$access_key  =md5($key); 
 $now=date("Y-m-d H:i:s");
 $expired=date("Y-m-d H:i:s",mktime(date("H")+2,date("i"),date("s"), date("m"), date("d"), date("Y")));

 $sql_data_array=array('access_key'=> $access_key,
                       'user_id'  => $user_id,
                       'user_ip_address'  => $ip_addres,
                       'inserted'     => $now,
                       'expired'     => $expired,
                       );
 tep_db_perform(APP_ACCESS_TABLE, $sql_data_array);
	return $access_key ;
}
function get_access_user($access_key,$user_type='jobseeker')
{
 $now=date("Y-m-d H:i:s");
 $user_id =0;
 if(!tep_not_null($access_key))
 return $user_id;
 switch ($user_type)
 { 
  case 'recruiter':
   if($check_row = getAnyTableWhereData(APP_ACCESS_TABLE.' as a left outer join '.RECRUITER_LOGIN_TABLE.' as rl on (a.user_id =rl.recruiter_id ) ', " a.access_key ='" . tep_db_input($access_key)."' and a.expired >='" . tep_db_input($now). "' and   rl.recruiter_status='Yes'", "a.user_id"))
   $user_id =$check_row['user_id'];
   break;
  default :
  if(  $check_row = getAnyTableWhereData(APP_ACCESS_TABLE.' as a left outer join '.JOBSEEKER_LOGIN_TABLE.' as jl on (a.user_id =jl.jobseeker_id ) ', " a.access_key ='" . tep_db_input($access_key)."' and a.expired >='" . tep_db_input($now). "' and   jl.jobseeker_status='Yes'", "a.user_id"))
   $user_id =$check_row['user_id'];
   break;
 }
 return $user_id;
}
?>