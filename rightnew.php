<?
$job_search_form=tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search');
$key=tep_draw_input_field('keyword','','class="form-control2" placeholder="Keyword"',false);
$locat= LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control2'","All Locations","",DEFAULT_COUNTRY_ID);
$button= ' <button type="submit" class="btn btn-default">Search Now</button>';

$now=date('Y-m-d H:i:s');
$table_namesrt=JOB_TABLE." as j,".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
$whereClausert="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//
$field_namesrt="j.job_id, j.job_title, j.job_salary, j.job_location,j.job_short_description,j.inserted, r.recruiter_company_name,job_country_id,r.recruiter_logo";
$queryrt = "select $field_namesrt from $table_namesrt where $whereClausert order by j.inserted desc limit 0,4" ;
//echo "<br>$queryrt";//exit;
$resultrt=tep_db_query($queryrt);
$x=tep_db_num_rows($resultrt);
//echo $x;exit;
$count=1;
$display_latest_jobatrt='';
while($rowrt = tep_db_fetch_array($resultrt))
{
 $ide=$rowrt["job_id"];
 $title_format=encode_category($rowrt['job_title']);
 $query_string=encode_string("job_id=".$ide."=job_id");


$description=(tep_not_null(strlen($rowrt['job_short_description']))>100?substr($rowrt['job_short_description'],0,98).'..':$rowrt['job_short_description']);
 $title=' <a href="'.tep_href_link($ide.'/'.$title_format.'.html').'" target="_blank">'.$rowrt['job_title'].'</a>';

 $country=get_name_from_table(COUNTRIES_TABLE, 'country_name', 'id',tep_db_output($rowrt['job_country_id']));
 $location=tep_db_output($rowrt['job_location']);
 $company_address=tep_not_null($location)?"$location, $country":"$country";
 $date =((tep_not_null($rowrt['expired'] && !$hide_date) )?formate_date(tep_db_output($rowrt['expired']),'d-M-Y'):'');
$display_latest_jobatrt.='<div class="jobs-block">
                                                                                        <h5>'.$title.'</h5>
                                                                                        <p class="company">'.$company.'</p>
                                                                                        <p class="location">'.$location.'</p>
                                                                                    </div>';
 $count++;
}


if(strtolower($_SERVER['PHP_SELF'])!="/".PATH_TO_MAIN.FILENAME_INDEX)
{
 	$right_banner =banner_display("4",1);
 define('RIGHT_HTML','<td width="30" valign="top">&nbsp;</td>
                                                    <td width="250" valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td class="gray-box">
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td>
                                                                                <div class="sidebar-title">Start New Job Search</div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                '.$job_search_form.'
                                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                        <tr>
                                                                                            <td>'.$key.'</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>'.$locat.'</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>'.$button.'</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </form>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="gray-box">
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td>Latest Jobs</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div class="job-widget-right">'.$display_latest_jobatrt.'
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>&nbsp;</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
</td>');
}
?>