<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
/*
Example usage:
$messageStack = new messageStack();
$messageStack->add('Error: Error 1', 'error');
$messageStack->add('Error: Error 2', 'warning');
if ($messageStack->size > 0) echo $messageStack->output();
*/
class messageStack extends tableBlock 
{
	var $size = 0;
	var $errors = array();
	function __construct() 
	{
		$this->errors = array();
		if (isset($_SESSION['messageToStack'])) 
		{
   for($i=0;$i<sizeof($_SESSION['messageToStack']);$i++)
   {
 			$this->add($_SESSION['messageToStack'][$i]['text'], $_SESSION['messageToStack'][$i]['type']);
   }
			unset($_SESSION['messageToStack']);
		}
	}
	function add($message, $type = 'error') 
	{
		if ($type == 'error') 
		{
			// $this->errors[] = array('params' => 'class="messageStackError"', 'text' => '<span class="error_warning_success">'.tep_image(PATH_TO_IMAGE.'error.gif') . '&nbsp;'.$message."</span>");
			$this->errors[] = array(
				'params' => 'class="messageStackError"', 
				'text' => '
				<div class="alert small alert-danger alert-dismissible fade show" id="myAlert" role="alert">
				'.tep_image(PATH_TO_IMAGE.'error.gif') . '
				'.$message."
				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
				</button></div>");
		} 
		elseif ($type == 'warning') 
		{
			$this->errors[] = array(
				'params' => 'class="messageStackWarning"', 
				'text' => '
				<div class="alert small alert-warning alert-dismissible fade show" id="myAlert" role="alert">
				'.tep_image(PATH_TO_IMAGE.'warning.gif') . ''.$message."
				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
				</button></div>");
		} 
		elseif ($type == 'success') 
		{
			$this->errors[] = array(
				'params' => 'class="messageStackSuccess"', 
				'text' => '
				<div class="alert small alert-success alert-dismissible fade show" id="myAlert" role="alert">
				'.tep_image(PATH_TO_IMAGE.'success.gif') .$message."
				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
				</button></div>");
		} 
		else 
		{
			$this->errors[] = array(
				'params' => 'class="messageStackError"', 
				'text' => '
				<div class="alert small alert-warning alert-dismissible fade show" id="myAlert" role="alert">
				'.tep_image(PATH_TO_IMAGE.'success.gif') . ''.$message."
				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
				</button></div>");
		}
		$this->size++;
	}
	function add_session($message, $type = 'error') 
	{
		$_SESSION['messageToStack'][] = array('text' => $message, 'type' => $type);
	}
	function reset() 
	{
		$this->errors = array();
		$this->size = 0;
	}
	function output() 
	{
		$this->table_data_parameters = 'class="messageBox"';
		return $this->tableBlock($this->errors);
	}
 function size($class) 
 {
  $count = 0;
  if(!empty($this->messages))
  for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) 
  {
   if ($this->messages[$i]['class'] == $class) 
   {
    $count++;
   }
  }
  return $count;
 }
}
?>