<?
$heading = array();
$contents = array();
$heading[] = array(
    'text'  => BOX_HEADING_TEST,
    'link'  => FILENAME_ADMIN1_LIST_OF_QUIZ . '?selected_box=admin_quiz',
    'default_row' => (($_SESSION['selected_box'] == 'admin_quiz') ? '1' : ''),
    'text_image' => '<ion-icon name="time-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
);
if ($_SESSION['selected_box'] == 'admin_quiz') {
    $blank_space = '<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
    
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_QUIZ, BOX_HEADING_QUIZ);
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
    
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_TEST_CATEGORY, BOX_HEADING_TEST_CATEGORY);
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
    
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_QUESTIONS, BOX_HEADING_QUESTIONS);
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
    
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_QUIZ, BOX_HEADING_EMP_TEST, 'action=employer_test');
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }

    $content = tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_ASSESSMENT, BOX_HEADING_ASSESSMENT);
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }

    $content = tep_admin_files_boxes(FILENAME_ADMIN1_QUIZ_REPORT, BOX_HEADING_CANDIDATE, 'report=candidates');
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
    
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_QUIZ, BOX_HEADING_VIDEOS, 'action=video-list');
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_QUIZ, BOX_HEADING_ESSAY, 'action=essay-list');
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
    $content = tep_admin_files_boxes(FILENAME_ADMIN1_QUIZ_REPORT, BOX_HEADING_QUIZ_REPORT, "action=report");
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
}
$box = new left_box;
$LEFT_HTML .= $box->menuBox($heading, $contents);
