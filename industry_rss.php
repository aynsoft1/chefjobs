<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_INDUSTRY_RSS);
$template->set_filenames(array('industry_rss' => 'industry_rss.htm'));
include_once(FILENAME_BODY);

$field_names="id,category_name";
$whereClause=" where 1 ";
$query11 = "select $field_names from ".JOB_CATEGORY_TABLE." $whereClause  order by  category_name  asc  ";
$result11=tep_db_query($query11);
$count=1;
$now=date('Y-m-d H:i:s');
while($row11 = tep_db_fetch_array($result11))
{
 $ide=$row11["id"];
 if($count%3==1 )
 { $query="select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$ide.")"; 
   $job_result=tep_db_query($query);
   $x=tep_db_num_rows($job_result);
  $template->assign_block_vars('job_rss', array( 
                               'category_name'=>tep_db_output($row11['category_name']),
                               'link_rss'=>'<a href="'.tep_href_link('rss/'.$ide.'.xml').'"  title="'.tep_db_output($row11['category_name']).' Jobs RSS">'.($x>0?'<img src="img/rss_green.jpg">':'<img src="img/rss.jpg">').'</a>',
                               ));
 }
 elseif($count%3==2 )
 {
		$query="select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$ide.")"; 
  	$job_result=tep_db_query($query);
			$x=tep_db_num_rows($job_result);
  $template->assign_block_vars('job_rss1', array( 
                               'category_name'=>tep_db_output($row11['category_name']),
                               'link_rss'=>'<a href="'.tep_href_link('rss/'.$ide.'.xml').'"  title="'.tep_db_output($row11['category_name']).' Jobs RSS">'.($x>0?'<img src="img/rss_green.jpg">':'<img src="img/rss.jpg">').'</a>',
                               ));
 }
 else
 {
		$query="select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$ide.")"; 
  	$job_result=tep_db_query($query);
			$x=tep_db_num_rows($job_result);
			$template->assign_block_vars('job_rss2', array( 
                               'category_name'=>tep_db_output($row11['category_name']),
                               'link_rss'=>'<a href="'.tep_href_link('rss/'.$ide.'.xml').'"  title="'.tep_db_output($row11['category_name']).' Jobs RSS">'.($x>0?'<img src="img/rss_green.jpg">':'<img src="img/rss.jpg">').'</a>',
                               ));
	 }
 $count++;
}
tep_db_free_result($result11);

$jobs_query="select distinct (j.job_id) from ".JOB_TABLE."  as j where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')"; 
$jobs_result=tep_db_query($jobs_query);
$y=tep_db_num_rows($jobs_result);
		
$template->assign_vars(array(
 'HEADING_TITLE'    => HEADING_TITLE,
 'INFO_TEXT_ALL_JOB_RSS' =>INFO_TEXT_ALL_JOB_RSS,
 'ALL_RSS'          => '<a href="'.tep_href_link('rss/all_jobs.xml').'">'.($y>0?'<img src="img/rss_green.jpg">':'<img src="img/rss.jpg">').'</a>',
 'FORUM_RSS'        => '<a href="'.tep_href_link('rss/forums.xml').'"><img src="img/rss.jpg"></a>',
 'TOPIC_RSS'        => '<a href="'.tep_href_link('rss/topics.xml').'"><img src="img/rss.jpg"></a>',
 'REPLY_RSS'        => '<a href="'.tep_href_link('rss/replies.xml').'">'.($y>0?'<img src="img/rss_green.jpg">':'<img src="img/rss.jpg">').'</a>',
 'LEFT_BOX_WIDTH'   => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'        => LEFT_HTML,
 'RIGHT_HTML'       => RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('industry_rss');
?>