<?
/*
************************************************************
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik #********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
************************************************************
*/
ini_set('error_reporting',E_ALL ^ E_NOTICE);
ini_set('display_errors','1');
include_once("../include_files.php");
include_once("../general_functions/pagerank.php");
$url=HOST_NAME;
$today=date('Y-m-').'-01';
$google_pagerank  = getpagerank($url);
$alexa_popularity = get_alexa_popularity($url); 
$sql_data_array =array('date'         => $today,
                       'google_rank' => $google_pagerank,
                       'alexa_rank'=> $alexa_popularity,
                      );

if(!$row=getAnyTableWhereData(PAGE_RANK_TABLE," date='".$today."'"))
{
 tep_db_perform(PAGE_RANK_TABLE,$sql_data_array);
}
else
{
 tep_db_perform(PAGE_RANK_TABLE,$sql_data_array,'update',"date='".$today."'");
}
?>