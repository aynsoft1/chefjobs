<?
if($_POST['action']=='search')
{
 define('HEADING_TITLE', 'Liste der Jobs');
}
else
 define('HEADING_TITLE', 'Suche nach Jobs nach Unternehmen');

define('INFO_TEXT_COMPANY_NAME',' Name der Firma : ');

define('TABLE_HEADING_JOB_TITLE', 'Berufsbezeichnung');
define('TABLE_HEADING_COMPANY_NAME', 'Name der Firma');
define('TABLE_HEADING_JOB_CATEGORY', 'Stellenkategorie');
define('TABLE_HEADING_ADVERTISED', 'Nach Datum');
define('TABLE_HEADING_EXPIRED', 'Abgelaufen am');
define('TABLE_HEADING_APPLY', 'Anwenden');

define('MESSAGE_JOB_ERROR','Leider existiert dieser Job nicht. Wenn das Problem weiterhin besteht, wenden Sie sich bitte an den Site-Administrator.');
define('ERROR_NO_COMPANIES_EXISTS','Fehler: Es sind leider keine Unternehmen vorhanden.');

define('IMAGE_RATE','Rate');
define('IMAGE_BACK','Zurück');
define('INFO_TEXT_APPLY_NOW','jetzt bewerben');
define('INFO_TEXT_JOB','Arbeit');
define('INFO_TEXT_JOBS','Arbeitsplätze');
define('INFO_TEXT_HAS_MATCHED','hat abgestimmt');
define('INFO_TEXT_TO_YOUR_SEARCH_CRITERIA','zu Ihren Suchkriterien.');
define('INFO_TEXT_HAS_NOT_MATCHED','hat keine Jobs zu Ihren Suchkriterien gefunden.');
define('INFO_TEXT_HAVE','hat');
define('INFO_TEXT_COMPANY_IN_DIRECTORY','Unternehmen im Firmenverzeichnis.');
define('INFO_TEXT_NO_COMPANY_DIRECTORY','Kein Unternehmen im Firmenverzeichnis.');

//*********************************************************************//
define('INFO_TEXT_CURRENT_RATING',' Aktuelle Bewertung: ');
define('INFO_TEXT_CURRENT_RATE_IT',' Bewerte es');
define('INFO_TEXT_CURRENT_RATE_STRING','');
define('SECTION_RATE_COMPANY','Unternehmen bewerten');
define('INFO_TEXT_NOT_RATED','Nicht bewertet');
define('MESSAGE_SUCCESS_RATED','Personalvermittler erfolgreich bewertet.');
define('MESSAGE_SUCCESS_FOLLOW','Erfolg: Sie erhalten regelmäßige Updates vom Unternehmen.');
?>