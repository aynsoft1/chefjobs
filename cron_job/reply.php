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
 $file_content.= '<rss version="2.0"  xmlns:job=\'http://'.$_SERVER['HTTP_HOST'].'/rss/replies.xml\'>' ;

 $file_content.= '<channel>' ;
 $file_content.= '<title>'.$_SERVER['HTTP_HOST'].'</title>' ;

 $file_content.= '<description> Forum Topics </description>'; 
 $file_content.= '<link>'.HOST_NAME.'</link>' ;
 $file_content.= '<copyright>Copyright 2011 '.$_SERVER['HTTP_HOST'].' All Rights Reserved</copyright>'; 
 //*/
 $now=date('Y-m-d H:i:s');
 $query = "select * from ".TOPIC_REPLY_TABLE." ORDER BY inserted desc";
 $result=tep_db_query($query);
 //echo "<br>$query";//exit;
 $x=tep_db_num_rows($result);
 while($row = tep_db_fetch_array($result))
 {
  $ide=$row["id"];
  $forum_id=$row["forum_id"];
  $title_format=encode_FORUM(((strlen($row['title'])<150)?$row['title']:substr($row['title'],0,148).".."));
  ///*
  $file_content.= '<item>' ;
  $file_content.= '<title>'.tep_db_output($row['title']).'</title>' ;
  $file_content.= '<description>'.tep_db_output($row['description']).'</description>' ;
  $file_content.= '<link>'.tep_db_output($row['title']).'</link>';
  $file_content.= '</item>' ;
 //*/
 }
 $file_content.= '</channel>' ;
 $file_content.= '</rss>';
 $handle = fopen(PATH_TO_MAIN_PHYSICAL.'rss/replies.xml', "w");
 fwrite($handle, stripslashes($file_content));
 fclose($handle);
tep_db_free_result($result);
?>