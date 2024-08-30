<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_FORUM_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_INDEX);
$template->set_filenames(array('index' => 'index.htm'));
include_once("../".FILENAME_BODY);
$category      = tep_db_prepare_input($_GET['category']);
#################JOB CATEGORY############################
if(tep_not_null($category))
$whereClause =" where id ='".tep_db_input($category)."'";
$field_names="id,category_name";
$query = "select $field_names from ".FORUM_CATEGORIES_TABLE." $whereClause order by category_name  asc";
$result=tep_db_query($query);
$x=tep_db_num_rows($result);
$i=1;
$forum_row_class=' class="forum_row_content small"';
$forum_row_class1=' class="forum_row_content1 small"';
if($x>0)
{
 while($row11 = tep_db_fetch_array($result))
 {
  $category_name= $row11['category_name'];
  $ide=$row11["id"];
  $key=((strlen($category_name)<100)?$category_name:substr($category_name,0,98)."..");
  $forum_category='<a href="'.tep_href_link(PATH_TO_FORUM,'category='.$ide).'" class="text-dark">'.tep_db_output($key).'</a>';
  $forum_query  = "select * from ".FORUM_TABLE." where category_id=".tep_db_input($ide)." and is_show ='Yes' and show_date <= curdate() order by id  asc";
  $forum_result = tep_db_query($forum_query);
  $total_forum=tep_db_num_rows($forum_result);

  $j=1;
  $forum='';
  if($total_forum)
  {
   $table_names1 = FORUM_TOPICS_TABLE." as t left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (t.user_id =jl.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (t.user_id =rl.recruiter_id && t.user_type='recruiter') ";
   $table_names2 = FORUM_TOPICS_TABLE." as t  left outer join ".TOPIC_REPLY_TABLE." as tr on ( t.id = tr.topic_id ) left outer join ".JOBSEEKER_LOGIN_TABLE." as jl on (tr.user_id =jl.jobseeker_id && tr.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (tr.user_id =rl.recruiter_id && tr.user_type='recruiter') ";
   while($forum_row = tep_db_fetch_array($forum_result))
   {

    $topic = getAnyTableWhereData($table_names1," t.forum_id='".$forum_row['id']."'  and  (jl.jobseeker_status='Yes' or rl.recruiter_status='Yes') ","count(*) as total");
    $topics_total=$topic['total'];

    $forum_desc=(strlen($forum_row['description'])>200?substr($forum_row['description'],0,197).'...':$forum_row['description']);

    $total_reply = getAnyTableWhereData($table_names2," t.forum_id='".$forum_row['id']."'  and  (jl.jobseeker_status='Yes' or rl.recruiter_status='Yes') ","count(*) as total");
    $total_reply=$total_reply['total'];
    
    //$row_selected=' class="dataTableRow'.($j%2==1?'2':'1').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $forum_link=getPermalink('forum_topics',array('ide'=>$forum_row['id'],'seo_name'=>encode_forum($forum_row['title'])));

    $forum_description = (tep_not_null($category)) ? $forum_desc : '';

    $forum.='<tr class="align-items-center">
              <td width="25"  '.$forum_row_class.' ><div align="">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text d-flex" viewBox="0 0 16 16">
                  <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                  <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                </svg>
                </div>
              </td>
              <td valign="top" '.$forum_row_class.'>
               <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                 <td><h5 class="m-0"><a href="'.$forum_link.'" class="">'.$forum_row['title'].'</a></h5></td>
                </tr>
                <tr>
                 <td class="forum_content">'.$forum_description.'</td>
                </tr>
               </table>
              </td>
              <td '.$forum_row_class1.'>'.$topics_total.'</td>
              <td '.$forum_row_class1.'>'.$total_reply.'</td>
             </tr>';

    $j++;
   }
  }
  else
  {
   $forum.='<tr >
          <td colspan="4" height="5"></td>
         </tr>';

  }

   
  $template->assign_block_vars('forum_result', array( 
                               'forum'=> $forum,
                               'forum_category'=> $forum_category,
                               ));  
  
  $i++;
}
tep_db_free_result($forum_result);
tep_db_free_result($result);
}
if(tep_not_null($category) && tep_not_null($category_name))
{
 $forum_header_link='<table width="100%" border="0" cellpadding="4" cellspacing="1" ><tr ><td  class="forum_sub_heading"><a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading">'.INFO_TEXT_HOME.'</a>';
 $forum_header_link.='&nbsp;&gt;&gt;&nbsp;'.tep_db_output($category_name).'</td></tr></table>';
 $page_title='<title>'.tep_db_output(ucfirst($category_name)).' Forum</title> ';
}
else
{
 $page_title='';
  $forum_header_link='';
}

/****************end of job category******************/
$template->assign_vars(array(
 'HEADING_TITLE' => HEADING_TITLE,
 'INFO_TEXT_HEADING_SEARCH' => '<a class="btn btn-sm btn-primary mm-minus" href="'.tep_href_link(PATH_TO_FORUM.FILENAME_FORUM_SEARCH).'" class="forum_heading_link">'.tep_db_output(INFO_TEXT_HEADING_SEARCH).' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search ms-2" viewBox="0 0 16 16">
  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
</svg></a>',
	'page_title'=> $page_title,
 'INFO_TEXT_FORUM_HEADER_LINK' => $forum_header_link,
	'TEXT_INFO_THREADS'=>TEXT_INFO_THREADS,
	'TEXT_INFO_POSTS'  =>TEXT_INFO_POSTS,
	'LEFT_HTML'=>(check_login("recruiter")?LEFT_HTML:(check_login("jobseeker")?LEFT_HTML_JOBSEEKER:'')),
 'update_message'=> $messageStack->output()));
$template->pparse('index');
?>