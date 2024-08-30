<?
$heading = array();
$contents = array();
$heading[] = array(
    'text'  => BOX_HEADING_LMS,
    'link'  => LMS_ADMIN_COURSES . '?selected_box=admin_lms',
    'default_row' => (($_SESSION['selected_box'] == 'admin_lms') ? '1' : ''),
    'text_image' => '<ion-icon name="school" style="color: #000000;margin: 1px 5px 0 10px;font-size: 18px;position: absolute;"></ion-icon>',
);
if ($_SESSION['selected_box'] == 'admin_lms') {
    $blank_space = '<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
    
    $content = tep_admin_files_boxes(LMS_ADMIN_COURSES, BOX_HEADING_LMS_LIST_COURSES);
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }

    $content = tep_admin_files_boxes(LMS_CATEGORY_FILENAME, BOX_HEADING_LMS_CATEGORY);
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }

    $content = tep_admin_files_boxes(LMS_ADMIN_COURSES, BOX_HEADING_LMS_COURSE_REPORT,'action=report');
    if (tep_not_null($content)) {
        $contents[] = array('text' => $blank_space . $content);
    }
}
$box = new left_box;
$LEFT_HTML .= $box->menuBox($heading, $contents);
