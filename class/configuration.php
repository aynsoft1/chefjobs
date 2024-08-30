<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class configuration
{
 function __construct()
	{
		$query = "select configuration_name,configuration_value from ".CONFIGURATION_TABLE;
		$result=tep_db_query($query);
		//echo "<br>$query";//exit;
		$x=tep_db_num_rows($result);
		//echo $x;exit;
		if($x > 0)
		{
			while($row = tep_db_fetch_array($result))
			{
				define($row['configuration_name'], $row['configuration_value']);
			}
		}
	}
}
?>