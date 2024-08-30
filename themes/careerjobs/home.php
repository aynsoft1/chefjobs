<?php
if($_SESSION['language']=='german')
  include_once(dirname(__FILE__).'/language/home_german.php');
else
  include_once(dirname(__FILE__).'/language/home_english.php');


#################FEATURED EMPLOYER############################
$feat_emp=banner_display("7",20,130,'class="card-custom  mb-3 theme1-featured-logo" aria-label="Featured Logo"');
for($i=0;$i<count($feat_emp);$i++)
{
  $template->assign_block_vars('featured', array(
                                'employer'=>$feat_emp[$i],
                              ));
}
/////////// FEATURED EMPLOYER END///////////////

#################JOB CATEGORY############################
function show_category_total_job($job_category='')
{
 $now=date('Y-m-d H:i:s');
 $total_job=0;
 $where ="j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and rl.recruiter_status='Yes'  and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id = '".$job_category."'";
 if($row=getAnyTableWhereData(JOB_TABLE."  as j  left  outer join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) left outer join   ".RECRUITER_LOGIN_TABLE." as rl on (j.recruiter_id = rl.recruiter_id)",$where,'count(j.job_id)  as total'))
 {
  if($row['total']>0)
  $total_job=$row['total'];
 }
 return $total_job;
}

$field_names="id,".TEXT_LANGUAGE."category_name,seo_name";
$whereClause=" where sub_cat_id is null";
$query11 = "select $field_names from ".JOB_CATEGORY_TABLE." $whereClause  order by ".TEXT_LANGUAGE."category_name  asc limit 0,15";// . (int)MODULE_THEME_JOBSITE8_MAX_JOB_CATEORY;";
$result11=tep_db_query($query11);
$i=1;
$job_category="<div class='col-md-12'>
			<div class='categories'>";
while($row11 = tep_db_fetch_array($result11))
{
 $ide=$row11["id"];
 $seo_name=$row11["seo_name"];
 /*------------------*/
 $total_jobs=show_category_total_job($ide);
 if($total_jobs>0) {
   $total_jobs = ' ('.$total_jobs.')';
 } else {
   $total_jobs = '';
 }
/*------------------*/
 $row11[TEXT_LANGUAGE.'category_name'];
// $job_category_form=tep_draw_form('job_category'.$i, FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search');

	//$key=((strlen($row11[TEXT_LANGUAGE.'category_name'])<20)?$row11[TEXT_LANGUAGE.'category_name']:substr($row11[TEXT_LANGUAGE.'category_name'],0,15)."..");
	$key1=$row11[TEXT_LANGUAGE.'category_name'];

    //$job_category_form=tep_draw_form('search', FILENAME_JOB_SEARCH,'','post', 'style="display: contents;"').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_category[]',$ide).tep_draw_hidden_field('search_by_text',$key1);

	$job_category.=$job_category_form."<div class='my-2'><a href='".getPermalink('category',array('seo_name'=>$seo_name))."'  title='".tep_db_output($key1)."' class=''>".tep_db_output($key1)."</a>".$total_jobs."</div></form>";
	//$job_category.=$job_category_form."<div class='my-2'><button type='submit' class='btn btn-default center-block skeleton p-0'>".tep_db_output($key1)."</button>".$total_jobs."</div></form>";
	if($i%5 == 0)
	{
   $job_category.="</div></div><div class='col-md-12'><div class='categories'>";
	}
$i++;
}
 $job_category.="</div></div>";
/****************end of JOB CATEGORY******************/

#################JOB LOCATION############################
$field_names="z.zone_name,c.country_name,ct.continent_name ";
$whereClause=" where z.zone_country_id ='".DEFAULT_COUNTRY_ID."' ";
$query11 = "select $field_names from ".ZONES_TABLE."  as z  left outer join ".COUNTRIES_TABLE." as c on (z.zone_country_id =c.id) left outer join  ".CONTINENT_TABLE." as ct on (c.continent_id = ct.id ) $whereClause  order by zone_name  asc limit 0,15";//. (int) MODULE_THEME_SAMPLE12_MAX_JOB_LOCATION;
$result11=tep_db_query($query11);
$i=1;
while($row1 = tep_db_fetch_array($result11))
{
 $continent_name = $row1['continent_name'];
 $country_name   = $row1['country_name'];
 $zone_name      = $row1['zone_name'];
 $template->assign_block_vars('job_location1', array(
                              'job_location'    => '<a href="'.encode_forum($continent_name).'/'.encode_forum($country_name).'/'.encode_forum($zone_name).'/"   title="'.tep_db_output($zone_name).'">' .tep_db_output($zone_name).'</a>',
                              ));
 $i++;

}
tep_db_free_result($result11);
/****************end of JOB LOCATION******************/

