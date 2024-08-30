<?
if(strtolower($_SERVER['PHP_SELF'])!="/".PATH_TO_MAIN.FILENAME_INDEX)
{
 	$right_banner =banner_display("4",1);
 define('RIGHT_HTML','
 <td width="14%" valign="top">
                    <!-- Right Sidebar -->
                    <div class="right-sidebar">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>'.$right_banner[0].'</td>
                      </tr>
                    </table>
                    </div>
                    <!-- Right Sidebar End-->
                    </td>
');
}
?>