<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/theme_functions.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_PAGE_EDITOR);
$template->set_filenames(array('page' =>'admin1_page_editor.htm','page1' =>'admin1_page_editor1.htm'));
include_once(FILENAME_ADMIN_BODY);
$action =tep_db_prepare_input($_GET['action']);
function get_page_content($file_name)
{
 $file_name = $file_name;
 $page_contents='';
 if(!is_file($file_name))
  return false;
 if($handle = @fopen($file_name, "r"))
 {
  $contents = @fread($handle, filesize($file_name));
  fclose($handle);
  $page_contents=explode('<!--START_FILE_EDIT_CONTENT-->',$contents);
  $page_contents=$page_contents[1];
  $page_contents=explode('<!--END_FILE_EDIT_CONTENT-->',$page_contents);
  $page_contents=$page_contents[0];    
 }
 return $page_contents;
}
function write_page_content($page_name,$content='')
{
 $file=$page_name;
 $write_content ='';
 if($handle = @fopen($file, "r"))
 {
  $contents = @fread($handle, filesize($file));
  fclose($handle);  
  $page_contents=explode('<!--START_FILE_EDIT_CONTENT-->',$contents);
  $write_content_top    = $page_contents[0];
  $page_contents    = $page_contents[1];
  if(!preg_match('#<!--START_FILE_EDIT_CONTENT-->#i',$write_content_top, $match))
  {
   $write_content_top=$write_content_top."\n".'<!--START_FILE_EDIT_CONTENT-->';
  }
  $page_contents1=explode('<!--END_FILE_EDIT_CONTENT-->',$page_contents);
  $write_content_bottom=$page_contents1[1];
  $write_content_bottom;
  if(!preg_match('#<!--END_FILE_EDIT_CONTENT-->#i',$page_contents, $match))
  {
   $write_content_bottom=$page_contents1[0];
  }
  $write_content_bottom='<!--END_FILE_EDIT_CONTENT-->'."\n".$write_content_bottom;

  $write_content= trim($write_content_top)."\n".trim(($content))."\n".trim($write_content_bottom);
  $write_content=str_replace("\n\n","\n",$write_content);  
  if($handle = @fopen($file, "w"))
  {
   @fwrite($handle, stripslashes($write_content));
   fclose($handle);  
   return true;
  }
  return false;
 }
}
if(tep_not_null($action))
{
 switch($action)
 {
  case'save':
    $page_id =tep_db_prepare_input($_GET['page_id']);
    $file_contents =($_POST['file_contents']);
    if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.$page_id))
    {
     $page_path=PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.$page_id;
     if($file_contents=write_page_content($page_path,$file_contents))
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    }
    tep_redirect(FILENAME_ADMIN1_PAGE_EDITOR);
   break;
  case'edit':
    $page_id =tep_db_prepare_input($_GET['page_id']);
    if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.$page_id))
    {
     $page_path=PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.$page_id;
     if (!is_writeable($page_path)) 
     {
      $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE,$page_path ), 'error'); 
     }
     $file_contents=get_page_content($page_path);
    }
   break;
 }

 if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/stylesheet.css'))
  $css_file=tep_href_link(PATH_TO_THEMES.MODULE_THEME_DEFAULT_THEME.'/stylesheet.css');

 $template->assign_vars(array(
  'HEADING_TITLE'          => HEADING_TITLE,
  'css_file'               => $css_file,
  'form'                   => tep_draw_form('file', PATH_TO_ADMIN.FILENAME_ADMIN1_PAGE_EDITOR, 'page_id='.$page_id.'&action=save'),
  'INFO_TEXT_FILE_CONTENT' => tep_draw_textarea_field('file_contents', 'soft', '120', '25', $file_contents,'id="file_contents"'),
  'HOST_NAME'           => HOST_NAME,
  


  'INFO_TEXT_BUTTON'    => tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"'),
  'update_message'=>$messageStack->output()));
 $template->pparse('page1');

}
else
{
 $page_list=array();
 $page_list[]=array('page_id'=>'about_us.htm','page_source'=>'about_us.php','page_name'=>'About Us');
 $page_list[]=array('page_id'=>'advertise.htm','page_source'=>'advertise.php','page_name'=>'Advertise');
 $page_list[]=array('page_id'=>'faq.htm','page_source'=>'faq.php','page_name'=>'FAQ');
 $page_list[]=array('page_id'=>'privacy.htm','page_source'=>'privacy.php','page_name'=>'Privacy Statements');
 $page_list[]=array('page_id'=>'services.htm','page_source'=>'services.php','page_name'=>'Services');
 $page_list[]=array('page_id'=>'terms.htm','page_source'=>'terms.php','page_name'=>'Terms & Conditions');
 $i=0;
 foreach($page_list as $page)
 {
  $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';

  $template->assign_block_vars('pages', array('row_selected'  => $row_selected,
                                              'page_name' => '<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_PAGE_EDITOR,'page_id='.$page['page_id'].'&action=edit').'" >'.tep_db_output($page['page_name']).'</a>',
                                              'page_edit' => '<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_PAGE_EDITOR,'page_id='.$page['page_id'].'&action=edit').'" >Edit</a>',
                                              'page_view' => '<a href="'.tep_href_link($page['page_source']).'" target="_left">View</a>',
                                              )); 
  $i++;
 }
 $template->assign_vars(array(
  'HEADING_TITLE'                => HEADING_TITLE,
  'TABLE_HEADING_PAGE_NAME' => TABLE_HEADING_PAGE_NAME,
  'TABLE_HEADING_PAGE_EDIT' => TABLE_HEADING_PAGE_EDIT,
  'TABLE_HEADING_PAGE_VIEW' => TABLE_HEADING_PAGE_VIEW,
  'update_message'=>$messageStack->output()));
 $template->pparse('page');
}
?>