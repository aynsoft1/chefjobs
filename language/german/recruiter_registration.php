<?
/*
***********************************************************
***********************************************************
**********#	Name				      : Shambhu Prasad Patnaik  		#***********
**********#	Company			    : Aynsoft							     #***********
**********#	Copyright (c) www.aynsoft.com 2004	#***********
***********************************************************
***********************************************************
*/
if(check_login("recruiter"))
define('HEADING_TITLE', 'Profil bearbeiten');
else
define('HEADING_TITLE', 'Melden Sie sich an');
define('MIN_FIRST_NAME_ERROR', 'Ihr Vorname muss mindestens ' . MIN_FIRST_NAME_LENGTH . ' Zeichen enthalten.');
define('MIN_LAST_NAME_ERROR', 'Ihr Nachname muss mindestens ' . MIN_LAST_NAME_LENGTH . ' Zeichen enthalten.');
define('SECTION_CONTACT_DETAILS', 'Ihre Kontaktdaten');
define('EMAIL_ADDRESS_ERROR', 'Ihre E-Mail-Adresse existiert bereits.');
define('EMAIL_ADDRESS_INVALID_ERROR', 'Ihre E-Mail-Adresse ist ungültig.');
define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR', 'Ihre bestätigte E-Mail-Adresse ist ungültig.');
define('EMAIL_ADDRESS_MATCH_ERROR', 'Ihre E-Mail-Adresse und Ihre bestätigte E-Mail-Adresse stimmen nicht überein.');
define('SECTION_PASSWORD_DETAILS', 'Ihr Passwort');
define('INFO_TEXT_PASSWORD', 'Passwort:');
define('MIN_PASSWORD_ERROR', 'Ihr Passwort muss mindestens ' . MIN_PASSWORD_LENGTH . ' Zeichen enthalten.');
define('INFO_TEXT_CONFIRM_PASSWORD', 'Bestätige das Passwort:');
define('MIN_CONFIRM_PASSWORD_ERROR', 'Ihr Bestätigungskennwort muss mindestens ' . MIN_PASSWORD_LENGTH . ' Zeichen enthalten.');
define('PASSWORD_MATCH_ERROR', 'Ihr Passwort und Ihre Passwortbestätigung stimmen nicht überein.');
##################################################
define('SECTION_COMPANY', 'Firmeninformation');
define('POSITION_ERROR', 'Bitte geben Sie Ihre Position ein.');
//define('MIN_POSITION_ERROR','Ihr Titel muss mindestens ' . MIN_POSITION_LENGTH . ' Zeichen enthalten.');
define('MIN_COMPANY_NAME_ERROR', 'Ihr Firmenname muss mindestens ' . MIN_COMPANY_NAME_LENGTH . ' Zeichen enthalten.');
define('MIN_ADDRESS_LINE1_ERROR', 'Ihre Adresszeile1 muss mindestens ' . MIN_ADDRESS_LINE1_LENGTH . ' Zeichen enthalten:');
define('MIN_ADDRESS2_ERROR', '');
define('ENTRY_COUNTRY_ERROR', 'Sie müssen aus dem Pulldown-Menü „Länder“ ein Land auswählen.');
define('ENTRY_STATE_ERROR_SELECT', 'Bitte wählen Sie einen Staat aus dem Pulldown-Menü „Staaten“ aus.');
define('ENTRY_STATE_ERROR', 'Sie müssen Ihren Staat oder Ihre Provinz angeben');
define('ZIP_CODE_ERROR', 'Bitte geben Sie die Postleitzahl ein.');
define('TELEPHONE_ERROR', 'Bitte geben Sie die Telefonnummer ein.');
define('INFO_TEXT_PHOTO', 'Firmenlogo');
define('INFO_TEXT_AGREEMENT', '<br>Hinweis: Wenn Sie auf die folgende Schaltfläche klicken, bedeutet dies, dass Sie unseren <a href="'.FILENAME_TERMS.'" target="terms">Terms & amp; Bedingungen</a> und <a href="'.FILENAME_PRIVACY.'" target="privacy">Datenschutzrichtlinie</a> zustimmen.');
define('MESSAGE_SUCCESS_UPDATED', 'Das Konto wurde erfolgreich aktualisiert.');
define('MESSAGE_SUCCESS_INSERTED', 'Konto erfolgreich eingefügt.');
define('NEW_RECRUITER_SUBJECT', 'Vielen Dank für Ihre Registrierung auf '.SITE_TITLE);
define('IMAGE_INSERT', 'Einfügen');
define('IMAGE_UPDATE', 'Aktualisieren');
define('INFO_TEXT_NEW_USER_REGISTRATION_DEMO', 'Neues Recruiter-Register von jobsite_demo');
define('INFO_TEXT_RECRUITER_NAME', 'Name des Anwerbers:');
define('INFO_TEXT_RECRUITER_MAIL', 'E-Mail des Personalvermittlers:');
define('LOGO_UPLOAD_ERROR', 'Bitte laden Sie ein Logo Ihres Unternehmens hoch');
define('LOGO_UPLOAD_TYPE_ERROR', 'Bitte laden Sie ein GIF-, JPEG- oder PNG-Format hoch.');
define('CAPTCHA_ERROR', 'CAPTCHA Fehler');
define('INFO_PERSONAL_DETAILS', 'Persönliche Daten');
define('INFO_COMPANY_DETAILS', 'Firmendetails');
define('INFO_UPLOAD_GIF', 'Hochladen: gif-, jpg-, jpeg-, png-Format');
define('INFO_TEXT_NEWSLETTER', 'Newsletter?');
define('INFO_TEXT_CONTINUE', 'Indem Sie fortfahren, bestätigen Sie, dass Sie unsere');
define('INFO_AND', 'und');
define('INFO_PREVIEW', 'Vorschau');
define('INFO_JOIN_USING', 'oder');
define('INFO_SUBSCRIBE', 'Newsletter');
define('INFO_ALREADY_MEMBER', 'Schon ein Mitglied?');
define('INFO_SIGN_IN', 'anmelden');
define('INFO_SIGN_UP', 'Melden Sie sich an');
/*placeholders*/
define('INFO_P_PASSWORD', 'Passwort');
define('INFO_P_EMAIL_ADDRESS', 'E-Mail-Adresse');
define('INFO_P_FNAME', 'Vorname');
define('INFO_P_LNAME', 'Nachname');
define('INFO_P_POSITION', 'Ihre Position');
define('INFO_P_ZIP', 'Postleitzahl');
define('INFO_P_CITY', 'Stadt');
define('INFO_P_WEB_ADD', 'Website-URL, z. B. http://www.aynsoft.com');
define('INFO_P_TERMS', 'Terms & amp; Bedingungen');
define('INFO_P_PRIVACY', 'Datenschutzrichtlinie');
define('INFO_P_COMPANY', 'Name der Firma');
define('INFO_P_JOIN_USING', 'oder');
define('INFO_P_COUNTRY', 'Bitte wählen Sie ein Land aus...');
define('INFO_P_FULL_ADD', 'Vollständige Adresse');
define('INFO_P_TEL_NO', 'Telefonnummer');
define('INFO_P_STATE', 'Zustand');
define('INFO_TEXT_COMPANY_PROFILE', 'Unternehmensprofil');
define('INFO_P_COMPANY_PRO', 'Unternehmen');

define('WITH_FACEBOOK','Weiter mit Facebook');
define('WITH_GOOGLE','Weiter mit Google');
define('WITH_LINKEDIN','Weiter mit LinkedIn');
define('WITH_TWITTER','Weiter mit Twitter');
?>