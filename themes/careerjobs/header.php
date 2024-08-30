<?//echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];

    if(check_login("admin"))

    {

    if(isset($_GET['jID']))

    {

    $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");

    unset_session_value($session_array);

    if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_id='".(int)tep_db_input($_GET['jID'])."'",'jobseeker_id'))

    {

    $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");

    set_session_value($session_array);

    }

    else

    {

    $messageStack->add_session(MESSAGE_JOBSEEKER_ERROR, 'error');

    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,'selected_box=jobseekers'));

    }

    }

    else if($_GET['add']=='jobseeker')

    {

    $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");

    unset_session_value($session_array);

    }

    else if(isset($_GET['rID']))

    {

    $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");

    unset_session_value($session_array);

    if($row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".(int)tep_db_input($_GET['rID'])."'",'recruiter_id'))

    {

    $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");

    set_session_value($session_array);

    }

    else

    {

    $messageStack->add_session(MESSAGE_RECRUITER_ERROR, 'error');

    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITERS,'selected_box=recruiters'));

    }

    }

    else if($_GET['add']=='recruiter')

    {

    $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");

    unset_session_value($session_array);

    }

    }

    //////////////////////////////

    /*$welcome_text='';

    if(check_login('jobseeker'))

    {

    if($row_11=getAnyTableWhereData(JOBSEEKER_TABLE," jobseeker_id ='".$_SESSION['sess_jobseekerid']."'","jobseeker_first_name,jobseeker_middle_name,jobseeker_last_name"))

    {

    $welcome_text=tep_db_output('Welcome,'.$row_11['jobseeker_first_name'].' '.$row_11['jobseeker_last_name']);

    }

    }

    else if(check_login('recruiter'))

    {

    if($row_11=getAnyTableWhereData(RECRUITER_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."'","recruiter_first_name,recruiter_last_name"))

    {

    $welcome_text=tep_db_output('Welcome,'.$row_11['recruiter_first_name'].' '.$row_11['recruiter_last_name']);

    }

    }

    else

    {

    $welcome_text='Welcome,Guest';

    }

    */

    if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_JOB_DETAILS)

    {

    $job_name=getAnyTableWhereData(JOB_TABLE," job_id ='".$_GET['query_string']."'","job_title,job_short_description");



    $meta_title=$job_name['job_title']."/".SITE_TITLE;



    $meta_description="<META NAME='Keywords' CONTENT='".$job_name['job_title']."'>

    <META NAME='Description' CONTENT='".strip_tags($job_name['job_short_description'], '<a><b><i><u><>')."'>";



    $meta_description.=$obj_title_metakeyword->metakeywords;



    }

    else

    {

    //print_r($obj_title_metakeyword);

    $meta_title   = $obj_title_metakeyword->title;

    $meta_description = $obj_title_metakeyword->metakeywords;

    }

    ///////////////////////////////

    if($_SESSION['language']=='german')

		include_once(dirname(__FILE__).'/language/german.php');

	else

		include_once(dirname(__FILE__).'/language/english.php');



    $add_script='';

    //autologin(); ///auto login

    if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_JOBSEEKER_RESUME2)

    {

    $add_script=' set_current_emp();';

    }

    $add_script_file='';

    if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_INDEX)

    {

        $add_script_file.='<script language="JavaScript">

    //<!--

    function search_company(company_name)

    {

    document.company_search.company_name.value=company_name;

    document.company_search.submit();

    }

    //-->

    </script>';

    }

    else

    {

    $add_script_file='<script src="'.tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/optionlist.js").'"></script>';

    $add_script.='initOptionLists();';

    }

    $abs=strstr($_SERVER['REQUEST_URI'],'?');

    $path1=(($abs)?(stristr($_SERVER['REQUEST_URI'],'language=')?substr($_SERVER['REQUEST_URI'],0,-2):$_SERVER['REQUEST_URI'].'&language='):$_SERVER['REQUEST_URI'].'?language=');

    if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_RECRUITER_POST_JOB)

    {

    $add_script.='show_hide();';

    }

    $header_banner=banner_display("3",1,380);

    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////CALL JOBSEEKER PIC, name & email ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////



    if(check_login("jobseeker"))

    {

        if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl, '.JOBSEEKER_TABLE.' as j',"jl.jobseeker_id='".$_SESSION['sess_jobseekerid']."' && jl.jobseeker_id=j.jobseeker_id","j.jobseeker_first_name,j.jobseeker_last_name"))

        {

            ///////name and email

            $name=$row['jobseeker_first_name'].' '.$row['jobseeker_last_name'];

            ///no of resumes

            $resume_query = tep_db_query("select distinct resume_id from " . JOBSEEKER_RESUME1_TABLE.' where jobseeker_id='.$_SESSION['sess_jobseekerid'] );

            $no_of_resumes= tep_db_num_rows($resume_query);

        }



            ///find pic

            $resume_photo_check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_photo!='' ","jobseeker_photo,resume_id");



            $photo='';

            if(tep_not_null($resume_photo_check['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$resume_photo_check['jobseeker_photo']))

            {

                $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$resume_photo_check['jobseeker_photo'],'','','','class=" "');

            }

            else

                // $photo='<img src="'.HOST_NAME.'image/no_pic.gif" class="jobseeker-profile2 img-fluid">';

                $photo=defaultProfilePhotoUrl($name,false,112,'class="img-responsive" id=""');;



    //call of premium membership

    $row=getAnyTableWhereData(JOBSEEKER_ACCOUNT_HISTORY_TABLE.' as jah',"jah.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jah.start_date<=CURDATE() and jah.end_date >=CURDATE()",'jah.id,jah.order_id');

    $membership=(!tep_not_null($row['id'])?'<a class="dropdown-item" href="'.tep_href_link(FILENAME_JOBSEEKER_RATES).'">'.INFO_TEXT_HEADER_MIDDLE_PREMIUM_MEMBERSHIP.'</a>':'<a class="dropdown-item" href="'.tep_href_link(FILENAME_JOBSEEKER_RATES).'">'.INFO_TEXT_HEADER_MIDDLE_PREMIUM_MEMBERSHIP.'</a>');

    }

    elseif(check_login("recruiter"))

    {

        if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl, '.RECRUITER_TABLE.' as r',"rl.recruiter_id='".$_SESSION['sess_recruiterid']."' && rl.recruiter_id=r.recruiter_id","r.recruiter_first_name,r.recruiter_last_name,r.recruiter_logo"))

        {

            ///////name and email

            $name=$row['recruiter_first_name'].' '.$row['recruiter_last_name'];

            $photo='';

            if(tep_not_null($row['recruiter_logo']) && is_file(PATH_TO_MAIN_PHYSICAL_LOGO.$row['recruiter_logo']))

                $photo =tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_LOGO.$row['recruiter_logo'],'','','','class="jobseeker-profile2 img-responsive"');

            else

                // $photo='<img src="'.HOST_NAME.'image/no_pic.gif" class="jobseeker-profile2 img-responsive">';

                $photo=defaultProfilePhotoUrl($name,false,112,'class="img-responsive" id=""');

            ///no. of jobs posted

            $resume_query = tep_db_query("select distinct job_id from " . JOB_TABLE.' where recruiter_id='.$_SESSION['sess_recruiterid'] );

            $no_of_resumes= tep_db_num_rows($resume_query);

        }

        else

            // $photo='<img src="'.HOST_NAME.'image/no_pic.gif" class="jobseeker-profile3 img-responsive">';

            $photo=defaultProfilePhotoUrl($name,false,112,'class="img-responsive" id=""');

    }

    else//if neither recruiter nor jobseeker

    {

    $photo='';

    }

    $language_button = "
    <a href='" . tep_href_link(FILENAME_INDEX) . "?language=de'><img alt='English' src='".tep_href_link('themes/careerjobs/images/gm-flag.gif')."' width='32' height='20' class='" . ($_SESSION['language'] == 'German' ? 'lang_active' : '') . "' style='border-radius: 50% !important;margin-right:10px;margin-left: 14px;object-fit: cover;height: 32px;'></a>
    <a href='" . tep_href_link(FILENAME_INDEX) . "?language=en'><img alt='German' src='".tep_href_link('themes/careerjobs/images/uk-flag.gif')."' width='32' height='20' class='" . ($_SESSION['language'] == 'english' ? 'lang_active' : '') . "' style='border-radius: 50% !important;object-fit: cover;height: 32px;'></a>  
    
    ";
    

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////JObseeker or recruiter cpanel display//////////////////////////////////////////////////////////////////

    if(check_login("jobseeker"))

    {	$jobrec_profilemenu='



      <!-- Notification Dropdown -->

      <li class="nav-item dropdown me-0">

      <a class="nav-link" href="#" id="bellIconId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 

            data-user-type="jobseeker">

          <div class="notification-box">

          <img src="https://ejobsitesoftware.com/jobboard_demo/themes/theme1/images/icon-bell.png" height="20" alt="Notification">

          <span class="noti-badge" id="notificationValue">'.count_admin_mail_responses_for_jobseeker()['total_data'].'

          <span class="visually-hidden">unread messages</span>

          </span>

          </div>

      </a>

      <div class="dropdown-menu noti-dropdown-height dropdown-menu-right py-0" aria-labelledby="dropdown01">

          <div class="media p-0">

             <div class="media-body">

             <div class="noti-header"><i class="bi bi-bell me-2"></i> Notifications</div>

            </div>

          </div>

        '.count_admin_mail_responses_for_jobseeker()['html'].'

	

      </div>

    </li>

    <!-- Top Profile Pictures-->

    <li class="nav-item dropdown">

    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$photo.'</a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown01">

    <h5 class="p-3 pb-0 m-text-white">'.$name.'</h5>

      <a class="dropdown-item" href="'.getPermalink(FILENAME_JOBSEEKER_CONTROL_PANEL).'">'.INFO_TEXT_HM_JOBSEEKER_CONTROL_PANEL.'</a>

      <a class="dropdown-item" href="'.getPermalink(FILENAME_JOB_SEARCH).'">'.INFO_TEXT_HM_JOB_SEARCH.'</a>

      <a class="dropdown-item" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES).'">'.INFO_TEXT_HM_MY_RESUMES.'</a>

      <a class="dropdown-item" href="'.getPermalink(FILENAME_JOBSEEKER_REGISTER1).'">'.INFO_TEXT_EDIT_PROFILE.'</a>

  '.(JOBSEEKER_MEMBERSHIP=='false'?'':$membership).'

      <a class="dropdown-item" href="'.tep_href_link(FILENAME_LOGOUT).'">'.INFO_TEXT_HM_LOGOUT.'</a>

    </div>

  </li>

	   <!-- Top Profile Pictures End-->





    ';

    }

    elseif(check_login("recruiter"))

    { $jobrec_profilemenu='



      <!-- Notification Dropdown -->

      <li class="nav-item dropdown me-0">

      <a class="nav-link" href="#" id="bellIconId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 

            data-user-type="recruiter">

          <div class="notification-box">

          <img src="https://ejobsitesoftware.com/jobboard_demo/themes/theme1/images/icon-bell.png" height="20" alt="Notification">

          <span class="noti-badge" id="notificationValue"> '.get_recruiter_application()['total_data'].'

          <span class="visually-hidden">'.UNREAD_MSG.'</span>

          </span>

          </div>

      </a>

      <div class="dropdown-menu noti-dropdown-height dropdown-menu-right py-0" aria-labelledby="dropdown01">

          <div class="media p-0">

             <div class="media-body">

             <div class="noti-header"><i class="bi bi-bell me-2"></i> '.NOTIFICAION.'</div>

            </div>

          </div>

          '.get_recruiter_application()['html'].'

      </div>

    </li>

    <!-- Top Profile Pictures recruiter-->

	<li class="nav-item dropdown">

        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$photo.'</a> 

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown01">

            <h5 class="p-3 pb-0 m-text-white">'.$name.'</h5>

			<a class="dropdown-item" href="'.getPermalink(FILENAME_RECRUITER_CONTROL_PANEL).'">'.INFO_TEXT_HM_RECRUITER_CONTROL_PANEL.'</a>

			<a class="dropdown-item" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'">'.INFO_TEXT_HM_RESUME_SEARCH.'</a>

			<a class="dropdown-item" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB).'">'.INFO_TEXT_HM_POST_JOB.'</a>

			<a class="dropdown-item" href="'.getPermalink(FILENAME_RECRUITER_REGISTRATION).'">'.INFO_TEXT_EDIT_PROFILE.'</a>

			<a class="dropdown-item" href="'.tep_href_link(FILENAME_LOGOUT).'">'.INFO_TEXT_HM_LOGOUT.'</a>

        </div>

      </li>

    <!-- Top Profile Pictures End-->





    ';

    }

    else {

        $jobrec_profilemenu='';

    }

