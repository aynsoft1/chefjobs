<?php
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
/*
Example usage:

$heading = array();
$heading[] = array('params' => 'class="menuBoxHeading"',
																'text'  => BOX_HEADING_TOOLS,
																'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=tools'));

$contents = array();
$contents[] = array('text'  => SOME_TEXT);

$box = new box;
echo $box->infoBox($heading, $contents);
*/

class left_box extends tableBlockLeft
{
	function __construct()
	{
		$this->heading = array();
		$this->contents = array();
	}
	function menuBox($heading, $contents)
	{
		$this->table_row_parameters = 'class="menuBoxHeading"';
		$this->table_data_parameters = '';
  $text_image='../img/red_rec.gif';
  if($heading[0]['text_image']!='')
  {
   $text_image=$heading[0]['text_image'];
  }
  if(($heading[0]['default_row'])==1)
  {
  	$this->table_row_parameters = 'class="menuBoxHeading1ddd"';
		 $this->table_data_parameters = '';
  }

		if (isset($heading[0]['link']))
		{
			$this->table_row_parameters .= ' onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . $heading[0]['link'] . '\'"';
			$heading[0]['text'] = '<div style="padding:8px 0;line-height: 18px;font-size: 16px;">'.$text_image.'<a href="' . $heading[0]['link'] . '" '.((($heading[0]['default_row'])==1)?' class="menuBoxHeadingLink1"':'class="menuBoxHeadingLink" ').' >' . $heading[0]['text'] . '</a></div>';		}
		else
		{
			$this->table_row_parameters .= ' onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
			$heading[0]['text'] = '&nbsp;<img src="../img/red_rec.gif">&nbsp;&nbsp;'.$heading[0]['text'];
		}
		$this->heading = $this->tableBlockLeft($heading);
		$this->table_row_parameters = '';
		$this->table_data_parameters = 'class="menuBoxContent ps-3" style="padding-top:2px;padding-bottom:2px;"';
  $this->table_parameters= 'class="left_contents bg-sub-nav"';
  $this->table_cellspacing ='0';
  $this->table_cellpadding ='0';
  $this->table_border='0';

		$this->contents = $this->tableBlockLeft($contents);
		return $this->heading . $this->contents;
	}
}
class right_box extends tableBlockRight
{
	function __construct()
	{
		$this->heading = array();
		$this->contents = array();
	}
	function right_box()
	{
		$this->heading = array();
		$this->contents = array();
	}
	function infoBox($heading, $contents)
	{
		$this->table_row_parameters = 'class="infoBoxHeading"';
		$this->table_data_parameters = 'class="infoBoxHeading"';
		$this->heading = $this->tableBlockRight($heading);
		$this->table_row_parameters = '';
		$this->table_data_parameters = 'class="infoBoxContent"';
		$this->contents = $this->tableBlockRight($contents);
		return $this->heading . $this->contents;
	}
}
?>