<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_FORUM_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_FORUM_TOPICS);
$template->set_filenames(array('topics' => 'forum_topics.htm','topics_add' => 'forum_topics_add.htm','topics_preview' => 'forum_topics_preview.htm'));
include_once("../".FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'forum_topics.js';

////////////////
if(tep_not_null($_GET['forum_id']))
{
 $forum_id=tep_db_prepare_input($_GET['forum_id']);
}
if(tep_not_null($_POST['forum_id']))
{
 $forum_id=tep_db_prepare_input($_POST['forum_id']);
}
//die("ok");
//print_r($_POST);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if(isset($_POST['action1']) && tep_not_null($_POST['action1']))
$action=tep_db_prepare_input($_POST['action1']);

if(!check_login('jobseeker') && !check_login('recruiter') && tep_not_null($action))
{
 $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(tep_href_link(FILENAME_LOGIN));
}

if(tep_not_null($forum_id))
{
 if(!$forum_info=getAnyTableWhereData(FORUM_TABLE." as f left outer join ".FORUM_CATEGORIES_TABLE." as c on (f.category_id=c.id) ","f.id='".tep_db_input($forum_id)."' and is_show ='Yes' and show_date <= curdate() ","f.id,f.title,f.category_id,c.category_name,f.description"))
 {
  $messageStack->add_session(MESSAGE_INVALID_FORUM_ERROR, 'error');
  tep_redirect(tep_href_link(PATH_TO_FORUM.FILENAME_INDEX));
 }
 $forum_id=$forum_info['id'];
}
else
{
 $messageStack->add_session(MESSAGE_INVALID_FORUM_ERROR, 'error');
 tep_redirect(tep_href_link(PATH_TO_FORUM.FILENAME_INDEX));
}
if(tep_not_null($action))
{
 switch($action)
 {
  case 'preview':
    $hidden_fields='';
   	$title             = tep_db_prepare_input($_POST['TR_title']);
    $description       = stripslashes($_POST['TR_description']);    
    $hidden_fields.=tep_draw_hidden_field('forum_id',$forum_id);
   	$hidden_fields.=tep_draw_hidden_field('TR_title',$title);
    $hidden_fields.=tep_draw_hidden_field('TR_description',$description);
	   if(strlen($title)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_TITLE, 'error');
     $error=true;
    }
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
     $error=true;
    }
   break;
  case 'back':
    $title             = tep_db_prepare_input($_POST['TR_title']);
    $description       = stripslashes($_POST['TR_description']);
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
     $error=true;
    }
    if(strlen($title)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_TITLE, 'error');
     $error=true;
    }
   break;
  case 'add':
  case 'save':
	  $_GET['action']='';
   $title             = tep_db_prepare_input($_POST['TR_title']);
   $description       = stripslashes($_POST['TR_description']);
   if(strlen($description)<=0)
   {
    $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
    $error=true;
   }
   if(strlen($title)<=0)
   {
    $messageStack->add(ERROR_ARTICLE_TITLE, 'error');
    $error=true;
   }
   if(!$error)
   {
    $description=($description);//valid_html_link
    if(check_login('jobseeker'))
    {
     $user_id   = $_SESSION['sess_jobseekerid'];
     $user_type = 'jobseeker';
    }
    else
    {
     $user_id   = $_SESSION['sess_recruiterid'];
     $user_type = 'recruiter';
    }
	$description= htmlentities(strip_tags($description,'<br><p><b>'));
    $description =nl2br(autolink($description));
    $sql_data_array=array( 'title'       => $title,
                           'description' => $description,
                           'forum_id'    => $forum_id,
                           'user_id'     => $user_id,
                           'user_type'   => $user_type,
                           'inserted'    => 'now()',
                           );
    tep_db_perform(FORUM_TOPICS_TABLE, $sql_data_array);
    $get_row=getAnyTableWhereData(FORUM_TOPICS_TABLE,"user_id='".tep_db_input($user_id)."' and user_type ='".tep_db_input($user_type)."' and  forum_id='".tep_db_input($forum_id)."' and title ='".tep_db_input($title)."' order by id desc ","id");
    $last_topic_id= $get_row['id'];
				/*
    $sql_data_array1=array('topic_id'         => $last_topic_id,
                           //'title'            => $title,
                           'description'      => $description1,
                           'user_id'          => $user_id,
                           'user_type'        => $user_type,
                           'inserted'         => 'now()',
                           );
  
     tep_db_perform(TOPIC_REPLY_TABLE, $sql_data_array1);
					*/
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     tep_redirect(getPermalink('forum_topics',array('ide'=>$forum_id,'seo_name'=>encode_forum($forum_info['title']))));
   } 
 }
}
////////////////////////////////////////////
if($error)
{
 if($edit)
  $action='edit';
 else
  $action='new';
} 
if($action=='new' || $action=='back')
{
 //$form=tep_draw_form('topic', PATH_TO_FORUM.FILENAME_FORUM_TOPICS, 'action=preview', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('forum_id',$forum_id);//tep_draw_hidden_field('forum_id',$_GET['forum_id']);
 $form=tep_draw_form('topic', PATH_TO_FORUM.FILENAME_FORUM_TOPICS, 'action=add', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('forum_id',$forum_id);//tep_draw_hidden_field('forum_id',$_GET['forum_id']);
}
elseif($action=='preview')
{
 $form=tep_draw_form('topic', PATH_TO_FORUM.FILENAME_FORUM_TOPICS, 'action=add', 'post', '').tep_draw_hidden_field('action1','').tep_draw_hidden_field('forum_id',$forum_id);
 $button='<a href="#" onclick="set_action(\'back\')">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE);
}
else
{
 ///////////// Middle Values 
 if($forum_id!="")
 {
  {
   //print_r($_POST);
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);

   $table_names = FORUM_TOPICS_TABLE." as t left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (t.user_id =jl.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (t.user_id =rl.recruiter_id && t.user_type='recruiter') ";
   $whereClause = " t.forum_id='".tep_db_input($forum_id)."'  and  (jl.jobseeker_status='Yes' or rl.recruiter_status='Yes')"; 
   $topic_query_sort_raw="select count(*) as x1 from ".$table_names." where $whereClause";
   $topic_query_sort_result = tep_db_query($topic_query_sort_raw);
   $sort_row=tep_db_fetch_array($topic_query_sort_result);
   $x1=$sort_row['x1'];
   ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("t.inserted");
   $obj_sort_by_clause=new sort_by_clause($sort_array,'t.inserted desc');
   $order_by_clause=$obj_sort_by_clause->return_value;
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'t.inserted desc ',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_ARTICLES);
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields1=tep_draw_hidden_field('sort',$sort);
  ///only for sorting ends
   $totalpage=ceil($x1/$higher);
   $topic_query_raw="select * from ".$table_names." where ".$whereClause." order by ".$order_by_clause." limit $lower,$higher";
   $topic_query = tep_db_query($topic_query_raw);
   $x=tep_db_num_rows($topic_query);
   //echo $x;exit;
   $pno= ceil($lower+$higher)/($higher);
   if($x > 0 && $x1 > 0)
   {
    $alternate=1;
    while ($topic = tep_db_fetch_array($topic_query)) 
    {
     $ide=$topic["id"];
     $key=((strlen($topic['title'])<150)?$topic['title']:substr($topic['title'],0,148)."..");
     $key1=$topic['title'];
     //   $topic="<a href='".tep_href_link(PATH_TO_FORUM.FILENAME_TOPIC_DETAILS,'topic_id='.$topic['id'])."'  title='".tep_db_output($key1)."' style='color:FFFFFF;font-weight:bold;'>".tep_db_output($key)."</a>";
     $topic_name   = "<a href='".getPermalink('topic_details',array('ide'=>$ide,'seo_name'=>encode_forum($key)))."'  title='".tep_db_output($key1)."' class='1forum_title1 text-dark'>".tep_db_output($key)."</a>";
		   //  'more_link'    => '<a href="'.tep_href_link(PATH_TO_FORUM.encode_forum($key).'_'.$topic['id'].'.html').'" class="forum_more" target="_blank">More >></a>',

     $row_selected='class="forum_row"';//' class="dataTableRow'.($alternate%2==1?'2':'1').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)""';
     if($topic['user_type']=='jobseeker')
     {
      $name    = getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$topic['user_id']."'","jobseeker_first_name,jobseeker_last_name");
      $author=$name['jobseeker_first_name'].' '.$name['jobseeker_last_name'];
     }
     else
     {
      $name    = getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$topic['user_id']."'","recruiter_first_name,recruiter_last_name");
      $author=$name['recruiter_first_name'].' '.$name['recruiter_last_name'];
     }
     $table_names1 = TOPIC_REPLY_TABLE." as t left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (t.user_id =jl.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (t.user_id =rl.recruiter_id && t.user_type='recruiter') ";

     $replies = getAnyTableWhereData($table_names1,"t.topic_id='".$ide."' and  (jl.jobseeker_status='Yes' or rl.recruiter_status='Yes')","count(*) as total");
     $reply_last_query_raw="select inserted,user_type,user_id from ".TOPIC_REPLY_TABLE." where topic_id=".$ide." order by inserted desc limit 1";
     $reply_last_query = tep_db_query($reply_last_query_raw);
     $reply_last = tep_db_fetch_array($reply_last_query);
					if($reply_last['user_type']=='jobseeker')
     {
      $name    = getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$reply_last['user_id']."'","jobseeker_first_name,jobseeker_last_name");
      $last_reply_author=$name['jobseeker_first_name'].' '.$name['jobseeker_last_name'];
     }
     else
     {
      $name    = getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$reply_last['user_id']."'","recruiter_first_name,recruiter_last_name");
      $last_reply_author=$name['recruiter_first_name'].' '.$name['recruiter_last_name'];
     }

     $template->assign_block_vars('topic', array( 'row_selected' => $row_selected,
      'topic'    => $topic_name,
      'description'=> (substr(strip_tags($topic['description'],'<br>'),0,200)).' ',
      'replies'  => $replies['total'],
      'last_post'=> tep_date_long($reply_last['inserted']),
      'author'   => $author,
      'last_reply_author'   => $last_reply_author,
      ));
     $alternate++;
     $lower = $lower + 1;
    }
    $plural=($x1=="1")?"topic":"Topics";
    $template->assign_vars(array('total'=>"Displaying ".((($pno-1)*$higher)+1)." to ".(($pno*$higher)>$x1?$x1:($pno*$higher))." (of $x1 ".$plural.")"));
    tep_db_free_result($topic_query);
    }
   else
   {
    $template->assign_vars(array('total'=>"There is no Topic in this Forum."));
   }
  }
  see_page_number();
 }
}
if($action=='preview') 
{
 $forum_header_link='<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading">'.INFO_TEXT_HOME.'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'.tep_href_link(PATH_TO_FORUM,'category='.$forum_info['category_id']).'" class="forum_sub_heading">'.tep_db_output($forum_info['category_name']).'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'.getPermalink('forum_topics',array('ide'=>$forum_info['id'],'seo_name'=>encode_forum($forum_info['title']))).'" class="forum_sub_heading">'.tep_db_input($forum_info['title']).'</a>';
 $template->assign_vars(array(
 'hidden_fields'         => $hidden_fields,
	'HEADING_TITLE'         =>'<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
 'INFO_TEXT_TITLE'       => INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'      => tep_db_output($title),
 'INFO_TEXT_DESCRIPTION' => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=> nl2br(stripslashes($description)),
 'button'                => $button,
 'form'                  => $form,
 'INFO_TEXT_FORUM_HEADER_LINK' => $forum_header_link,
 'forum_title'   => '<a href="'.getPermalink('forum_topics',array('ide'=>$forum_info['id'],'seo_name'=>encode_forum($forum_info['title']))).'" style="font-family: Arial, Helvetica, sans-serif;font-size: 16px;font-weight: bold;">'.$forum_info['title'].'</a>',
 'update_message'        => $messageStack->output()));
 $template->pparse('topics_preview');
}
elseif($action=='new' || $action=='back') 
{
 $forum_header_link='<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading">'.INFO_TEXT_HOME.'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'.tep_href_link(PATH_TO_FORUM,'category='.$forum_info['category_id']).'" class="forum_sub_heading">'.tep_db_output($forum_info['category_name']).'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'. getPermalink('forum_topics',array('ide'=>$forum_info['id'],'seo_name'=>encode_forum($forum_info['title']))).'" class="forum_sub_heading">'.tep_db_input($forum_info['title']).'</a>';
 $template->assign_vars(array( 
 'HEADING_TITLE'  =>'<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
 'INFO_TEXT_TITLE'=>INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'=>tep_draw_input_field('TR_title',$title,'size="45" class="form-control required"',true),
 'INFO_TEXT_DESCRIPTION'=>INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=>tep_draw_textarea_field('TR_description', 'soft', '100', '8', stripslashes($description), 'class="form-control required"', '', true),
//  'button'=>tep_image_submit(PATH_TO_BUTTON.'button_save.gif',IMAGE_SAVE),
'button'=>tep_button_submit('btn btn-primary','Save'),
 'INFO_TEXT_FORUM_HEADER_LINK'  => $forum_header_link,
 'forum_title'   => '<a href="'.getPermalink('forum_topics',array('ide'=>$forum_info['id'],'seo_name'=>encode_forum($forum_info['title']))).'" style="">'.$forum_info['title'].'</a>',
 'form'=>$form,
 'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
 'update_message'=> $messageStack->output()));
 $template->pparse('topics_add'); 
}
else
{
 $forum_header_link='<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading1">'.INFO_TEXT_HOME.'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'.tep_href_link(PATH_TO_FORUM,'category='.$forum_info['category_id']).'" class="forum_sub_heading1">'.tep_db_output($forum_info['category_name']).'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'. getPermalink('forum_topics',array('ide'=>$forum_info['id'],'seo_name'=>encode_forum($forum_info['title']))).'" class="forum_sub_heading1">'.tep_db_input($forum_info['title']).'</a>';
 $template->assign_vars(array(
 'HEADING_TITLE' => '<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
 'HOST_NAME'     => HOST_NAME,
 'form'          => '<form name="page" method="post">'.tep_draw_hidden_field('lower',$lower).tep_draw_hidden_field('higher',$higher),
 'hidden_fields1'=> $hidden_fields1,
 //'forum_title'   => '<a href="'.tep_href_link(PATH_TO_FORUM.$forum_id.'/'.encode_forum($forum_info['title']).'.html').'"  class="forum_sub_heading" >'.$forum_info['title'].'</a>',
 'forum_title'   => $forum_info['title'],

 // 'new_button'    => '<a href="'.FILENAME_FORUM_TOPICS.'?action=new&forum_id='.$_GET['forum_id'].'">'.tep_image_button(PATH_TO_BUTTON.'button_new.gif',IMAGE_NEW).'</a>&nbsp;&nbsp;',
 // 'new_button'    => "<a href='".tep_href_link(PATH_TO_FORUM.FILENAME_FORUM_TOPICS,'action=new&forum_id='.$forum_id)."'>".tep_image_button(PATH_TO_BUTTON.'create_new_topics.gif',IMAGE_NEW)."</a>&nbsp;&nbsp;",


'new_button' => tep_link_button_Name( tep_href_link(PATH_TO_FORUM.FILENAME_FORUM_TOPICS,'action=new&forum_id='.$forum_id),
                'btn btn-primary mmt-15', 
                'Post New Thread', ''),

'INFO_TEXT_FORUM_HEADER_LINK'  => $forum_header_link,
 'INFO_TEXT_HEADING_SEARCH' => '<a class="btn btn-outline-secondary me-3" href="'.tep_href_link(PATH_TO_FORUM.FILENAME_FORUM_SEARCH).'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
 <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
</svg> '.tep_db_output(INFO_TEXT_HEADING_SEARCH).'</a>',
 'page_title'=> $forum_info['title'],
 'meta_title' => 'Forum Topic - '.$forum_info['title'].' - Forum - '.SITE_TITLE.' - '.HOST_NAME,
 'meta_description' =>'Forum Topic - '.$forum_info['title'].', '.strip_tags($forum_info['description'],' < > <a ">').' - Forum - '.SITE_TITLE.' - '.HOST_NAME,
 'update_message'=> $messageStack->output()));
$template->pparse('topics');
}
?>