//////////////////// LATEST JOBS STARTS ///////////////////
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j,".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
$whereClause="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//
$field_names="j.job_id, j.job_title, j.job_type, j.job_salary,j.job_featured, j.job_location,j.job_short_description,j.inserted,j.min_experience,j.max_experience, j.re_adv, r.recruiter_company_name,job_country_id,r.recruiter_logo";
$order_by_field_name = "j.job_featured, j.inserted";
// $query = "select $field_names from $table_names where $whereClause order by rand() DESC limit 0,6" ;// " . (int) MODULE_THEME_JOBSITE12_MAX_LATEST_JOB;
$query = "select $field_names from $table_names where $whereClause order by $order_by_field_name DESC limit 0,9" ;// " . (int) MODULE_THEME_JOBSITE12_MAX_LATEST_JOB;

//echo "<br>$query";//exit;
$result=tep_db_query($query);
$x=tep_db_num_rows($result);
//echo $x;exit;
$count=1;
while($row = tep_db_fetch_array($result))
{
 $ide=$row["job_id"];
 $title_format=encode_category($row['job_title']);
 $query_string=encode_string("job_id=".$ide."=job_id");

  if(strlen($row['recruiter_company_name']) > 20)
  $company_name_short=	substr($row['recruiter_company_name'],0,15).'..';
 else
  $company_name_short=	substr($row['recruiter_company_name'],0,20);
	$company=$company_name_short;

  /////logo
 $recruiter_logo='';
 $company_logo=$row['recruiter_logo'];
 if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=150",'','','','class="featured-logo thumbnail img-responsive img-hover" aria-label="Featured Logo"');
else
     $recruiter_logo=defaultProfilePhotoUrl($row['job_title'],false,55, 'class="featured-logo" alt="Logo"');


///////////////
$description=(strlen($row['job_short_description'])>80?substr($row['job_short_description'],0,75).'..':$row['job_short_description']);
if(strlen($row['job_title']) > 30)
  $name_short=	substr($row['job_title'],0,25).'..';
 else
  $name_short=	substr($row['job_title'],0,30);
 $title=' <a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)) .'" target="_blank">'.$name_short.'</a>';

///job type
$row_type=getAnyTableWhereData(JOB_TYPE_TABLE,"id='".$row['job_type']."'", '*');

//echo $row_type['type_name'];
if ($row_type[TEXT_LANGUAGE.'type_name']) {
  $jobtype='<span class="'.$row_type['type_name'].'">'.$row_type[TEXT_LANGUAGE.'type_name'].'</span>';
}
else {
  TEXT_LANGUAGE == 'de_'? $jobtype='<span class="Full-time">Vollzeit</span>' : $jobtype='<span class="Full-time">Full time</span>';
}

// salary with currency
$row_cur = getAnyTableWhereData(CURRENCY_TABLE, "code ='" . DEFAULT_CURRENCY . "'", 'symbol_left,symbol_right');
$sym_left = (tep_not_null($row_cur['symbol_left']) ? $row_cur['symbol_left'] . '' : '');
$salary = (tep_not_null($row['job_salary']) ? $sym_left . tep_db_output($row['job_salary']) . $sym_rt : (TEXT_LANGUAGE == 'de_'? 'Verhandelbar' : 'Negotiable'));
$job_posted = tep_date_long(tep_db_output($row['re_adv']));
 $template->assign_block_vars('latest_jobs', array(
                              'title'     => $title,
                              'location'  => tep_db_output($row['job_location']) ? ''. tep_db_output($row['job_location']): '',
						                  'logo'	    => $recruiter_logo,
                              'summary'   => $description,
                              'jobtype'   => $jobtype,
                              'salary'    => $salary,
                              'job_posted' => $job_posted,
                              'job_featured'=> (tep_db_output($row['job_featured']) == 'Yes') ? 'featured-job-tag' : '',
							                'company'   =>$row['recruiter_company_name'],
                              'experience'  => ''.tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
                              ));
 $count++;
}
//// LATEST JOB ENDS ////

