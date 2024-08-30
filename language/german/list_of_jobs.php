<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if($_GET['action']=='add_screener' || $_GET['action']=='edit_screener')
{
    define('HEADING_TITLE', 'Screener hinzufügen/bearbeiten');
}
else
{
 define('HEADING_TITLE', 'Arbeitsplätze');
}
//////////////////////////
define('HEADING_TITLE_READV', 'Stelle erneut ausschreiben');
define('INFO_TEXT_READVERTISE', 'Erneute Anzeige von: ');
define('INFO_TEXT_ADVERTISE_WEEKS','Wie viele Wochen möchten Sie diese Stelle ausschreiben?:');
////////////
define('TEXT_INFO_EDIT_JOB_INTRO', 'Wählen Sie für die Aktion bitte einen Job aus.');
define('TEXT_DELETE_INTRO', 'Möchten Sie diesen Job löschen?');
define('TEXT_SCREENER_DELETE_INTRO', 'Möchten Sie diesen Screener löschen?');
if($_GET['j_status']=='deleted')
 define('TEXT_DELETE_WARNING', '<font color="red"><b>Warnung:</b></font> Mit diesem Job werden auch sämtliche Daten dieses Jobs gelöscht.');
else
 define('TEXT_DELETE_WARNING', '<font color="red"><b>Warnung: </b></font>Der Job wird nicht physisch aus der Datenbank gelöscht. Er wird einfach in die Kategorie <b>gelöschte Jobs</b> verschoben.');
