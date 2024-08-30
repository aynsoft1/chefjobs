<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_INDEX);
$template->set_filenames(array(
  'index' => 'index.htm'
));
include_once("../" . FILENAME_BODY);

// count quiz has question
function quizHasQuestion($id)
{
  $query = "SELECT COUNT(*) as totalQuestion FROM " . QUES_TABLE . " WHERE quiz_id = " . $id . "";

  $data = tep_db_query($query);

  $countData = tep_db_fetch_array($data);

  return $countData['totalQuestion'];
}

/**
 * return image tag
 *
 * @param [string] $value
 */
function quizPicture($value, $img_name = 'none')
{
  if ($value) {
    $image = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_FORUM_IMAGE . $value . "&size=200", '', '', '', 'align="center" class="forum-icon-bg p-3 mr-2"');
  } else {
    $image = defaultProfilePhotoUrl($img_name, false, 75);
  }
  return $image;
}

/**
 * return string with char limit
 *
 * @param string $text
 */
function limitString(string $text, $limit = 128)
{
  if ($text) {
    $val = substr($text, 0, $limit);
    return $val;
  }

  return false;
}

function get_all_public_quiz() {
  global $template;
  $query = "SELECT quiz.* FROM ".QUIZ_TABLE." as quiz 
            WHERE quiz.isActive = '1' AND quiz.save_as_template = 0 AND quiz.recruiter_id IS NULL
            ORDER BY quiz.created_at DESC";

  $result = tep_db_query($query);
  if (tep_db_num_rows($result) > 0) {
    while ($quiz = tep_db_fetch_array($result)) {
      $quizLink = $quiz['id'] . '/' . encode_forum($quiz['title']) . '.html';
      $template->assign_block_vars('quizs', array(
        'id' => tep_db_output($quiz['id']),
        'title' => '<a href="' . $quizLink . '" class="forum_heading">' . tep_db_output($quiz['title']) . '</a>',
        'totalQuestion' => quizHasQuestion($quiz['id']),
        'picture' => quizPicture($quiz['picture'], $quiz['title']),
        'description' => limitString($quiz['description'],150).'...',
        'created_at' => tep_date_short($quiz['created_at']),
        'startQuizButton' => tep_link_button_Name(
          tep_href_link(PATH_TO_QUIZ . FILENAME_QUIZ_TOPICS, 'action=quiz_start&quiz_id=' . $quiz['id']),
          'btn btn-primary ',
          TAKE_QUIZ,
          ''
        ),
      ));
    }
    tep_db_free_result($result);

    return true;
  }

  return false;
}

// get all public quiz from admin
$quizData = get_all_public_quiz();

$template->assign_vars(array(
  'HEADING_TITLE' => HEADING_TITLE,
  'QUESTION' => QUESTION,
  'page_title' => $page_title,
  'NOT_FOUND_QUIZ' => ($quizData) ? '' : '<div class="text-center h4">' . NOT_FOUND_QUIZ . '</div>',
  'update_message' => $messageStack->output()
));
$template->pparse('index');