//////////////END PROFILE MENU CODING------------------//////////////////////

//***************COOKIE ALERT POPUP CODE *****************************************************/



if(COOKIE_ALERT_POPUP=='true')



$cookie_alert_popup='



<!-- /.container -->



<div class="alert-warning alert-dismissible fade cookiealert" role="alert">

'.COOKIE_TEXT.' <a class="btn btn-sm acceptcookies-btn" href="'.getPermalink(FILENAME_PRIVACY).'" target="_blank">'.COOKIE_POLICY.'</a>

<button type="button" class="btn btn-sm acceptcookies" aria-label="Close">'.TEXT_GOTIT.'</button>

</div>



';



else



$cookie_alert_popup='';



///////////////////////////////////////////////////////////////////////////////////////////////////



    //---------------------------------------------------------------------------------------------------------//

    //------------------------different header for internal page and for home page  begins-------------------



  $menu =get_menu_list();

  $menu_str='';

  if($_SESSION['language']!='english')

  $manu_text_language='1';

  else

    $manu_text_language='';

  if(is_array($menu))

  {

	  $i=1;

   foreach($menu as $l)

   {

	if(!tep_not_null($l['sub_menu']))

	 $have_sub_menu=false;

	else

    $have_sub_menu=true;

   

    if(!is_show_menu($l['user_type']))

	 continue;



	if($have_sub_menu)

     $menu_str.='<li class="nav-item dropdown"><a class="nav-link"  href="'.($l['link']).'" '.tep_db_output($l['parameter']).' id="navbarDropdown'.$i.'" aria-haspopup="true" aria-expanded="false" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.tep_db_output($l['text'.$manu_text_language]).' <i class="bi bi-chevron-compact-down"></i></a>'."\n";

    else

     $menu_str.='<li class="nav-item"><a class="nav-link"  href="'.($l['link']).'" '.tep_db_output($l['parameter']).'>'.tep_db_output($l['text'.$manu_text_language]).'</a>'."\n";

    

	if(tep_not_null($l['sub_menu']))

	{

     $menu_str.='<div class="dropdown-menu" aria-labelledby="navbarDropdown'.$i.'">'."\n";

	 foreach($l['sub_menu'] as $s)

	 {

       //////////

	   if(is_show_menu($s['user_type']))	 

        $menu_str.='<a class="dropdown-item"  href="'.($s['link']).'" '.tep_db_output($s['parameter']).'>'.tep_db_output($s['text'.$manu_text_language]).'</a>'."\n";

	  ///////////

	 }

	 $menu_str.='</div>'."\n";

    }

    $menu_str.='</li>'."\n";

   }

  }







    define('HEADER_HTML','<!DOCTYPE html>

    <html lang="en">

        <head>

            <meta>

            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>'.$meta_title.'</title>

            <meta http-equiv="Content-Type" >



            '.$meta_description.'

			<link rel="icon" href="'.HOST_NAME.'img/'.DEFAULT_SITE_FAVICON.'" type="ico" sizes="16x16">

			<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/bootstrap.min.css").'">

            <link rel="stylesheet" type="text/css" href="'.tep_href_link("css/careerjobs.css").'">

            <!--<link rel="stylesheet" type="text/css" href="'.tep_href_link("fonts/font-awesome.min.css").'">

            <link rel="preconnect" href="https://fonts.gstatic.com">

			<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">-->



            <link rel="stylesheet" type="text/css" href="'.tep_href_link("themes/careerjobs/css/theme1.css").'">

            <link rel="stylesheet" href="'.tep_href_link("css/cookiealert.css").'">

            '.$add_script_file.'

            <script language="JavaScript" type="text/JavaScript">

            <!--

            function body_load()

            {

                '.$add_script.'

            }

            //-->

            </script>

            <!--<script src="'.tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/push.js").'"></script>-->
