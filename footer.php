<?php
$admin_control_panel='';
$show_page_parse_time='';
if(check_login("admin"))
{
 $admin_control_panel="\n".
 '      <table border="0" cellspacing="0" cellpadding="0">'."\n".
 '       <tr>'."\n".
 '        <td align="middle" class="footer12"><a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONTROL_PANEL).'"><b>"'.INFO_TEXT_ADMIN_CONTROL_PANEL.'"</a>&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_LOGOUT).'"><b>"'.INFO_TEXT_LOG_OUT.'"</a></td>'."\n".
 '       </tr>'."\n".
 '      </table>';
 if (DISPLAY_PAGE_PARSE_TIME == 'true')
 {
  if (!is_object($logger))
   $logger = new logger;
  $show_page_parse_time=$logger->timer_stop(DISPLAY_PAGE_PARSE_TIME);
 }
}
define('FOOTER_HTML','</div>'.$admin_control_panel.'
 </body>
</html>
');
?>