<?
/*
************************************************************
************************************************************
**********#	Name				      : Kamal Kumar Sahoo		 #***********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Date Created	 : 03/02/2005   					  #***********
**********#	Date Modified	: 03/02/2005     	    #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
************************************************************
*/
///*
//header('Content-Type: text/xml'); 
//*/
include_once("../include_files.php");
///*
$cat_field_names="id,category_name";
$cat_whereClause=" where 1 ";
$cat_query = "select $cat_field_names from ".JOB_CATEGORY_TABLE." $cat_whereClause order by  ".TEXT_LANGUAGE."category_name  asc  ";
$cat_result=tep_db_query($cat_query);
$count=1;
while($row_cat = tep_db_fetch_array($cat_result))
{
 $file_content ='';
 $file_content.= '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
 $file_content.= '<rss version="2.0"  xmlns:job="http://'.$_SERVER['HTTP_HOST'].'/rss/">' ."\n";
 $file_content.= '<channel>'."\n" ;
 $file_content.= '<title>'.tep_db_output($row_cat['category_name']).' RSS - '.tep_db_output(SITE_TITLE).'</title>'."\n" ;
 $file_content.= '<description>'.tep_db_output($row_cat[TEXT_LANGUAGE.'category_name']).' jobs </description>'."\n"; 
 $file_content.= '<link>'.HOST_NAME.'</link>' ;
 $file_content.= '<copyright>Copyright '.date('Y').' '.$_SERVER['HTTP_HOST'].' All Rights Reserved</copyright>'."\n"; 
 $file_content.= '<job:link  href="http://'.$_SERVER['HTTP_HOST'].'/rss/'.$row_cat['id'].'.xml" rel="self" type="application/rss+xml"/>'."\n" ;
 //*/
 $now=date('Y-m-d H:i:s');
 $table_names=JOB_TABLE." as j left join ".COUNTRIES_TABLE." as c on (j.job_country_id=c.id) left join ".RECRUITER_USERS_TABLE." as ru on ( j.recruiter_user_id=ru.id or ru.id is NULL ), ".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
 $whereClause="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and ";
 $search_category1 =get_search_job_category($row_cat['id']);
 $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$search_category1.")"; 
 $whereClause .=' job_id in ( ';
 $whereClause .=$whereClause_job_category;
 $whereClause .=" ) ";

 $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description, j.job_state, j.job_state_id, ";
 $field_names.="j.job_description, c.".TEXT_LANGUAGE."country_name, rl.recruiter_email_address, ru.email_address, ";
 $field_names.="j.job_location, j.job_salary, j.job_allowance, j.recruiter_id, r.recruiter_company_name, ";
 $field_names.="r.recruiter_url";
 $query = "select $field_names from $table_names where $whereClause ORDER BY re_adv desc limit  0,20";
 $result=tep_db_query($query);
 //$file_content.= "<br>$query";//exit;
 $x=tep_db_num_rows($result);
 //$file_content.= $x;exit;
 while($row = tep_db_fetch_array($result))
 {
  $ide=$row["job_id"];
  $title_format=encode_category($row['job_title']);
  $recruiter_id=$row["recruiter_id"];
  $query_string=encode_string("job_id=".$ide."=job_id");
  ///*
  $file_content.= '<item>'."\n" ;
  $file_content.= '<title>'.tep_db_output($row['job_title']).'</title>'."\n" ;
  $file_content.= '<description>'.tep_db_output($row['job_short_description']).'</description>' ."\n";
  //$file_content.= '<description>'.tep_db_output($row['job_description']).'</description>' ;
  $file_content.= '<job:job_id>'.tep_db_output($ide).'</job:job_id>' ."\n";
  $file_content.= '<job:state>'.tep_db_output(($row['job_state_id'] > 0 && tep_not_null($row['job_state_id'])?get_name_from_table(ZONES_TABLE, TEXT_LANGUAGE.'zone_name', 'zone_id', $row['job_state_id']):$row['job_state'])).'</job:state>' ."\n";
  $file_content.= '<job:location>'.(tep_not_null($row['job_location'])?tep_db_output($row['job_location']):'').'</job:location>' ."\n";
  ///$file_content.= '<job:salary>'.tep_db_output($row['job_salary']).(tep_not_null($row['job_allowance'])?' - '.$row['job_allowance']:'').'</job:salary>' ;
  //$file_content.= '<job:companyname>'.tep_db_output($row['recruiter_company_name']).'</job:companyname>' ;
  //$file_content.= '<job:companyurl>'.tep_db_output($row['recruiter_url']).'</job:companyurl>' ;
  //$file_content.= '<job:contactemail>'.tep_db_output(tep_not_null($row['email_address'])?$row['email_address']:$row['recruiter_email_address']).'</job:contactemail>' ;
  $file_content.= '<link>'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)) .'</link>'."\n";
   $file_content.= '<guid>'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'</guid>'."\n";
 $file_content.= '</item>'."\n" ;
 //*/
 }
 $file_content.= '</channel>'."\n" ;
 $file_content.= '</rss>';
 $handle = fopen(PATH_TO_MAIN_PHYSICAL.'rss/'.$row_cat['id'].'.xml', "w");
 fwrite($handle, stripslashes($file_content));
 fclose($handle);
}
tep_db_free_result($cat_result);
?>