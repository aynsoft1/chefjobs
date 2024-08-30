<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class mysql_to_excel
{
 var $sql,$title,$excel_sheet; 
 function __construct($sql,$title="",$type="excel")
 {
  $this->set_sql($sql);
  $this->set_title($title);
  $this->set_type($type);
  $now_date = date('m-d-Y H:i'); 
  $result = tep_db_query($this->sql);
  if($this->type=='excel')
  {
   $file_type = "vnd.ms-excel"; 
   $file_ending = "xls"; 
  }
  else
  {
   $file_type = "msword"; 
   $file_ending = "doc"; 
  }
  if($this->type=='excel')
  {
   if($this->title!=="")
    $excel_sheet.="$this->title"."\n\n";
   $sep="\t";
   for($j=0; $j<tep_db_num_fields($result);$j++) 
   { 
    $field_name = tep_db_field_name($result,$j); 
    switch($field_name)
    {
     case 'jobseeker_id':
      $excel_sheet=""; 
     break;
     case 'id':
      $excel_sheet=""; 
     break;
     default :
      $excel_sheet .= "$field_name\t"; 
    }
   }
   $excel_sheet .= "\n"; 
   while($row = tep_db_fetch_row($result)) 
   { 
    $schema_insert = ""; 
    for($j=0; $j<tep_db_num_fields($result);$j++) 
    { 
     if(!isset($row[$j])) 
     { 
      $schema_insert .= "NULL".$sep; 
     } 
     elseif ($row[$j] != "") 
     {
      $field_name = tep_db_field_name($result,$j);
      if($field_name!='jobseeker_id' && $field_name!='id')
		 {
         $schema_insert .= "".str_replace(array("\r\n","\n")," " ,$row[$j])."".$sep; 
      // $schema_insert .= "$row[$j]".$sep; 
		 }
     } 
     else 
     { 
      $schema_insert .= "".$sep; 
     } 
    }
    $schema_insert = str_replace($sep."$", "", $schema_insert); 
    $excel_sheet.=trim($schema_insert); 
    $excel_sheet .= "\n"; 
    //end of each mysql row 
    //creates line to separate data from each MySQL table row 
   } 
  }
  tep_db_free_result($result);
  $file_name=date("YmdHis").'report.xls';
  $excel_file=PATH_TO_MAIN_PHYSICAL_EXCEL.$file_name;
  $handle = fopen($excel_file, "w");
  fwrite($handle, $excel_sheet);
  fclose($handle);
  header('Content-type: application/x-octet-stream');
  header('Content-disposition: attachment; filename=' . $file_name);
  readfile($excel_file);
  unlink($excel_file);
  exit;
 }
 /////////////////////
 function set_sql($sql) 
 {
  $this->sql = $sql;
 }
 function set_title($title) 
 {
  $this->title = $title;
 }
 function set_type($type) 
 {
  $this->type = $type;
 }
}