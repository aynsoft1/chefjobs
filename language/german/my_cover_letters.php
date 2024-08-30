<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/

$action = (isset($_GET['action']) ? $_GET['action'] : '');
if($action=='add_new')
define('HEADING_TITLE', 'Anschreiben hinzufügen');
else if(tep_not_null($_GET['cID']))
{
 define('HEADING_TITLE', 'Anschreiben bearbeiten');
}
else
 define('HEADING_TITLE', 'Liste der Anschreiben');
//////////////////////////
define('TABLE_HEADING_COVER_LETTER_NAME', 'Name');
define('TABLE_HEADING_COVER_LETTER_VALUE', 'Wert');
define('TABLE_HEADING_INSERTED', 'Eingefügt');
define('TABLE_HEADING_UPDATED', 'Aktualisiert');
define('TABLE_HEADING_EDIT', 'Bearbeiten');
define('TABLE_HEADING_DELETE', 'Löschen');
define('TABLE_HEADING_VIEW', 'Sicht');
define('TABLE_HEADING_DUPLICATE', 'Duplikat');
define('INFO_TEXT_COVER_LETTER_NAME', 'Name : ');
define('INFO_TEXT_COVER_LETTER_NAME_ERROR', 'Bitte geben Sie den Namen im Anschreiben ein.');
define('INFO_TEXT_COVER_LETTER_DESCRIPTION', 'Beschreibung : ');
define('INFO_TEXT_COVER_LETTER_DESCRIPTION_ERROR', 'Bitte geben Sie eine Beschreibung des Anschreibens ein.');
define('SAME_COVER_LETTER_NAME_ERROR', 'Entschuldigung, dieser Name existiert bereits.');
define('INFO_TEXT_MAX_COVERLETTER', 'Hinweis: Sie können bis zu %d Anschreiben erstellen.');
define('ERROR_EXCEED_MAX_NO_COVERLETTER','Fehler: Entschuldigung, Sie haben bereits <b>%d</b> Anschreiben erstellt und dies ist die maximale Anzahl an Anschreiben, die ein Arbeitssuchender erstellen kann.');
define('MESSAGE_SUCCESS_SAVED','Erfolg: Anschreiben erfolgreich gespeichert.');
define('MESSAGE_SUCCESS_UPDATED','Erfolg: Anschreiben erfolgreich aktualisiert.');
define('MESSAGE_SUCCESS_DELETED','Erfolg: Anschreiben erfolgreich gelöscht.');
define('MESSAGE_SUCCESS_DUPLICATED','Erfolg: Anschreiben erfolgreich dupliziert.');
define('MESSAGE_COVER_LETTER_ERROR','Leider existiert dieses Anschreiben nicht. Wenn das Problem weiterhin besteht, wenden Sie sich bitte an den Site-Administrator.');
define('IMAGE_UPDATE','Aktualisieren');
define('IMAGE_SAVE','Speichern');
define('IMAGE_CANCEL','Stornieren');
define('INFO_TEXT_ADD_COVER_LETTER','Anschreiben hinzufügen');
?>