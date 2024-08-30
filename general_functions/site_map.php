<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_SITE_MAP);
$template->set_filenames(array('site_map' => 'site_map.htm'));
include_once(FILENAME_BODY);

$jobseeker_site_map='
<p class="card-text"><a href="'.getPermalink(FILENAME_JOBSEEKER_LOGIN).'">'.INFO_TEXT_LOGIN.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL).'">'.INFO_POST_RESUME.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_JOB_SEARCH).'">'.INFO_TEXT_SEARCH_JOBS.'</a></p>


<p class="card-text"><a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'">'.INFO_TEXT_BY_CATEGORY.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION).'">'.INFO_TEXT_BY_LOCATION.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_COMPANY_DESCRIPTION).'">'.INFO_TEXT_BY_COMPANY.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_JOB_SEARCH).'">'.INFO_TEXT_ADVANCE_SEARCH.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_ARTICLE).'">'.INFO_TEXT_CAREER_ADVICE.'</a></p>';

$recruiter_site_map='

<p class="card-text"><a href="'.getPermalink(FILENAME_RECRUITER_LOGIN).'">'.INFO_TEXT_LOGIN.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB).'">'.INFO_TEXT_POST_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'">'.INFO_TEXT_SEARCH_RESUMES.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_RATES).'">'.INFO_TEXT_RATES.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">'.INFO_TEXT_VIEW_RESUME_TO_JOB.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">'.INFO_TEXT_COMPARE_RESUMES.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_IMPORT_JOBS).'">'.INFO_TEXT_IMPORT_MULTIPLE_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'">'.INFO_TEXT_SAVE_RESUME.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS).'">'.INFO_TEXT_LIST_OF_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS).'">'.INFO_TEXT_ACTIVE_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS).'">'.INFO_TEXT_EXPIRED_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS).'">'.INFO_TEXT_DELETED_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">'.INFO_TEXT_SORT_RESUME.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">'.INFO_TEXT_ROUNDWISE_REPORT.'</a></p>';

$others_site_map='
<p class="card-text"><a href="'.tep_href_link().'">'.INFO_TEXT_HOME.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_ABOUT_US).'">'.INFO_TEXT_ABOUT_US.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_FAQ).'">'.INFO_TEXT_FAQS.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_FAQ).'">'.INFO_TEXT_JOB_SEEKER_FAQS.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_FAQ).'">'.INFO_TEXT_EMPLOYER_FAQS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_INDUSTRY_RSS).'">'.INFO_TEXT_RSS_ALL_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link("https://ejobsitesoftware.com/jobboard_demo/rss/all_jobs.xml").'">'.INFO_TEXT_RSS_ALL_JOBS.'</a></p>

<p class="card-text"><a href="'.tep_href_link(FILENAME_JOBSEEKER_LOGIN).'">'.INFO_TEXT_NEWSLETTER.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_ARTICLE).'">'.INFO_TEXT_BLOG.'</a></p>

<p class="card-text"><a href="'.tep_href_link('forum/').'">'.INFO_TEXT_FORUM.'</a></p>

<p class="card-text"><a href="'.getPermalink(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME).'">'.INFO_TEXT_COURSES.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_JOBFAIR).'">'.INFO_TEXT_JOBFAIR.'</a></p>

<p class="card-text"><a href="'.tep_href_link('quiz/').'">'.INFO_TEXT_QUIZ.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_TERMS).'">'.INFO_TEXT_TERMS.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_PRIVACY).'">'.INFO_TEXT_PRIVACY.'</a></p>

<p class="card-text"><a href="'.getPermalink(FILENAME_CONTACT_US).'">'.INFO_TEXT_Contact_Us.'</a></p>';

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_JOBSEEKERS'=>INFO_TEXT_JOBSEEKERS,
 'INFO_TEXT_EMPLOYER' =>INFO_TEXT_EMPLOYER,
 'INFO_TEXT_OTHERS'   =>INFO_TEXT_OTHERS,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
	'JOBSEEKER_SITE_MAP' =>$jobseeker_site_map,
	'RECRUITER_SITE_MAP' => $recruiter_site_map,
	'OTHERS_SITE_MAP'    => $others_site_map,
 'update_message'=>$messageStack->output()));
$template->pparse('site_map');
?>