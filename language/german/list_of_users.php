<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if($_GET['action1']=='change_password')
define('HEADING_TITLE', 'Kennwort ändern');
else
 define('HEADING_TITLE', 'Benutzer hinzufügen/bearbeiten');
define('HEADING_TITLE1', 'Liste der Benutzer');
//////////////////////////
define('INFO_TEXT_FULL_NAME', 'Vollständiger Name : ');
define('ADD_USER','Benutzer hinzufügen');
define('FULL_NAME_ERROR','Ihr vollständiger Name muss mindestens ein Zeichen enthalten.');
define('INFO_TEXT_EMAIL_ADDRESS','E-Mail-Adresse :');
define('EMAIL_ADDRESS_ERROR','Ihre E-Mail-Adresse existiert bereits.');
define('EMAIL_ADDRESS_INVALID_ERROR','Ihre E-Mail-Adresse ist ungültig.');
define('INFO_TEXT_CONFIRM_EMAIL_ADDRESS','E-Mail-Adresse bestätigen :');
define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR','Ihre bestätigte E-Mail-Adresse ist ungültig.');
define('EMAIL_ADDRESS_MATCH_ERROR','Ihre E-Mail-Adresse und Ihre bestätigte E-Mail-Adresse stimmen nicht überein.');
define('INFO_TEXT_PASSWORD','Passwort :');
define('MIN_PASSWORD_ERROR','Ihr Passwort muss mindestens ' . MIN_PASSWORD_LENGTH . ' Zeichen enthalten.');
define('INFO_TEXT_CONFIRM_PASSWORD','Bestätige das Passwort :');
define('MIN_CONFIRM_PASSWORD_ERROR','Ihr Bestätigungskennwort muss mindestens ' . MIN_PASSWORD_LENGTH . ' Zeichen enthalten.');
define('PASSWORD_MATCH_ERROR','Ihr Passwort und Ihre Passwortbestätigung stimmen nicht überein.');
define('MESSAGE_ERROR_USER','Fehler: Entschuldigung, dieser Benutzer existiert nicht.');
define('TABLE_HEADING_NAME','Vollständiger Name');
define('TABLE_HEADING_EMAIL_ADDRESS','E-Mail-Adresse');
define('TABLE_HEADING_INSERTED','Eingefügt');
define('TABLE_HEADING_NUMBER_OF_JOBS','Arbeitsplätze');
define('TABLE_HEADING_STATUS','Status');
define('TABLE_HEADING_CHANGE_PASSWORD','Aktion');
define('INFO_CHANGE_PASSWORD','Kennwort ändern');
define('INFO_DELETE_USER','Benutzer löschen');
define('MESSAGE_SUCCESS_DELETED','Erfolg: Benutzer erfolgreich gelöscht');
define('STATUS_USER_INACTIVE','Inaktiv');
define('STATUS_USER_ACTIVATE','Aktivieren Sie ?');
define('STATUS_USER_INACTIVATE','Deaktivieren?');
define('STATUS_USER_ACTIVE','Aktiv');
define('INFO_TEXT_OLD_PASSWORD','Altes Kennwort :');
define('INFO_TEXT_NEW_PASSWORD','Neues Kennwort :');
define('INFO_TEXT_CONFIRM_PASSWORD','Bestätige das Passwort :');
define('MESSAGE_SUCCESS_INSERTED','Erfolg: Benutzer erfolgreich eingefügt.');
define('MESSAGE_SUCCESS_UPDATED','Erfolg: Benutzer erfolgreich aktualisiert.');
define('IMAGE_NEW','Neuen Benutzer hinzufügen');
define('IMAGE_UPDATE','Benutzer aktualisieren');
define('IMAGE_CONFIRM','Bestätigen');
?>