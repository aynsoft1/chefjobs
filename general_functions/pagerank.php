<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2008  #**********
***********************************************************
*/
//global variable 
ini_set('max_execution_time','0');

$alexa_backlink=0; 
$alexa_reach=0; 

//--> for google pagerank 
function StrToNum($Str, $Check, $Magic) 
{ 
 $Int32Unit = 4294967296;  // 2^32 

 $length = strlen($Str); 
 for ($i = 0; $i < $length; $i++) { 
     $Check *= $Magic;      
     //If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31), 
     //  the result of converting to integer is undefined 
     //  refer to http://www.php.net/manual/en/language.types.integer.php 
     if ($Check >= $Int32Unit) { 
         $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit)); 
         //if the check less than -2^31 
         $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check; 
     } 
     $Check += ord($Str{$i}); 
 } 
 return $Check; 
} 

//--> for google pagerank 
/* 
* Genearate a hash for a url 
*/ 
function HashURL($String) 
{ 
 $Check1 = StrToNum($String, 0x1505, 0x21); 
 $Check2 = StrToNum($String, 0, 0x1003F); 

 $Check1 >>= 2;      
 $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F); 
 $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF); 
 $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);    
 
 $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F ); 
 $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 ); 
 return ($T1 | $T2); 
} 

//--> for google pagerank 
/* 
* genearate a checksum for the hash string 
*/ 
function CheckHash($Hashnum) 
{ 
  $CheckByte = 0; 
  $Flag = 0; 

  $HashStr = sprintf('%u', $Hashnum) ; 
  $length = strlen($HashStr); 
  
  for ($i = $length - 1;  $i >= 0;  $i --) { 
      $Re = $HashStr{$i}; 
      if (1 === ($Flag % 2)) {              
          $Re += $Re;      
          $Re = (int)($Re / 10) + ($Re % 10); 
      } 
      $CheckByte += $Re; 
      $Flag ++;    
  } 

  $CheckByte %= 10; 
  if (0 !== $CheckByte) { 
      $CheckByte = 10 - $CheckByte; 
      if (1 === ($Flag % 2) ) { 
          if (1 === ($CheckByte % 2)) { 
              $CheckByte += 9; 
          } 
          $CheckByte >>= 1; 
      } 
  } 

  return '7'.$CheckByte.$HashStr; 
} 

//get google pagerank 
function getpagerank($url) { 
    $query="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".CheckHash(HashURL($url)). "&features=Rank&q=info:".$url."&num=100&filter=0"; 
    $data=file_get_contents_curl($query); 
    //print_r($data); 
    $pos = strpos($data, "Rank_"); 
    if($pos === false){} else{ 
        $pagerank = substr($data, $pos + 9); 
        return $pagerank; 
    } 
} 

//get alexa popularity 
function get_alexa_popularity($url) 
{    
global $alexa_backlink, $alexa_reach; 
    $alexaxml = "http://xml.alexa.com/data?cli=10&dat=nsa&url=".$url; 
    
    $xml_parser = xml_parser_create(); 
    /* 
    $fp = fopen($alexaxml, "r") or die("Error: Reading XML data."); 
    $data = ""; 
    while (!feof($fp)) { 
        $data .= fread($fp, 8192); 
        //echo "masuk while<br />"; 
    } 
    fclose($fp); 
    */ 
    $data=file_get_contents_curl($alexaxml); 
    xml_parse_into_struct($xml_parser, $data, $vals, $index); 
    xml_parser_free($xml_parser); 
    
    //print_r($vals); 
    //echo "<br />"; 
    //print_r($index); 
    
    $index_popularity = $index['POPULARITY'][0]; 
    $index_reach = $index['REACH'][0]; 
    $index_linksin = $index['LINKSIN'][0]; 
    //echo $index_popularity."<br />"; 
    //print_r($vals[$index_popularity]); 
    $alexarank = $vals[$index_popularity]['attributes']['TEXT']; 
    $alexa_backlink = $vals[$index_linksin]['attributes']['NUM']; 
    $alexa_reach = $vals[$index_reach]['attributes']['RANK']; 
    
    return $alexarank; 
} 

//get alexa backlink 
function alexa_backlink($url) 
{ 
    global $alexa_backlink; 
    if ($alexa_backlink!=0) 
    { 
        return $alexa_backlink; 
    } else { 
        $rank=get_alexa_popularity($url); 
        return $alexa_backlink; 
    } 
} 

//get alexa reach rank 
function alexa_reach_rank($url) 
{ 
    global $alexa_reach; 
    if ($alexa_reach!=0) 
    { 
        return $alexa_reach; 
    } else { 
        $rank=get_alexa_popularity($url); 
        return $alexa_reach; 
    } 
} 
//get google backlink 
function google_backlink($uri) 
{ 
    $uri = trim(preg_replace('/http:\/\//i', '', $uri));
    $uri = trim(preg_replace('/http/i', '', $uri)); 
    $url = 'http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=link:'.$uri.'&filter=0'; 
    $v = file_get_contents_curl($url); 
    preg_match('/of about \<b\>(.*?)\<\/b\>/si',$v,$r); 
    preg_match('/of \<b\>(.*?)\<\/b\>/si',$v,$s); 
    if ($s[1]!=0) { 
        return $s[1]; 
    } else { 
        return ($r[1]) ? $r[1] : '0'; 
    } 
} 
//get google indexed page 
function google_indexed($uri) 
{ 
    $uri = trim(preg_replace('/http:\/\//i', '', $uri)); 
    $uri = trim(preg_replace('/http/i', '', $uri)); 
    $url = 'http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=site:'.$uri.'&filter=0'; 
    $v = file_get_contents_curl($url); 
    preg_match('/of about \<b\>(.*?)\<\/b\>/si',$v,$r); 
    preg_match('/of \<b\>(.*?)\<\/b\>/si',$v,$s); 
    if ($s[1]!=0) { 
        return $s[1]; 
    } else { 
        return ($r[1]) ? $r[1] : '0'; 
    } 
} 
function file_get_contents_curl($url) { 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser. 
    curl_setopt($ch, CURLOPT_URL, $url); 
    $data = curl_exec($ch); 
    curl_close($ch); 

    return $data; 
} 
/*/
$url='ejobsitesoftware.com';//$_REQUEST['url']; 
echo "Google pagerank = ".getpagerank($url)."<br />"; 
echo "Alexa popularity = ".get_alexa_popularity($url)."<br />"; 
echo "Alexa backlink = ".alexa_backlink($url)."<br />"; 
echo "Google backlink = ".google_backlink($url)."<br />"; 
echo "Google indexed = ".google_indexed($url)."<br />"; 
echo "Googlebot last access = ".googlebot_lastaccess($url)."<br />"; 
//*/
?>