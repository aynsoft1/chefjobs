<?php
include_once("../include_files.php");
include_once('facebook_body.php');
$template->set_filenames(array('index' => 'text.htm'));
$template->pparse('index');
?>