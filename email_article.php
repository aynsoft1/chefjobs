<?php
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_EMAIL_ARTICLE);
$template->set_filenames(array('email_article' => 'email_article.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'email_article.js';
 $action = (isset($_POST['action']) ? $_POST['action'] : '');
 //print_r($_POST);
 //	print_r($_GET);
if(tep_not_null($_POST['article_id']))
{
 $article_id=(isset($_POST['article_id']) ? $_POST['article_id'] : '');
 $aid=encode_string("article_id@@".$article_id."@@article_id");
}
if(tep_not_null($_GET['article_id']))
{
 $aid=(isset($_GET['article_id']) ? $_GET['article_id'] : '');
 $article_id=check_data($aid,"@@","article_id","article_id");
}
if(tep_not_null($article_id))
{
 if($art_row=getAnyTableWhereData(ARTICLE_TABLE,"id='".tep_db_input($article_id)."'","title,seo_name"))
 {
  $seo_name = $art_row["seo_name"];
  $art_link=getPermalink('article',array('ide'=> $ide,'seo_name'=>$seo_name));
  $subject="<a href='".$art_link."'> ".INFO_TEXT_CLICK_HERE." </a>";
 }
 else
 {
  tep_redirect(tep_href_link(FILENAME_ARTICLE));
 }
}
else
{
 tep_redirect(tep_href_link(FILENAME_ARTICLE));
}
if($action=='send')
{
 $from_email_name=stripslashes($_POST['TR_your_full_name']);
 $from_email_address=stripslashes($_POST['TREF_your_email_address']);
 $to_name=stripslashes($_POST['TR_your_friend_full_name']);
 $to_email_address=stripslashes($_POST['TREF_your_friend_email_address']);
 $error = false;
 $email_text='<div style="font: normal 12px/17px Verdana, Arial, Helvetica, sans-serif;">'.INFO_TEXT_HI.' <b>'.$to_name.',</b>';
 $email_text.='<br>&nbsp;'.INFO_TEXT_YOUR_FRIEND.' <b>'.$from_email_name.'</b>  '.INFO_TEXT_INTERESTING_ARTICLE;
 $email_text.='<br>&nbsp; '.INFO_TEXT_EMAIL_ADDRESS.' : <b>'.$from_email_address.' </b>.';
 $email_text.='<br>&nbsp; '.INFO_TEXT_MESSAGE_AS.'<br><br><b>'.INFO_TEXT_ARTICLE.' :</b>'.stripslashes($art_row["title"]);
 $email_text.='<br>Please '.$subject.'to read this Article.';
 $email_text.='</div>';
 $email_subject=INFO_TEXT_ARTICLE_FROM.' '.SITE_TITLE;
 if(!tep_not_null($from_email_name))
 {
  $messageStack->add(YOUR_NAME_ERROR, 'error');
  $error = true;
 }
 if(!tep_not_null($from_email_address))
 {
  $messageStack->add(YOUR_EMAIL_ADDRESS_ERROR, 'error');
  $error = true;
 }
 if(!tep_not_null($to_name))
 {
  $messageStack->add(YOUR_FRIEND_NAME_ERROR, 'error');
  $error = true;
 }
 if(!tep_not_null($to_email_address))
 {
  $messageStack->add(YOUR_FRIEND_EMAIL_ADDRESS_ERROR, 'error');
  $error = true;
 }
 //echo $email_text;	die();
 if(!$error)
 {
	 tep_mail($to_name, $to_email_address, $email_subject, $email_text, SITE_TITLE,EMAIL_FROM);
  $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
  //tep_redirect(FILENAME_EMAIL_ARTICLE.'?article_id='.$aid);
  tep_redirect($art_link);
 }
}
$as_string=(strlen($art_row['title'])>200)?substr($art_row['title'],0,197)."...":$art_row['title'];
////// List of Categories start///////
$article_categories='';
$categories=tep_db_query("select * from " .ARTICLE_CATEGORY_TABLE ." where sub_cat_id='' ORDER BY ".TEXT_LANGUAGE."category_name");
$article_categories.='<table border="0" width="100%" cellpadding="0" cellspacing="0">';
while($category = tep_db_fetch_array($categories))
{
  $article_categories.="<tr  class='dataTableRow2' onmouseover='rowOverEffect(this)' onmouseout='rowOutEffect(this)'><td>
  <a class='list-group-item list-group-item-action' href='".getPermalink('article_category',array('ide'=>$category['id'])) ."' title='".$category[TEXT_LANGUAGE.'category_name']."' class='style3'><i class='fa fa-caret-right' aria-hidden='true'></i> ".ucfirst($category[TEXT_LANGUAGE."category_name"])."</a></td></tr>
    <!--<tr ><td bgcolor='#ffffff' background='img/dotted_line.gif'><img src='img/dotted_line.gif' height='1' width='4'/></td></tr>-->
    ";
	$query_sub_category="select * from ".ARTICLE_CATEGORY_TABLE."  where sub_cat_id='".$category['id']."' ";
	$sub_category_result=tep_db_query($query_sub_category);
	$rows=tep_db_num_rows($sub_category_result);
	$article_categories.='<tr bgcolor="#FFFFFF">
	                       <td>
																									<table border="0" width="100%" align="right" cellpadding="0" cellspacing="0">
																									 ';
	$row=1;
	while($sub_cate=tep_db_fetch_array($sub_category_result))
	{
		$article_categories.="<tr class='dataTableRow2' onmouseover='rowOverEffect(this)' onmouseout='rowOutEffect(this)'><td align='left'> <i class='fa fa-caret-right' aria-hidden='true'></i> <a href='".getPermalink('article_category',array('ide'=>$sub_cate['id'])) ."' title='".$sub_cate[TEXT_LANGUAGE.'category_name']."' class=\"style3\">".ucfirst($sub_cate[TEXT_LANGUAGE.'category_name'])."</a></td></tr><tr ><td bgcolor='#ffffff' background='img/dotted_line.gif'><img src='img/dotted_line.gif' height='1' width='4'/></td></tr>";
		$query_sub_sub_category="select * from ".ARTICLE_CATEGORY_TABLE."  where sub_cat_id='".$sub_cate['id']."' ";
		$sub_sub_category_result=tep_db_query($query_sub_sub_category);
		$rows=tep_db_num_rows($sub_sub_category_result);
		$article_categories.='<tr bgcolor="#FFFFFF">
																									<td>
																										<table border="0" width="100%" align="right" cellpadding="0" cellspacing="0">
																											';
		$row=1;
	while($sub_sub_cate=tep_db_fetch_array($sub_sub_category_result))
	{
		$article_categories.="



		<tr class='dataTableRow2' onmouseover='rowOverEffect(this)' onmouseout='rowOutEffect(this)'>
		<td align='left'> <i class='fa fa-caret-right' aria-hidden='true'></i> <a href='".getPermalink('article_category',array('ide'=>$sub_sub_cate['id'])) ."' title='".$sub_sub_cate[TEXT_LANGUAGE.'category_name']."' class=\"style3\">".ucfirst($sub_sub_cate[TEXT_LANGUAGE.'category_name'])."</a></td></tr><tr><td bgcolor='#ffffff' background='img/dotted_line.gif'><img src='img/dotted_line.gif' height='1' width='4'/></td></tr>";
  $row++;
	}
 $article_categories.="</table></td></tr>";
	}
 $article_categories.="</table></td>";
}
$article_categories.="</tr></table>";
///////List of categories End //////
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'tabletext'=>$article_categories,
 'INFO_TEXT_NEWSLETTER'=> '<a href="'.tep_href_link(FILENAME_ARTICLE).'" class="article_home">'.INFO_TEXT_NEWSLETTER.'</a>',
 'INFO_TEXT_FROM_NAME' => INFO_TEXT_FROM_NAME,
 'INFO_TEXT_FROM_NAME1'=> tep_draw_input_field('TR_your_full_name', $from_email_name,'size="40" class="form-control"',true),
 'INFO_TEXT_FROM_EMAIL_ADDRESS'=>INFO_TEXT_FROM_EMAIL_ADDRESS,
 'INFO_TEXT_FROM_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_your_email_address', $from_email_address,'size="40" class="form-control"',true),
 'INFO_TEXT_TO_NAME'  => INFO_TEXT_TO_NAME,
 'INFO_TEXT_TO_NAME1' => tep_draw_input_field('TR_your_friend_full_name', $to_name,'size="40" class="form-control"',true),
 'INFO_TEXT_TO_EMAIL_ADDRESS'=>INFO_TEXT_TO_EMAIL_ADDRESS,
 'INFO_TEXT_TO_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_your_friend_email_address', $to_email_address,'size="40" class="form-control"',true),
 'INFO_TEXT_SUBJECT'  => INFO_TEXT_SUBJECT,
 'INFO_TEXT_CATEGORY' => INFO_TEXT_CATEGORY,
 'INFO_TEXT_SUBJECT1' => $as_string,
 'title'              => $title,
 'form'               => tep_draw_form('send', FILENAME_EMAIL_ARTICLE."?article_id=".$aid,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send'),

 'button'             => tep_button_submit('btn btn-primary', IMAGE_SEND),

 'button_search'      => tep_button_submit('btn btn-primary', IMAGE_SEARCH),
 'INFO_TEXT_JSCRIPT_FILE' => '<script src="'.$jscript_file.'"></script>',
 'LEFT_BOX_WIDTH'     => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'    => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'          => LEFT_HTML,
 'RIGHT_HTML'         => RIGHT_HTML,
 'update_message'     => $messageStack->output()));
$template->pparse('email_article');
?>