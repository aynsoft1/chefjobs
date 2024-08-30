<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/


class decoding
{
 var $explode_string;
 var $id;
 function __construct($query_string)
 {
  $this->explode_string=explode("=",decode_string($query_string));
 }
 function check_row($left_string,$right_string,$table_name,$field_name,$and_clause="",$fields)
 {
  if($this->explode_string[0]==$left_string && $this->explode_string[2]==$right_string)
  {
   if($row=getAnyTableWhereData($table_name,"$field_name='".tep_db_input($this->explode_string[1])."'".$and_clause,$fields))
   {
    $this->id=$this->explode_string[1];
    return true;
   }
   else
   {
    return false;
   }
  }
  else
  {
   return false;
  }
 }
}
?>