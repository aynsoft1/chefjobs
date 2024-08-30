<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");

$query = "select continent_id, country_name  from ".COUNTRIES_TABLE." order by country_name ";
$result=tep_db_query($query);
 //echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
$c_string=''; 
if($x>0)
{
  while($row = tep_db_fetch_array($result))
  {
   $c_string.='<country>'."\n";
   $c_string.= '<id>'.tep_db_output($row['continent_id']).'</id>'."\n";
   $c_string.= '<name>'.tep_db_output($row['country_name']).'</name>'."\n";
   $c_string.='</country>'."\n";
  }
  tep_db_free_result($result);
}
$output = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
$output .='<countries>'."\n";
$output .='<title>'.tep_db_output(SITE_TITLE).' Country List</title>'."\n";
$output .='<status>success</status>'."\n";
$output .='<total>'.$x.'</total>'."\n";
$output .=$c_string;
//echo $x;die();
 
$output .='</countries>';
header('Content-Type: text/xml'); 
echo $output ;
?>