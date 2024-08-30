<?
if((strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.PATH_TO_ADMIN.FILENAME_ADMIN1_RATE_RESUMES) || (strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_REPORTS) || (strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_REPORTS) )
$body_load="onLoad=\"initOptionLists()\"";
elseif((strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT))
$body_load="onLoad='set_type();'";
//$_SESSION['language']='german';
//$_SESSION['languages_id']='2';
/*if($_SESSION['languages_id']!=1)
	tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONTROL_PANEL)."?language=de");
*/
$ADMIN_HEADER_HTML='
<!DOCTYPE html>
<html>

<head>
<title>'.SITE_TITLE.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <script src="lib/jquery-1.10.2.min.js"></script>

<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/bootstrap.min.css").'">
<link rel="stylesheet" type="text/css" href="'.tep_href_link("fonts/font-awesome.min.css").'">
<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/perfect-scrollbar.css").'"/>
<!--<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/material-design-iconic-font.min.css").'"/>-->
<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/jquery-jvectormap-1.2.2.css").'"/>
<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/jqvmap.min.css").'"/>
<link rel="stylesheet" type="text/css" href="'.tep_href_link("css/bootstrap-datetimepicker.min.css").'"/>
<link rel="stylesheet" href="'.tep_href_link("css/app.css").'" type="text/css"/>
<link rel="stylesheet" href="'.tep_href_link("css/admin.css").'" type="text/css"/>
<link rel="preconnect" href="https://fonts.gstatic.com">
<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800;900&family=Suez+One&display=swap" rel="stylesheet">

<link rel="stylesheet"
        href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
        integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
</head>

<body>';
if (strtolower($_SERVER['PHP_SELF'])!="/".PATH_TO_MAIN.PATH_TO_ADMIN.FILENAME_INDEX)
{
$ADMIN_HEADER_HTML.='
<div class="be-wrapper be-fixed-sidebar">
<nav class="navbar navbar-expand fixed-top be-top-header">
      <div class="container-fluid">
        <div class="be-navbar-header">
           <a class="navbar-brand display-1" href="'.tep_href_link(PATH_TO_ADMIN).'">'.SITE_TITLE.'</a>
        </div>
        
        <div class="page-title">
            <span>
                '.((check_login("admin"))?'
                <a href="'.tep_href_link().'" target="_blank">
                    Visit Site
                </a>':'').'
            </span>
        </div>


        <div class="be-right-navbar">
            <ul class="nav navbar-nav float-right be-user-nav">
<li><br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONTROL_PANEL) . '?language=en"><font color="white">English</font></a>  | 
    <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONTROL_PANEL) . '?language=de"><font color="white">German</font></a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                        role="button" aria-expanded="false" id="bellIconId">
                        <span class="bell-icon" id="notificationValue">'.notification_get_latest_orders()['total_data'].'</span>
                        <i class="bi bi-bell-fill" style="font-size: 20px;color: #b6c2c8 !important;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right mt-0 overflow-auto" role="menu" style="max-height: 550px;width: 300px;overflow-y: scroll !important;scrollbar-width: thin;left: auto;">
                        '.notification_get_latest_orders()['html'].'
                    </div>
                </li>
            </ul>
        </div>
        '.((check_login("admin"))?'
            <div class="be-right-navbar">
                <ul class="nav navbar-nav float-right be-user-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                            role="button" aria-expanded="false">
                            <i class="fa fa-user-circle" style="font-size: 20px;color: #b6c2c8 !important;" aria-hidden="true"></i>
                        </a>
                        <div class="dropdown-menu" role="menu">
                                <div class="user-info">
                                    <div class="user-name">Administrator</div>
                                    <div class="user-position online">Available</div>
                                </div>
                                <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT) . '">
                                <i class="bi bi-person-fill me-2"></i>
                                    My Account
                                </a>
                                <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_LOGOUT) . '">
                                <i class="bi bi-unlock me-2"></i>
                                    Logout
                                </a>
                        </div>
                    </li>
                </ul>
            </div>
        ':'').'

      </div>
    </nav>
