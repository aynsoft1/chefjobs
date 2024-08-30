<?
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik #********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
///*
class block_ip_address
{
 function __construct()
 {
  global $messageStack;
		$query = "select * from ".IPADDRESS_BLOCK_TABLE;
		$result=tep_db_query($query);
		//echo "<br>$query";//exit;
		$x=tep_db_num_rows($result);
		//echo $x;exit;
		if($x > 0)
		{
			while($row = tep_db_fetch_array($result))
			{
    if(tep_not_null($row['ip_address2']))
    {
     if($this->lock(tep_get_ip_address(),$row['ip_address1'],$row['ip_address2']))
      die('Error : Sorry, your IP-address is blocked for security reasons.');
    }
    else
    {
     if(tep_get_ip_address()==$row['ip_address1'])
     {
      die('Error : Sorry, your IP-address is blocked for security reasons.');
     }
    }
   }
  }
  tep_db_free_result($result);
 }
 function lock($ip,$ip1,$ip2)
 {
  $lip1=sprintf("%u",ip2long($ip1));
  $lip=sprintf("%u",ip2long($ip));
  $lip2=sprintf("%u",ip2long($ip2));
  //echo $lip1."<br/>".$lip."<br/>".$lip2."<br/>";
  if($lip>=$lip1 && $lip<=$lip2) 
   return true;
  else 
   return false;
 }
}
?>