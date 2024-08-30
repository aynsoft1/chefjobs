<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Date Created  : 07/09/2005          #**********
**********# Date Modified : 07/09/2005          #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
class gift
{
 var $gift_text_array=array();
 function __construct()
 {
  if($this->check_status())
  {
   $this->gift_text_array['text']="If you have been given a promotion code please enter it in the box below : ";
   $this->gift_text_array['field']=tep_draw_input_field('gift_code',$_SESSION['gift_code'],'maxlength="32"');
  }
  return $this->gift_text_array;
 } 
 function check_status($gift_code='',$user='recruiter')
 { 
  if(tep_not_null($gift_code))
  {
   if($row=getAnyTableWhereData(GIFT_TABLE,'user="'.tep_db_input($user).'" and certificate_number="'.tep_db_input($gift_code).'" and expired>="'.date("Y-m-d").'"',"*"))
   {
    return new objectInfo($row);
   }
   else
    return false;
  }   
  else if(no_of_records(GIFT_TABLE,"user='".tep_db_input($user)."'  and   expired>='".date("Y-m-d")."'","*")>0)
  {
	     $this->gift_text_array['text']="If you have been given a promotion code please enter it in the box below : ";
   $this->gift_text_array['field']=tep_draw_input_field('gift_code',$_SESSION['gift_code'],'maxlength="32"');

   return true;
  }
  else
   return false;
 }
}
?>