<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
function email_list($parameters,$header="",$header_value="",$selected="")
{
	$string="";
 $selected=explode(",",$selected);
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='".htmlspecialchars($header_value)."'";
  if(in_array($header_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($header)."</option>";  
 }
 $recruiter_id=$_SESSION['sess_recruiterid'];
 $row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id='$recruiter_id'","recruiter_id,recruiter_email_address");
 $value=$row['recruiter_email_address'];
 $id=$row['recruiter_id'];
 $string.="<option value='".htmlspecialchars($value)."'";
 if(in_array($value,$selected))
 {
  $string.=" selected";
 }
 $string.=">".stripslashes($value)."</option>";  
	$query = "select id,email_address from ".RECRUITER_USERS_TABLE." where recruiter_id='$recruiter_id'";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
  $id=$row['id'];
		$value=$row['email_address'];
  $string.="<option value='".htmlspecialchars($value)."'";
  if(in_array($value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($value)."</option>";  
 }
	$string.="</select>";
	@tep_db_free_result($result);
	return $string;
}
///// conversion to points ////////
function points1($point)
{
 $point=ceil($point/7);
 return $point;
}

function points($str)
{
	if($str=="One week")
	{
		return "1";
	}
	if($str=="Two weeks")
	{
		return "2";
	}
	if($str=="Three weeks")
	{
		return "3";
	}
	if($str=="One month")
	{
		return "4";
	}
}
///// conversion to datetime ////////
function datetime($str,$inserted)
{
 $y=substr($inserted,0,4);
 $m=substr($inserted,5,2);
 $d=substr($inserted,8,2);
	if($str=="One week")
	{
		$str=date("Y-m-d 23:59:59",mktime(date("H"),date("i"), date("s"), $m  , ($d+7), $y));
	}
	else if($str=="Two weeks")
	{
		$str=date("Y-m-d 23:59:59",mktime(date("H"),date("i"), date("s"), $m  , ($d+14), $y));
	}
	else if($str=="Three weeks")
	{
		$str=date("Y-m-d 23:59:59",mktime(date("H"),date("i"), date("s"), $m  , ($d+21), $y));
	}
	else if($str=="One month")
	{
		$str=date("Y-m-d 23:59:59",mktime(date("H"),date("i"), date("s"), ($m+1)  , $d, $y));
	}
	return $str;
}
function recruiter_plan_type_name()
{
 $now=date("Y-m-d");
 if($row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and start_date <= '$now' and end_date >='$now'","plan_type_name"))
 {
  if($row['plan_type_name']=="Demo")
   $_SESSION['sess_plan_type_name']='Demo';
  else 
   unset($_SESSION['sess_plan_type_name']);
 }
}
//function like dateDiff Microsoft
//bug update for previous

function dateDiff($interval,$dateTimeBegin,$dateTimeEnd) 
{
 //Parse about any English textual datetime
 //$dateTimeBegin, $dateTimeEnd
 $dateTimeBegin=strtotime($dateTimeBegin);
 if($dateTimeBegin === -1) 
 {
  return -1;
 }
 $dateTimeEnd=strtotime($dateTimeEnd);
 if($dateTimeEnd === -1) 
 {
  return -1;
 }
 $dif=$dateTimeEnd - $dateTimeBegin;
 switch($interval) 
 {
  case "s"://seconds
   return($dif);
  case "n"://minutes
   return(floor($dif/60)); //60s=1m
  case "h"://hours
   return(floor($dif/3600)); //3600s=1h
  case "d"://days
   return(floor($dif/86400)); //86400s=1d
  case "ww"://Week
   return(floor($dif/604800)); //604800s=1week=1semana
  case "m": //similar result "m" dateDiff Microsoft
   $monthBegin=(date("Y",$dateTimeBegin)*12)+date("n",$dateTimeBegin);
   $monthEnd=(date("Y",$dateTimeEnd)*12)+date("n",$dateTimeEnd);
   $monthDiff=$monthEnd-$monthBegin;
   return($monthDiff);
  case "yyyy": //similar result "yyyy" dateDiff Microsoft
   return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));
  default:
   return(floor($dif/86400)); //86400s=1d
 }
}

?>