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
 $file_content ='';
 $file_content.= '<?xml version="1.0" encoding="iso-8859-1"?>';
 $file_content.= '<rss version="2.0"  xmlns:job=\'http://'.$_SERVER['HTTP_HOST'].'/rss/topics.xml\'>' ;

 $file_content.= '<channel>' ;
 $file_content.= '<title>'.$_SERVER['HTTP_HOST'].'</title>' ;

 $file_content.= '<description> Forum Topics </description>'; 
 $file_content.= '<link>'.HOST_NAME.'</link>' ;
 $file_content.= '<copyright>Copyright 2011 '.$_SERVER['HTTP_HOST'].' All Rights Reserved</copyright>'; 
 //*/
 $now=date('Y-m-d H:i:s');
 $table_names = "(select topic_id,count(topic_id)  as total  from ".TOPIC_REPLY_TABLE."  as tr  left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (tr.user_id =jl.jobseeker_id && tr.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (tr.user_id =rl.recruiter_id && tr.user_type='recruiter') group by tr.topic_id order by total desc) as r left outer join  ".FORUM_TOPICS_TABLE."   as t on (r.topic_id=t.id)   left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (t.user_id =jl.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (t.user_id =rl.recruiter_id && t.user_type='recruiter') left outer join ".FORUM_TABLE." as f on (t.forum_id=f.id)";

 $query = "select r.total,t.* from  $table_names  where f.is_show ='Yes' and f.show_date <= curdate()  ORDER BY r.total desc limit 0,20";
 $result=tep_db_query($query);
 //echo "<br>$query";exit;
 $x=tep_db_num_rows($result);
 while($row = tep_db_fetch_array($result))
 {
  $ide=$row["id"];
  $forum_id=$row["forum_id"];
  $title_format=encode_forum($row['title']);
  ///*
  $file_content.= '<item>' ;
  $file_content.= '<title>'.tep_db_output($row['title']).'</title>' ;
  $file_content.= '<description>'.tep_db_output($row['description']).'</description>' ;
  $file_content.= '<link>'.getPermalink('forum_topics',array('ide'=>$ide,'seo_name'=>$title_format)).'</link>';
  $file_content.= '</item>' ;
 //*/
 }
 $file_content.= '</channel>' ;
 $file_content.= '</rss>';
 $handle = fopen(PATH_TO_MAIN_PHYSICAL.'rss/topics.xml', "w");
 fwrite($handle, stripslashes($file_content));
 fclose($handle);
tep_db_free_result($result);
?>