<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
//**************************************************************************************
//Function to get one record from anytable *********************************************
//**************************************************************************************

function getAnyTableWhereData($table,$whereClause,$fields="*",$debug=false)
{
	$query="select $fields from $table where $whereClause";
	$result=tep_db_query($query);
 if($debug)
 {
  echo "<br>$query";
  exit;
 }
	//echo mysql_num_rows($result);
	if($row=tep_db_fetch_array($result))
	{
		tep_db_free_result($result);
		return $row;
	}
	else
	{
		return false;
	}
}
//**************************************************************************************
//Function to show date-month-year drop downs ******************************************
//**************************************************************************************
function datelisting($date, $day_parameter, $month_parameter, $year_parameter,
                         $startYear="", $endYear="", $required=false,$display_name=false)
{
	$datelist="";
	list($year,$month,$day)= explode ("-", $date);
	$datelist.= '<div class="row g-2 row-cols-3"><div class="col-md-4 pr-0 date-rage-inline"><select '.$day_parameter.'><option value="">'.INFO_TEXT_DAY.'</option>';
	$ct=0;
	for($ct=1;$ct<=31;$ct++)
	{
		if(strlen($ct)<2)
		{
			$ct="0".$ct;
		}
		$datelist.="<option value='".($ct)."'";
		if($ct==$day)
		{
			$datelist.=" selected";
		}
		$datelist.= ">".$ct."</option>";
	}
	$datelist.="</select></div>";
	// $datelist.=" - ";
	$datelist.='<div class="col-md-4 pr-0 date-rage-inline"><select '.$month_parameter.'><option value="">'.INFO_TEXT_MONTH1.'</option>';
	$ct=1;
 $month_array=array("01"=>INFO_TEXT_JANUARY,"02"=>INFO_TEXT_FEBRUARY,"03"=>INFO_TEXT_MARCH,
                    "04"=>INFO_TEXT_APRIL,"05"=>INFO_TEXT_MAY,"06"=>INFO_TEXT_JUNE,"07"=>INFO_TEXT_JULY,
                    "08"=>INFO_TEXT_AUGUST,"09"=>INFO_TEXT_SEPTEMBER,"10"=>INFO_TEXT_OCTOBER,
                    "11"=>INFO_TEXT_NOVEMBER,"12"=>INFO_TEXT_DECEMBER);
	for($ct=1;$ct<=12;$ct++)
	{
		if(strlen($ct)<2)
		{
			$ct="0".$ct;
		}
		$datelist.="<option value='".($ct)."'";
		if(($ct)==$month)
		{
			$datelist.=" selected";
		}
		$datelist.= ">".($month_array[$ct]) ."</option>";
	}
	$datelist.='</select></div>';
	// $datelist.="-";
	$datelist.= '<div class="col-md-4 date-rage-inline"><select '.$year_parameter.'><option value="">'.INFO_TEXT_YEAR1.'</option>';
	$ct=0;
	if($startYear=="")
	{
		$s_yy=(date('Y')-80);
	}
	else
	{
		$s_yy = $startYear;
	}
	if($endYear=="")
	{
		$e_yy=(date('Y')+20);
	}
	else
	{
		$e_yy = $endYear;
	}
	for($ct=$s_yy;$ct<=$e_yy;$ct++)
	{
		$datelist.= "<option value='".($ct)."'";
		if($ct==$year)
		{
			$datelist.= " selected";
		}
		$datelist.= ">".$ct."</option>";
	}
	$datelist.= "</select></div></div>";
 if($required)
 	$datelist.= '&nbsp;<span class="inputRequirement">*</span>';
 if($display_name)
	$datelist.= '&nbsp;<span class="footer">'.INFO_TEXT_DAY_MONTH_YEAR.'</span>';
 //$datelist.='<script language="javascript">calender.writeControl(); calender.dateFormat="yyyy-MM-dd";</script>';
 	return $datelist;
}

