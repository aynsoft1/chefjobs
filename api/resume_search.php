<?php
include_once("../include_files.php");
$request = $_SERVER['REQUEST_METHOD'];
$resumeID = (isset($_GET['resumeId']) ?  $_GET['resumeId'] : '');
// header("Access-Control-Allow-Origin: *");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
if (check_login("recruiter")) {
    $recruiterId = $_SESSION['sess_recruiterid'];

    switch ($request) {
        case 'GET':
            if ($resumeID) {
                // print_r($resumeID);
                // exit;
                get_resume_profile_data($resumeID);
            } else {
                echo json_encode('Not Found!');
            }
            break;
        default:
            echo json_encode('This method is not allowed');
            break;
    }
} else {
    echo json_encode('Authentication required');
}



function get_resume_profile_data($resumeId = null, $recruiterId = null)
{
    $output = array();
    $where_clause = "jr_1.resume_id = $resumeId";
    $joinClauses = "LEFT JOIN jobseeker AS js_1 ON js_1.jobseeker_id = jr_1.jobseeker_id 
                    LEFT JOIN jobseeker_login AS js_2 ON js_2.jobseeker_id = jr_1.jobseeker_id";

    $db_raw_query = "SELECT jr_1.*, 
                        CONCAT(js_1.jobseeker_first_name,' ',js_1.jobseeker_middle_name,' ',js_1.jobseeker_last_name) AS user_fullname,
                        js_1.phone_code,js_1.jobseeker_phone,js_1.jobseeker_work_phone,js_1.jobseeker_mobile,js_1.jobseeker_address1,
                        js_1.jobseeker_address2,js_1.jobseeker_city,js_1.jobseeker_zip,js_1.jobseeker_state_id,js_1.jobseeker_country_id,
                        js_2.jobseeker_email_address
                    FROM jobseeker_resume1 AS jr_1
                    $joinClauses
                    WHERE $where_clause";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {

        while ($row_data = tep_db_fetch_array($query)) {
            $resume_id = $row_data['resume_id'];

            $query_string1 = encode_string("search_id==" . $resume_id . "==search");

            // get job type
            if ($row_data['job_type_id']) {
                $jobType = get_name_from_table(JOB_TYPE_TABLE, TEXT_LANGUAGE . 'type_name', 'id', $row_data['job_type_id']);
            } else {
                $jobType = 'Any Type';
            }

            // job category
            if ($row_data['job_category']) {
                $target_category = get_category_name_with_parent(get_name_from_table(RESUME_JOB_CATEGORY_TABLE, 'job_category_id', 'resume_id', tep_db_output($resume_id)));
            } else {
                $target_category = null;
            }

            // salary
            if ($row_data['expected_salary']) {
                $rowCurrency = get_name_from_table(CURRENCY_TABLE, 'code', 'currencies_id', $row_data['currency']);
                $rowSalary = tep_db_output($row_data['expected_salary']);
                $rowExpectedSalary = tep_db_output($row_data['expected_salary_per']);

                $salary =  "$rowCurrency $rowSalary/$rowExpectedSalary";
            } else {
                $salary = null;
            }


            // resume download link
            $resume_directory = get_file_directory($row_data['jobseeker_resume'], 6);
            if (is_file(PATH_TO_MAIN_PHYSICAL_RESUME . $resume_directory . '/' . stripslashes($row_data['jobseeker_resume']))) {
                $query_string3 = encode_string("resume_id@@@" . $resume_id . "@@@resume");
                $resumeFile = [
                    'link' => tep_href_link(FILENAME_JOBSEEKER_RESUME_DOWNLOAD, (tep_not_null($resume_id) ? 'query_string=' . $query_string3 : '')),
                    'fileName' => stripslashes(stripslashes(substr($row_data['jobseeker_resume'], 14))),
                ];
            } else {
                $resumeFile = null;
            }


            // user phone number
            // $all_phone_numbers = '';
            // $all_phone_numbers .= ($row_data['jobseeker_phone'] != '' || $row_data['jobseeker_work_phone'] != '' || $row_data['jobseeker_mobile'] != '' ? '' : '');
            // $all_phone_numbers .= ($row_data['jobseeker_phone'] != '' ? tep_db_output($row_data['jobseeker_phone']) : '');
            // $all_phone_numbers .= ($row_data['jobseeker_work_phone'] != '' ? '   ' . tep_db_output($row_data['jobseeker_work_phone']) : '');
            // $all_phone_numbers .= ($row_data['jobseeker_mobile'] != '' ? '   ' . tep_db_output($row_data['jobseeker_mobile']) : '');
            // $all_phone_numbers .= ($row_data['jobseeker_phone'] != '' || $row_data['jobseeker_work_phone'] != '' || $row_data['jobseeker_mobile'] != '' ? '</span>' : '');

            // $phone_code = $row_data['phone_code'] ? $row_data['phone_code'] : '';
            // $phone_with_code = fixPhoneNumber($phone_code.$all_phone_numbers);

            $all_phone_numbers = '';
            $phoneSvg = '<i class="bi bi-phone me-2"></i>';
            
            $phone_code = ($row_data['phone_code'] != '' ? $row_data['phone_code'] . ' ' : '');
            $all_phone_numbers .= ($row_data['jobseeker_phone'] != '' || $row_data['jobseeker_work_phone'] != '' || $row_data['jobseeker_mobile'] != '' ? '' : '');
            $all_phone_numbers .= ($row_data['jobseeker_mobile'] != '' ? '   ' . $phone_code  . tep_db_output($row_data['jobseeker_mobile']) . ', ' : '');
            $all_phone_numbers .= ($row_data['jobseeker_phone'] != '' ? '<i class="bi bi-telephone ms-3 me-2"></i>' . ' ' . tep_db_output($row_data['jobseeker_phone']) : '');
            $all_phone_numbers .= ($row_data['jobseeker_work_phone'] != '' ? ',   ' . tep_db_output($row_data['jobseeker_work_phone']) : '');
            $all_phone_numbers .= ($row_data['jobseeker_phone'] != '' || $row_data['jobseeker_work_phone'] != '' || $row_data['jobseeker_mobile'] != '' ? '</span>' : '');

            $phone_with_code = ($all_phone_numbers == '--hidden--') ? '' : $phoneSvg . $all_phone_numbers;


            // address
            $address1 = tep_db_output($row_data['jobseeker_address1']);
            $address2 = tep_not_null($row_data['jobseeker_address2']) ? $row_data['jobseeker_address2'] : ' ';
            $city     = tep_not_null($row_data['jobseeker_city']) ? $row_data['jobseeker_city'] : '';
            $zip      = tep_not_null($row_data['jobseeker_zip']) ? $row_data['jobseeker_zip'] : '';
            if ($row_data['jobseeker_state_id'] > 0) {
                $state = get_name_from_table(ZONES_TABLE, TEXT_LANGUAGE . 'zone_name', 'zone_id', $row_data['jobseeker_state_id']);
            } else {
                $state = (tep_not_null($row_data['jobseeker_state']) ? ', ' . $row_data['jobseeker_state'] : '');
            }
            if (tep_not_null($row_data['jobseeker_country_id'])) {
                $country = get_name_from_table(COUNTRIES_TABLE, TEXT_LANGUAGE . 'country_name', 'id', $row_data['jobseeker_country_id']);
            } else {
                $country = '';
            }

            $row_rating = getAnyTableWhereData(JOBSEEKER_RATING_TABLE, " recruiter_id='" . $recruiterId . "' and resume_id='" . $resume_id . "'", 'point,private_notes');
            if (tep_not_null($row_rating['point'])) {
                $rating = number_format($row_rating['point'], 1);
            } else {
                $rating = 'not rated';
            }

            if (tep_not_null($row_data['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO . $row_data['jobseeker_photo'])) {
                $profile_img = tep_href_link(PATH_TO_PHOTO . $row_data['jobseeker_photo']);
            } else {
                $profile_img = defaultUserProfileUrl($row_data['user_fullname']);
            }

            // social profile array
            $socialProfileArr = [
                'facebook_url'      => tep_not_null($row_data['facebook_url']) ? $row_data['facebook_url'] : null,
                'google_url'        => tep_not_null($row_data['google_url']) ? $row_data['google_url'] : null,
                'twitter_url'       => tep_not_null($row_data['twitter_url']) ? $row_data['twitter_url'] : null,
                'linkedin_url'      => tep_not_null($row_data['linkedin_url']) ? $row_data['linkedin_url'] : null,
            ];

            // user profile array
            $userProfileArr = [
                'user_name'         => $row_data['user_fullname'],
                'job_designation'   => $row_data['target_job_titles'],
                'user_email'        => $row_data['jobseeker_email_address'],
                // 'phone'             => $all_phone_numbers,
                'phone'             => $phone_with_code,
                'address'           => "$address1 $address2 $city $zip $state $country",
                'rating'            => $rating,
                'profile_img'       => $profile_img,
            ];

            // job related info array
            $jobDataArr = [
                'target_job'            => $row_data['target_job_titles'],
                'job_type'              => $jobType,
                'job_category'          => tep_db_output($target_category),
                'salary'                => $salary,
                'relocate'              => tep_db_output($row_data['relocate']) ? tep_db_output($row_data['relocate']) : null,
            ];

            // video resume
            if ($row_data['jobseeker_video'] != '') {
                $jobseeker_video_link = $row_data['jobseeker_video'];

                // if (preg_match("/watch\?v=/i", $jobseeker_video_link)) {
                //     $photo_arr = (explode("watch?v=", (basename($jobseeker_video_link))));
                //     $photo_vd = 'https://img.youtube.com/vi/' . trim($photo_arr[1]) . '/2.jpg';
                // } elseif (preg_match("#youtu.be/(.*)#i", $jobseeker_video_link, $mat)) {
                //     $photo_vd = 'https://img.youtube.com/vi/' . trim($mat[1]) . '/2.jpg';
                // }

                $vquery_string = encode_string("video_dispaly===" . $resume_id . "===videoid");

                $videoSrc = tep_href_link(FILENAME_DISPLAY_VIDEO, "query_string1=" . $vquery_string);
            } else {
                $videoSrc = null;
            }

            $mainDataArr = [
                'resume_id'             => $resume_id,
                'jobseeker_id'          => $row_data['jobseeker_id'],
                'user_profile'          => $userProfileArr,
                'job_info'              => $jobDataArr,
                'resumeDownloadLink'    => $resumeFile,
                'objective'             => $row_data['objective'],
                'social_link'           => $socialProfileArr,
                'total_experience'      => countUserTotalExperience($row_data),
                'work_history'          => jobseekerWorkHistory($resume_id),
                'education'             => getJobseekerEducationList($resume_id),
                'skills'                => getJobseekerJobSkills($resume_id),
                'references'            => getJobseekerReference($resume_id),
                'languages'             => getJobseekerKnowLanguages($resume_id),
                'video_resume'          => $videoSrc,
                'view_resume_page_link' => tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME, 'query_string1=' . $query_string1),
                'connect_link'          => tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME, 'query_string1=' . $query_string1 . '&action=book_mark'),
                'contact_person'        => tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME, 'query_string1=' . $query_string1 . '&action1=save&action=contact'),
                'pasted_resume'         => getTextCopiedResume($resume_id),
                'rate_resume_main'      => tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME, 'query_string1=' . $query_string1),
                'download_resume'       => tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME, 'query_string1=' . $query_string1 . '&action=download')
            ];

            array_push($output, $mainDataArr);
        }

        tep_db_free_result($query);
    }

    // set response code - 200 OK
    http_response_code(200);

    echo json_encode($output);
}

