<?
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik #********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
header('Content-Type: text/xml'); 
//*/
include_once("../include_files.php");
///*
echo '<?xml version="1.0" encoding="iso-8859-1"?>';
echo '<rss version="2.0">' ;

echo '<channel>' ;
echo '<title>'.tep_db_output(SITE_TITLE).' Job listings</title>' ;
echo '<description>'.tep_db_output(SITE_TITLE).' is the most efficient site for listing jobs in any category.</description>'; 
echo '<link>'.HOST_NAME.'</link>' ;
echo '<copyright>Copyright 2005 '.$_SERVER['HTTP_HOST'].' All Rights Reserved</copyright>'; 
//*/
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j left join ".COUNTRIES_TABLE." as c on (j.job_country_id=c.id) left join ".RECRUITER_USERS_TABLE." as ru on ( j.recruiter_user_id=ru.id or ru.id is NULL ), ".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
$whereClause.="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
$field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description, j.job_state, j.job_state_id, ";
$field_names.="j.job_description, c.country_name, rl.recruiter_email_address, ru.email_address, ";
$field_names.="j.job_location, j.job_salary, j.job_allowance, j.recruiter_id, r.recruiter_company_name, ";
$field_names.="r.recruiter_url";
$query = "select $field_names from $table_names where $whereClause ORDER BY re_adv desc";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
//echo $x;exit;
while($row = tep_db_fetch_array($result))
{
 $ide=$row["job_id"];
 $recruiter_id=$row["recruiter_id"];
 $title_format=encode_category($row['job_title']);
 $query_string=encode_string("job_id=".$ide."=job_id");
 ///*
 echo '<item>' ;
 echo '<title>'.tep_db_output($row['job_title']).'</title>' ;
 echo '<shortdescription>'.tep_db_output($row['job_short_description']).'</shortdescription>' ;
 echo '<description>'.tep_db_output($row['job_description']).'</description>' ;
 echo '<country>'.tep_db_output($row['country_name']).'</country>' ;
 echo '<state>'.tep_db_output(($row['job_state_id'] > 0 && tep_not_null($row['job_state_id'])?get_name_from_table(ZONES_TABLE, 'zone_name', 'zone_id', $row['job_state_id']):$row['job_state'])).'</state>' ;
 echo '<location>'.(tep_not_null($row['job_location'])?tep_db_output($row['job_location']):'').'</location>' ;
 echo '<salary>'.tep_db_output($row['job_salary']).(tep_not_null($row['job_allowance'])?' - '.$row['job_allowance']:'').'</salary>' ;
 echo '<companyname>'.tep_db_output($row['recruiter_company_name']).'</companyname>' ;
 echo '<companyurl>'.tep_db_output($row['recruiter_url']).'</companyurl>' ;
 echo '<contactemail>'.tep_db_output(tep_not_null($row['email_address'])?$row['email_address']:$row['recruiter_email_address']).'</contactemail>' ;
 echo '<links>'.tep_href_link($ide.'/'.$title_format.'.html').'</links>';
 echo '</item>' ;
//*/
}
///*
echo '</channel>' ;
echo '</rss>';
//*/
?>