define('TEXT_DELETE_SCREENER_WARNING', '<font color="red"><b>Warnung: </b></font>Screener werden physisch aus der Datenbank gelöscht.');
define('TEXT_INFO_NEW_JOB_INTRO', 'Es werden keine Jobinformationen hinzugefügt.');
define('TEXT_INFO_JOB_INSERTED', 'Job hinzugefügt am:');
define('TEXT_INFO_JOB_UPDADED', 'Auftrag geändert am:');
define('TEXT_INFO_FULLNAME', 'Name:');
define('TEXT_INFO_EMAIL', 'E-Mail-Adresse:');
define('TEXT_INFO_JOB_STARTS', 'Jobbeginn:');
define('TEXT_INFO_JOB_ENDS', 'Job endet am:');
define('TEXT_INFO_JOB_JOB_STATUS', 'Beruflicher Status:');
define('TEXT_INFO_JOB_NO_OF_JOBS', 'Max. Anzahl Jobs:');
define('TEXT_INFO_JOB_CV_STATUS', 'Lebenslaufstatus:');
define('TEXT_INFO_JOB_NO_OF_CVS', 'Max. Anzahl Tage für die Lebenslaufsuche:');
define('TABLE_HEADING_REFERENCE', 'Referenz');
define('TABLE_HEADING_TITLE', 'Titel');
define('TABLE_HEADING_INSERTED', 'Hinzugefügt');
define('TABLE_HEADING_EXPIRED', 'Läuft ab am');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_VIEWED', 'Angesehen');
define('TABLE_HEADING_CLICKED', 'Angeklickt');
define('TABLE_HEADING_APPLICATIONS', 'Anwendung');
define('TABLE_HEADING_ACTION', 'Aktion');
define('STATUS_JOB_INACTIVE', 'Inaktiv');
define('STATUS_JOB_INACTIVATE', 'Deaktivieren?');
define('STATUS_JOB_ACTIVE', 'Aktiv');
define('STATUS_JOB_ACTIVATE', 'Aktivieren Sie?');
define('MESSAGE_SUCCESS_DELETED','Erfolg: Job erfolgreich gelöscht.');
define('MESSAGE_SUCCESS_UPDATED','Erfolg: Job erfolgreich aktualisiert.');
define('MESSAGE_UNSUCCESS_SCREENER_DELETED','Fehler: Aufgrund eines Problems wird der Screener nicht gelöscht.');
define('MESSAGE_SUCCESS_SCREENER_DELETED','Erfolg: Screener erfolgreich gelöscht.');
define('MESSAGE_SUCCESS_JOB_UNDELETED','Erfolg: Der Job wurde erfolgreich erneut hinzugefügt.');
define('MESSAGE_SUCCESS_SCREENER_INSERTED','Erfolg: Screener erfolgreich eingefügt.');
define('MESSAGE_SUCCESS_SCREENER_UPDATED','Erfolg: Screener erfolgreich aktualisiert.');
define('MESSAGE_JOB_SUCCESS_READVERTISED','Erfolg: Stelle erfolgreich erneut ausgeschrieben.');
define('MESSAGE_JOB_UNSUCCESS_READVERTISED','Fehler: Aus irgendeinem Grund kann die Stelle nicht erneut ausgeschrieben werden.');
define('MESSAGE_JOB_UNSUCCESS_READVERTISED1','Fehler: Sie haben noch %s Jobpunkte übrig. Bitte reduzieren Sie Ihre Vakanzwochen');
define('MESSAGE_JOB_UNSUCCESS_READVERTISED2','Fehler: Ihnen bleiben noch %s Jobpunkte. Bitte kontaktieren Sie den Administrator.');
define('MESSAGE_JOB_ERROR','Leider existiert dieser Job nicht oder kann nicht erneut ausgeschrieben werden. Wenn er existiert, warten Sie, bis er abgelaufen ist. Wenn das Problem weiterhin besteht, wenden Sie sich bitte an den Administrator der Site.');
define('MESSAGE_SUCCESS_STATUS_UPDATED','Job erfolgreich aktualisiert.');
define('INFO_TEXT_RESUME_WEIGHT','CV-Gewicht');
define('IMAGE_NEW','Neuen Job hinzufügen');
define('IMAGE_BACK','Zurück');
define('IMAGE_NEXT','Nächste');
define('IMAGE_CANCEL','Stornieren');
define('IMAGE_INSERT','Einfügen');
define('IMAGE_EDIT','Bearbeiten');
define('IMAGE_UPDATE','Aktualisieren');
define('IMAGE_DELETE','Löschen');
define('IMAGE_CONFIRM','Löschen bestätigen');
define('IMAGE_PREVIEW','Job-Vorschau');
define('IMAGE_UPDATE','Aktualisieren');
define('IMAGE_EDIT_JOB','Bearbeiten');
define('IMAGE_DELETE_JOB','Löschen');
define('IMAGE_UNDELETE_JOB','Job rückgängig machen');
define('IMAGE_READVERTISE','Stelle erneut ausschreiben');
define('IMAGE_APPLICATIONS','Anwendungen');
define('IMAGE_ADD_SCREENER','Screener hinzufügen');
define('IMAGE_EDIT_SCREENER','Screener bearbeiten');
define('IMAGE_DELETE_SCREENER','Screener löschen');
define('IMAGE_REPORT','Berichte');
define('IMAGE_SELECTED_APPLICATIONS','Ausgewählt');
define('ERROR_QUESTION','Sie müssen Frage Nr. <b>%s</b> ausfüllen.');
define('IMAGE_VIEW_JOB','Sicht');
define('INFO_TEXT_QUESTION','Frage-');
define('INFO_TEXT_ACTIVE_JOBS','Aktive Jobs');
define('INFO_TEXT_EXPIRED_JOBS','Abgelaufene Jobs');
define('INFO_TEXT_DELETED_JOBS','Gelöschte Jobs');
define('INFO_TEXT_OTHER_JOBS','Andere Beschäftigungen');
define('INFO_TEXT_SPECIFY_VACANCY_PERIOD','Geben Sie den Vakanzzeitraum an...');
define('INFO_TEXT_ONE_WEEK','Eine Woche');
define('INFO_TEXT_TWO_WEEKS','Zwei Wochen');
define('INFO_TEXT_THREE_WEEKS','Drei Wochen');
define('INFO_TEXT_ONE_MONTH','Ein Monat');
define('INFO_TEXT_YES','Ja');
define('INFO_TEXT_NO','NEIN');
define('INFO_TEXT_SCREENER_QUESTION','Sie können dieser Ausschreibung so wenige oder bis zu zehn Screeningfragen hinzufügen. Wenn sich Arbeitssuchende auf diese Ausschreibung bewerben, werden ihnen diese Fragen als Teil des Bewerbungsprozesses präsentiert. Das Hinzufügen von Screeningfragen kann Ihnen dabei helfen, Kandidaten vorab zu qualifizieren und zu bewerten.<br>
             <em>Dies ist eine optionale Funktion und nicht erforderlich, um diese Position zu veröffentlichen. </em>');
define('INFO_TEXT_ADD_UPTO_FIVE','Fügen Sie bis zu fünf offene Fragen hinzu.<br>
             Bei offenen Fragen kann der Arbeitssuchende in einem Textfeld mit eigenen Worten antworten. Ein Beispiel für eine mögliche offene Frage ist &quot;Wo liegen Ihre Stärken und Schwächen?&quot; ');
?>