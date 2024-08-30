<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


define('HEADING_TITLE','Registrieren - Jobsucher');
if(check_login("jobseeker"))
	define('INFO_TEXT_CREATE_ACCOUNT','Profil bearbeiten');
else
	define('INFO_TEXT_CREATE_ACCOUNT','Erstelle deinen kostenfreien Account');
define('INFO_TEXT_JOIN','beitreten mit');
define('SECTION_ACCOUNT_DETAILS','Kontodetails');
define('SECTION_CONTACT_DETAILS','Persönliche Daten');
define('INFO_TEXT_UPLOAD_RESUME','Lebenslauf hochladen:');
define('INFO_TEXT_UPLOAD_RESUME_HELP','Hochladen: doc-, txt-, pdf-Format');
define('SECTION_ACCOUNT_PRIVACY','Privatsphäreeinstellung');
define('INFO_TEXT_PRIVACY_HIDE_ALL','Meine Kontaktinformationen vor allen Arbeitgebern verbergen.');
define('INFO_TEXT_PRIVACY_HIDE_CONTACT','Meine Kontaktdaten den Arbeitgebern anzeigen, bei denen ich mich beworben habe.');
define('INFO_TEXT_PRIVACY_HIDE_NOTHING','Meine Kontaktdaten allen Arbeitgebern anzeigen.');
define('INFO_TEXT_PRIVACY_HIDE_RESUME','Privat: Ich möchte nicht, dass Arbeitgeber meinen Lebenslauf finden.');
define('INFO_TEXT_ALREADY_MEMBER','Schon ein Mitglied?');
define('SECTION_PASSWORD','Ihr Passwort');
define('SECTION_ACCOUNT_RESUME_NAME','Name des Lebenslaufs');
define('INFO_TEXT_PRIVACY','Privatsphäre :');
define('INFO_TEXT_RESUME_SEARCHEABLE','Mein Lebenslauf ist durchsuchbar:');
define('PRIVACY_ERROR','Bitte wählen Sie Datenschutz.');
define('MIN_FIRST_NAME_ERROR','Der Vorname muss mindestens ' . MIN_FIRST_NAME_LENGTH . ' Zeichen enthalten.');
define('MIN_LAST_NAME_ERROR','Der Nachname muss mindestens ' . MIN_LAST_NAME_LENGTH . ' Zeichen enthalten.');
define('EMAIL_ADDRESS_ERROR','Diese E-Mail Adresse ist bereits vergeben.');
define('EMAIL_ADDRESS_INVALID_ERROR','Bitte geben Sie eine gültige Email Adresse an.');
define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR','Ihre bestätigte E-Mail-Adresse ist ungültig.');
define('EMAIL_ADDRESS_MATCH_ERROR','Ihre E-Mail-Adresse und Ihre bestätigte E-Mail-Adresse stimmen nicht überein.');
define('MIN_PASSWORD_ERROR','Ihr Passwort muss mindestens ' . MIN_PASSWORD_LENGTH . ' Zeichen enthalten.');
define('MIN_CONFIRM_PASSWORD_ERROR','Ihr Bestätigungskennwort muss mindestens ' . MIN_PASSWORD_LENGTH . ' Zeichen enthalten.');
define('PASSWORD_MATCH_ERROR','Ihr Passwort und Ihre Passwortbestätigung stimmen nicht überein.');
define('MIN_ADDRESS_LINE1_ERROR','Die Adresse muss mindestens ' . MIN_ADDRESS_LINE1_LENGTH . ' Zeichen enthalten.');
define('ENTRY_COUNTRY_ERROR', 'Wählen Sie bitte ein Land aus dem Pulldown-Menü „Länder“ aus.');
define('PLEASE_SELECT','Bitte auswählen...');
define('ENTRY_STATE_ERROR_SELECT', 'Wählen Sie bitte einen Staat aus dem Pulldown-Menü „Staaten“ aus.');
define('ENTRY_STATE_ERROR', 'Sie müssen Ihren Staat oder Ihre Provinz angeben');
define('MIN_CITY_ERROR','Die Stadt muss mindestens ' . MIN_CITY_LENGTH . ' Zeichen enthalten.');
define('MIN_ZIP_ERROR', 'Die Postleitzahl muss mindestens ' . MIN_ZIP_LENGTH . ' Zeichen enthalten.');
define('ENTRY_HOME_PHONE_ERROR', 'Bitte geben Sie die primäre Telefonnummer ein.');
define('INFO_TEXT_NEWS_LETTER','Newsletter?');
define('INFO_TEXT_AGREEMENT','Indem Sie fortfahren, bestätigen Sie, dass Sie unsere <a href="'.FILENAME_TERMS.'">Allgemeinen Geschäftsbedingungen</a> und <a href="'.FILENAME_PRIVACY.'">Datenschutzrichtlinie</a> akzeptieren.');
define('NEW_JOBSEEKER_SUBJECT','Vielen Dank für Ihre Registrierung auf '.SITE_TITLE);
define('NEW_JOBSEEKER_EMAIL_TEXT','Lieber <b>%s</b>,'."\n\n".'Vielen Dank für Ihre Registrierung auf '.SITE_TITLE."\n\n".'Dein Benutzername: <b>%s</b>'."\n\n".'Ihr Passwort: xxxxx'."\n\n".'Mit diesem Benutzernamen/Passwort können Sie auf unsere Site zugreifen.'. "\n\n" .'Danke!' . "\n" . '%s ( Administrator )'."\n\n" . 'Dies ist eine automatisierte Antwort, bitte antworten Sie nicht!');
define('MESSAGE_SUCCESS_UPDATED','Das Konto wurde erfolgreich aktualisiert.');
define('MESSAGE_SUCCESS_INSERTED','Konto erfolgreich eingefügt.');
define('NEW_RECRUITER_SUBJECT','Erfolgreiche Registrierung bei '.SITE_TITLE);
define('CAPTCHA_ERROR','CAPTCHA Fehler');
define('IMAGE_INSERT','Einfügen');
define('IMAGE_UPDATE','Aktualisieren');
define('IMAGE_NEXT','Weiter >>');
define('INFO_TEXT_NEW_JOBSEEKER_REGISTER','Neues Jobsucher-Register von jobsite_demo');
define('INFO_TEXT_JOBSEEKER_NAME','Name des Arbeitssuchenden');
define('INFO_TEXT_JOBSEEKER_EMAIL','E-Mail des Arbeitssuchenden');
define('INFO_TEXT_YES','Ja');
define('INFO_TEXT_NO','NEIN');
define('INFO_TEXT_SUBSCRIBE','Newsletter');
define('INFO_TEXT_PLEASE_SELECT_COUNTRY','Bitte wählen Sie ein Land');
/*placeholders*/
define('INFO_P_PASSWORD','Passwort');
define('INFO_P_EMAIL_ADDRESS','E-Mail-Adresse');
define('INFO_P_FNAME','Vorname');
define('INFO_P_LNAME','Familienname, Nachname');
define('INFO_P_FULL_ADD','Vollständige Adresse');
define('INFO_P_ZIP','PLZ');
define('INFO_P_CITY','Stadt');
define('INFO_P_PHONE','Kontakt-Nr');
define('INFO_P_SUBSCRIBE','Newsletter');
define('INFO_P_ALREADY_MEMBER','Schon ein Mitglied?');
define('INFO_P_NATIONALITY','Staatsangehörigkeit');
define('INFO_P_SIGN_IN','anmelden');
define('INFO_P_SIGN_UP','Registrieren');
define('INFO_P_MOBILE','Handy, Mobiltelefon');
define('INFO_P_JOIN_USING','oder');
define('INFO_P_STATE','Staat');

define('WITH_FACEBOOK','Weiter mit Facebook');
define('WITH_GOOGLE','Weiter mit Google');
define('WITH_LINKEDIN','Weiter mit LinkedIn');
define('WITH_TWITTER','Weiter mit Twitter');
?>