<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_FORUM_POST);
$template->set_filenames(array('forum_post' => 'admin1_list_of_forum_post.htm','forum_post1' => 'admin1_list_of_forum_post1.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="")
{
 switch ($action)
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id='".tep_db_input($id)."'");
   tep_db_query("delete from ".FORUM_TOPICS_TABLE." where id='".tep_db_input($id)."'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST,tep_get_all_get_params(array('action','selected_box','id'))));
  case 'delete_reply':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id='".tep_db_input($id)."'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST,tep_get_all_get_params(array('action','selected_box','id'))));
  case 'post_featured':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("update ".FORUM_TOPICS_TABLE ." set featured='yes' where id='".$id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST,tep_get_all_get_params(array('action','selected_box'))));
  case 'post_not_featured':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("update ".FORUM_TOPICS_TABLE ." set featured='no' where id='".$id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST,tep_get_all_get_params(array('action','selected_box'))));
  case 'update':
   $post_title    = tep_db_prepare_input($_POST['TR_post_title']);
   $forum_id      = tep_db_prepare_input($_POST['TR_forum']);
   $description   = stripslashes($_POST['description1']);
   $id            =  tep_db_prepare_input($_GET['id']);
   $sql_data_array=array(
                         'title'       => $post_title,
                         'forum_id'    => $forum_id,
                         'description' => $description,
                        );
   $error=false;
   if(!tep_not_null($post_title))
   {
 			$messageStack->add(MESSAGE_POST_TITLE_ERROR, 'error');
    $error=true;
	  }
   if(!tep_not_null($forum_id))
   {
 			$messageStack->add(MESSAGE_FORUM_ID_ERROR, 'error');
    $error=true;
	  }
   if(!tep_not_null($description))
   {
 			$messageStack->add(MESSAGE_POST_DESCRIPTION_ERROR, 'error');
    $error=true;
	  }
   if(!$error)
   {
    tep_db_perform(FORUM_TOPICS_TABLE, $sql_data_array, 'update', "id = '" .$id. "'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    tep_redirect(FILENAME_ADMIN1_LIST_OF_FORUM_POST.'?page='.$_GET['page'].'&id='.$id);
   }
  break;
 }
}
if($action=='update' ||$action=='edit')
{
 $id = tep_db_prepare_input($_GET['id']);
 if(($action=='update' || $action=='edit') && $id>0)
 {
  if($action=='edit')
  {
   $table_name    = FORUM_TOPICS_TABLE." as f left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (f.user_id =jl.jobseeker_id && f.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (f.user_id =rl.recruiter_id && f.user_type='recruiter')"  ;
 	 $row_result    = getAnyTableWhereData($table_name,"f.id='".tep_db_input($id)."'","f.id,f.forum_id,f.user_type,f.title,f.description,if(f.user_type='jobseeker',jl.jobseeker_email_address,rl.recruiter_email_address) as user_email_address");
   $post_title    = tep_db_prepare_input($row_result['title']);
   $forum_id      = tep_db_prepare_input($row_result['forum_id']);
   $description   = $row_result['description'];
   $post_user     = $row_result['user_email_address'].' ('.($row_result['user_type']).')';
  }
  elseif($error)
  {
   $table_name    = FORUM_TOPICS_TABLE." as f left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (f.user_id =jl.jobseeker_id && f.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (f.user_id =rl.recruiter_id && f.user_type='recruiter')"  ;
 	 $row_result    = getAnyTableWhereData($table_name,"f.id='".tep_db_input($id)."'","f.id,f.forum_id,f.user_type,f.title,if(f.user_type='jobseeker',jl.jobseeker_email_address,rl.recruiter_email_address) as user_email_address");
   $post_user     = $row_result['user_email_address'].' ('.($row_result['user_type']).')';
  }
  $feed_form     = tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST,'id='.$id.'&page='.$_GET['page'].'&action=update','post',' onsubmit="return ValidateForm(this)"');
 // $feed_button   = '<a href="'.FILENAME_ADMIN1_LIST_OF_FORUM_POST.'">'.tep_button('Cancel','class="btn btn-primary"').'</a> '.tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
 $feed_button   = '<a class="btn btn-secondary" href="'.FILENAME_ADMIN1_LIST_OF_FORUM_POST.'">
                    Cancel</a> 
                  <button class="btn btn-primary mr-2" type="submit">'.IMAGE_UPDATE1.'</button>';
 }

 $template->assign_vars(array(
  'HEADING_TITLE'         => HEADING_TITLE,
  'feed_form'             => $feed_form,
  'submit_button'         => $feed_button,
  'TEXT_INFO_POST_USER'   => TEXT_INFO_POST_USER,
  'TEXT_INFO_POST_USER1'  => tep_db_output($post_user),
  'TEXT_INFO_POST_TITLE'  => TEXT_INFO_POST_TITLE,
  'TEXT_INFO_POST_TITLE1' => tep_draw_input_field('TR_post_title',$post_title,'class="form-control form-control-sm"',true),
  'TEXT_INFO_POST_FORUM'  => TEXT_INFO_POST_FORUM,
  'TEXT_INFO_POST_FORUM1' => LIST_SET_DATA(FORUM_TABLE,"",'title','id',"title",'name="TR_forum" class="form-control form-control-sm"',"",'',$forum_id),
  'TEXT_INFO_POST_DESCRIPTION' => TEXT_INFO_POST_DESCRIPTION,
  'TEXT_INFO_POST_DESCRIPTION1'=> tep_draw_textarea_field('description1', 'soft', '100', '20', stripslashes($description), 'class="form-control form-control-sm"', '', true),
  'update_message'=>$messageStack->output()));
 $template->pparse('forum_post1');
}
else
{
 $query_string='';
 $whereClause="";
 if(tep_not_null($_GET['search_forum']))
 {
  $search_forum=tep_db_prepare_input($_GET['search_forum']);
  $query_string.='&search_forum='.$search_forum;
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" f.forum_id ='".tep_db_input($search_forum)."'";
 }
 if(tep_not_null($_GET['search_post']))
 {
  $search_post=tep_db_prepare_input($_GET['search_post']);
  $query_string.='&search_post='.$search_post;
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" f.id ='".tep_db_input($search_post)."'";
 }
 if(tep_not_null($_GET['TNEF_email']))
 {
  $email_address=tep_db_prepare_input($_GET['TNEF_email']);
  $query_string.='&TNEF_email='.$email_address;
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" (jl.jobseeker_email_address ='".tep_db_input($email_address)."' or rl.recruiter_email_address ='".tep_db_input($email_address)."' )";
 }
 if(tep_not_null($whereClause))
 $whereClause=" where ".$whereClause;
 ///////////// Middle Values
 $sort_array=array("f.title","f.forum_id","f.inserted","f.featured","f.hits");
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'f.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 $feeds_query_raw="select f.id,f.forum_id,f.user_type,f.title,f.inserted,if(f.user_type='jobseeker',jl.jobseeker_email_address,rl.recruiter_email_address) as user_email_address,f.featured,f.hits from ".FORUM_TOPICS_TABLE." as f left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (f.user_id =jl.jobseeker_id && f.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (f.user_id =rl.recruiter_id && f.user_type='recruiter')   $whereClause order by ".$order_by_clause;
 $feeds_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $feeds_query_raw, $feeds_query_numrows);
 ///echo $feeds_query_raw;
 $feeds_query = tep_db_query($feeds_query_raw);
 if(tep_db_num_rows($feeds_query) > 0)
 {
  $alternate=1;
  while ($feeds = tep_db_fetch_array($feeds_query))
  {
   if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $feeds['id']))) && !isset($fInfo) && (substr($action, 0, 3) != 'new'))
   {
    $fInfo = new objectInfo($feeds);
   }
   if ( (isset($fInfo) && is_object($fInfo)) && ($feeds['id'] == $fInfo->id) )
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   }
   $alternate++;
   if ( (isset($fInfo) && is_object($fInfo)) && ($feeds['id'] == $fInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page='.$_GET['page'].'&id=' . $feeds['id'].$query_string) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   /*
   if ($feeds['status'] == 'active')
   {
    $status='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$feeds['id'].'&action=feed_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_FEED_INACTIVATE, 10, 10) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_feed_active, 10, 10);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif',STATUS_feed_inactive, 10, 10) . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$feeds['id'].'&action=feed_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_FEED_ACTIVATE, 10, 10) . '</a>';
   }
   */
   if ($feeds['featured'] == 'yes')
   {
    $featured_status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $feeds['id'] . '&action=post_not_featured' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_POST_NOT_FEATURED, 10, 10) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_POST_FEATURED, 10, 10);
   }
   else
   {
    $featured_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_POST_NOT_FEATURE, 10, 10) . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $feeds['id'] . '&action=post_featured' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_POST_FEATURED, 10, 10) . '</a>';
   }

   $template->assign_block_vars('search_tag', array( 'row_selected' => $row_selected,
                                                     'action'       => $action_image,
                                                     'title'        => tep_db_output($feeds['title'])."<br>".(($feeds['user_type']=='jobseeker')?'j -  ':'r - ').'('.tep_db_output($feeds['user_email_address']).')',
                                                     'forum'        => tep_db_output($feeds['forum_id']),
                                                     'featured'     => $featured_status,
                                                     'inserted'     =>tep_db_output(formate_date1($feeds['inserted'])),
                                                     'hits'         => tep_db_output($feeds['hits']),
                                                     ));
  }
 }
 //// for right side
 $ADMIN_RIGHT_HTML="";

 $heading = array();
 $contents = array();
 switch ($action)
 {
  case 'delete':
  //  $heading[] = array('text' => '<b>' . $fInfo->title . '</b>');
  $heading[] = array('text' => '<div class="">
  <div class="font-weight-bold">'.$fInfo->title.'
  <div class="h5">'.TEXT_DELETE_INTRO.'</div>
  </div></div>
  ');
   $contents = array('form' => tep_draw_form('search_feed_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
   
  //  $contents[] = array('text' => TEXT_DELETE_INTRO);
  //  $contents[] = array('text' => '<br><b>' . $fInfo->title . '</b>');
  //  $contents[] = array('align' => 'left', 'text' => '<br>
  //  <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete'.$query_string) . '">
  //   '.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'
  //   </a>&nbsp;
  //   <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].$query_string) . '">
  //   ' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '
  //   </a>');
  //  $contents[] = array('text' => '<br>'.TEXT_DELETE_WARNING.'<br>&nbsp;');

  $contents[] = array('align' => 'left', 'text' => '
  <div class="py-2">
  <a class="btn btn-primary" href="
    ' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete'.$query_string) . '">
    '.IMAGE_CONFIRM.'</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].$query_string) . '">
    '.IMAGE_CANCEL.'
    </a>
  </div>');
  
  $contents[] = array('text' => '<div class="py-2">'.TEXT_DELETE_WARNING.'</div>');
  break;
  default:
   if (isset($fInfo) && is_object($fInfo))
   {
    // $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_POST.'</b>');
    // $contents[] = array('text' => tep_db_output($fInfo->title));
    // $contents[] = array('align' => 'left', 'text' => '<br>
    // <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] .'&id=' . $fInfo->id .'&action=edit'.$query_string) . '">
    // '.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'
    // </a>&nbsp;
    
    // <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] .'&id=' . $fInfo->id. '&action=delete'.$query_string) . '">
    // '.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'
    // </a>');
    
    // $contents[] = array('align' => 'left', 'text' => '
    // <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY,'search_post=' . $fInfo->id ) . '" target="left">
    // '.tep_image_button(PATH_TO_BUTTON.'post_reply1.gif',IMAGE_POST_REPLY).'
    // </a>');
    // $contents[] = array('align' => 'left', 'text' => '
    // <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] .'&id=' . $fInfo->id .'&action=delete_reply'.$query_string) . '
    // " onclick="return confirm(\''.tep_db_output(INFO_TEXT_DELETE_REPLY_TEXT).'\')" >
    // '.tep_db_output(INFO_TEXT_DELETE_REPLY).'</a>');
    // $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
    
    $heading[] = array('text' => '<div class=""><div class="font-weight-bold">'.TEXT_INFO_HEADING_POST.'</div></div>');
    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1">'.tep_db_output($fInfo->title).'</div>
    
    <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] .'&id=' . $fInfo->id .'&action=edit'.$query_string) . '">
    '.IMAGE_EDIT.'
    </a>
    
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] .'&id=' . $fInfo->id. '&action=delete'.$query_string) . '">
    '.IMAGE_DELETE.'
    </a>
    
    <a class="btn btn-primary mt-2 mb-2" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY,'search_post=' . $fInfo->id ) . '" target="left">
    '.IMAGE_POST_REPLY.'
    </a>
    
    <a class="btn btn-secondary href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, 'page=' . $_GET['page'] .'&id=' . $fInfo->id .'&action=delete_reply'.$query_string) . '
    " onclick="return confirm(\''.tep_db_output(INFO_TEXT_DELETE_REPLY_TEXT).'\')" >
    '.tep_db_output(INFO_TEXT_DELETE_REPLY).'
    </a>

    <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
    </div>');
   }
   break;
 }
 if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
 {
  $box = new right_box;
  $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
  $RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
 }
 else
 {
  $RIGHT_BOX_WIDTH='0';
 }
 /////

 $template->assign_vars(array(
  'TABLE_HEADING_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_FORUM'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_FORUM.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_FEATURED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_FEATURED.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_HITS'    => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_POST, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_HITS.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
  'TABLE_HEADING_ACTION'   =>TABLE_HEADING_ACTION,
  'INFO_TEXT_SEARCH_POST'  =>INFO_TEXT_SEARCH_POST,
  'INFO_TEXT_SEARCH_POST1' =>tep_draw_input_field('search_post',$search_post,'class="form-control form-control-sm" placeholder="'.INFO_TEXT_SEARCH_POST.'"'),
  'INFO_TEXT_SEARCH_FORUM' =>INFO_TEXT_SEARCH_FORUM,
  'INFO_TEXT_SEARCH_FORUM1'=>tep_draw_input_field('search_forum',$search_forum,'class="form-control form-control-sm" placeholder="'.INFO_TEXT_SEARCH_FORUM.'"'),
  'INFO_TEXT_SEARCH_EMAIL' =>INFO_TEXT_SEARCH_EMAIL,
  'INFO_TEXT_SEARCH_EMAIL1'=> tep_draw_input_field('TNEF_email',$email_address,'class="form-control form-control-sm" placeholder="'.INFO_TEXT_SEARCH_EMAIL.'"'),

  'count_rows'=>$feeds_split->display_count($feeds_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FORUM_POST),
  'no_of_pages'=>$feeds_split->display_links($feeds_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','id','action','search_forum','TNEF_email')).$query_string),
  'HEADING_TITLE'=>HEADING_TITLE,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('forum_post');
}
?>