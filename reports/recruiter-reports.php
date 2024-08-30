<?php

include_once("../include_files.php");
include_once(PATH_TO_MAIN_REPORTS_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_REPORTS);
$template->set_filenames(array(
    'reports' => 'recruiter-reports.htm',
));
include_once("../" . FILENAME_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$date = date("Y-m-d H:i:s"); // current date
$currentMonth = isset($_GET['m']) ? $_GET['m'] : intval(date('m'));
$currentYear = isset($_GET['y']) ? $_GET['y'] : intval(date('Y'));

// check if recruiter is logged in  or not
if (!check_login("recruiter")) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
} else {
    $recruiter_id   = $_SESSION['sess_recruiterid'];
    $user_type = 'recruiter';
}

// default value pass
$template->assign_vars(array(
    'report_link'    => tep_href_link(PATH_TO_REPORTS . FILENAME_REPORTS, 'm='),
    'update_message' => $messageStack->output(),
    'CHART_JS_SCRIPT' => tep_href_link('language/english/jscript/chart.umd.js'),
));


$chartData = chart_total_data($recruiter_id, $_GET['m'], $_GET['y']);
$monthDropdownElement = month_dropdown($_GET['m'], $_GET['y']);
$chartInitialValue = [
    ['month' => $currentMonth, 'year' => $currentYear, 'type' => 'clicks']
];
$template->assign_vars(array(
    'HEADING_TITLE'             => '<h1>' . HEADING_TITLE,
    'POSTING_STATS'             => POSTING_STATS,
    'TOTAL_APPLICATIONS'        => TOTAL_APPLICATIONS,
    'CLICKS'                    => CLICKS,
    'IMPRESSIONS'               => IMPRESSIONS,


    'month_dropdown'       => $monthDropdownElement,
    'total_clicks'         => $chartData['total_clicks'],
    'total_impressions'    => $chartData['total_impressions'],
    'total_applications'   => $chartData['total_applications'],
    'chartInitialValue'    => json_encode($chartInitialValue),
    'chart_url'            => tep_href_link('api/report-chart.php', 'type='),
    'tabButtons'           => createTabButtons($currentMonth, $currentYear),
));
$template->pparse('reports');

function chart_total_data($recruiter_id, $month = null, $year = null)
{

    $data = ['total_clicks' => 0, 'total_impressions' => 0, 'total_applications' => 0];

    if ($month == null || $year == null) {
        $month = intval(date('m'));
        $year = intval(date('Y'));
    }

    $query = "SELECT MONTH(job_stats.date) AS month, YEAR(job_stats.date) AS year,
                    SUM(job_stats.applications) AS total_applications, 
                    SUM(job_stats.viewed) AS total_impressions, 
                    SUM(job_stats.clicked) AS total_clicks,
                    j.recruiter_id
                FROM `job_statistics_day` AS job_stats
                LEFT JOIN jobs AS j ON j.job_id = job_stats.job_id
                WHERE MONTH(job_stats.date) = $month 
                AND YEAR(job_stats.date) = $year
                AND j.recruiter_id = $recruiter_id";
    $row_query = tep_db_query($query);
    if (tep_db_num_rows($row_query) > 0) {
        $applicationData = tep_db_fetch_array($row_query);
        $data['total_applications'] = $applicationData['total_applications'] ?? 0;
        $data['total_clicks'] = $applicationData['total_clicks'] ?? 0;
        $data['total_impressions'] = $applicationData['total_impressions'] ?? 0;
    }

    return $data;
}

function month_dropdown($selectedMonth = null, $selectedYear = null) {
    $htmlElem = '<select class="form-select form-select-lg color-dropdown" id="monthSelect">';

    if ($selectedMonth !== null && $selectedYear !== null) {
        $selectedMonthValue = $selectedMonth;
        $selectedYearValue = $selectedYear;
    } else {
        $selectedMonthValue = intval(date('m'));
        $selectedYearValue = intval(date('Y'));
    }

    $months = array(
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    );

    $monthOptions = [];

    for ($i = 0; $i < 3; $i++) {
        $monthIndex = intval(date('m')) - $i; // do not change the current month
        $yearOffset = intval(date('Y')); // do not change the current year

        if ($monthIndex < 1) {
            $monthIndex += 12; 
            $yearOffset--;
        }

        array_unshift($monthOptions, ['month' => $monthIndex, 'year' => $yearOffset]);
    }

    $htmlElem .= '<option value="" selected disabled>'.$months[$selectedMonthValue].' '.$selectedYearValue.'</option>';
    foreach ($monthOptions as $option) {
        $monthName = $months[$option['month']];
        $isSelected = ($option['month'] == $selectedMonthValue && $option['year'] == $selectedYearValue) ? '' : '';
        $htmlElem .= '<option value="' . $option['month'] . '-' . $option['year'] . '" ' . $isSelected . '>' . $monthName . ' ' . $option['year'] . '</option>';
    }

    $htmlElem .= '</select>';

    return $htmlElem;
}

function createTabButtons($monthValue, $yearValue)
{
    $htmlElem = '<ul class="nav nav-tabs border-bottom-0 ms-0 mb-2" id="myTab" role="tablist">';
    $htmlElem .= '<div class="only-for-desktop dashboard-small-title me-4">'.MOST_POPPULAR_JOBS.'</div>';

    $tabs = [
        'items' => [
            [
                'text' => ''.TOTAL_APPLICATIONS.'',
                'onclick' => 'updateChart(\'application\', '.$monthValue.', '.$yearValue.')',
                'active' => false,
                'id' => 'home-tab',
                'target' => '#home-tab-pane',
                'aria_controls' => 'home-tab-pane'
            ],
            [
                'text' => ''.CLICKS.'',
                'onclick' => 'updateChart(\'clicks\', '.$monthValue.', '.$yearValue.')',
                'active' => true,
                'id' => 'profile-tab',
                'target' => '#profile-tab-pane',
                'aria_controls' => 'profile-tab-pane'
            ],
            [
                'text' => ''.IMPRESSIONS.'',
                'onclick' => 'updateChart(\'impressions\', '.$monthValue.', '.$yearValue.')',
                'active' => false,
                'id' => 'contact-tab',
                'target' => '#contact-tab-pane',
                'aria_controls' => 'contact-tab-pane'
            ]
        ]
    ];
    // Generate button for each tab
    foreach ($tabs['items'] as $tab) {
        $activeClass = ($tab['active']) ? 'active' : '';
        $htmlElem .= '<li class="nav-item me-2 mml-0" role="presentation">';
        $htmlElem .= '<button onclick="' . $tab['onclick'] . '" class="nav-link nav-link-dashboard ' . $activeClass . '" id="' . $tab['id'] . '" data-bs-toggle="tab" data-bs-target="' . $tab['target'] . '" type="button" role="tab" aria-controls="' . $tab['aria_controls'] . '" aria-selected="' . ($tab['active'] ? 'true' : 'false') . '" tabindex="-1">' . $tab['text'] . '</button>';
        $htmlElem .= '</li>';
    }

    $htmlElem .= '</ul>';

    return $htmlElem;
}