';
}

 $ADMIN_HEADER_HTML.='';

 function storeNotification($type, $reference_id, $is_read)
 {
    $current_time = date('Y-m-d H:i:s');
    $sql_data = [
        'type' => $type,
        'reference_id' => $reference_id,
        'is_read' => 0,
        'created_at' => $current_time,
        'updated_at' => $current_time,
    ];

    // first check is already exists based on reference_id and type if exist do nothing otherwise store
    $query = "SELECT * FROM admin_notifications WHERE type = '$type' AND reference_id = '$reference_id'";
    $result = tep_db_query($query);
    if (tep_db_num_rows($result) > 0) {
        // Notification already exists check is_read value 0 if yes then simple return true otherwise false
        $res = tep_db_fetch_array($result);
        return $res['is_read'] == 0 ? true : false;
    }else{
        $data = tep_db_perform('admin_notifications', $sql_data);
        // return $data;
        return true;
    }
 }
 function notification_get_latest_orders()
 {
     $query = "SELECT 
                     o.orders_id, 
                     o.recruiter_name, 
                     o.payment_method, 
                     o.date_purchased, 
                     o.last_modified, 
                     o.currency, 
                     o.currency_value, 
                     r.recruiter_company_name,
                     r.recruiter_logo,
                     s.orders_status_name, 
                     ot.text AS order_total 
                 FROM 
                     " . ORDER_TABLE . " AS o 
                 LEFT JOIN 
                     " . ORDER_TOTAL_TABLE . " AS ot ON o.orders_id = ot.orders_id 
                 LEFT JOIN 
                     " . ORDER_STATUS_TABLE . " AS s ON o.orders_status = s.orders_status_id
                 LEFT JOIN 
                    ".RECRUITER_TABLE." AS r ON r.recruiter_id = o.recruiter_id
                GROUP BY 
                    o.orders_id 
                 ORDER BY 
                     o.orders_id DESC
                 LIMIT 5";
     $result = tep_db_query($query);
     $html_elem = '';
     $total_data = 0;

     if ($result && tep_db_num_rows($result) > 0) {
         while ($responseData = tep_db_fetch_array($result)) {
             $company_logo = $responseData['recruiter_logo'];
             if (tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL . PATH_TO_LOGO . $company_logo)) {
                //  $logo = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_LOGO . $company_logo . '&size=75', '', '', '', 'class="img-thumbnail resume--result-profile-img"');
                 $logo = '<img class="img-fluid" style="border-radius: 100px;width: 48px;height: 48px;" src="'.tep_href_link(FILENAME_IMAGE . "?image_name=" . PATH_TO_LOGO . $company_logo).'" />';
             } else {
                 $logo = defaultProfilePhotoUrl($responseData['recruiter_company_name'], true, 50, 'class="no-pic" id="seeker-img"');
             }
     
             // Store the notification and check if it was a new notification
            if (!empty(storeNotification('order', $responseData['orders_id'], 0))) {
                $total_data++;
            }

             $order_id = $responseData['orders_id'];
             $order_link = tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_RECRUITER_ORDERS, 'selected_box=orders');
             $order_content = '<div class="row">';
             $order_content .= '<div class="col-md-3 pe-0">' . $logo . '</div>'; // Image on the left
             $order_content .= '<div class="col-md-9">'; // Content on the right
             $order_content .= '<p class="m-0 fw-bold">' . $responseData['recruiter_name'] . '</p>';
             $order_content .= '<p class="m-0">' . $responseData['order_total'] . '</p>';
             $order_content .= '<p class="m-0">Order ID: ' . $order_id . '</p>';
             $order_content .= '<p class="m-0">' . $responseData['orders_status_name'] . '</p>';
             $order_content .= '<p class="m-0">' . $responseData['payment_method'] . '</p>';
             $order_content .= '<p class="m-0">' . date('j F Y', strtotime($responseData['date_purchased'])) . '</p>';
             $order_content .= '</div>'; // End of content column
             $order_content .= '</div>'; // End of row
     
             $html_elem .= '<a class="dropdown-item text-wrap" href="' . $order_link . '">' . $order_content . '</a>';
         }
     }
 
     $adminMailQuery = "SELECT m.id, m.msg_subject, m.inserted, m.msg_seen, m.msg_mark, m.user_name, m.user_email_address 
                         FROM admin_message AS m 
                         WHERE m.msg_status='active' 
                         ORDER BY m.inserted DESC LIMIT 0,5";
     $mailResult = tep_db_query($adminMailQuery);
 
     if ($mailResult && tep_db_num_rows($mailResult) > 0) {
         while ($responseData = tep_db_fetch_array($mailResult)) {

            // print_r(storeNotification('admin_message',$responseData['id'],0));exit;
            if (!empty(storeNotification('admin_message', $responseData['id'], 0))) {
                $total_data++;
            }

            // Assuming you have these fields available in your database
            $msg_subject = $responseData['msg_subject'];
            $user_name = $responseData['user_name'];
            $user_email_address = $responseData['user_email_address'];
            $inserted = $responseData['inserted'];
     
            // Create content for the notification
            $notification_content = '<div class="row">';
            $notification_content .= '<div class="col-md-3"><i class="fas fa-envelope fa-2x" style="width: 48px;height: 48px;background: #eee;text-align: center;border-radius: 100px;line-height: 40px;padding: 5px;color: #b1b1b1;
            "></i></div>';
            $notification_content .= '<div class="col-md-9">';
            $notification_content .= '<p class="m-0 fw-bold">Subject: ' . $msg_subject . '</p>';
            $notification_content .= '<p class="m-0">User: ' . $user_name . '</p>';
            $notification_content .= '<p class="m-0">Email Address: ' . $user_email_address . '</p>';
            $notification_content .= '<p class="m-0">Inserted: ' . $inserted . '</p>';
            $notification_content .= '</div>';
            $notification_content .= '</div>';
     
            // Assuming you have a link to view the message detail
            $notification_link = 'link_to_view_message.php?message_id=' . $responseData['id'];
    
            // Generate HTML for the notification
            $html_elem .= '<a class="dropdown-item text-wrap" href="' . $notification_link . '">' . $notification_content . '</a>';
         }
     }
 
    $employerMailQuery = "SELECT em.id, em.subject, em.attachment_file, em.inserted, em.receiver_see, em.receiver_mark, r.recruiter_company_name, r.recruiter_id 
                             FROM admin_employer_mails AS em 
                             LEFT JOIN recruiter AS r ON (em.sender_id = r.recruiter_id) 
                             WHERE em.receiver_id = 0 AND em.receiver_mail_status = 'active' 
                             ORDER BY em.inserted DESC 
                             LIMIT 0,5";
     $empmailResult = tep_db_query($employerMailQuery);
 
     if ($empmailResult && tep_db_num_rows($empmailResult) > 0) {
         while ($responseData = tep_db_fetch_array($mailResult)) {
             $mail_id = $responseData['id'];
             
            //  storeNotification('employer_mail',$responseData['id'],0);
            if (!empty(storeNotification('employer_mail', $responseData['id'], 0))) {
                $total_data++;
            }

             $subject = $responseData['subject'];
             $inserted = $responseData['inserted'];
             $receiver_see = $responseData['receiver_see'];
             $receiver_mark = $responseData['receiver_mark'];
             $recruiter_company_name = $responseData['recruiter_company_name'];
             $recruiter_id = $responseData['recruiter_id'];
 
            // Create content for the notification
            $notification_content = '<div class="row">';
            $notification_content .= '<div class="col-md-3"><i class="fas fa-envelope fa-2x"></i></div>'; // Icon on the left
            $notification_content .= '<div class="col-md-9">'; // Content on the right
            $notification_content .= '<p>Subject: ' . $subject . '</p>';
            $notification_content .= '<p>Recruiter: ' . $recruiter_company_name . '</p>';
            $notification_content .= '<p>Inserted: ' . $inserted . '</p>';
            $notification_content .= '</div>'; // End of content column
            $notification_content .= '</div>'; // End of row
             // Assuming you have a link to view the message detail
             $notification_link = 'link_to_view_message.php?mail_id=' . $mail_id;
 
             // Generate HTML for the notification
             $html_elem .= '<a class="dropdown-item text-wrap" href="' . $notification_link . '">' . $notification_content . '</a>';
         }
     }
 
     return array('html' => $html_elem, 'total_data' => $total_data);
    //  return array('html' => $html_elem, 'total_data' => 0);
 }
?>