<style>
.lang_active {
    border-radius: 50%;
    border: 2px solid #000; /* Adjust the border width as needed */
}
</style>
        </head>

        <body onLoad="body_load();">

        '.$cookie_alert_popup.'

            <!-- Navigation -->



            <nav class="navbar navbar-expand-lg 1navbar-dark bg-dark fixed-top">

			<div class="container">

                <a class="navbar-brand" href="'.tep_href_link("").'"><img src="'.tep_href_link('img/'.DEFAULT_SITE_LOGO).'" width="300" alt="Jobboard Logo"></a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">

                <i class="bi bi-list"></i>

                </button>





                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <ul class="navbar-nav ms-auto align-items-center">

                     '.$menu_str.'

                    <li class="nav-item mmt-15">'.$language_button.'</li>



                    '.$jobrec_profilemenu.'



                    </ul>

                </div>

				</div>

            </nav>



    ');



function get_recruiter_application()

{

    $field_names = 'a.job_id, jb.job_title, a.application_id,a.id, 

        j.jobseeker_first_name, j.jobseeker_last_name, 

        if(j.jobseeker_privacy = 3, jl.jobseeker_email_address, "*****") as email_address, 

        j.jobseeker_city, a.inserted, a.applicant_select';



    $table_names = APPLICATION_TABLE . " as a 

                        LEFT JOIN " . JOB_TABLE . " as jb ON (a.job_id = jb.job_id) 

                        LEFT JOIN " . JOBSEEKER_TABLE . " as j ON (j.jobseeker_id = a.jobseeker_id) 

                        LEFT OUTER JOIN " . JOBSEEKER_RESUME1_TABLE . " as jr1 ON (a.resume_id = jr1.resume_id) 

                        LEFT JOIN " . JOBSEEKER_LOGIN_TABLE . " as jl ON (j.jobseeker_id = jl.jobseeker_id) ";



    $whereClause = "jb.recruiter_id = '" . tep_db_input($_SESSION['sess_recruiterid']) . "'";



    $query = "SELECT $field_names FROM $table_names WHERE $whereClause ORDER BY a.inserted DESC LIMIT 5";



    $result = tep_db_query($query);



    $html_elem = '';

    $total_data = 0;



    if ($result && tep_db_num_rows($result) > 0) {

        while ($responseData = tep_db_fetch_array($result)) {

            $job_title = $responseData['job_title'];

            $jobseeker_first_name = $responseData['jobseeker_first_name'];

            $jobseeker_last_name = $responseData['jobseeker_last_name'];

            $email_address = $responseData['email_address'];

            $jobseeker_city = $responseData['jobseeker_city'];



            // Store the notification and check if it was a new notification

            if (!empty(store_notification_for('recruiter_notifications', 'applications', $responseData['id'], 0))) {

                $total_data++;

            }

            // Construct HTML for each application

            $html_elem .= '<a class="dropdown-item noti-list unread" href="' . tep_href_link('applicant_tracking.php') . '">

                                  <div class="d-flex align-items-center">

                                      <div class="noti-bell"><i class="bi bi-bell"></i></div>

                                      <div>

                                          <div class="noti-title">' . $job_title . '</div>

                                          <div class="noti-small">' . $jobseeker_first_name . ' ' . $jobseeker_last_name . ' from ' . $jobseeker_city . ' applied. Email: ' . $email_address . '</div>

                                      </div>

                                      <div class="noti-envelop ms-auto"><i class="bi bi-envelope"></i></div>

                                  </div>

                              </a>';

        }

    } else {

        $html_elem .= '<div class="text-center">

                                <div><i class="bi bi-envelope-paper"></i></div>

                                <div>No Notification</div>

                            </div>';

    }

    return array('html' => $html_elem, 'total_data' => $total_data);

}



