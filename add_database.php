<?
/*
************************************************************
************************************************************
**********#	Name				      : Shambhu Prasad PAtnaik		 #***********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/

ini_set('max_execution_time','3600');
include_once("include_files.php");
$cat_name= file(PATH_TO_MAIN_PHYSICAL."country.txt");
die();
for($i=0;$i<count($cat_name);$i++) 
{
  $sql_data_array=array(
                       'parent_id'=>'1',
                       'status'=>'Y',
                       'hits'=>'0',
                       'inserted'=>'now()',
                       'agent_id'=>'0',
                       'type'=>'0',
			                    'featured'=>'', 
                      );
  tep_db_perform(TABLE_CATEGORIES,$sql_data_array);

		$cat_id=tep_db_insert_id();
 
		$sql_data_array1=array(
			                      'categories_name'=>$cat_name[$i],
                       'languages_id'=>'1',
                       'categories_id'=> $cat_id,
			                    'cate_desc'=>$cat_name[$i],
                      );
 if(!$row_check=getAnyTableWhereData(TABLE_CATEGORIES_DESCRIPTION,"categories_id='".$cat_id."' and  categories_name= '".tep_db_prepare_input($cat_name[$i])."'"))
  tep_db_perform(TABLE_CATEGORIES_DESCRIPTION,$sql_data_array1);
 }
 die('ssss');







?>