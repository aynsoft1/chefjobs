<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_FORUM_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_TOPIC_DETAILS);
$template->set_filenames(array('topics' => 'topic_details.htm','reply_add' => 'reply_add.htm','reply_preview' => 'reply_preview.htm'));
include_once("../".FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'topic_details.js';

////////////////
if(tep_not_null($_GET['topic_id']))
{
 $topic_id=$_GET['topic_id'];
}
if(tep_not_null($_POST['topic_id']))
{
 $topic_id=$_POST['topic_id'];
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if(isset($_POST['action1']) && tep_not_null($_POST['action1']))
$action=tep_db_prepare_input($_POST['action1']);

if(!check_login('jobseeker') && !check_login('recruiter') && tep_not_null($action))
{
 $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(tep_href_link(FILENAME_LOGIN));
}
if(!$topic_info=getAnyTableWhereData(FORUM_TOPICS_TABLE.' as t left outer join '.FORUM_TABLE.' as f on (t.forum_id=f.id)'," t.id='".tep_db_input($topic_id)."'","t.title,t.description,f.id,f.title as forum_title,f.description as forum_description,t.inserted as topic_inserted,t.user_type,t.user_id"))
{
 $messageStack->add_session(MESSAGE_INVALID_TOPIC_ERROR, 'error');
 tep_redirect(tep_href_link(PATH_TO_FORUM.FILENAME_INDEX));
}
if($topic_info['user_type']=='jobseeker')
{
	$name    = getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$topic_info['user_id']."'","jobseeker_first_name,jobseeker_last_name");
	$topic_author=$name['jobseeker_first_name'].' '.$name['jobseeker_last_name'];
}
else
{
	$name    = getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$topic_info['user_id']."'","recruiter_first_name,recruiter_last_name");
	$topic_author=$name['recruiter_first_name'].' '.$name['recruiter_last_name'];
}

$topic_viewed = tep_db_prepare_input($_COOKIE["forum_topic_".$topic_id]);
if($topic_viewed!='viewed')  
{
 @SetCookie("forum_topic_".$topic_id, "viewed",time() + 3600);
 tep_db_query("update ".FORUM_TOPICS_TABLE." set hits=hits+1 where id='".tep_db_input($topic_id)."'");
}

