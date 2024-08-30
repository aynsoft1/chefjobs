<?
/*-----------------------SEARCH CODE---------------------------------------------------------*/
$job_search_form=tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search');
$key=tep_draw_input_field('keyword','','class="form-control" type="search" placeholder="Enter Keywords"',false);
$locat= LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control'","All Locations","",DEFAULT_COUNTRY_ID);
$experience_1=experience_drop_down('name="experience" class="form-control"', 'Experience', '', $experience);

$button= '<button type="submit" class="btn btn-default"><i class="fa fa-search" aria-hidden="true"></i></button>';
/********************************  SEARCH CODE ENDS********************************************* */

if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_INDEX)
{
 define('HEADER_MIDDLE_HTML','    <!-- Header Carousel -->
    <header id="myCarousel" class="carousel slide">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
			<li data-target="#myCarousel" data-slide-to="3"></li>
			<li data-target="#myCarousel" data-slide-to="4"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <div class="item active">
                <div class="fill img-responsive" style="background-image:url(\'themes/theme10/images/slider1.jpg\');"></div>
                <div class="carousel-caption">
                    <h2>Find the career you deserve</h2>
					<p>Your job search starts and ends with us.</p>
                </div>
            </div>
            <div class="item">
                <div class="fill img-responsive" style="background-image:url(\'themes/theme10/images/slider2.jpg\');"></div>
                <div class="carousel-caption">
                    <h2>Find the career you deserve</h2>
					<p>Your job search starts and ends with us.</p>
                </div>
            </div>
            <div class="item">
                <div class="fill img-responsive" style="background-image:url(\'themes/theme10/images/slider3.jpg\');"></div>
                <div class="carousel-caption">
                    <h2>Find the career you deserve</h2>
					<p>Your job search starts and ends with us.</p>
                </div>
            </div>
			<div class="item">
                <div class="fill img-responsive" style="background-image:url(\'themes/theme10/images/slider4.jpg\');"></div>
                <div class="carousel-caption">
                    <h2>Find the career you deserve</h2>
					<p>Your job search starts and ends with us.</p>
                </div>
            </div>
			<div class="item">
                <div class="fill img-responsive" style="background-image:url(\'themes/theme10/images/slider5.jpg\');"></div>
                <div class="carousel-caption">
                    <h2>Find the career you deserve</h2>
					<p>Your job search starts and ends with us.</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>

    </header>

	<div class="search-block-home">
		<div class="container">
		<div class="row">
		<div class="col-md-3"></div>
			<div class="col-md-6">'.$job_search_form.'
				  <div class="input-group">
                             '.$key.'
                            <span class="input-group-btn">
                             '.$button.'
                            </span>
                        </div></form>
			</div>
			<div class="col-md-3"></div>

		</div>
		</div>
		</div><!-- End Sarch Block -->');
}
else
{
if(check_login("jobseeker"))
{
 $row=getAnyTableWhereData(JOBSEEKER_ACCOUNT_HISTORY_TABLE.' as jah',"jah.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jah.start_date<=CURDATE() and jah.end_date >=CURDATE()",'jah.id,jah.order_id');

define('HEADER_MIDDLE_HTML','
  <tr><td>
  <!-- Employer Job Seeker Top Bar -->
<div class="employer-jobseeker-top-bar">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <ul class="left-content">
<li><a href="#"><i class="fa fa-bell" aria-hidden="true"></i> Job Alerts by Email</a></li>
</ul>
    </td>
    <td>
    <ul class="right-content">
<li><a href="#">Job Seeker:</a></li>
<li><a href="#">Logout</a></li>
<li><a href="#">Dashboard</a></li>
<li><a href="#">Job Search</a></li>
<li><a href="#">My Resume</a></li>
</ul>
    </td>
  </tr>
</table>
</div>
<!-- End Employer Job Seeker Top Bar -->


</td></tr>
  <tr>
    <td>');
}
elseif(check_login("recruiter"))
{
 define('HEADER_MIDDLE_HTML','
  <tr>
     <td>
		<table width="100%"  border="0" align="right" cellpadding="0" cellspacing="0">
		<tr>
        <td><div class="spacer"></div></td>
      </tr>
	<tr>
      <td height="40"><div align="right"><b>EMPLOYERS: </b>&nbsp;&nbsp;&nbsp;
			'.(check_login("recruiter")?'<a href="'.tep_href_link(FILENAME_LOGOUT).'" >'.INFO_TEXT_HM_LOGOUT.'</a>':'<a href="'.tep_href_link(FILENAME_RECRUITER_LOGIN).'" >'.INFO_TEXT_HM_LOGIN.'</a>').'

			| '.(check_login("recruiter")?'<a href="'.tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL).'" >'.INFO_TEXT_HM_RECRUITER_CONTROL_PANEL.'</a>':'<a href="'.tep_href_link(FILENAME_RECRUITER_REGISTRATION).'" >'.INFO_TEXT_HM_JOBSEEKER_REGISTER.'</a>').'

			| '.(check_login("recruiter")?'<a href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB).'" >'.INFO_TEXT_HM_POST_JOB.'</a>':'<a href="'.tep_href_link(FILENAME_RECRUITER_LOGIN).'" >'.INFO_TEXT_HM_POST_JOB.'</a>').'

			| '.(check_login("recruiter")?'<a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'" >'.INFO_TEXT_HM_SEARCH_RESUMES.'</a>':'<a href="'.tep_href_link(FILENAME_RECRUITER_LOGIN).'" >'.INFO_TEXT_HM_SEARCH_RESUMES.'</a>').'&nbsp;&nbsp;</div></td>
      </tr>

    </table></td>
  </tr>
  <tr>
    <td>');
}
else
{
 define('HEADER_MIDDLE_HTML','<tr>
    <td><table width="1160" border="0" cellspacing="0" cellpadding="0">
<tr>
        <td><div class="spacer"></div></td>
      </tr>
      <tr>
        <td class="middle-container">');
}
}
?>