//////////////////// FEATURED JOBS STARTS ///////////////////
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j,".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
$whereClause="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and j.job_featured='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//
$field_names="j.job_id, j.job_title,j.job_type,j.job_salary,j.job_featured,j.min_experience,j.max_experience,j.re_adv,j.job_location,j.job_short_description,j.inserted, r.recruiter_company_name,job_country_id,r.recruiter_logo";
$query = "select $field_names from $table_names where $whereClause order by rand() limit 0,6" ;// " . (int) MODULE_THEME_JOBSITE12_MAX_LATEST_JOB;
//echo "<br>$query";//exit;
$result=tep_db_query($query);
$x=tep_db_num_rows($result);
//echo $x;exit;
$is_first = true;
$count=1;
while($row = tep_db_fetch_array($result))
{
  $active_class = $is_first ? 'active' : '';
  $is_first = false;
 $ide=$row["job_id"];
 $title_format=encode_category($row['job_title']);
 $query_string=encode_string("job_id=".$ide."=job_id");
  if(strlen($row['recruiter_company_name']) > 20)
  $company_name_short=	substr($row['recruiter_company_name'],0,15).'..';
 else
  $company_name_short=	substr($row['recruiter_company_name'],0,20);
	$company=$company_name_short;
/////logo
 $recruiter_logo='';
 $company_logo=$row['recruiter_logo'];
 if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo)) {
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo,'','','','class="theme1-featured-logo" aria-label="Featured Logo"');
 } else {
	 $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_IMG."nologo.jpg&size=150",'','','','class="img-fluid img-thumbnail mini-profile-img theme10-mini-profile-img img-hover float-left mr-3 mobile-margin-bottom"');
 }
///////////////
$description=(tep_not_null(strlen($row['job_short_description']))>100?substr($row['job_short_description'],0,98).'..':$row['job_short_description']);  if(strlen($row['job_title']) > 25)  $title=	substr($row['job_title'],0,20).'..'; else  $title=	substr($row['job_title'],0,25);
$titlenew=' <a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'" target="_blank">'.$title.'</a>';
	///location
	$location1=tep_db_output($row['job_location']);
	$country1=get_name_from_table(COUNTRIES_TABLE, 'country_name', 'id',tep_db_output($row['job_country_id']));
	$company_address1=tep_not_null($location1)?"$location1, $country1":"$country1";

 $template->assign_block_vars('featured_jobs', array(
								'title'    		=> $titlenew,
								'logo'	 		=> $recruiter_logo,
								'company'  		=> ''.$row['recruiter_company_name'],
								'summary'  		=> $description,
								'jobtype'  		=> $jobtype ? $jobtype : '',
								'location'  	=> $company_address1 ? ''.$company_address1 : '',
								'salary'    	=> $salary  ? ''.$salary : '',
								'job_posted'	=> $job_posted ? ''.$job_posted : '',
                'active_class'      =>$active_class,
								'job_featured'	=> (tep_db_output($row['job_featured']) == 'Yes') ? 'featured-job-tag' : '',
								'experience'  	=> ''.tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
                              ));
 $count++;
}
//// FEATURED JOB ENDS ////

//////////////////// CAREER TOOLS STARTS ///////////////////
$now=date("Y-m-d H:i:s");
$query = "select a.id,a.title,a.seo_name, a.short_description,a.article_photo,a.show_date  from ".ARTICLE_TABLE." as a  where a.show_date <='$now' and a.is_show='Yes'  order by rand() limit 0,3";
//echo "<br>$query";//exit;
$result1=tep_db_query($query);
$x=tep_db_num_rows($result1);
$count=1;
$articles1='';
$articles1.='';
while($article = tep_db_fetch_array($result1))
{
 $ide=$article['id'];
 $seo_name = $article["seo_name"];
 $article_url=getPermalink('article',array('ide'=> $ide,'seo_name'=>$seo_name));

	if(strlen($article['title']) > 20)
  $article_name_short=	substr($article['title'],0,15).'..';
 else
  $article_name_short=	substr($article['title'],0,20);
 $title='<a aria-label="Read more" href="'.$article_url.'"  target="_blank">'.$article_name_short.'</a>';
  $article_image='';
  if(tep_not_null($article["article_photo"]) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ARTICLE_PHOTO.$article["article_photo"]))
    $article_image='<a aria-label="Read more" href="'.$article_url.'"  target="_blank">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_ARTICLE_PHOTO.$article["article_photo"]."&size=400",'','','','class="card-img-top"').'</a>';
  else
    $article_image='<a aria-label="Read more" href="'.$article_url.'" target="_blank">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_ARTICLE_PHOTO."blank_com.gif",'','','','class="card-img-top"').'</a>';
 	$description=((strlen($article['short_description'])<90)?$article['short_description']:substr($article['short_description'],0,75)."..");
