<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_PAGE_RANK);
$template->set_filenames(array('page_rank' => 'admin1_page_rank.htm'));
include_once(FILENAME_ADMIN_BODY);
///////////// Middle Values 
$sort_array=array("date","google_rank"," if(alexa_rank=0 or alexa_rank is null,1,0),alexa_rank");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'date desc');
$order_by_clause=$obj_sort_by_clause->return_value;

$page_ranks_query_raw="select date,google_rank,alexa_rank from " . PAGE_RANK_TABLE ." order by ".$order_by_clause;
$page_ranks_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $page_ranks_query_raw, $page_ranks_query_numrows);
$page_ranks_query = tep_db_query($page_ranks_query_raw);
if(tep_db_num_rows($page_ranks_query) > 0)
{
 $alternate=1;
 $curr_year_month=date('Y-m');
 while ($page_ranks = tep_db_fetch_array($page_ranks_query)) 
 {
  if($page_ranks['date']=='0000-00-00')
  continue;
  $row_date=explode('-',$page_ranks['date']);
  if($row_date[0].'-'.$row_date[1]==$curr_year_month)
  {
   $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  }
  $alternate++;  
  $template->assign_block_vars('search_tag', array( 'row_selected' => $row_selected,
                                                    'month' =>tep_db_output(formate_date1($page_ranks['date'],'%b %Y')),
                                                    'google_rank' => tep_db_output($page_ranks['google_rank']),                                                    
                                                    'alexa_rank'=>$page_ranks['alexa_rank']==0?'': tep_db_output($page_ranks['alexa_rank']),
                                                    ));
 }
 tep_db_free_result($page_ranks_query);
}

$template->assign_vars(array(
 'TABLE_HEADING_RANK_DATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_PAGE_RANK, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_RANK_DATE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_GOOGLE_PAGERANK'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_PAGE_RANK, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_GOOGLE_PAGERANK.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_ALEXA_PAGERANK'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_PAGE_RANK, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_ALEXA_PAGERANK.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'count_rows'=>$page_ranks_split->display_count($page_ranks_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAGE_RANKS),
 'no_of_pages'=>$page_ranks_split->display_links($page_ranks_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','id','action'))),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('page_rank');
?>