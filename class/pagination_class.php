<?
class Pagination_class{
	var $result;
	var $anchors;
	var $total;
	function __construct($qry,$starting,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius)
	{
		$rst		=	tep_db_query($qry);// or die(mysql_error());
		$numrows	=	tep_db_num_rows($rst);
		$qry		 .=	" limit $starting, $recpage";
		$this->result	=	tep_db_query($qry) ;//or die(mysql_error());
		$next		=	$starting+$recpage;
		$var		=	((intval($numrows/$recpage))-1)*$recpage;
		$page_showing	=	intval($starting/$recpage)+1;
		$total_page	=	ceil($numrows/$recpage);

		if($numrows % $recpage != 0){
			$last = ((intval($numrows/$recpage)))*$recpage;
		}else{
			$last = ((intval($numrows/$recpage))-1)*$recpage;
		}
		$previous = $starting-$recpage;
		$anc = "<ul id='pagination-flickr'>";
		if($previous < 0){
			$anc .= "<li class='previous-off'>".tep_db_output(INFO_TEXT_PAGE_FIRST)."</li>";
			$anc .= "<li class='previous-off'>".tep_db_output(INFO_TEXT_PAGE_PREVIOUS)."</li>";
		}else{
			$anc .= "<li class='next'><a href='javascript:pagination(0,\"".$keyword."\",\"".$location."\",\"".$word1."\",\"".$country."\",\"".$state."\",\"".$job_category[0]."\",\"".$experience."\",\"".$job_post_day."\",\"".$search_zip_code."\",\"".$zip_code."\",\"".$radius."\",\"".HOST_NAME."\");'>".tep_db_output(INFO_TEXT_PAGE_FIRST)."</a></li>";
			$anc .= "<li class='next'><a href='javascript:pagination($previous,\"".$keyword."\",\"".$location."\",\"".$word1."\",\"".$country."\",\"".$state."\",\"".$job_category[0]."\",\"".$experience."\",\"".$job_post_day."\",\"".$search_zip_code."\",\"".$zip_code."\",\"".$radius."\",\"".HOST_NAME."\");'>".tep_db_output(INFO_TEXT_PAGE_PREVIOUS)."</a></li>";
		}
		
		################If you dont want the numbers just comment this block###############	
		$norepeat = 4;//no of pages showing in the left and right side of the current page in the anchors 
		$j = 1;
		$anch = "";
		for($i=$page_showing; $i>1; $i--){
			$fpreviousPage = $i-1;
			$page = ceil($fpreviousPage*$recpage)-$recpage;
			$anch = "<li><a href='javascript:pagination($page,\"".$keyword."\",\"".$location."\",\"".$word1."\",\"".$country."\",\"".$state."\",\"".$job_category[0]."\",\"".$experience."\",\"".$job_post_day."\",\"".$search_zip_code."\",\"".$zip_code."\",\"".$radius."\",\"".HOST_NAME."\");'>$fpreviousPage </a></li>".$anch;
			if($j == $norepeat) break;
			$j++;
		}
		$anc .= $anch;
		$anc .= "<li class='active'>".$page_showing."</li>";
		$j = 1;
		for($i=$page_showing; $i<$total_page; $i++){
			$fnextPage = $i+1;
			$page = ceil($fnextPage*$recpage)-$recpage;
			$anc .= "<li><a href='javascript:pagination($page,\"".$keyword."\",\"".$location."\",\"".$word1."\",\"".$country."\",\"".$state."\",\"".$job_category[0]."\",\"".$experience."\",\"".$job_post_day."\",\"".$search_zip_code."\",\"".$zip_code."\",\"".$radius."\",\"".HOST_NAME."\");'>$fnextPage</a></li>";
			if($j==$norepeat) break;
			$j++;
		}
		############################################################
		if($next >= $numrows){
			$anc .= "<li class='previous-off'>".tep_db_output(INFO_TEXT_PAGE_NEXT)."</li>";
			$anc .= "<li class='previous-off'>".tep_db_output(INFO_TEXT_PAGE_LAST)."</li>";
		}else{
			$anc .= "<li class='next'><a href='javascript:pagination($next,\"".$keyword."\",\"".$location."\",\"".$word1."\",\"".$country."\",\"".$state."\",\"".$job_category[0]."\",\"".$experience."\",\"".$job_post_day."\",\"".$search_zip_code."\",\"".$zip_code."\",\"".$radius."\",\"".HOST_NAME."\");'>".tep_db_output(INFO_TEXT_PAGE_NEXT)."</a></li>";
			$anc .= "<li class='next'><a href='javascript:pagination($last,\"".$keyword."\",\"".$location."\",\"".$word1."\",\"".$country."\",\"".$state."\",\"".$job_category[0]."\",\"".$experience."\",\"".$job_post_day."\",\"".$search_zip_code."\",\"".$zip_code."\",\"".$radius."\",\"".HOST_NAME."\");'>".tep_db_output(INFO_TEXT_PAGE_LAST)."</a></li>";
		}
			$anc .= "</ul>";
		$this->anchors = $anc;
		
		$this->total = "Page : $page_showing  of  $total_page";
	}
}
?>