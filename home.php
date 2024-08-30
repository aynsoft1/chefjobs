<?phpif($_SESSION['language']=='english')include_once(dirname(__FILE__).'/language/home_english.php');
#################FEATURED EMPLOYER############################$featured_emp=banner_display("7",10,130,"class='img-fluid img-thumbnail extra-mini-profile-img'","class='img-fluid img-thumbnail extra-mini-profile-img'");//$featured_emp =array(1,2);//print_r($featured_emp);die();for($i=0;$i<count($featured_emp);$i=$i+5)
{$template->assign_block_vars('banner', array(
                                'banner1'=>$featured_emp[$i],
								'banner2'=>$featured_emp[$i+1],
								'banner3'=>$featured_emp[$i+2],
								'banner4'=>$featured_emp[$i+3],
								'banner5'=>$featured_emp[$i+4],));}
/////////// FEATURED EMPLOYER END///////////////

#################JOB CATEGORY############################
/*------------------*/
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
/*------------------*/
$field_names="id,".TEXT_LANGUAGE."category_name";
$whereClause=" where sub_cat_id is null";
$query11 = "select $field_names from ".JOB_CATEGORY_TABLE." $whereClause  order by ".TEXT_LANGUAGE."category_name  asc limit 0,20";//.(int) MODULE_THEME_DEFAULT_MAX_JOB_CATEORY;
$result11=tep_db_query($query11);
$i=1;
$job_category="<div class='col-md-6'>";
while($row11 = tep_db_fetch_array($result11))
{
 $ide=$row11["id"];
/*------------------*/
 $total_jobs=show_category_total_job($ide);
 if($total_jobs>0)
 $total_jobs = ' ('.$total_jobs.')';
 else
 $total_jobs = '';
/*------------------*/
 $row11[TEXT_LANGUAGE.'category_name'];
 $job_category_form=tep_draw_form('job_category'.$i, FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search');
 //$key=((strlen($row11['category_name'])<30)?$row11['category_name']:substr($row11['category_name'],0,27)."...");
	$key=((strlen($row11[TEXT_LANGUAGE.'category_name'])<30)?$row11[TEXT_LANGUAGE.'category_name']:substr($row11[TEXT_LANGUAGE.'category_name'],0,28)."..");
	$key1=$row11[TEXT_LANGUAGE.'category_name'];
	$job_category.="<p><a href='".$ide.'/'.encode_category($key1)."-jobs.html"."'  title='".tep_db_output($key1)."'>".tep_db_output($key)."</a>".$total_jobs."</p>";
	if($i%10 == 0)
	{
     $job_category.="</div><div class='col-md-6'>";
	}
$i++;
}
$job_category.="</div>";
/****************end of JOB CATEGORY******************/

/****************JOBS BY COMPANY*****************************/
$now=date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
$whereClause1="where rl.recruiter_status='Yes'";
$whereClause1.="and r.recruiter_id in (select distinct(j.recruiter_id) as recruiter_id from ".JOB_TABLE."  as j  where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00'))";
$fields_c="recruiter_company_name,recruiter_email_address";
$query_c = "select $fields_c  from ".RECRUITER_TABLE." as r left join ".RECRUITER_LOGIN_TABLE." as rl on ( r.recruiter_id = rl.recruiter_id) $whereClause1 limit 0,11";//.(int) MODULE_THEME_SAMPLE12_MAX_JOB_COMPANY;
$result_c=tep_db_query($query_c);//echo "<br>$query";//exit;
$x=tep_db_num_rows($result_c);//echo $x;exit;
$k=1;
$company_name1_old="";
$company_form=tep_draw_form('company_search', FILENAME_JOBSEEKER_COMPANY_PROFILE,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('company_name','');
$job_company="".$company_form."";
while($row_c=tep_db_fetch_array($result_c))
{
	$company_name1=strtoupper(substr($row_c["recruiter_company_name"],0,1));
	$company_name="";
 if($company_name1!=$company_name1_old || $company_name1_old=='')
 {
  $title="<a id='".tep_db_output($company_name1)."'>".tep_db_output($company_name1)."</a>";
  $link_array[]=$company_name1;
 }
 $email_id=$row_c["recruiter_email_address"];
 $query_string1=encode_string("recruiter_email=".$email_id."=mail");
 $company_name="<a href='#' onclick='search_company(\"".$query_string1."\")'>".tep_db_output($row_c['recruiter_company_name'])."</a> ";
	$job_company.="<p class='mb-2'>".$company_name."</p>";
	$company_name1_old=$company_name1;
 $k++;
}
$job_company.="";
/***************end of JOBS BY COMPANY************************/

//////////////////// LATEST JOBS STARTS ///////////////////
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j,".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
$whereClause="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//
$field_names="j.job_id, j.job_title, j.job_salary, j.job_location,j.job_short_description,j.inserted, r.recruiter_company_name,job_country_id,r.recruiter_logo";
$query = "select $field_names from $table_names where $whereClause order by rand() limit 0,7" ;// " . (int) MODULE_THEME_JOBSITE12_MAX_LATEST_JOB;
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


$description=(tep_not_null(strlen($row['job_short_description']))>100?substr($row['job_short_description'],0,98).'..':$row['job_short_description']);
 $title=' <a href="'.tep_href_link($ide.'/'.$title_format.'.html').'" target="_blank">'.$row['job_title'].'</a>';

 $country=get_name_from_table(COUNTRIES_TABLE, 'country_name', 'id',tep_db_output($row['job_country_id']));
 $location=tep_db_output($row['job_location']);
 $company_address=tep_not_null($location)?"$location, $country":"$country";
 $date =((tep_not_null($row['expired'] && !$hide_date) )?formate_date(tep_db_output($row['expired']),'d-M-Y'):'');

 $template->assign_block_vars('latest_jobs', array(
                              'title'    => $title,
                              'location'    => $company_address,
						      'company'	 =>$row['recruiter_company_name'],
                              ));
 $count++;
}
//// LATEST JOB ENDS ////

//////////////////// CAREER TOOLS STARTS ///////////////////
$now=date("Y-m-d H:i:s");
$query = "select a.id,a.title,a.short_description,a.article_photo  from ".ARTICLE_TABLE." as a  where a.show_date <='$now' and a.is_show='Yes'  order by rand() limit 0,4";
//echo "<br>$query";//exit;
$result1=tep_db_query($query);
$x=tep_db_num_rows($result1);
$count=1;
$articles1='';
$articles1.='';
while($article = tep_db_fetch_array($result1))
{
 $ide=$article['id'];
	if(strlen($article['title']) > 20)
  $article_name_short=	substr($article['title'],0,15).'..';
 else
  $article_name_short=	substr($article['title'],0,20);
 $title='<a class="d-block" href="article_'.$ide.'.html"  target="_blank">'.$article['title'].'</a>';
  $article_image='';
  if(tep_not_null($article["article_photo"]) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ARTICLE_PHOTO.$article["article_photo"]))
    $article_image='<a href="article_'.$ide.'.html"  target="_blank">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_ARTICLE_PHOTO.$article["article_photo"]."&size=58",'','','','').'</a>';
  else
    $article_image='<a href="article_'.$ide.'.html" target="_blank">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_ARTICLE_PHOTO."blank_com.gif&size=58",'','','','').'</a>';
 	$description=((strlen($article['short_description'])<90)?$article['short_description']:substr($article['short_description'],0,30)."..");
//$MORE='<a class="mb-3" href="article_'.$ide.'.html"  target="_blank"><div>...more&gt;&gt;</div></a>';
$articles1.=''.$title.''.tep_db_output($description).''.$MORE.' </span>
																				';
/*  if($count%2 == 0)
		{
    $articles1.='</tr><tr><td><img src="themes/sample5/img/spacer.gif" width="5" height="2"></td></tr><tr>';
		}
*/$count++;
}
$articles1.='</ul>';
//// CAREER TOOLS ENDS ////
/*************************codeing to display different form and save search link for login and non login users *********************/
if(check_login("jobseeker"))
{
	$save_search= tep_draw_form('save_search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','save_search');
    $INFO_TEXT_ALERT_TEXT=(($action1=='save_search')?'':"<a href='#' onclick='document.save_search.submit();' class='btn btn-lg btn-info info-custom'>Create Job Alert</a>");
}
else
{
	 $save_search= tep_draw_form('save_search', FILENAME_JOB_ALERT_AGENT_DIRECT,'','post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','new');
	 $INFO_TEXT_ALERT_TEXT=$save_search.'<div class="input-group">'.tep_draw_input_field('TREF_job_alert_email', $TREF_job_alert_email,'class="form-control form-custom" placeholder="Email Address" ',false).'</div>'."<button type='submit' class='btn btn-sm btn-danger btn-block mt-2'>Create Job Alert</button></form>";
}

