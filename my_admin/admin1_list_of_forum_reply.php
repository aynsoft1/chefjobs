<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY);
$template->set_filenames(array('forum_post_reply' => 'admin1_list_of_forum_reply.htm','forum_post_reply1' => 'admin1_list_of_forum_reply1.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$whereClause="";
if(tep_not_null($action1))
{
 switch ($action1)
 {
  case 'delete':
    if(isset($_POST['reply_ids']))
     $reply_ids= implode(',',tep_db_prepare_input($_POST['reply_ids']));
    if(count($_POST['reply_ids'])>0)
    {
     $whereClause= (tep_not_null($whereClause)?$whereClause.' and ':'');
     $whereClause =' r.id in ('.tep_db_input($reply_ids).')';
    }
    else
     unset($action1);
   break;
  case 'confirm_bulk_delete':
    if(isset($_POST['reply_ids']) && count($_POST['reply_ids'])>0)
    {
     $reply_ids= implode(',',tep_db_prepare_input($_POST['reply_ids']));
     tep_db_query("delete from ".TOPIC_REPLY_TABLE." where id in (".tep_db_input($reply_ids).")");
		  	$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY,tep_get_all_get_params(array('action','selected_box','rID'))));
    }
    else
     unset($action1);

   break;
 }
}

