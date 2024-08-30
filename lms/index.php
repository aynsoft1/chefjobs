<?php
// if user hit the url only lms the it will redirect to the homepage of courses lms/courses-list.php

function redirect($url, $statusCode = 303)
{
   header('Location: ' . $url, true, $statusCode);
   die();
}

redirect('courses-list.php');