/**********************************************************************************************************************************/


$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>INFO_TEXT_ALL_CATEGORIES));
$default_country = DEFAULT_COUNTRY_ID;
$template->assign_vars(array(
'JOB_CATEGORY'=> $job_category,
'ARTICLE_HOME'=>$articles1,
'CONTACT_US'=>'<a href="'.tep_href_link(FILENAME_CONTACT_US).'">contact us</a>',

'JOBSEEKER_SIGN_IN'=> (check_login("jobseeker")?'<a href="'.tep_href_link(FILENAME_LOGOUT).'">Logout</a>':'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LOGIN).'">Login</a>'),
'JOBSEEKER_REGISTER'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'" class="btn btn-sm btn-outline-secondary">Register Now!</a>',
'POST_RESUME'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'">Post Resume</a>',
'FIND_JOB'=>'<a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">Find Jobs</a>',
'RECRUITER_SIGN_IN'=> (check_login("recruiter")?'<a href="'.tep_href_link(FILENAME_LOGOUT).'">Logout</a>':'<a href="'.tep_href_link(FILENAME_RECRUITER_LOGIN).'">Login</a>'),
'REC_REGISTER'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_REGISTRATION).'" class="btn btn-sm btn-outline-secondary">Register Now!</a>',
'REC_POST_JOB'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB).'">Post Job</a>',
'REC_SEARCH_RESUME'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'">Find Resume</a>',
 'ALL_JOBS'=>tep_draw_form('all_jobs', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').'<a onclick="document.all_jobs.submit()" class="btn btn-sm btn-outline-secondary">View all</a></form>',
'POST_RES'=>'<button onclick="location.href=\''.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'\'" type="submit" class="btn btn-primary button"><i class="fa fa-file" aria-hidden="true"></i> Post Resume</button>',
 'ALL_CATEGORY'=>'<a href="'.tep_href_link(FILENAME_JOB_SEARCH_BY_INDUSTRY).'" class="readmore pull-right button">view All</a>',
'JOB_COMPANY'=> $job_company,
 'ALL_COMPANY'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_PROFILE).'" class="btn btn-sm btn-outline-secondary">view all</a>',
 'ALL_ARTICLES'=> '<a href="'.tep_href_link(FILENAME_ARTICLE).'" class="btn btn-sm btn-outline-secondary">view all</a>',
 'POST_RESUME_MIDDLELINK'=>(check_login("jobseeker")?'<button type="button" class="btn btn-primary button" onclick="location.href=\''.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'\'"><i class="fa fa-file" aria-hidden="true"></i> Post Resume</button>':'<button type="button" class="btn btn-primary button" onclick="location.href=\''.tep_href_link(FILENAME_JOBSEEKER_LOGIN).'\'"><i class="fa fa-file" aria-hidden="true"></i> Post Resume</button>'),
 'VIEW_ALL_EMPLOYER'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_PROFILE).'" class="readmore pull-right button">view All</a>',
 'LEFT_BOX_WIDTH'=> '',
'CREATE_ALERT'=>$INFO_TEXT_ALERT_TEXT,
 'RIGHT_BOX_WIDTH'=> RIGHT_BOX_WIDTH1,
 'RIGHT_HTML'=> RIGHT_HTML,
 'LEFT_HTML'=> '',
 'update_message'=> $messageStack->output(),
		));
	?>