function count_admin_mail_responses_for_jobseeker()

{

    $jobseekerId = $_SESSION['sess_jobseekerid'];

    $query = "SELECT 

                    ai.id,

                    a.application_id,

                    ai.subject,

                    ai.attachment_file,

                    ai.inserted,

                    ai.user_see,

                    ai.jobseeker_mark,

                    r.recruiter_company_name,

                    ai.sender_user

                FROM 

                    applicant_interaction AS ai

                LEFT JOIN 

                    application AS a ON (a.id = ai.application_id)

                LEFT JOIN 

                    recruiter AS r ON (ai.sender_id = r.recruiter_id AND ai.sender_user = 'recruiter')

                WHERE 

                    a.jobseeker_id = '$jobseekerId'

                    AND ai.sender_user = 'recruiter'

                ORDER BY 

                    ai.id DESC 

                LIMIT 5";



    $result = tep_db_query($query);



    $html_elem = '';

    $total_data = 0;



    if ($result && tep_db_num_rows($result) > 0) {

        while ($responseData = tep_db_fetch_array($result)) {

            // Store the notification and check if it was a new notification

            if (!empty(store_notification_for('jobseeker_notifications','applicant_interaction', $responseData['id'], 0))) {

                $total_data++;

            }

            // Construct HTML for each application

            $html_elem .= '<a class="dropdown-item noti-list unread" href="' . tep_href_link('jobseeker_mails.php') . '">

                                  <div class="d-flex align-items-center">

                                      <div class="noti-bell"><i class="bi bi-bell"></i></div>

                                      <div>

                                      <div class="noti-title">' . $responseData['subject'] . '</div>

                                      <div class="noti-small">' . $responseData['recruiter_company_name'] . '</div>

                                      </div>

                                      <div class="noti-envelop ms-auto"><i class="bi bi-envelope"></i></div>

                                  </div>

                              </a>';

        }

    } else {

        $html_elem .= '<div class="text-center">

                                <div><i class="bi bi-envelope-paper"></i></div>

                                <div>No Notification</div>

                            </div>';

    }



    return array('html' => $html_elem, 'total_data' => $total_data);

}



function store_notification_for($table_names, $type, $reference_id, $is_read){

    $current_time = date('Y-m-d H:i:s');

    $sql_data = [

        'type' => $type,

        'reference_id' => $reference_id,

        'is_read' => $is_read,

        'created_at' => $current_time,

        'updated_at' => $current_time,

    ];

    // first check is already exists based on reference_id and type if exist do nothing otherwise store

    $query = "SELECT * FROM $table_names WHERE type = '$type' AND reference_id = '$reference_id'";

    $result = tep_db_query($query);

    if (tep_db_num_rows($result) > 0) {

        // Notification already exists check is_read value 0 if yes then simple return true otherwise false

        $res = tep_db_fetch_array($result);

        return $res['is_read'] == 0 ? true : false;

    }else{

        tep_db_perform($table_names, $sql_data);

        return true;

    }

}

    

?>