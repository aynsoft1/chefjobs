<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
class all_jobseekers 
{
 var $show_choose_audience, $title, $content,$attachment;
 function __construct($title, $content,$attachment) 
 {
  $this->show_choose_audience = false;
  $this->title      = $title;
  $this->content    = $content;
  $this->attachment = $attachment;
 }
 function choose_audience() 
 {
  return true;
 }
 function confirm() 
 {
  $mail_query = tep_db_query("select count(jobseeker_id) as count from " . JOBSEEKER_TABLE ." where jobseeker_newsletter ='Yes'");
  $mail = tep_db_fetch_array($mail_query);

  $confirm_string = '<table border="0" cellspacing="0" cellpadding="2">' . "\n" .
   '  <tr>' . "\n" .
   '    <td class="main"><font color="#ff0000"><b>' . sprintf('Users receiving newsletter : %s', $mail['count']) . '</b></font></td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '    <td>' . tep_draw_separator('image/pixel_trans.gif', '1', '10') . '</td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '    <td class="main"><b>' . $this->title. '</b></td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '    <td class="main"><u>' . $this->attachment. '</u></td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '    <td>' . tep_draw_separator('image/pixel_trans.gif', '1', '10') . '</td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '    <td class="main"><tt>' . nl2br(stripslashes($this->content)) . '</tt></td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '    <td>' . tep_draw_separator('image/pixel_trans.gif', '1', '10') . '</td>' . "\n" .
   '  </tr>' . "\n" .
   '  <tr>' . "\n" .
   '  '.tep_draw_form('newsletter_send', PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm_send') . "\n" .
   '    <td align="left">' .   tep_draw_checkbox_field('test_mode', 'test').'Test Mode<br>[If you click on this checkbox, E-Mail will go only to : '.EMAIL_FROM.']<br><br>'.tep_image_submit(PATH_TO_BUTTON.'button_send.gif', IMAGE_SEND) . '&nbsp;&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a></td>' . "\n" .
   '  </tr></form>' . "\n" .
   '</table>';
  return $confirm_string;
 }
 function send($id) 
 {
  set_time_limit(0);
  if($_POST['test_mode']=='test')
  {
   $mimemessage = new email(array('X-Mailer: '.tep_db_output(SITE_TITLE)));
			if(tep_not_null($this->attachment))
			{
    $file_name = basename($this->attachment);  
				$handle = fopen($this->attachment, "r");
				$contents = fread($handle, filesize($this->attachment));
				fclose($handle);
				$mimemessage->add_attachment($contents,substr($file_name,14));
			}

   $mimemessage->add_html(stripslashes($this->content));
   $mimemessage->build_message();
   $to_name=tep_db_output(SITE_OWNER);
   //$to_name="";
   $to_addr=tep_db_output(EMAIL_FROM);
   $from_name=tep_db_output(SITE_OWNER);
   //$from_name="";
   $from_addr=tep_db_output(EMAIL_FROM);
   $subject=tep_db_output($this->title);
   $mimemessage->send($to_name, $to_addr, $from_name, $from_addr, $subject);
  }
  else
  {
   //////////// History  Add/////////////////////////
 		if(tep_not_null($this->attachment))
   {
    $file_name = basename($this->attachment);  
    $new_file_name=stripslashes(date("YmdHis").substr($file_name,14));
    @copy(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.stripslashes($file_name),PATH_TO_MAIN_PHYSICAL_NEWSLETTER_HISTORY.stripslashes($new_file_name));
   }
   $sql_data_array1= array(  
                            'title'          =>tep_db_prepare_input($this->title),
                            'content'        =>stripslashes($this->content),
                            'attachment_file'=>tep_db_prepare_input($new_file_name),
                            'send_to'        =>'jobseeker',
                            'date_send'       =>'now()',
                           );
   tep_db_perform(NEWSLETTERS_HISTORY_TABLE, $sql_data_array1);
   /////////////////////////////////////
   $query_count=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE . " as jl left outer  join ".JOBSEEKER_TABLE." as j  on (jl.jobseeker_id=j.jobseeker_id )","jobseeker_newsletter ='Yes'",'count(j.jobseeker_id) as count ');
   $total_user =(int) $query_count['count'];
   $query_newletter_count =(int) $query_count['count'];
   $query_newletter_count1=100;
   if($query_newletter_count>$query_newletter_count1)
    $query_newletter_count =ceil($query_newletter_count/$query_newletter_count1);
   else
    $query_newletter_count=1;
   $x1=0;
   $lower_limit=0;
   $upper_limit=$query_newletter_count1;
   $i=1;
   //////////////////////////
   ?>
   <html>
   <head>
   <title>Jobseeker Newslewtter</title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <style type="text/css">
   <!--
   body {
    text-align: center;
    font-family: "Trebuchet MS", Arial, Helvetica;
    font-size: 12px;
    padding-top: 170px;
   }
   .mailbar {
    background-image: url(../image/mailerbar-bg.gif);
    background-repeat: no-repeat;
    height: 60px;
    width: 514px;
    margin-right: auto;
    margin-left: auto;
   }
   .baritems {
    padding-top: 10px;
    padding-left: 7px;
    text-align: left;
   }
   .statusbox {
    background-color: #FFFFFF;
    position: absolute;
    left: 50%;
    width: 514px;		
    margin-left: -257px;
    height: 90px;		
   }
   .percentbox {
    background-color: #FFFFFF;
    position: absolute;
    left: 50%;
    width: 514px;
    margin-left: -257px;
    height: 90px;
    font-family: "Trebuchet MS", Arial, Helvetica;
    font-size: 24px;
    font-weight: bold;
    color: #999999;
    text-align: center;
   }
   -->
   </style>
   </head>
   <body>
   <?php  
   // Flush all buffers
   ob_end_flush();  
   flush();
   // Define and preset variable

   // Total loops required. This is used to calculate how many percentages to advance per loop,
   // So it needs to be known before looping and progressbar starts
   $loopsize = $total_user; 

   //die();
   // Calculate how many percents to advance per loop
   $percent_per_loop = 100 / $loopsize;
   // Preset variable to remember the percentage of the previous loop
   $percent_last = 0;
   // Address to redirect to after the loop has finished.
   // Use empty string to disable redirecting
   $redirect_url = tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']);
   // $redirect_url = "http://www.ilmiantajat.fi/"
   ?>
  <div id="status1" class="statusbox" style="z-index:1; top: 90px;">
	<strong>Newsletter Sending...</strong>
	<br>
	<br>
	Total Jobseeker <?php echo $loopsize; ?> 
	</div>
<div class="mailbar">
  <div class="baritems">

  <?
   for($c=0;$c<$query_newletter_count;$c++)
   {
    $mail_query = tep_db_query("select jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as full_name from " . JOBSEEKER_LOGIN_TABLE . " as jl, ".JOBSEEKER_TABLE." as j where jl.jobseeker_id=j.jobseeker_id  and  jobseeker_newsletter ='Yes' limit $lower_limit ,$upper_limit");
    $lower_limit=$lower_limit+$query_newletter_count1;
    while ($mail = tep_db_fetch_array($mail_query)) 
    {
     $mimemessage = new email(array('X-Mailer: '.tep_db_output(SITE_TITLE)));
     if(tep_not_null($this->attachment))
     {
      $file_name = basename($this->attachment);  
      $handle = fopen($this->attachment, "r");
      $contents = fread($handle, filesize($this->attachment));
      fclose($handle);
      $mimemessage->add_attachment($contents,substr($file_name,14));
     }
     $email_message=$this->content;
     $email_address_unsub=encode_string("email###".$mail['jobseeker_email_address']."###unsubscribe");
     $email_message.="<br><p> <font color='#ff0000'>*</font> This is not an unsolicited message. You are getting this mail as you are a registered user of ".SITE_TITLE." To unsubscribe please <a href='". tep_href_link(FILENAME_UNSUBSCRIBE,'user_type=jobseeker&email='.$email_address_unsub)."'><font color='#0000ff'>Click Here</font></a> ";
     $mimemessage->add_html(stripslashes($email_message));
     $mimemessage->build_message();
     $to_name=tep_db_output($mail['full_name']);
     $to_addr=tep_db_output($mail['jobseeker_email_address']);
     $from_name=tep_db_output(SITE_OWNER);
     $from_addr=tep_db_output(EMAIL_FROM);
     $subject=tep_db_output($this->title);
     $mimemessage->send($to_name, $to_addr, $from_name, $from_addr, $subject);
     //sleep(1);
     // Here are the commands to calculate the advance in percentages and print out the necessary progress
     // By flushing out images and an optional div showing the percentage in numbers
     $percent_now = round($i * $percent_per_loop);

     if($percent_now != $percent_last) 
     {?><span class="percentbox" style="z-index:<?php echo $percent_now; ?>; top: 260px;"><?php echo $percent_now; ?> %</span><?php
      $difference = $percent_now - $percent_last;
      for($j=1;$j<=$difference;$j++) 
      {
       echo '<img src="../image/mailerbar-single.gif" width="5" height="15">';
      }
      $percent_last = $percent_now;
     }
     // Finally, flush the output of this loop, advancing the progressbar as needed
     flush();
     $i++;
    }
   }
   $id = tep_db_prepare_input($id);
   tep_db_query("update " . NEWSLETTERS_TABLE . " set date_sent = now(), status = '1' where id = '" . tep_db_input($id) . "'");
   ?>
   </div>
   </div>
   <?php
    // Finally, output the closing html and possible ending notes after the loop has finished
   ?>
   <div id="status2" class="statusbox" style="z-index:2; top: 90px;">
   <strong>Jobseeker Newsletter Sending Complated</strong>
   <br>
   <br>
   Jobseeker <?php echo $loopsize; ?>
   </div>
   <?php
	  if($redirect_url != "")
   { 
    ?>
    <script language="JavaScript" type="text/JavaScript">
    <!--
    top.location.href='<?php echo $redirect_url; ?>'
    //-->
    </script>
   <?php 
   }
   ?>
   </body>
   </html>
   <?
  }
 }
}
?>