function defaultUserProfileUrl($name = null, $rounded = false, $size = null, $attributes = null)
{
    $url = "https://ui-avatars.com/api/?background=random&size=$size&rounded=$rounded&name=" . urlencode($name) . "";


    return $url;
}

function getTextCopiedResume($resume_id)
{
    $cut_paste_query = "select * from " . JOBSEEKER_RESUME1_TABLE . " where resume_id='" . $resume_id . "' ";
    $cut_paste_result = tep_db_query($cut_paste_query);
    $rows = tep_db_num_rows($cut_paste_result);
    $output = [];

    if ($rows > 0) {
        while ($row1 = tep_db_fetch_array($cut_paste_result)) {

            // $pastedData = str_replace('"',"'",$row1['jobseeker_resume_text']);
            // $copiedResume = htmlspecialchars($pastedData)

            $data = [
                'cvs'   => $row1['jobseeker_resume_text']
            ];

            array_push($output, $data);
        }
        tep_db_free_result($cut_paste_result);
    }

    return $output;
}

function getJobseekerKnowLanguages($resume_id)
{
    $language_query = "select * from " . JOBSEEKER_RESUME5_TABLE . " where resume_id='" . $resume_id . "' ";
    $language_result = tep_db_query($language_query);
    $rows = tep_db_num_rows($language_result);
    $output = [];
    if ($rows > 0) {
        while ($row1 = tep_db_fetch_array($language_result)) {
            $data = [
                'language'      => get_name_from_table(JOBSEEKER_LANGUAGE_TABLE, 'name', 'languages_id', tep_db_output($row1['language'])),
                'proficiency'   => get_name_from_table(LANGUAGE_PROFICIENCY_TABLE, TEXT_LANGUAGE . 'language_proficiency', 'id', tep_db_output($row1['proficiency']))
            ];

            array_push($output, $data);
        }
        tep_db_free_result($language_result);
    }
    return $output;
}

