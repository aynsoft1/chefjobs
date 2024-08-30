<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
function startElement($parser, $name, $attrs) {
   global $obj;
   
   // If var already defined, make array
   eval('$test=isset('.$obj->tree.'->'.$name.');');
   if ($test) {
     eval('$tmp='.$obj->tree.'->'.$name.';');
     eval('$arr=is_array('.$obj->tree.'->'.$name.');');
     if (!$arr) {
       eval('unset('.$obj->tree.'->'.$name.');');
       eval($obj->tree.'->'.$name.'[0]=$tmp;');
       $cnt = 1;
     }
     else {
       eval('$cnt=count('.$obj->tree.'->'.$name.');');
     }
     
     $obj->tree .= '->'.$name."[$cnt]";
   }
   else {
     $obj->tree .= '->'.$name;
   }
   if (count($attrs)) {
    eval($obj->tree.'->attr=$attrs;');
   }
}
function endElement($parser, $name) {
   global $obj;
   // Strip off last ->
   for($a=strlen($obj->tree);$a>0;$a--) {
    if (substr($obj->tree, $a, 2) == '->') {
    $obj->tree = substr($obj->tree, 0, $a);
    break;
    }
   }
}
function characterData($parser, $data) {
   global $obj;
   eval($obj->tree.'->data.=\''.trim(addslashes($data)).'\';');
}
function read_xml($filename)
{
   global $obj;
   $obj='';
$obj->tree = '$obj->xml';
$obj->xml = '';
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "characterData");
if (!($fp = fopen($filename, "r"))) {
   die("could not open XML input");
}
while ($data = fread($fp, 4096)) {
   if (!xml_parse($xml_parser, $data, feof($fp))) {
       die(sprintf("XML error: %s at line %d",
                   xml_error_string(xml_get_error_code($xml_parser)),
                   xml_get_current_line_number($xml_parser)));
   }
}
xml_parser_free($xml_parser);
//print_r($obj->xml->RSS->CHANNEL->ITEM);
//print_r($obj->xml->RSS->RESULTS->RESULT);

//print_r($obj->xml->RESPONSE->RESULTS->RESULT);die();
$total_record=count($obj->xml->RESPONSE->RESULTS->RESULT);
//echo $total_record;
$arrow_image='&nbsp;<font color="#FF0000">&gt;&gt;</font>&nbsp;';
$string=array();
if($total_record>1)
{
 for($i=0;$i<$total_record;$i++)
 {
  $string[]='<table border="0" width="70%" cellspacing="0" cellpadding="0"><tr><td width="10" valign="top"><b>&#8226;</b></td><td style="font-size:12px;"valign="top"><a href="'.$obj->xml->RESPONSE->RESULTS->RESULT[$i]->URL->data.'" style="color:#0000ff" target="_blank"><b>'.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->JOBTITLE->data).'</b></a><b><br>'.
            tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->COMPANY->data).
            (tep_not_null($obj->xml->RESPONSE->RESULTS->RESULT[$i]->CITY->data)?'<br>'.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->CITY->data):'').
            (tep_not_null($obj->xml->RESPONSE->RESULTS->RESULT[$i]->STATE->data)?','.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->STATE->data):'').
            ','.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->COUNTRY->data).
            '</b><br><span style="font-size:11px;">'.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->SNIPPET->data).'</span>'.
           '</td></tr></table>';
 }
}
else if($total_record==1)
{
 $string[]='<table border="0"  width="70%" cellspacing="0" cellpadding="0"><tr><td width="10"  valign="top"><b>&#8226;</b></td><td style="font-size:12px;"valign="top"><a href="'.$obj->xml->RESPONSE->RESULTS->RESULT[$i]->URL->data.'" style="color:#0000ff" target="_blank"><b>'.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->JOBTITLE->data).'</b></a><b><br>'.
            tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->COMPANY->data).
            (tep_not_null($obj->xml->RESPONSE->RESULTS->RESULT[$i]->CITY->data)?'<br>'.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->CITY->data):'').
            (tep_not_null($obj->xml->RESPONSE->RESULTS->RESULT[$i]->STATE->data)?','.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->STATE->data):'').
            ','.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->COUNTRY->data).
            '</b><br><span style="font-size:11px;">'.tep_db_output($obj->xml->RESPONSE->RESULTS->RESULT[$i]->SNIPPET->data).'</span>'.
           '</td></tr></table>';
}
else
{
 $string[]='<table border="0" cellspacing="0" cellpadding="0"><tr><td width="10"  valign="top"><b>&#8226;</b></td><td valign="top">Sorry no job available.</td></tr></table>';
}
if(count($string>0))
{
 $string=implode("<br>",$string);
}
else
 $string='';
return $string;
}
?>