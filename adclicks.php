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
include_once("include_files.php");
include_once(FILENAME_BODY);
$banner_id = (int)(isset($_GET['bID']) ? tep_db_prepare_input($_GET['bID']) : '');
/* ---------------- Begin code to increase the no of adclicks -------------------------*/
if($banner_click=getAnyTableWhereData(BANNER_TABLE," id='".$banner_id."'",'adclicks,href'))
{
 if(tep_not_null($banner_click['href']) && $banner_click['href']!='#' )
 {
  if ((substr($banner_click['href'],0,7)=='http://' )|| (substr($banner_click['href'],0,8)=='https://'))
   $banner_href=$banner_click['href'];
  else
   $banner_href="http://".$banner_click['href'];
  }
  else
  $banner_href="./";
  // END check for http:// or https:// exists in href or not ----
 $adclicks=$banner_click['adclicks']+1;
 $sql_data_array['adclicks']=$adclicks;
 tep_db_perform(BANNER_TABLE, $sql_data_array,'update',"id='".(int)$banner_id."'");
 tep_redirect($banner_href);
}
else
 tep_redirect('./');
/* ---------------- End code to increase the no of adclicks -------------------------*/
?>