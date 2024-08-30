<?php

include_once("../include_files.php");
// $conn = mysqli_connect('localhost', 'root', '', 'jobsite_bpojob') or die('Connection failed');

$request = $_SERVER['REQUEST_METHOD'];

$jobId = $_GET['id'];



switch ($request) {

    case 'GET':

        if ($jobId) {

            response(getData($jobId));

        } else {

            echo json_encode(false);

        }

        break;

    default:

        echo json_encode('This method is not allowed');

        break;

}



function getData($id)

{

    $table_names = JOB_TABLE .

        " as j left outer join " .

        RECRUITER_LOGIN_TABLE .

        ' as rl on (j.recruiter_id=rl.recruiter_id) left outer join ' .

        RECRUITER_TABLE .

        ' as r on (rl.recruiter_id=r.recruiter_id)  left outer join ' .

        ZONES_TABLE .

        ' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join ' .

        COUNTRIES_TABLE .

        ' as c on (j.job_country_id =c.id) left outer join ' .

        JOB_TYPE_TABLE . ' as jt on (j.job_type =jt.id)';



    $whereClause = "job_id=$id";



    $field_names = "j.job_id,

    j.job_title,

    j.job_description,

    j.re_adv,

    j.inserted,

    j.job_short_description,

    j.recruiter_id,

    j.min_experience,

    j.max_experience,

    j.job_salary,

    j.job_industry_sector,

    j.job_type,

    jt.type_name,

    j.expired,

    j.recruiter_id,

    r.recruiter_company_name as company_name,

    r.recruiter_company_seo_name as company_slug,

    r.recruiter_logo as logo,

    r.recruiter_applywithoutlogin,

    j.job_source,

    j.post_url,

    j.url,

    j.job_featured,

    concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location,

    c.country_name,

    job_skills";



    $sql = "select $field_names from $table_names where $whereClause";

    // $sql = "SELECT * FROM jobs WHERE job_id=$id";



    $output = array();



    $result = tep_db_query($sql);



    $result = tep_db_fetch_array($result);

    

    if ($result) {



        // apply job button

        $query_string=encode_string("job_id=".$id."=job_id");

        $apply_job_link = HOST_NAME.FILENAME_APPLY_NOW.'?query_string='.$query_string;

        

        $row_apply=getAnytableWhereData(APPLY_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id ='".$id."'",'id,jobseeker_apply_status');

        

        if (check_login("jobseeker")) {

            $apply_job_status = ($row_apply['id']>0 && $row_apply['jobseeker_apply_status']='active') ? 'true' : $apply_job_link;

        }



        // save job button

        if(check_login("jobseeker"))

        {

            $row_check=getAnyTableWhereData(SAVE_JOB_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id='".$id."'");

            if($row_check) {

                $saveJobLink = 'true';



            } else {

                $saveJobLink = HOST_NAME.FILENAME_JOB_DETAILS.'?query_string1='.$query_string.'&action=save';

            }

        }



        $row_cur = getAnyTableWhereData(CURRENCY_TABLE, "code ='" . DEFAULT_CURRENCY . "'", 'symbol_left,symbol_right');

        $sym_left = (tep_not_null($row_cur['symbol_left']) ? $row_cur['symbol_left'] . ' ' : '');

        $sym_rt = (tep_not_null($row_cur['symbol_right']) ? ' ' . $row_cur['symbol_right'] : '');

        

        // get job title

        $title_format= getPermalink('job',array('ide'=>$id,'seo_name'=>encode_category($result['job_title'])))  ;

        

        // get jobCategory

        $job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',tep_db_output($id));

        $jobCategory= ((tep_db_output($job_category_ids)!='0' && $job_category_ids!='')?get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name', 'id', tep_db_output($job_category_ids)):' All job category');

        

        // get logo

        $recruiter_logo='';

        $company_logo=$result['logo'];   

        if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo)){

            $recruiter_logo=HOST_NAME.FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120";

        }else{
            $recruiter_logo = "https://ui-avatars.com/api/?background=random&size=&rounded=false&name=" . urlencode($result['company_name']) . "";
        }



        // get Salary

        $totalSalary = (tep_not_null($result['job_salary']) ? $sym_left . tep_db_output($result['job_salary']) . $sym_rt : 'Negotiable');

        

        // get Experience

        $totalExp = tep_db_output(calculate_experience($result['min_experience'],$result['max_experience']));



        // apply without login link

        if (check_login('jobseeker')) {

            $witoutloginlink = '';

        }else{

            $witoutloginlink = (($result['recruiter_applywithoutlogin']=='Yes' && !check_login("jobseeker"))

                                ?HOST_NAME.FILENAME_APPLY_NOLOGIN.'?query_string='.$query_string

                                :'');

        }



        $senPostLink = (HOST_NAME.FILENAME_TELL_TO_FRIEND.'?query_string='.$query_string) ?? '';



        $result['totalSalary'] .= $totalSalary;

        $result['totalExperience'] .= $totalExp;

        $result['jobType'] .= tep_db_output($result['type_name']);

        $result['jobCategory'] .= $jobCategory;

        $result['logoPath'] .= $recruiter_logo;

        $result['titleLink'] .= $title_format;

        $result['apply_before'] .= tep_date_short($result['expired']);
        
        $relativeDate = new Relative_Date($result['inserted']);
        // $result['posted_on'] .= tep_date_short($result['inserted']);
        $result['posted_on'] .= $relativeDate->relative_formatted_date;

        $result['yourHomeURL'] .= HOST_NAME;

        $result['applyJob'] .= ($apply_job_status) ?? HOST_NAME.'login.php';

        $result['saveJob'] .= ($saveJobLink) ?? HOST_NAME.'login.php';

        $result['applyWithoutLogin'] .= $witoutloginlink;

        $result['sendPost'] .= $senPostLink;

        $result['companyLink'] .= tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string);
		$curr_date =date('Y-m-d');
 if($check_row=getAnytableWhereData(JOB_STATISTICS_DAY_TABLE,"job_id='".tep_db_input($id)."'  and  date='".tep_db_input($curr_date)."' ",'job_id,clicked'))
 {
  $sql_data_array=array('job_id'=>$id,
                        'clicked'=>($check_row['clicked']+1)
                        );
  tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "job_id='".tep_db_input($id)."'  and  date='".tep_db_input($curr_date)."'");
 }
 else
 {
  $sql_data_array=array('job_id'=>$id,
                        'clicked'=>1,
						'viewed'=>1,
	                    'date'=>$curr_date
                        );
  tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array);
 }

    }



    $output[] = $result;



    return $output;

}



function response($data)

{

    echo json_encode($data);

}

