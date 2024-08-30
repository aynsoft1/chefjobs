<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_FORUM_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_FORUM_SEARCH);
$template->set_filenames(array('forum_search' => 'forum_search.htm','search_result'=> 'forum_result.htm'));
include_once("../".FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'forum_search.js';

$search_within_array  = array();
$search_within_array[]= array('id'=>'','text'=>'All title and description ');
$search_within_array[]= array('id'=>'title','text'=>'title Only');
$search_within_array[]= array('id'=>'description','text'=>'description Only');

$search_posted_array  = array();
$search_posted_array[]= array('id'=>'','text'=>'All Posts');
$search_posted_array[]= array('id'=>'0','text'=>'Today');
$search_posted_array[]= array('id'=>'1','text'=>'Yesterday');
$search_posted_array[]= array('id'=>'7','text'=>'Last 1 week');
$search_posted_array[]= array('id'=>'14','text'=>'Last 2 weeks');
$search_posted_array[]= array('id'=>'21','text'=>'Last 3 weeks');
$search_posted_array[]= array('id'=>'30','text'=>'Last 30 days');
$search_posted_array[]= array('id'=>'90','text'=>'Last 90 days');
$search_posted_array[]= array('id'=>'custom','text'=>'custom search');

$action           = tep_db_prepare_input($_POST['action']);
$error=false;

if($action=="search")
{
 $keyword           = tep_db_prepare_input($_POST['keyword']);
 $word1             = tep_db_prepare_input($_POST['word1']);
 $search_within     = tep_db_prepare_input($_POST['search_within']);
 $category          = tep_db_prepare_input($_POST['category']);
 $forum_id          = tep_db_prepare_input($_POST['forum_id']);
 $posted            = tep_db_prepare_input($_POST['posted']);
 $search_id         = tep_db_prepare_input($_POST['search_id']);
 $s_date            = tep_db_prepare_input($_POST['s_date']);
 $s_month           = tep_db_prepare_input($_POST['s_month']);
 $s_year            = tep_db_prepare_input($_POST['s_year']);
 $e_date            = tep_db_prepare_input($_POST['e_date']);
 $e_month           = tep_db_prepare_input($_POST['e_month']);
 $e_year            = tep_db_prepare_input($_POST['e_year']);
 $security_code1    = tep_db_prepare_input($_POST['TR_security_code']);
 $start_date        = $s_year.'-'.$s_month.'-'.$s_date;
 $end_date          = $e_year.'-'.$e_month.'-'.$e_date;
 $search_key  = array ("'[\s]+'");                    
 $replace_key = array (" ");
 $keyword = preg_replace($search_key, $replace_key, $keyword);
 if((MODULE_FORUM_SEARCH_CAPTCHA=='enable' ))
 {
  if($search_id!=$_SESSION['SESSION_FORUM_SEARCH'] || $search_id=='' )
  {
   unset($_SESSION['SESSION_FORUM_SEARCH']);
   if($security_code1!=$_SESSION['security_code']  )
   {
    $error=true;
    $messageStack->add(ENTRY_SECURITY_CODE_ERROR,'error');
   }
  }
 }

//print_r($_POST);
if(!$error)
 {
  if(!isset($_SESSION['SESSION_FORUM_SEARCH']))
  {
   $search_id=md5(date("Y-m-d H:i:s"));
   $_SESSION['SESSION_FORUM_SEARCH']=$search_id;
  }
  $whereClause='';
  $action        = tep_db_prepare_input($_POST['action']);
  $field         = tep_db_prepare_input($_POST['field']);
  $order         = tep_db_prepare_input($_POST['order']);
  $lower         = (int)tep_db_prepare_input($_POST['lower']);
  $higher        = (int)tep_db_prepare_input($_POST['higher']);
  $hidden_fields.= tep_draw_hidden_field('action',$action);
  $hidden_fields.= tep_draw_hidden_field('word1',$word1);
  $hidden_fields.=tep_draw_hidden_field('posted',$posted);
  $hidden_fields.=tep_draw_hidden_field('s_year',$s_year);
  $hidden_fields.=tep_draw_hidden_field('s_month',$s_month);
  $hidden_fields.=tep_draw_hidden_field('s_date',$s_date);
  $hidden_fields.=tep_draw_hidden_field('e_year',$e_year);
  $hidden_fields.=tep_draw_hidden_field('e_month',$e_month);
  $hidden_fields.=tep_draw_hidden_field('e_date',$e_date);
  $hidden_fields.=tep_draw_hidden_field('search_id',$search_id);

  if(tep_not_null($keyword)) //   keyword search starts ///
  {
   $hidden_fields.=tep_draw_hidden_field('keyword',$keyword);
   $explode_string=array($keyword);
   if($word1=='Yes')
   $explode_string=explode(' ',$keyword);
   $total_keys = count($explode_string);
   $key_search=false;
   $whereClause1='';
   for($i=0;$i<$total_keys;$i++)
   {
    if(strlen($explode_string[$i])< (int) MODULE_FORUM_SEARCH_MIN_KEYWORD_LENGTH)
     continue;
    if($search_within=='' || $search_within=='title')
    $whereClause1.=" t.title  like '%".tep_db_input($explode_string[$i])."%' or ";
    if($search_within=='' || $search_within=='description')
    $whereClause1.=" t.description like '%".tep_db_input($explode_string[$i])."%' or ";
    $key_search=true;
   }
   if($key_search && $whereClause1!='')
   {
    $whereClause1=substr($whereClause1,0,-4);
    $whereClause1='('.$whereClause1.')';
   }
   $whereClause= $whereClause1;
  }
  if(tep_not_null($category)) //   category search  ///
  {
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   $hidden_fields.=tep_draw_hidden_field('category',$category);
   $whereClause .=" f.category_id ='".tep_db_input($category)."'";
  }
  if(tep_not_null($forum_id)) //   forum search  ///
  {
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   $hidden_fields.=tep_draw_hidden_field('forum_id',$forum_id);
   $whereClause .=" t.forum_id ='".tep_db_input($forum_id)."'";
  }
  if(tep_not_null($posted)) //   posted  ///
  {
   $search_date=get_search_date($posted,$s_year,$s_month,$s_date,$e_year,$e_month,$e_date);
   if($search_date=get_search_date($posted,$s_year,$s_month,$s_date,$e_year,$e_month,$e_date))
   { 
    if(tep_not_null($search_date['search_from']) && tep_not_null($search_date['search_to']))
    {
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
     $whereClause .=" t.inserted <= '".tep_db_input($search_date['search_from'])."' and  t.inserted >= '".tep_db_input($search_date['search_to'])."' ";
    }
   }
  }
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause .=" f.is_show ='Yes' and f.show_date <= curdate() and (jl.jobseeker_status='Yes' or rl.recruiter_status='Yes') ";
  $table_names=FORUM_TOPICS_TABLE." as t left outer join ".FORUM_TABLE." as f on (t.forum_id=f.id) left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on (t.user_id =jl.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_LOGIN_TABLE." as rl  on (t.user_id =rl.recruiter_id && t.user_type='recruiter') left outer join  ".JOBSEEKER_TABLE." as j on (t.user_id =j.jobseeker_id && t.user_type='jobseeker') left outer join ".RECRUITER_TABLE." as r  on (t.user_id =r.recruiter_id && t.user_type='recruiter') ";
  $field_names="t.id as topic_id ,t.title,t.description,if(t.user_type='jobseeker',j.jobseeker_first_name,r.recruiter_first_name) as first_name,if(t.user_type='jobseeker',j.jobseeker_last_name,r.recruiter_last_name) as last_name,t.hits,t.inserted";
  $query1 = "select count(t.id) as x1 from $table_names where $whereClause ";
  $result1=tep_db_query($query1);
  $tt_row=tep_db_fetch_array($result1);
  $x1=$tt_row['x1'];
  include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
  $sort_array=array("t.featured asc,t.inserted","t.title",'t.inserted');
  $obj_sort_by_clause=new sort_by_clause($sort_array,'t.featured asc,t.inserted desc');
  $order_by_clause=$obj_sort_by_clause->return_value;
  $see_before_page_number_array  = see_before_page_number($sort_array,$field,'t.featured asc,t.inserted ',$order,'desc',$lower,'0',$higher,(int) MODULE_FORUM_SEARCH_MAX_RESULT_DISPLAY);
  $lower=$see_before_page_number_array['lower'];
  $higher=$see_before_page_number_array['higher'];
  $field=$see_before_page_number_array['field'];
  $order=$see_before_page_number_array['order'];
  $hidden_fields.=tep_draw_hidden_field('sort',$sort);

  $totalpage=ceil($x1/$higher);
  $query = "select $field_names from $table_names where $whereClause ORDER BY $field $order limit $lower,$higher ";
  $query_result=tep_db_query($query);
  //echo "<br>$query";//exit;
  $x=tep_db_num_rows($query_result);
  //echo $x;exit;
  $pno= ceil($lower+$higher)/($higher);
  if($x > 0 && $x1 > 0)
  {
   $alternate=1;
   $i=0;
   while ($result_row = tep_db_fetch_array($query_result))
   {
    //$row_selected=' class="dataTableRow'.($i%2==1?'2':'1').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
				$row_selected='class="forum_row"';
    $ide=$result_row['topic_id'];
    $key=((strlen($result_row['title'])<150)?$result_row['title']:substr($result_row['title'],0,148)."..");
    $key1=$result_row['title'];
    $post_link=getPermalink('topic_details',array('ide'=>$ide,'seo_name'=>encode_forum($key))) ;//tep_href_link(PATH_TO_FORUM.encode_forum($key).'_'.$ide.'.html');
    $replies = getAnyTableWhereData(TOPIC_REPLY_TABLE,"topic_id='".$ide."'","count(*) as total");
 
    $template->assign_block_vars('search_result', array(
                                 'row_selected' => $row_selected,
                                 'title'        => '<a href="'.$post_link.'"  title="'.tep_db_input($key1).'" class="forum_title text-info fw-bold"  target="_blank">'.tep_db_output($key).'</a>',
                                 'description'  => nl2br(stripslashes(substr($result_row['description'],0,200)).' ...'),
                                 'more_link'    => '<a href="'.$post_link.'" class="forum_more" target="_blank">More >></a>',
                                 'posted_by'    => tep_db_output($result_row['first_name'].' '.$result_row['last_name']),
                                 'reply'        => $replies['total'],
                                 'view'         => $result_row['hits'],
                                'inserted'      => tep_date_long($result_row['inserted']),

                                ));
    $lower = $lower + 1;
    $i++;
   }
   see_page_number();
   $plural=($x1=="1")?"Post":"Posts";
   $template->assign_vars(array('total'=>SITE_TITLE." has matched <font color='red'><b>$x1</b></font> ".$plural." to your search criteria."));
  }
  else
   $template->assign_vars(array('total'=>SITE_TITLE." has not matched any data to your search criteria."));
  tep_db_free_result($query_result);
 }
}

$forum_header_link='<a href="'.tep_href_link(PATH_TO_FORUM).'" class="forum_sub_heading">'.INFO_TEXT_HOME.'</a>';
$forum_header_link.='&nbsp;&gt;&gt;&nbsp;<a href="'.tep_href_link(PATH_TO_FORUM.FILENAME_FORUM_SEARCH).'" class="forum_sub_heading">'.INFO_TEXT_HEADING_SEAECH.'</a>';

if($action=='search' &&!$error)
{
 $template->assign_vars(array(
	'HEADING_TITLE'               => '<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
 'INFO_TEXT_FORUM_HEADER_LINK' => $forum_header_link,
 'INFO_TEXT_HIDE_START'        => ($x)?'':'<!--',
 'INFO_TEXT_HIDE_END'          => ($x)?'':'-->',
 'hidden_fields'               => $hidden_fields,
 'TABLE_HEADING_TOPICS'        => TABLE_HEADING_TOPICS,
 'TABLE_HEADING_REPLIES'       => TABLE_HEADING_REPLIES,
 'TABLE_HEADING_AUTHOR'        => TABLE_HEADING_AUTHOR,
 'TABLE_HEADING_VIEW'          => TABLE_HEADING_VIEW,
 'update_message' => $messageStack->output()));
 $template->pparse('search_result');
}
else
{
 if(!$error)
 {
  $start_date =date("Y-m-d");
  $end_date =date("Y-m-d", mktime(0, 0, 0, date("m")-1,date("d"), date("Y")));
  unset($_SESSION['SESSION_SEARCH']);
 } 

 /****************end of job category******************/
 $forum_query ='select f.title as forum_title,f.id ,c.category_name from '.FORUM_TABLE.' as f left outer join '.FORUM_CATEGORIES_TABLE.' as c  on (f.category_id=c.id) where  is_show ="Yes" and show_date <= curdate() order by  f.title,c.category_name';

 $template->assign_vars(array(
		'HEADING_TITLE'              =>'<a href="'.tep_href_link(PATH_TO_FORUM).'"  >'.HEADING_TITLE.'</a>',
  'INFO_TEXT_FORUM_HEADER_LINK'=> $forum_header_link,
  'form'                       => tep_draw_form('search',PATH_TO_FORUM.FILENAME_FORUM_SEARCH,'','post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','search'),
  'INFO_TEXT_KEAYWORD'         => INFO_TEXT_KEAYWORD,
  'INFO_TEXT_KEAYWORD1'        => tep_draw_input_field('keyword',$keyword,'size="45" class="form-control" placeholder="Enter keyword"'),
  'INFO_TEXT_KEYWORD_CRITERIA' => INFO_TEXT_KEYWORD_CRITERIA,
  'INFO_TEXT_KEYWORD_CRITERIA1'=> '<div class="form-check form-check-inline">'.tep_draw_radio_field('word1', 'Yes', '' , $word1,'id="radio_word1" class="form-check-input"').'<label class="form-check-label" for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label></div><div class="form-check form-check-inline">'.tep_draw_radio_field('word1', 'No', true, $word1,'id="radio_word2" class="form-check-input"').'<label class="form-check-label" for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label></div>',
  'INFO_TEXT_SEARCH_WITHIN'    => INFO_TEXT_SEARCH_WITHIN, 
  'INFO_TEXT_SEARCH_WITHIN1'   => tep_draw_pull_down_menu('search_within', $search_within_array, $search_within,'class="form-select"'),
  'INFO_TEXT_SEARCH_CATEGORY'  => INFO_TEXT_SEARCH_CATEGORY,
  'INFO_TEXT_SEARCH_CATEGORY1' => LIST_SET_DATA(FORUM_CATEGORIES_TABLE,"",'category_name','id',"category_name","name='category' class='form-select'" ,'All categories ','',$category),
  'INFO_TEXT_SEARCH_FORUM'     => INFO_TEXT_SEARCH_FORUM,
//  'INFO_TEXT_SEARCH_FORUM1'    => LIST_TABLE2(FORUM_TABLE.'as f',"where is_show ='Yes' and show_date <= curdate()",'title','id',"title","name='forum_id'" ,'All ','',$forum_id),
  'INFO_TEXT_SEARCH_FORUM1'    => LIST_TABLE2(0,'',"",'forum_title','category_name','id',$order_by='',$addoption_value="",$addstart="" ,$addmiddle=" (",$addend=")", $forum_query,"name='forum_id' class='form-select'","All forum title","",$selected="",$footer="",$footer_value=""),
 
  'INFO_TEXT_FORUM_POSTED'     => INFO_TEXT_FORUM_POSTED,
  'INFO_TEXT_FORUM_POSTED1'    => tep_draw_pull_down_menu('posted', $search_posted_array, $posted,'onchange="show_custom_search();" id="search_time" class="form-select"'),
  'INFO_TEXT_SEARCH_FROM_DATE' => datelisting($start_date,'name="s_date" class="form-select"','name="s_month" class="form-select"','name="s_year" class="form-select"','2009',date('Y'),'','',false),
  'INFO_TEXT_SEARCH_TO_DATE'   => datelisting($end_date,'name="e_date" class="form-select"','name="e_month" class="form-select"','name="e_year" class="form-select"','2009',date('Y'),'','',false),

  'INFO_TEXT_SECURITY_CODE' => ((MODULE_FORUM_SEARCH_CAPTCHA!='enable')?'':INFO_TEXT_SECURITY_CODE), 
  'INFO_TEXT_SECURITY_CODE1'=> ((MODULE_FORUM_SEARCH_CAPTCHA!='enable')?'':tep_draw_input_field('TR_security_code','','',true)),
  'INFO_TEXT_TYPE_CODE'     => ((MODULE_FORUM_SEARCH_CAPTCHA!='enable')?'':'<img src="../AynSecurityImages.php" />'.INFO_TEXT_TYPE_CODE),     
  'INFO_TEXT_CAPTCHA_HIDE'  => ((MODULE_FORUM_SEARCH_CAPTCHA!='enable')?'<!--':''),
  'INFO_TEXT_CAPTCHA_HIDE1' => ((MODULE_FORUM_SEARCH_CAPTCHA!='enable')?'-->':''),     
  'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>' ,
   
  // 'button' => tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
  'button'=>tep_button_submit('btn btn-primary px-4','Search'),
  'update_message'=> $messageStack->output()));
 $template->pparse('forum_search');
}
?>