//$MORE='<a href="article_'.$ide.'.html"  target="_blank"><span class="new_style13">...<u>more&gt;&gt;</u></span></a>';
$articles1.='


<div class="col-md-4 mb-3">
<div class="card card-custom card-hover">
  <div class="skeleton">'.$article_image.'</div>
  <div class="card-body card-body-custom">
	<h3 class="article-title skeleton">'. $title.'</h3>
    <p class="card-text skeleton">'.tep_db_output($description).''.$MORE.'</p>
    <p class="card-text small skeleton"><i class="bi bi-calendar2-week"></i> '.tep_date_long(tep_db_output($article['show_date'])).'</p>
   </div>
</div>
</div>
';

$count++;
}
//// CAREER TOOLS ENDS ////

/*************************codeing to display different form and save search link for login and non login users *********************/
if(check_login("jobseeker"))
{
	$save_search= tep_draw_form('save_search', FILENAME_JOB_ALERT_AGENT,($edit?'sID='.$save_search_id:''),'post','onsubmit="return ValidateForm(this)" class="input-group"').tep_draw_hidden_field('action1','save_search');
    $INFO_TEXT_ALERT_TEXT=$save_search.(($action1=='save_search')?'':"<a href='#' onclick='document.save_search.submit();' class='btn btn-warning'>".CREATE_JOB_ALERT."</a></form>");
}
else
{
	 $save_search= tep_draw_form('save_search', FILENAME_JOB_ALERT_AGENT_DIRECT,'','post','onsubmit="return ValidateForm(this)" class="input-group"').tep_draw_hidden_field('action','new');
	 $INFO_TEXT_ALERT_TEXT=$save_search.''.tep_draw_input_field('TREF_job_alert_email', $TREF_job_alert_email,'class="form-control mb-3" placeholder="'.TEXT_EMAIL_ADDRESS.'" ',false).''."<button type='submit' class='btn btn-green'>".CREATE_JOB_ALERT."</button></form>";
}



/**********************************************************************************************************************************/
//// CAREER TOOLS ENDS ////

$social_login_button='';
if(!check_login("jobseeker"))
{
 if(MODULE_FACEBOOK_PLUGIN=='enable' && MODULE_FACEBOOK_PLUGIN_JOBSEEKER=='enable')
 $social_login_button.=' <a href="'.FILENAME_FACEBOOK_APPLICATION.'" title="Sign in with Facebook" class="btn btn-default border btn-facebook"><i class="bi bi-facebook"></i></a>';

 if(MODULE_GOOGLE_PLUGIN=='enable' && MODULE_GOOGLE_PLUGIN_JOBSEEKER=='enable')
 $social_login_button.=' <a href="'.FILENAME_GOOGLE_APPLICATION.'" title="Sign in with Google" class="btn btn-default border btn-google-plus"><i class="bi bi-google"></i></a>';

 if(MODULE_LINKEDIN_PLUGIN=='enable' && MODULE_LINKEDIN_PLUGIN_JOBSEEKER=='enable')
 $social_login_button.=' <a href="'.FILENAME_LINKEDIN_APPLICATION.'" title="Sign in with Linkedin" class="btn btn-default border btn-linkedin"><i class="bi bi-linkedin"></i></a>';

 if(MODULE_TWITTER_PLUGIN_JOBSEEKER=='enable' && MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY!='')
 $social_login_button.=' <a href="'.FILENAME_TWITTER_APPLICATION.'" title="Sign in with Twitter" class="btn btn-default border btn-twitter"><i class="bi bi-twitter"></i></a>';
}