if(tep_not_null($action))
{
 switch($action)
 {
  case 'preview':

		  $hidden_fields='';
    $description       = stripslashes($_POST['TR_description']);
    $hidden_fields.=tep_draw_hidden_field('topic_id',$topic_id);
    $hidden_fields.=tep_draw_hidden_field('TR_description',$description);
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
     $error=true;
    }
   break;
  case 'back':
    $description       = stripslashes($_POST['TR_description']);
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
     $error=true;
    }
   break;
  case 'add':
  case 'save':
     $description       = stripslashes($_POST['TR_description']);
     if(strlen($description)<=0)
     {
      $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
      $error=true;
     }
     if(!$error)
     {
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
      $description=($description);//valid_html_link
      $description= htmlentities(strip_tags($description,'<br><p><b>'));
      $description =nl2br(autolink($description));
      $sql_data_array=array('description'  => $description,
		                           'topic_id'    => $topic_id,
                             'user_id'     => $user_id,
                             'user_type'   => $user_type,
                             );
       $sql_data_array['inserted']='now()';
       tep_db_perform(TOPIC_REPLY_TABLE, $sql_data_array);
       $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
       tep_redirect(getPermalink('topic_details',array('ide'=>$topic_id,'seo_name'=>encode_forum($topic_info['title']))) );
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
 $form=tep_draw_form('topic', PATH_TO_FORUM.FILENAME_TOPIC_DETAILS, 'action=add', 'post', ' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('topic_id',$topic_id);//tep_draw_hidden_field('topic_id',$_GET['topic_id']);
 //$form=tep_draw_form('topic', PATH_TO_FORUM.FILENAME_TOPIC_DETAILS, 'action=preview', 'post', ' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('topic_id',$topic_id);//tep_draw_hidden_field('topic_id',$_GET['topic_id']);
}
elseif($action=='preview')
{
 $form=tep_draw_form('topic', PATH_TO_FORUM.FILENAME_TOPIC_DETAILS, 'action=add', 'post', '').tep_draw_hidden_field('action1','').tep_draw_hidden_field('topic_id',$_POST['topic_id']);
 $button='<a href="#" onclick="set_action(\'back\')">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE);
}
else
{
 ///////////// Middle Values
 if($topic_id!="")
 {
  $field=tep_db_prepare_input($_POST['field']);
  $order=tep_db_prepare_input($_POST['order']);
  $lower=(int)tep_db_prepare_input($_POST['lower']);
  $higher=(int)tep_db_prepare_input($_POST['higher']);
  $table_names = TOPIC_REPLY_TABLE." as t left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (t.user_id =jl.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (t.user_id =rl.recruiter_id && t.user_type='recruiter') ";
  $whereClause = " t.topic_id='".tep_db_input($topic_id)."'  and  (jl.jobseeker_status='Yes' or rl.recruiter_status='Yes')"; 

  $topic_query_sort_raw="select count(*) as x1 from ".$table_names." where ".$whereClause ;
  $topic_query_sort_result = tep_db_query($topic_query_sort_raw);
  $sort_row=tep_db_fetch_array($topic_query_sort_result);
  $x1=$sort_row['x1'];
  ///only for sorting starts
  include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
  $sort_array=array("t.inserted");
  $obj_sort_by_clause=new sort_by_clause($sort_array,'t.inserted asc');
  $order_by_clause=$obj_sort_by_clause->return_value;
  $see_before_page_number_array=see_before_page_number($sort_array,$field,'t.inserted',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_ARTICLES);
  $lower=$see_before_page_number_array['lower'];
  $higher=$see_before_page_number_array['higher'];
  $field=$see_before_page_number_array['field'];
  $order=$see_before_page_number_array['order'];
  $hidden_fields1=tep_draw_hidden_field('sort',$sort);
  ///only for sorting ends
  $totalpage=ceil($x1/$higher);
  $topic_query_raw="select t.id,t.topic_id ,t.user_id,t.user_type,t.description,t.inserted from $table_names where $whereClause  order by $order_by_clause limit $lower,$higher";
  $topic_query = tep_db_query($topic_query_raw);
  $x=tep_db_num_rows($topic_query);
  $pno= ceil($lower+$higher)/($higher);
  if($x > 0 && $x1 > 0)
  {
   $alternate=1;
   while ($topic = tep_db_fetch_array($topic_query)) 
   {
    $ide=$topic["id"];
    $row_selected='class="forum_row"';//' class="dataTableRow'.($alternate%2==1?'2':'1').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
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
    //$topic_last_query_raw="select inserted from ".TOPIC_REPLY_TABLE." where topic_id=".$topic['topic_id']." order by inserted desc limit 1";
    ///$topic_last_query = tep_db_query($topic_last_query_raw);
    //$topic_last = tep_db_fetch_array($topic_last_query);
    $lower = $lower + 1;
    $template->assign_block_vars('topic', array( 'row_selected' => $row_selected,
     'desc'  => nl2br(stripslashes($topic['description'])),
     'reply_no'  => tep_db_output('#'.$lower),
     'author'   => $author,
    'last_post' => tep_date_long($topic['inserted']),
     ));
    $alternate++;
   }
    $plural=($x1=="1")?"Reply":"Replies";
    $template->assign_vars(array('total'=>"Displaying ".((($pno-1)*$higher)+1)." to ".(($pno*$higher)>$x1?$x1:($pno*$higher))." (of $x1 ".$plural.")"));
    }
    else
   {
    $template->assign_vars(array('total'=>"There is no Reply in this Topic."));
   }
    see_page_number();
   tep_db_free_result($topic_query);
  }
 }
if($action=='preview') 
{
 $template->assign_vars(array(
 'hidden_fields'         => $hidden_fields,
	'HEADING_TITLE'         =>'<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
 'INFO_TEXT_DESCRIPTION' => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=> nl2br(stripslashes($description)),
 'button'                => $button,
 'form'                  => $form,
 'forum_title'   => $topic_info['title'],
 'INFO_TEXT_FORUM_HEADER_LINK'  => '<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading">'.INFO_TEXT_HOME.'</a>&nbsp;&gt;&gt;&nbsp;<a href="'.getPermalink('forum_topics',array('ide'=>$_POST['topic_id'],'seo_name'=>encode_forum(((strlen($topic_info['title'])<150)?$topic_info['title']:substr($topic_info['title'],0,148)."..")))).'" class="forum_sub_heading">'.$topic_info['forum_title'].'</a>&nbsp;&gt;&gt;&nbsp;<a href="'.getPermalink('topic_details',array('ide'=>$_POST['topic_id'],'seo_name'=> encode_forum(((strlen($topic_info['title'])<150)?$topic_info['title']:substr($topic_info['title'],0,148)."..")) )) .'" class="forum_sub_heading">'.$topic_info['title'].'</a>',
 'update_message'        => $messageStack->output()));
 $template->pparse('reply_preview');
}
elseif($action=='new' || $action=='back') 
{
 {
  if($title=='')
  {
   $title=$topic_info['title'];
  }
  else
   $title=$title;
		$template->assign_vars(array( 
			'HEADING_TITLE'  =>'<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
			//'INFO_TEXT_TITLE'=>INFO_TEXT_TITLE,
			//'INFO_TEXT_TITLE1'=>tep_draw_input_field('TR_title',$title,'size="45"',true),
			'INFO_TEXT_DESCRIPTION'=>INFO_TEXT_DESCRIPTION,
			'INFO_TEXT_DESCRIPTION1'=>tep_draw_textarea_field('TR_description', 'soft', '100', '8', stripslashes($description), '', '', true),
			// 'button'=>tep_image_submit(PATH_TO_BUTTON.'button_save.gif',IMAGE_SAVE),
			'button'=>tep_button_submit('btn btn-primary','Send'), // This tep_button_submit Called from html_output file
			'form'=>$form,
			'forum_title'   => $topic_info['title'],
			'INFO_TEXT_FORUM_HEADER_LINK'          => '<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading">'.INFO_TEXT_HOME.'</a>&nbsp;&gt;&gt;&nbsp;<a href="'.getPermalink('topic_details',array('ide'=>$topic_id,'seo_name'=>encode_forum((strlen($topic_info['title'])<150)?$topic_info['title']:substr($topic_info['title'],0,148).".."))).'" class="forum_sub_heading">'.$topic_info['forum_title'].'</a>&nbsp;&gt;&gt;&nbsp;<a href="'.((strlen($topic_info['title'])<150)?$topic_info['title']:substr($topic_info['title'],0,148)."..") .'" class="forum_sub_heading">'.$topic_info['title'].'</a>',
   'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
			'update_message'=> $messageStack->output()));
			$template->pparse('reply_add');
 }
}
else
{
$template->assign_vars(array(
 'HEADING_TITLE' => '<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
 'topic_title'   => $topic_info['title'],
 'topic_posted'   => tep_date_long($topic_info['topic_inserted']),
 'topic_description'   => nl2br(stripslashes($topic_info['description'])),
 'topic_author'   =>   tep_db_output($topic_author),

 'form'          => '<form name="page" method="post">'.tep_draw_hidden_field('lower',$lower).tep_draw_hidden_field('higher',$higher),
//  'new_button'    => '<a href="'.FILENAME_TOPIC_DETAILS.'?action=new&topic_id='.$_GET['topic_id'].'">'.tep_image_button(PATH_TO_BUTTON.'reply_to_thread.gif',IMAGE_NEW).'</a>&nbsp;&nbsp;',
 'new_button' => tep_link_button_Name( FILENAME_TOPIC_DETAILS.'?action=new&topic_id='.$_GET['topic_id'],
                'btn btn-primary mmt-15', 
                'Reply To Thread',
                'Link'),
 'INFO_TEXT_FORUM_HEADER_LINK'          => '<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading1">'.INFO_TEXT_HOME.'</a>&nbsp;&gt;&gt;&nbsp;<a href="'.getPermalink('forum_topics',array('ide'=>$topic_info['id'],'seo_name'=>encode_forum($topic_info['forum_title']))).'" class="forum_sub_heading1">'.$topic_info['forum_title'].'</a>&nbsp;&gt;&gt;&nbsp;<a href="'.getPermalink('topic_details',array('ide'=>$_GET['topic_id'],'seo_name'=>encode_forum((strlen($topic_info['title'])<150)?$topic_info['title']:substr($topic_info['title'],0,148).".."))).'" class="forum_sub_heading1">'.$topic_info['title'].'</a>',
 'INFO_TEXT_HEADING_SEARCH' => '<a href="'.tep_href_link(PATH_TO_FORUM.FILENAME_FORUM_SEARCH).'"  class="btn btn-outline-secondary px-4 me-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
 <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
</svg> '.tep_db_output(INFO_TEXT_HEADING_SEARCH).'</a>',
 'hidden_fields1'=> $hidden_fields1,
 
	'page_title'=> $topic_info['title'].' - '.$topic_info['forum_title'],
	'meta_title' => $topic_info['title'].' - Forum Topic - '.$topic_info['forum_title'].' - Forum - '.SITE_TITLE.' - '.HOST_NAME,
	'meta_description' =>$topic_info['title'].', '.strip_tags($topic_info['description'],' < > <a ">').', Forum Topic - '.$topic_info['forum_title'].', '.strip_tags($topic_info['forum_description'],' < > <a ">').' - Forum - '.SITE_TITLE.' - '.HOST_NAME,

 'update_message'=> $messageStack->output()));
$template->pparse('topics');
}
?>