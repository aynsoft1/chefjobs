<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class sort_by_clause
{
 var $return_value;
 var $return_sort_array=array();
 function __construct($sort_array,$default_value='inserted desc',$default_name='sort')
 {
  $temp_array=array();
  $return_value=$default_value;
  $size=sizeof($sort_array);
  if($size > 0)
  {
   for($i=1;$i<=$size;$i++)
   {
    for($j=0;$j<2;$j++)
    {
     $temp_array[]=($i).($j=="0"?"a":"b");
    }
   }
   if(in_array($_POST[$default_name],$temp_array))
   {
    $sort=$_POST[$default_name];
   }
   else if(in_array($_GET[$default_name],$temp_array))
   {
    $sort=$_GET[$default_name];
   }
   else
    $sort='';
   for($i=1;$i<=$size;$i++)
   {
    if($sort==$i."a")
    {
     for($k=1;$k<=$size;$k++)
     {
      if($k==$i)
      {
       $this->return_sort_array["name"][]=$k."b";
       $this->return_sort_array["image"][]=tep_image(PATH_TO_IMAGE.'asc.png', "Asc",'','','valign="middle"');
       $return_value=$sort_array[($i-1)]." asc";
      }
      else
      {
       $this->return_sort_array["name"][]=$k."a";
       $this->return_sort_array["image"][]='';
      }
     }
     break;
    }
    else if($sort==$i."b")
    {
     for($k=1;$k<=$size;$k++)
     {
      if($k==$i)
      {
       $this->return_sort_array["name"][]=$k."a";
       $return_value=$sort_array[($i-1)]." desc";
       $this->return_sort_array["image"][]=tep_image(PATH_TO_IMAGE.'desc.png', "Desc",'','','valign="middle"');
      }
      else
      {
       $this->return_sort_array["name"][]=$k."a";
       $this->return_sort_array["image"][]='';
      }
     }
     break;
    }
    else if($sort=='')
    {
     for($k=1;$k<=$size;$k++)
     {
      $this->return_sort_array["name"][]=$k."a";
     }
     break;
    }
   }
   $this->return_value=$return_value;
  }
  else
  {
   die();
  }
 }
}
?>