function getJobseekerReference($resume_id)
{
    $reference_query = "select * from " . JOBSEEKER_RESUME6_TABLE . " where resume_id='" . $resume_id . "' ";
    $reference_result = tep_db_query($reference_query);
    $rows = tep_db_num_rows($reference_result);
    $output = [];
    if ($rows > 0) {
        while ($row1 = tep_db_fetch_array($reference_result)) {
            $data = [
                'name'          => tep_db_output($row1['name']),
                'company_name'  => tep_db_output($row1['company_name']),
                'country'       => get_name_from_table(COUNTRIES_TABLE, TEXT_LANGUAGE . 'country_name', 'id', tep_db_output($row1['country'])),
                'position'      => tep_db_output($row1['position_title']),
                'email'         => tep_db_output($row1['email_address']),
                'contact_no'    => tep_db_output($row1['contact_no']),
                'relationship'  => tep_db_output($row1['relationship']),
            ];
            array_push($output, $data);
        }
        tep_db_free_result($reference_result);
    }
    return $output;
}

function getJobseekerJobSkills($resume_id)
{
    $skills_query = "select * from " . JOBSEEKER_RESUME4_TABLE . " where resume_id='" . $resume_id . "' ";
    $skills_result = tep_db_query($skills_query);
    $rows = tep_db_num_rows($skills_result);
    $output = [];

    if ($rows > 0) {
        while ($row1 = tep_db_fetch_array($skills_result)) {

            $data = [
                'skill'             => tep_db_output($row1['skill']),
                'skill_level'       => get_name_from_table(SKILL_LEVEL_TABLE, TEXT_LANGUAGE . 'skill_name', 'id', tep_db_output($row1['skill_level'])),
                'last_used'         => get_name_from_table(SKILL_LAST_USED_TABLE, 'skill_last_used', 'id', tep_db_output($row1['last_used'])),
                'year_of_exp'       => tep_db_output($row1['years_of_exp']) . ' Yrs. Exp.',
            ];

            array_push($output, $data);
        }
        tep_db_free_result($skills_result);
    }

    return $output;
}

