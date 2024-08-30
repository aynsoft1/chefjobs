<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/



define('HEADING_TITLE','Passwort vergessen?');
define('HEADING_CONTENT','Wenn Sie Ihr Passwort vergessen haben, geben Sie bitte Ihre E-Mail-Adresse in das Feld unten ein und klicken Sie auf „Bestätigen“, um Ihr Passwort per E-Mail zu erhalten. Alternativ können Sie, wenn Sie kein <b><a href="'.tep_href_link(FILENAME_INDEX).'">'.tep_db_output(SITE_TITLE).'</a></b> Konto haben, jetzt eines erstellen <b><a href="'.tep_href_link(FILENAME_RECRUITER_REGISTRATION).'">klicken Sie hier</a>.</b>');
define('RECRUITER_FORGOT_PASSWORD_SUBJECT','Ihr Passwort für '.SITE_TITLE);
define('RECRUITER_FORGOT_PASSWORD_TEXT','<font face="Verdana, Arial, Helvetica, sans-serif" size="1">Hallo <b>%s</b>,' . "\n\n" . 'Ihr Passwort wurde geändert. '. "\n\n" .'Ihr neues Passwort lautet: <b>%s</b>' . "\n\n" . 'Danke!' . "\n" . '%s' . "\n\n" . 'Dies ist eine automatisierte Antwort, bitte antworten Sie nicht!</font>'); 
define('IMAGE_CONFIRM','Bestätigen');
define('EMAIL_ADDRESS','E-Mail-Adresse');
?>