$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>INFO_TEXT_ALL_CATEGORIES));
$default_country = DEFAULT_COUNTRY_ID;
$template->assign_vars(array(
'JOB_CATEGORY'=> $job_category,
'ARTICLE_HOME'=>$articles1,
'CONTACT_US'=>'<a href="'.getPermalink(FILENAME_CONTACT_US).'">'.CONTACT_US.'</a>',

'HOME_RIGHT_BANNER'=>$home_right_banner,
'JOBSEEKER_SIGN_UP'=>(check_login('jobseeker')?'<button aria-label="Sign Out" class="btn btn-outline-secondary skeleton" onclick="location.href=\''.tep_href_link(FILENAME_LOGOUT).'\'" type="submit">'.SIGN_OUT.' <i class="bi bi-arrow-right"></i></button>':'<button aria-label="Sign Up" class="btn btn-text border skeleton" onclick="location.href=\''.getPermalink(FILENAME_JOBSEEKER_REGISTER1).'\'" type="submit">'.SIGN_UP.' <i class="bi bi-arrow-right"></i></button>'),
'RECRUITER_SIGN_UP'=>(check_login('recruiter')?'<button aria-label="Sign Out" class="btn btn-outline-secondary skeleton" onclick="location.href=\''.tep_href_link(FILENAME_LOGOUT).'\'" type="submit">'.SIGN_OUT.' <i class="bi bi-arrow-right"></i></button>':'<button aria-label="Sign Up" class="btn btn-text border skeleton" onclick="location.href=\''.getPermalink(FILENAME_RECRUITER_REGISTRATION).'\'" type="submit">'.SIGN_UP.' <i class="bi bi-arrow-right"></i></button>'),
'ADVERTISER_SIGN_UP'=>'<button aria-label="Contact Us" class="btn btn-text border skeleton" onclick="location.href=\''.getPermalink(FILENAME_CONTACT_US).'\'" type="submit">Contact Us <i class="bi bi-arrow-right"></i></button>',
'INFO_MESSAGE1'=>INFO_MESSAGE1,
'LATEST_JOBS'=>LATEST_JOBS,
'JOB_CATEGORY_TEXT'=>JOB_CATEGORY_TEXT,
'FEATURED_RECRUITERS'=>FEATURED_RECRUITERS,
'FEATURED_RECRUITERS_TEXT'=>FEATURED_RECRUITERS_TEXT,
'LATEST_ARTICLES'=>LATEST_ARTICLES,
'WELCOME_MESSAGE'=>WELCOME_MESSAGE,
'WELCOME_MESSAGE_TEXT'=>WELCOME_MESSAGE_TEXT,
'INFO_JOBSEEKER'=>INFO_JOBSEEKER,
'INFO_EMPLOYER'=>INFO_EMPLOYER,
'EMPLOYER_TEXT'=>EMPLOYER_TEXT,
'INFO_ADVERTISER'=>INFO_ADVERTISER,
'ADVERTISER_TEXT'=>ADVERTISER_TEXT,
'ABOUT_US_HEADING'=>ABOUT_US_HEADING,
'ABOUT_US_HEADING2'=>ABOUT_US_HEADING2,
'ABOUT_US_TEXT'=>ABOUT_US_TEXT,
'GET_EMAIL_TEXT'=>GET_EMAIL_TEXT,
'FEATURED_TEXT'=>FEATURED_TEXT,
'SUBMIT_RESUME_TEXT'=>SUBMIT_RESUME_TEXT,
'CREATE_JOB_ALERT' => CREATE_JOB_ALERT,
'CATEGORIES' => CATEGORIES,
'RECENT_JOBS' => RECENT_JOBS,
'FEATURED_JOBS' => FEATURED_JOBS,
'TEXT_COMPANY' => TEXT_COMPANY,
'PREVIOUS' => PREVIOUS,
'NEXT' => NEXT,
'ALL_CATEGORY' => ALL_CATEGORY,
'SEARCH_BY' => SEARCH_BY,
'FIND_JOB_THAT_FITS' => FIND_JOB_THAT_FITS,
'CONNECTING_THE_BEST_JOBS' => CONNECTING_THE_BEST_JOBS,
'ADVANCED_SEARCH'=> ADVANCED_SEARCH,
'searchform'=>tep_draw_form('search_select_form',FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').'<label for="days-select" class="sr-only">Select the time range:</label>
<select id="days-select" name="days" onchange="this.form.submit()" class="form-select form-select-sm theme9-form">
  <option value="0">Posted</option>
  <option value="7">Last 1 week</option>
  <option value="14">Last 2 weeks</option>
  <option value="21">Last 3 weeks</option>
  <option value="30">Last 30 days</option>
  <option>30+ days</option>
</select>'.tep_draw_hidden_field('job_post_day',$_POST["days"]).'</form>',
'INFO_JOBSEEKER_LOGIN_BOX' => (
    check_login("jobseeker") || check_login("recruiter") ? '' : '
    <div class="card card-custom skeleton mb-4">
            <div class="card-body card-body-custom">
        <h2 class="card-header-title mb-3">'.INFO_TEXT_HM_EMAIL.'</h2>

            ' . tep_draw_form('login', FILENAME_EMP_JOBSEEKER_LOGIN, '', 'post', 'onsubmit="return ValidateForm(this)"') . tep_draw_hidden_field('action', 'check') . '
            <div class="mb-3">
                ' . tep_draw_input_field('TREF_email_address1', $TREF_email_address1, 'class="form-control" placeholder="'.TEXT_EMAIL_ADDRESS.'" aria-describedby="basic-addon2" required', false, 'email') . '
            </div>
            <div class="mb-3">
                ' . tep_draw_password_field('TR_password1', $TR_password1, false, 'class="form-control" placeholder="'.TEXT_PASSWORD.'" aria-describedby="basic-addon2" required') . '
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-green btn-full my-3">'.TEXT_LOGIN.'</button>
            </div>
            <div class="text-center small">
                <a href="' . tep_href_link(FILENAME_JOBSEEKER_FORGOT_PASSWORD) . '">'.TEXT_FORGET_PASSWORD.'</a>
            </div>
            <div class="text-center small">'.TEXT_OR.'</div>
            <div class="text-center small mb-3">'.TEXT_SIGN_IN_WITH.'</div>
            <div class="text-center mt-1"> <a href="facebook_application.php" title="Sign in with Facebook" class="btn btn-default border btn-facebook"><i class="bi bi-facebook"></i></a> <a href="google_application.php" title="Sign in with Google" class="btn btn-default border btn-google-plus"><i class="bi bi-google"></i></a></div>
            <a type="button" class="btn btn-outline-secondary mt-3 w-100 d-block" href="' . getPermalink(FILENAME_JOBSEEKER_REGISTER1) . '">'.TEXT_SIGN_UP.'</a>
        </form>
    </div>
    </div>
    '
),

'JOB_SEARCH'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'" class="badge badge-light border p-2 me-2">'.TEXT_CATEGORY.'</a><a class="badge badge-light border p-2 me-2" href="'.getPermalink(FILENAME_JOBSEEKER_COMPANY_PROFILE).'">'.TEXT_COMPANY.'</a><a class="badge badge-light border p-2 me-2" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION).'">'.TEXT_LOCATION.'</a><a class="badge badge-light border p-2 me-2" href="'.getPermalink(FILENAME_JOB_BY_MAP).'">'.TEXT_MAP.'</a>',
 'LEFT_BOX_WIDTH'=> '',
 'ALL_JOBS'=>tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').'<button aria-label="All Jobs" class="btn btn-text border" type="submit">'.ALL_JOBS.' <i class="bi bi-arrow-right"></i></button></form>',
 'ALL_CATEGORY'=>'<button aria-label="All Categories" class="btn btn-text border" onclick="location.href=\''.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'\'" type="submit"><span class="m-none">'.ALL_CATEGORIES.'</span> <i class="bi bi-arrow-right"></i></button>',
 'ALL_RECRUITERS'=>'<button aria-label="All Recruiters" class="btn btn-text border" onclick="location.href=\''.getPermalink(FILENAME_JOBSEEKER_COMPANY_PROFILE).'\'" type="submit"><span class="m-none">'.ALL_RECRUITERS.'</span> <i class="bi bi-arrow-right"></i></button>',
 'ALL_ARTICLES'=> '<button aria-label="All Articles" class="btn btn-text border" onclick="location.href=\''.getPermalink(FILENAME_ARTICLE).'\'" type="submit"><span class="m-none">'.ALL_ARTICLES.'</span> <i class="bi bi-arrow-right"></i></button>',
'CREATE_ALERT'=>$INFO_TEXT_ALERT_TEXT,
 'RIGHT_BOX_WIDTH'=> RIGHT_BOX_WIDTH1,
 'RIGHT_HTML'=> RIGHT_HTML,
 'LEFT_HTML'=> '',
 'update_message'=> $messageStack->output(),
		));
	?>