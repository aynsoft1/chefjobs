<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
$admin_control_panel='';
$show_page_parse_time='';
if(check_login("admin"))
{
 $admin_control_panel="\n".

 '        
 <a class="mr-4" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONTROL_PANEL).'">'.FOOTER_TITLE_CONTROL_PANEL.'</a>
 <a class="mr-4" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_LOGOUT).'">'.FOOTER_TITLE_LOGOUT.'</a></td>'."\n".
 '       '."\n".
 '     Powered by <a href="http://ejobsitesoftware.com/">Job Board Software</a>
 ';
	if (DISPLAY_PAGE_PARSE_TIME == 'true')
	{
		if (!is_object($logger))
			$logger = new logger;
		$show_page_parse_time=$logger->timer_stop(DISPLAY_PAGE_PARSE_TIME);
	}
}
$ADMIN_FOOTER_HTML='
<footer class="p-2 text-center" style="position: relative;bottom: 0;">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				'.$show_page_parse_time.'
				'.$admin_control_panel.'
			</div>
		</div>
	</div>
</footer>
 <script src="'.tep_href_link("css/js/bootstrap.bundle.min.js").'"></script>
<script src="'.tep_href_link("jscript/common.js").'"></script>
<script src="'.tep_href_link("jscript/page.js").'"></script>
<!--<script src="'.tep_href_link("jscript/counting.js").'"></script>
<script src="'.tep_href_link("jscript/optionlist.js").'"></script>-->
<script src="numscroller-1.0.js"></script>
<script src="lib/prism.js"></script>

<!--
<script src="'.HOST_NAME.'jscript/admin_js/error_success_message_timeout.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
	  //-initialize the javascript
	  App.init();
	  App.dashboard();
  
  });
</script>
-->
<script src="'.tep_href_link("jscript/admin_notification.js").'"></script>
</body>
</html>' ;
?>