function getJobseekerEducationList($resume_id)
{
    $education_query = "select * from " . JOBSEEKER_RESUME3_TABLE . " where resume_id='" . $resume_id . "' ";
    $education_result = tep_db_query($education_query);
    $rows = tep_db_num_rows($education_result);
    $output = [];
    if ($rows > 0) {
        while ($row1 = tep_db_fetch_array($education_result)) {
            $course = get_name_from_table(EDUCATION_LEVEL_TABLE, TEXT_LANGUAGE . 'education_level_name', 'id', tep_db_output($row1['degree']));
            if (tep_not_null($row1['specialization'])) {
                $subject = ' (' . tep_db_output($row1['specialization']) . ')';
            } else {
                $subject = '';
            }

            $country = get_name_from_table(COUNTRIES_TABLE, TEXT_LANGUAGE . 'country_name', 'id', tep_db_output($row1['country'])) . ',' . tep_db_output($row1['city']);

            $start_date = '';
            $end_date = '';

            if ($row1['start_year'] > 0 && $row1['start_month'] > 0) {
                $start_date  = formate_date(tep_db_output($row1['start_year']) . '-' . tep_db_output($row1['start_month']) . '-01', " M Y ");
            }

            if ($row1['end_year'] > 0 && $row1['end_month'] > 0) {
                $end_date  = formate_date(tep_db_output($row1['end_year']) . '-' . tep_db_output($row1['end_month']) . '-01', " M Y ");
            }

            $data = [
                'specializtion' =>  $course . $subject,
                'school'        => (tep_not_null($row1['school']) ? tep_db_output($row1['school']) : null),
                'country'       => $country,
                'date'          => $start_date . '-' . $end_date,
                'info'          => (tep_not_null($row1['related_info'])) ? tep_db_output($row1['related_info']) : null,
            ];

            array_push($output, $data);
        }
        tep_db_free_result($education_result);
    }
    return $output;
}

