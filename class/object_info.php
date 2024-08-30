<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class objectInfo 
{
	// class constructor
	function __construct($object_array) 
	{
		reset($object_array);
		foreach($object_array as $key=> $value)
		//while (list($key, $value) = each($object_array)) 
		{
			$this->$key = $value;
		}
	}
}
?>