if ($action!="")
{
 switch ($action)
	{
  case 'confirm_delete':
   $rID = tep_db_prepare_input($_GET['rID']);
   tep_db_query("delete from ".TOPIC_REPLY_TABLE." where id='".tep_db_input($rID)."'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY,tep_get_all_get_params(array('action','selected_box','rID'))));
  case 'update':
   $description   = stripslashes($_POST['description1']);
   $rID           =  tep_db_prepare_input($_GET['rID']);
   $sql_data_array=array(
                         'description' => $description,
                        );
   $error=false;
   if(!tep_not_null($description))
   {
 			$messageStack->add(MESSAGE_POST_DESCRIPTION_ERROR, 'error');
    $error=true;
	  }
   if(!$error)
   {
    tep_db_perform(TOPIC_REPLY_TABLE, $sql_data_array, 'update', "id = '" .$rID. "'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    tep_redirect(FILENAME_ADMIN1_LIST_OF_FORUM_REPLY.'?page='.$_GET['page'].'&rID='.$rID);
   }
  break;
 }
}
if($action=='update' ||$action=='edit')
{
 $rID = tep_db_prepare_input($_GET['rID']);
 if(($action=='update' || $action=='edit') && $rID>0)
 {
  if($action=='edit')
  {
   $table_name    = TOPIC_REPLY_TABLE." as r left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (r.user_id =jl.jobseeker_id && r.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (r.user_id =rl.recruiter_id && r.user_type='recruiter')"  ;
 	 $row_result    = getAnyTableWhereData($table_name,"r.id='".tep_db_input($rID)."'","r.id,r.topic_id,r.user_type,r.description,if(r.user_type='jobseeker',jl.jobseeker_email_address,rl.recruiter_email_address) as user_email_address");
   $topic_id      = tep_db_prepare_input($row_result['topic_id']);
   $description   = $row_result['description'];
   $post_user     = $row_result['user_email_address'].' ('.($row_result['user_type']).')';
  }
  elseif($error)
  {
   $table_name    = TOPIC_REPLY_TABLE." as r left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (r.user_id =jl.jobseeker_id && r.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (r.user_id =rl.recruiter_id && r.user_type='recruiter')"  ;
 	 $row_result    = getAnyTableWhereData($table_name,"r.id='".tep_db_input($rID)."'","r.id,r.topic_id,r.user_type,if(r.user_type='jobseeker',jl.jobseeker_email_address,rl.recruiter_email_address) as user_email_address");
   $topic_id      = tep_db_prepare_input($row_result['topic_id']);
   $post_user     = $row_result['user_email_address'].' ('.($row_result['user_type']).')';
  }
  $feed_form     = tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY,'rID='.$rID.'&page='.$_GET['page'].'&action=update','post',' onsubmit="return ValidateForm(this)"');
 // $feed_button   = '<a href="'.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY.'">'.tep_button('Cancel','class="btn btn-primary"').'</a> '.tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
 $feed_button   = '<a class="btn btn-secondary mr-2" href="'.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY.'">Cancel</a> 
                      <button class="btn btn-primary" type="submit">Update</button>';
 }
 $row_topic    = getAnyTableWhereData(FORUM_TOPICS_TABLE,"id='".tep_db_input($topic_id)."'","title");

 $template->assign_vars(array(
  'HEADING_TITLE'         => HEADING_TITLE,
  'feed_form'             => $feed_form,
  'submit_button'         => $feed_button,
  'TEXT_INFO_REPLY_USER'   => TEXT_INFO_REPLY_USER,
  'TEXT_INFO_REPLY_USER1'  => tep_db_output($post_user),
  'TEXT_INFO_POST_TITLE'  => TEXT_INFO_POST_TITLE,
  'TEXT_INFO_POST_TITLE1' => tep_draw_input_field('TR_post_title',$post_title,'',true),
  'TEXT_INFO_REPLY_POST'  => TEXT_INFO_REPLY_POST,
  'TEXT_INFO_REPLY_POST1' => tep_db_output($row_topic['title']),
  'TEXT_INFO_REPLY_DESCRIPTION' => TEXT_INFO_REPLY_DESCRIPTION,
  'TEXT_INFO_REPLY_DESCRIPTION1'=> tep_draw_textarea_field('description1', 'soft', '100', '20', stripslashes($description), 'class="form-control form-control-sm"', '', true),
  'update_message'=>$messageStack->output()));
 $template->pparse('forum_post_reply1');
}
else
{
 ///////////// Middle Values
 $sort_array=array("r.description","r.topic_id","r.inserted");
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'r.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 $query_string='';
 if(tep_not_null($_GET['search_post']))
 {
  $search_post=tep_db_prepare_input($_GET['search_post']);
  $query_string.='&search_post='.$search_post;
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" r.topic_id ='".tep_db_input($search_post)."'";
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
 $feeds_query_raw="select r.id,r.topic_id,r.user_type,r.description,r.inserted,if(r.user_type='jobseeker',jl.jobseeker_email_address,rl.recruiter_email_address) as user_email_address from ".TOPIC_REPLY_TABLE." as r left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (r.user_id =jl.jobseeker_id && r.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (r.user_id =rl.recruiter_id && r.user_type='recruiter')   $whereClause order by ".$order_by_clause;
 $feeds_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $feeds_query_raw, $feeds_query_numrows);
 ///echo $feeds_query_raw;
 $feeds_query = tep_db_query($feeds_query_raw);
 $db_result_num_row=tep_db_num_rows($feeds_query);
 if($db_result_num_row > 0)
 {
  $alternate=1;
  while ($feeds = tep_db_fetch_array($feeds_query))
  {
   if($action1!='delete')
   {
    if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ($_GET['rID'] == $feeds['id']))) && !isset($fInfo) && (substr($action, 0, 3) != 'new'))
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
    if ( (isset($fInfo) && is_object($fInfo)) && ($feeds['id'] == $fInfo->id) )
    {
     $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
    }
    else
    {
     $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page='.$_GET['page'].'&rID=' . $feeds['id'].$query_string) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
    }
   }
   else
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';


   $alternate++;
   $reply_description=$feeds['description'];
   if($reply_description>100)
    $reply_description=substr($reply_description,0,100).'...';
   $template->assign_block_vars('search_tag', array( 'row_selected' => $row_selected,
                                                     'check_box'    => tep_draw_checkbox_field('reply_ids[]',$feeds['id'],($action1=='delete'?true:false)),
                                                     'action'       => $action_image,
                                                     'title'        => $reply_description."<br>".(($feeds['user_type']=='jobseeker')?'j -  ':'r - ').'('.tep_db_output($feeds['user_email_address']).')',
                                                     'topic'        => tep_db_output($feeds['topic_id']),
                                                     'featured'     => $featured_status,
                                                     'inserted'     =>tep_db_output(formate_date1($feeds['inserted'])),
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
   $reply_description=strip_tags($fInfo->description);
   if($reply_description>50)
   $reply_description=substr($reply_description,0,50).'...';
  //  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_POST.'</b>');
  $heading[] = array('text' => '<div class="">
  <div class="font-weight-bold">' . TEXT_INFO_HEADING_POST . '
  <div class="h5">'.TEXT_DELETE_INTRO.'</div><div>'.$reply_description.'</div>
  </div></div>
  ');
  $contents = array('form' => tep_draw_form('search_feed_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] . '&rID=' . $nInfo->id . '&action=deleteconfirm'));
  //  $contents[] = array('text' => TEXT_DELETE_INTRO);
  //  $contents[] = array('text' => '<br><b>' . $reply_description. '</b>');

  //  $contents[] = array('align' => 'left', 'text' => '<br>
  //  <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'].'&action=confirm_delete'.$query_string) . '">'
  //  .tep_button('Confirm','class="btn btn-primary"').'
  //  </a>&nbsp;
  //  <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'].$query_string) . '">'
  //   . tep_button('Cancel','class="btn btn-primary"') . '</a>');
  //  $contents[] = array('text' => '<br>'.TEXT_DELETE_WARNING.'<br>&nbsp;');
  
  $contents[] = array('align' => 'left', 'text' => '
  <div class="py-2">
  <a class="btn btn-primary" href="
    ' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'].'&action=confirm_delete'.$query_string) . '">
    Confirm</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'].$query_string) . '">
    Cancel
    </a>
  </div>');
  
  $contents[] = array('text' => '<div class="py-2">'.TEXT_DELETE_WARNING.'</div>');
  break;
  default:
   if (isset($fInfo) && is_object($fInfo))
   {
    $reply_description=strip_tags($fInfo->description);
    if($reply_description>50)
    $reply_description=substr($reply_description,0,50).'...';
    // $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_POST.'</b>');
    // $contents[] = array('text' => tep_db_output($reply_description));
    // $contents[] = array('align' => 'left', 'text' => '<br>
    // <a href="
    // ' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] .'&rID=' . $fInfo->id .'&action=edit'.$query_string) . '">
    // '.tep_button('Edit','class="btn btn-primary"').'
    // </a>&nbsp;
    // <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] .'&rID=' . $fInfo->id. '&action=delete'.$query_string)
    //  . '">'.tep_button('Delete','class="btn btn-primary"').'</a>');
    // $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);

    $heading[] = array('text' => '<div class=""><div class="font-weight-bold">'.TEXT_INFO_HEADING_POST.'</div></div>');
    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1">'.tep_db_output($reply_description).'</div>
    <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] .'&rID=' . $fInfo->id .'&action=edit'.$query_string) . '">
    Edit
    </a>
    <a class="btn btn-secondary" href="
    ' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, 'page=' . $_GET['page'] .'&rID=' . $fInfo->id. '&action=delete'.$query_string). '">
      Delete
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
 if($action1=='delete')
 {
  $check_link='<br>'.TEXT_DELETE_WARNING.'<br><br><a href="#"  style="color:#0000ff" onclick="DeleteSelected(\'confirm_bulk_delete\')">'.tep_button('Confirm','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY).'">' . tep_button('Cancel','class="btn btn-primary"') . '</a>';
  $template->assign_vars(array(
   'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
   'TABLE_HEADING_FORUM'=>TABLE_HEADING_FORUM,
   'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
  ));
 }
 else
 {
  $check_link='<a href="#" style="color:#0000ff" onclick="checkall()">Check All</a> / <a href="#"  style="color:#0000ff" onclick="uncheckall()">Uncheck All</a> <b>With Selected <a href="#"  style="color:#0000ff" onclick="DeleteSelected(\'delete\')">Delete</a></b></font>';
  $template->assign_vars(array(
   'TABLE_HEADING_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
   'TABLE_HEADING_FORUM'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_FORUM.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
   'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUM_REPLY, tep_get_all_get_params(array('sort','rID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
   'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
   'INFO_TEXT_SEARCH_POST'=>INFO_TEXT_SEARCH_POST,
   'INFO_TEXT_SEARCH_POST1'=>tep_draw_input_field('search_post',$search_post,'size="5" class="form-control form-control-sm mb-2" placeholder="'.INFO_TEXT_SEARCH_POST.'"'),
   'INFO_TEXT_SEARCH_EMAIL'=>INFO_TEXT_SEARCH_EMAIL,
   'INFO_TEXT_SEARCH_EMAIL1'=>tep_draw_input_field('TNEF_email',$email_address,'size="25" class="form-control form-control-sm" placeholder="'.INFO_TEXT_SEARCH_EMAIL.'"'),
  //  'INFO_TEXT_SEARCH_GO'=>'<input type="submit" value="Go">',
   'INFO_TEXT_SEARCH_GO'=>tep_button_submit('btn btn-primary','Go'),
   ));
 }

 $template->assign_vars(array(
  'check_link'=>($db_result_num_row>0)?$check_link:'',
  'hidden_fields'=>tep_draw_hidden_field('action1',''),

  'count_rows'=>$feeds_split->display_count($feeds_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FORUM_POST_REPLY),
  'no_of_pages'=>$feeds_split->display_links($feeds_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','rID','action','search_post','TNEF_email')).$query_string),
  'HEADING_TITLE'=>HEADING_TITLE,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('forum_post_reply');
}
?>