//********************************************************************************************************************************
//Function to show date-month-year drop downs for admin as adminisnot bootstrap so div removed from datelisting function *********
//********************************************************************************************************************************
function datelisting_admin($date, $day_parameter, $month_parameter, $year_parameter,
                         $startYear="", $endYear="", $required=false,$display_name=false)
{
	$datelist="";
	list($year,$month,$day)= explode ("-", $date);
	$datelist.= '<select '.$day_parameter.'><option value="">'.INFO_TEXT_DAY.'</option>';
	$ct=0;
	for($ct=1;$ct<=31;$ct++)
	{
		if(strlen($ct)<2)
		{
			$ct="0".$ct;
		}
		$datelist.="<option value='".($ct)."'";
		if($ct==$day)
		{
			$datelist.=" selected";
		}
		$datelist.= ">".$ct."</option>";
	}
	$datelist.="</select>";
	// $datelist.=" - ";
	$datelist.='<select '.$month_parameter.'><option value="">'.INFO_TEXT_MONTH1.'</option>';
	$ct=1;
 $month_array=array("01"=>INFO_TEXT_JANUARY,"02"=>INFO_TEXT_FEBRUARY,"03"=>INFO_TEXT_MARCH,
                    "04"=>INFO_TEXT_APRIL,"05"=>INFO_TEXT_MAY,"06"=>INFO_TEXT_JUNE,"07"=>INFO_TEXT_JULY,
                    "08"=>INFO_TEXT_AUGUST,"09"=>INFO_TEXT_SEPTEMBER,"10"=>INFO_TEXT_OCTOBER,
                    "11"=>INFO_TEXT_NOVEMBER,"12"=>INFO_TEXT_DECEMBER);
	for($ct=1;$ct<=12;$ct++)
	{
		if(strlen($ct)<2)
		{
			$ct="0".$ct;
		}
		$datelist.="<option value='".($ct)."'";
		if(($ct)==$month)
		{
			$datelist.=" selected";
		}
		$datelist.= ">".($month_array[$ct]) ."</option>";
	}
	$datelist.='</select>';
	// $datelist.="-";
	$datelist.= '<select '.$year_parameter.'><option value="">'.INFO_TEXT_YEAR1.'</option>';
	$ct=0;
	if($startYear=="")
	{
		$s_yy=(date('Y')-80);
	}
	else
	{
		$s_yy = $startYear;
	}
	if($endYear=="")
	{
		$e_yy=(date('Y')+20);
	}
	else
	{
		$e_yy = $endYear;
	}
	for($ct=$s_yy;$ct<=$e_yy;$ct++)
	{
		$datelist.= "<option value='".($ct)."'";
		if($ct==$year)
		{
			$datelist.= " selected";
		}
		$datelist.= ">".$ct."</option>";
	}
	$datelist.= "</select>";
 if($required)
 	$datelist.= '&nbsp;<span class="inputRequirement">*</span>';
 if($display_name)
	$datelist.= '&nbsp;<span class="footer">'.INFO_TEXT_DAY_MONTH_YEAR.'</span>';
 //$datelist.='<script language="javascript">calender.writeControl(); calender.dateFormat="yyyy-MM-dd";</script>';
 	return $datelist;
}
//**************************************************************************************
//Listing names from a table Table *****************************************************
//**************************************************************************************
function LIST_TABLE($table_name,$field_name,$order_by="",$parameters="",$header="",$header_value="",$selected="",$footer="",$footer_value="")
{
	$string="";
 $selected=explode(",",$selected);
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='$header_value'";
  if(in_array($header_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($header)."</option>";
 }
  if($order_by!="")
    $query = "select id,$field_name from $table_name order by $order_by asc";
  else
    $query = "select id,$field_name from $table_name order by $field_name asc";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
		$c=$row[$field_name];
		$code=$row['id'];
  $string.="<option value='$code'";
  if(in_array($code,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($c)."</option>";
 }
 if($footer!="")
 {
  $string.="<option value='$footer_value'";
  if(in_array($footer_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($footer)."</option>";
 }
	$string.="</select>";
	tep_db_free_result($result);
	return $string;
}
//**************************************************************************************
//Listing states from a table Table *****************************************************
//**************************************************************************************
function LIST_ZONE($table_name,$field_name,$order_by="",$parameters="",$header="",$header_value="",$selected="",$footer="",$footer_value="")
{
	$string="";
 $selected=explode(",",$selected);
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='$header_value'";
  if(in_array($header_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($header)."</option>";
 }
  if($order_by!="")
    $query = "select zone_id,$field_name from $table_name where zone_country_id=".DEFAULT_COUNTRY_ID." order by $order_by asc";
  else
    $query = "select zone_id,$field_name from $table_name where zone_country_id=".DEFAULT_COUNTRY_ID." order by $field_name asc";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
		$c=$row[$field_name];
		$code=$row['zone_name'];
  $string.="<option value='$code'";
  if(in_array($code,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($c)."</option>";
 }
 if($footer!="")
 {
  $string.="<option value='$footer_value'";
  if(in_array($footer_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($footer)."</option>";
 }
	$string.="</select>";
	tep_db_free_result($result);
	return $string;
}
//**************************************************************************************
//Experience   **************************************************************************
//**************************************************************************************
function experience_drop_down($parameters, $header="", $header_value="", $selected="")
{
 $string="";
 if($selected==null)
	 $selected="";
 $selected=explode(",",$selected);
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='$header_value'";
  if(in_array($header_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($header)."</option>";
 }
 $query="select id, min_experience, max_experience from " . EXPERIENCE_TABLE ." order by priority";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
  $code=$row['id'];
  $min_experience=tep_db_output($row['min_experience']);
  $max_experience=tep_db_output($row['max_experience']);
  $value=calculate_experience($min_experience,$max_experience);
  $string.="<option value='".$min_experience."-".$max_experience."'";
  if(in_array($min_experience."-".$max_experience,$selected))
  {
   $string.=" selected";
  }
  $string.=">".$value."</option>";
 }
	$string.="</select>";
	tep_db_free_result($result);
	return $string;
}
function experience_drop_down1($parameters, $header="", $header_value="", $selected="")
{
 $string="";
 $selected=explode(",",$selected);
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='$header_value'";
  if(in_array($header_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($header)."</option>";
 }
 $query="select id, min_experience, max_experience from " . EXPERIENCE_TABLE ." order by priority";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
  $code=$row['id'];
  $min_experience=tep_db_output($row['min_experience']);
  $max_experience=tep_db_output($row['max_experience']);
  $value=calculate_experience($min_experience,$max_experience);
  $string.="<option value='".$code."'";
  if(in_array($code,$selected))
  {
   $string.=" selected";
  }
  $string.=">".$value."</option>";
 }
	$string.="</select>";
	tep_db_free_result($result);
	return $string;
}
//**************************************************************************************
//Job categories **************************************************************************
//**************************************************************************************
function INDUSTRY_SECTOR($checked="")
{
	$INDUSTRY_SECTOR_STRING="";
	$INDUSTRY_SECTOR_STRING.="\n<table border='0' width='100%' align='center'>\n<tr>\n";
	$query = "select * from ".JOB_CATEGORY_TABLE." order by ".TEXT_LANGUAGE."category_name asc";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	$i=1;
 $checked=explode(",",$checked);
	while($row = tep_db_fetch_array($result))
	{
		$cid=$row['id'];
		$c=tep_db_output(($row[TEXT_LANGUAGE.'category_name']));
		$INDUSTRY_SECTOR_STRING.="<td>";
		$INDUSTRY_SECTOR_STRING.=tep_draw_checkbox_field('industry[]', $cid, (in_array($cid,$checked)?true:false), '', 'id="industry_sector_checkbox'.$cid.'"');
		$INDUSTRY_SECTOR_STRING.="</td>\n<td class='small'><label for='industry_sector_checkbox".$cid."' onMouseOver=\"this.style.color='#0000ff'\" onMouseOut=\"this.style.color='#000080'\">".$c."</label></td>\n";
		if($i % 3==0)
		{
			$INDUSTRY_SECTOR_STRING.="</tr>\n<tr>\n";
		}
		$i++;
	}
	$INDUSTRY_SECTOR_STRING.="</table>";
	tep_db_free_result($result);
	return $INDUSTRY_SECTOR_STRING;
}
//**************************************************************************************
//Job types **************************************************************************
//**************************************************************************************
function JOB_TYPE($checked="")
{
	$JOB_TYPE_STRING="";
	$JOB_TYPE_STRING.="\n<table border='0'>\n<tr>\n";
	$query = "select * from ".JOB_TYPE_TABLE." order by ".TEXT_LANGUAGE."type_name asc";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	$i=1;
 $checked=explode(",",$checked);
	while($row = tep_db_fetch_array($result))
	{
		$cid=$row['id'];
		$c=tep_db_output(($row[TEXT_LANGUAGE.'type_name']));
		$JOB_TYPE_STRING.="<td>";
		$JOB_TYPE_STRING.=tep_draw_checkbox_field('job_type[]', $cid, (in_array($cid,$checked)?true:false), '', 'class="form-check-input" id="job_type_checkbox'.$cid.'"');
		$JOB_TYPE_STRING.="</td>\n<td class='form-check2 small'><label class='form-check-label' for='job_type_checkbox".$cid."'>".$c."</label></td>\n";
		if($i % 3==0)
		{
			$JOB_TYPE_STRING.="</tr>\n<tr>\n";
		}
		$i++;
	}
	$JOB_TYPE_STRING.="</table>";
	tep_db_free_result($result);
	return $JOB_TYPE_STRING;
}
//**************************************************************************************
//Shift types **************************************************************************
//**************************************************************************************
function SHIFT_TYPE($checked="")
{
	$SHIFT_TYPE_STRING="";
	$SHIFT_TYPE_STRING.="\n<table border='0'>\n<tr>\n";
	$query = "select * from ".SHIFT_TABLE." order by priority asc";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	$i=1;
 $checked=explode(",",$checked);
	while($row = tep_db_fetch_array($result))
	{
		$cid=$row['id'];
		$c=tep_db_output(($row[TEXT_LANGUAGE.'shift_name']));
		$SHIFT_TYPE_STRING.="<td>";
		$SHIFT_TYPE_STRING.=tep_draw_checkbox_field('shift_type[]', $cid, (in_array($cid,$checked)?true:false), '', 'id="shift_type_checkbox'.$cid.'"');
		$SHIFT_TYPE_STRING.="</td>\n<td><label for='shift_type_checkbox".$cid."' onMouseOver=\"this.style.color='#0000ff'\" onMouseOut=\"this.style.color='#000080'\">".$c."</label></td>\n";
		if($i % 3==0)
		{
			$SHIFT_TYPE_STRING.="</tr>\n<tr>\n";
		}
		$i++;
	}
	$SHIFT_TYPE_STRING.="</table>";
	tep_db_free_result($result);
	return $SHIFT_TYPE_STRING;
}

//**************************************************************************************
//reason for seeking new job************************************************************
//**************************************************************************************
function SEEKING_JOB_REASON($checked="")
{
	$SEEKING_JOB_REASON_STRING="";
	$SEEKING_JOB_REASON_STRING.="\n<table border='0'>\n<tr>\n";
	$query = "select * from ".SEEKING_JOB_REASON_TABLE." order by priority asc";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	$i=1;
	while($row = tep_db_fetch_array($result))
	{
		$cid=$row['id'];
		$c=tep_db_output(($row['seeking_job_reason_name']));
		$SEEKING_JOB_REASON_STRING.="<td>";
		$SEEKING_JOB_REASON_STRING.=tep_draw_radio_field('seeking_job_reason', $cid, '', $checked, 'id="seeking_job_reason_checkbox'.$cid.'"');
		$SEEKING_JOB_REASON_STRING.="</td>\n<td><label for='seeking_job_reason_checkbox".$cid."' onMouseOver=\"this.style.color='#0000ff'\" onMouseOut=\"this.style.color='#000080'\">".$c."</label></td>\n";
		if($i % 3==0)
		{
			$SEEKING_JOB_REASON_STRING.="</tr>\n<tr>\n";
		}
		$i++;
	}
	$SEEKING_JOB_REASON_STRING.="</table>";
	tep_db_free_result($result);
	return $SEEKING_JOB_REASON_STRING;
}


//*************************************************************************************
//Company name display (have jobs)*****************************************************
//*************************************************************************************
function company_drop_down($parameters, $header="", $header_value="", $selected="")
{
 $string="";
 $selected=explode(",",$selected);
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='$header_value'";
  if(in_array($header_value,$selected))
  {
   $string.=" selected";
  }
  $string.=">".stripslashes($header)."</option>";
 }
 $query="select distinct(r.recruiter_id),r.recruiter_company_name from ".JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE." as rl on (j.recruiter_id=rl.recruiter_id) left outer join ".RECRUITER_TABLE." as r on (rl.recruiter_id=r.recruiter_id)";
	$result=tep_db_query($query);
	//echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	//echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
  $code=$row['recruiter_id'];
  $value=tep_db_output($row['recruiter_company_name']);
  $string.="<option value='".$code."'";
  if(in_array($code,$selected))
  {
   $string.=" selected";
  }
  $string.=">".$value."</option>";
 }
	$string.="</select>";
	tep_db_free_result($result);
	return $string;
}
//**************************************************************************************


//*************************************************************************************
//Follow company -Job Alert record save  **********************************************
//*************************************************************************************
function follow_company($recruiter)
{
 $query="select r.recruiter_id,r.recruiter_company_name from ".JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE." as rl on (j.recruiter_id=rl.recruiter_id) left outer join ".RECRUITER_TABLE." as r on (rl.recruiter_id=r.recruiter_id) where r.recruiter_id='".$recruiter."'";
	$result=tep_db_query($query);
	echo "<br>$query";exit;
	$x=tep_db_num_rows($result);
	echo $x;exit;
	while($row = tep_db_fetch_array($result))
	{
  $code=$row['recruiter_id'];
  $value=tep_db_output($row['recruiter_company_name']);
  $string.="<option value='".$code."'";
  if(in_array($code,$selected))
  {
   $string.=" selected";
  }
  $string.=">".$value."</option>";
 }
	$string.="</select>";
	tep_db_free_result($result);
	return $string;
}
//**************************************************************************************
//**************************************************************************************
//Check Login **************************************************************************
//**************************************************************************************
function check_login($name)
{
 $return=false;
 switch($name)
 {
  case 'jobseeker':
		if(isset($_SESSION['sess_jobseekerlogin']) && $_SESSION['sess_jobseekerlogin']=="y")
		{
			$return=true;
		}
  break;
  case 'recruiter':
		if(isset($_SESSION['sess_recruiterlogin']) && $_SESSION['sess_recruiterlogin']=="y")
		{
			$return=true;
		}
  break;
  case 'admin':
		if(isset($_SESSION['sess_adminlogin']) && $_SESSION['sess_adminlogin']=="y")
		{
			$return=true;
		}
  break;
  case 'default':
			$return=false;
 }
 return $return;
}
// Take an input string and encoded it into a slightly encoded hexval
// that we can use as a session cookie.
function encode_string ( $instr ,$encode_data="")
{
	//global $offsets;
	$offsets = array ( 73, 56, 31, 58, 77, 75 );
	//echo "<P>ENCODE<BR>";
	$ret = "";
	for ( $i = 0; $i < strlen ( $instr ); $i++ )
	{
		//echo "<P>";
		$ch1 = substr ( $instr, $i, 1 );
		$val = ord ( $ch1 );
		//echo "val = $val for \"$ch1\" <br>\n";
		$j = $i % count ( $offsets );
		//echo "Using offsets $j = $offsets[$j]<br>";
		$newval = $val + $offsets[$j];
		$newval %= 256;
		//echo "newval = $newval for \"$ch1\" <br>\n";
		$ret .= bin2hex ( chr ( $newval ) );
	}
	return $ret;
}

// Define an array to use to jumble up the key
//$offsets = array ( 31, 41, 59, 26, 54 );
//echo $encode_data;
//$offsets = array ( 74, 56, 34, 58, 35, 78 );

function hextoint ( $val )
{
  if ( empty ( $val ) )
    return 0;
  switch ( strtoupper ( $val ) )
	{
    case "0": return 0;
    case "1": return 1;
    case "2": return 2;
    case "3": return 3;
    case "4": return 4;
    case "5": return 5;
    case "6": return 6;
    case "7": return 7;
    case "8": return 8;
    case "9": return 9;
    case "A": return 10;
    case "B": return 11;
    case "C": return 12;
    case "D": return 13;
    case "E": return 14;
    case "F": return 15;
  }
  return 0;
}


function decode_string ( $instr ,$encode_data="")
{
  //global $offsets;
	 $offsets = array ( 73, 56, 31, 58, 77, 75 );
  //echo "<P>DECODE <BR>";
  $orig = "";
  for ( $i = 0; $i < strlen ( $instr ); $i += 2 )
	{
    //echo "<P>";
    $ch1 = substr ( $instr, $i, 1 );
    $ch2 = substr ( $instr, $i + 1, 1 );
    $val = hextoint ( $ch1 ) * 16 + hextoint ( $ch2 );
    //echo "decoding \"" . $ch1 . $ch2 . "\" = $val <br>\n";
    $j = ( $i / 2 ) % count ( $offsets );
    //echo "Using offsets $j = " . $offsets[$j] . "<br>";
    $newval = $val - $offsets[$j] + 256;
    $newval %= 256;
    //echo " neval \"$newval\" <br>\n";
    $dec_ch = chr ( $newval );
    //echo " which is \"$dec_ch\" <br>\n";
    $orig .= $dec_ch;
  }
  return $orig;
}
function autologin()
{
 if(isset($_COOKIE["autologin2"]))
 {
  $explode_array=explode("|",decode_string($_COOKIE["autologin2"]));
  $recruiter_email_address=tep_db_prepare_input($explode_array[0]);
  $recruiter_password=$explode_array[1];
  $whereClause="rl.recruiter_email_address='".tep_db_input($recruiter_email_address)."' and rl.recruiter_status='Yes' and rl.recruiter_id=r.recruiter_id";
  $fields='rl.recruiter_id,rl.recruiter_password';
  if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl, '.RECRUITER_TABLE.' as r',$whereClause,$fields))
  {
   if(tep_validate_password($recruiter_password, $row['recruiter_password']))
   {
    $_SESSION['sess_recruiterlogin']="y";
    $_SESSION['sess_recruiterid']=$row["recruiter_id"];
   }
  }
  else if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($recruiter_email_address)."' and status='Yes'","id,recruiter_id,password"))
  {
   if(tep_validate_password($recruiter_password, $row['password']))
   {
    $_SESSION['sess_recruiterlogin']="y";
    $_SESSION['sess_recruiterid']=$row["recruiter_id"];
    $_SESSION['sess_recruiteruserid']=$row["id"];
   }
  }
 }
 else if(isset($_COOKIE["autologin1"]))
 {
  $explode_array=explode("|",decode_string($_COOKIE["autologin1"]));
  $email_address=$explode_array[0];
  $jobseeker_password=$explode_array[1];
  $whereClause="jl.jobseeker_email_address='".tep_db_input($email_address)."' and jl.jobseeker_status='Yes' and jl.jobseeker_id=j.jobseeker_id";
  $fields="jl.jobseeker_id,jl.jobseeker_password";
  if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl, '.JOBSEEKER_TABLE.' as j',$whereClause,$fields))
  {
   if(tep_validate_password($jobseeker_password, $row['jobseeker_password']))
   {
    $_SESSION['sess_jobseekerid']=$row['jobseeker_id'];
    $_SESSION['sess_jobseekerlogin']='y';
   }
  }
 }
}


// default image ui-avatars
function defaultProfilePhotoUrl($name = null, $rounded=false, $size=null, $attributes = null)
{
	$url = "https://ui-avatars.com/api/?background=random&size=$size&rounded=$rounded&name=".urlencode($name)."";
	
	$img_tag = '<img src="'.$url.'" '.$attributes.' />';

    return $img_tag;
}
?>