function jobseekerWorkHistory($resume_id)
{
    if ($resume_id) {
        $work_history_query = "select * from " . JOBSEEKER_RESUME2_TABLE . " where resume_id='" . $resume_id . "' order by start_year desc ,start_month desc";
        $work_history_result = tep_db_query($work_history_query);
        $output = [];

        if (tep_db_num_rows($work_history_result) > 0) {
            while ($row1 = tep_db_fetch_array($work_history_result)) {

                $jobCategory = get_name_from_table(JOB_CATEGORY_TABLE, TEXT_LANGUAGE . 'category_name', 'id', tep_db_output($row1['company_industry']));
                $jobLocation = get_name_from_table(COUNTRIES_TABLE, TEXT_LANGUAGE . 'country_name', 'id', tep_db_output($row1['country'])) . tep_db_output($row1['city']);

                // start date
                if ($row1['start_month'] > 0 and  $row1['start_year'] > 0) {
                    $start_date = formate_date($row1['start_year'] . '-' . $row1['start_month'] . '-1', "M Y");
                } else {
                    $start_date = '-';
                }

                // end date
                if ($row1['end_month'] > 0 and $row1['end_year'] > 0) {
                    $end_date = formate_date($row1['end_year'] . '-' . $row1['end_month'] . '-1', "M Y");
                } elseif ($row1['still_work'] == 'Yes') {
                    $end_date = 'still working ';
                } else {
                    $end_date = '-';
                }

                $workingDate = $start_date . '&nbsp;&nbsp;to&nbsp;&nbsp;' . $end_date;


                $data = [
                    'company'           => tep_db_output($row1['company']),
                    'job_title'         => tep_db_output($row1['job_title']),
                    'job_category'      => $jobCategory,
                    'job_location'      => $jobLocation,
                    'working_date'      => $workingDate
                ];
                array_push($output, $data);
            }
            tep_db_free_result($work_history_result);
        }

        return $output;
    } else {
        print_r('in jobseker work history method resume id required');
        exit;
    }
}

function countUserTotalExperience($row_data): string
{
    // experience count
    $experience_string = '';
    if ($row_data['experience_year'] > 0 || $row_data['experience_month'] > 0) {
        if ($row_data['experience_year'] > 1) {
            $experience_string = $row_data['experience_year'] . ' ' . INFO_TEXT_YEARS;
        } elseif ($row_data['experience_year'] > 0) {
            $experience_string = $row_data['experience_year'] . ' ' . INFO_TEXT_YEAR;
        }

        if ($row_data['experience_month'] > 1) {
            $experience_string .= $row_data['experience_month'] . ' ' . INFO_TEXT_MONTHS;
        } elseif ($row_data['experience_month'] > 0) {
            $experience_string .= $row_data['experience_month'] . ' ' . INFO_TEXT_MONTH;
        }

        return 'Total Work Experience :' . tep_db_output($experience_string);
    }

    return false;
}
