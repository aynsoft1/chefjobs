<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if(isset($_GET['order_id']))
define('HEADING_TITLE', 'Aufträge');
else
 define('HEADING_TITLE', 'Liste der Bestellungen');
//////////////////////////
define('TABLE_HEADING_PLAN_TYPE_NAME', 'Plantyp');
define('TABLE_HEADING_PRICE', 'Preis');
define('TABLE_HEADING_PLAN_TYPE_TIME_PERIOD', 'Zeitraum');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_INSERTED', 'Startdatum');
define('TABLE_HEADING_LAST_UPDATED', 'Endtermin');
define('MESSAGE_SUCCESS_DELETED','Erfolg: Bestellung erfolgreich gelöscht.');
define('MESSAGE_ORDER_ERROR','Leider existiert diese Bestellung nicht. Wenn das Problem weiterhin besteht, wenden Sie sich bitte an den Administrator.');
define('HEADING_TITLE_SEARCH', 'Auftragsnummer:');
define('HEADING_TITLE_STATUS', 'Status:');
define('TABLE_HEADING_COMMENTS', 'Admin-Kommentare: ');
define('TABLE_HEADING_MY_COMMENTS', 'Meine Kommentare: ');
define('TABLE_HEADING_CUSTOMERS', 'Personalvermittler');
define('TABLE_HEADING_ORDER_TOTAL', 'Auftragssumme');
define('TABLE_HEADING_DATE_PURCHASED', 'Kaufdatum');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Aktion');
define('TABLE_HEADING_PRODUCTS', 'Produkte: ');
define('TABLE_HEADING_TOTAL_PRICE', 'Gesamtpreis: ');
define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Kunde benachrichtigt');
define('TABLE_HEADING_DATE_ADDED', 'Datum hinzugefügt');
define('ENTRY_CUSTOMER', 'Kunde:');
define('ENTRY_SOLD_TO', 'VERKAUFT AN:');
define('ENTRY_DELIVERY_TO', 'Lieferung nach:');
define('ENTRY_SHIP_TO', 'AUSLIEFERN:');
define('ENTRY_SHIPPING_ADDRESS', 'Lieferanschrift:');
define('ENTRY_BILLING_ADDRESS', 'Rechnungsadresse:');
define('ENTRY_PAYMENT_METHOD', 'Bezahlverfahren:');
define('ENTRY_CREDIT_CARD_TYPE', 'Kreditkartentyp:');
define('ENTRY_CREDIT_CARD_OWNER', 'Kreditkartenbesitzer:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Kreditkartennummer:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Kreditkarte läuft ab:');
define('ENTRY_SUB_TOTAL', 'Zwischensumme:');
define('ENTRY_TAX', 'Steuer:');
define('ENTRY_SHIPPING', 'Versand:');
define('ENTRY_TOTAL', 'Gesamt:');
define('ENTRY_DATE_PURCHASED', 'Kaufdatum:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_DATE_LAST_UPDATED', 'Datum der letzten Aktualisierung:');
define('ENTRY_NOTIFY_CUSTOMER', 'Kunden benachrichtigen:');
define('ENTRY_NOTIFY_COMMENTS', 'Kommentare anhängen:');
define('ENTRY_PRINTABLE', 'Rechnung drucken');
define('TEXT_INFO_HEADING_DELETE_ORDER', 'Bestellung löschen');
define('TEXT_INFO_DELETE_INTRO', 'Möchten Sie diese Bestellung wirklich löschen?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Produktmenge auffüllen');
define('TEXT_DATE_ORDER_CREATED', 'Datum erstellt:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Zuletzt bearbeitet:');
define('TEXT_INFO_PAYMENT_METHOD', 'Bezahlverfahren:');
define('TEXT_ALL_ORDERS', 'Alle Bestellungen');
define('TEXT_NO_ORDER_HISTORY', 'Kein Bestellverlauf verfügbar');
define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Bestellaktualisierung');
define('EMAIL_TEXT_ORDER_NUMBER', 'Bestellnummer:');
define('EMAIL_TEXT_INVOICE_URL', 'Detaillierte Rechnung:');
define('EMAIL_TEXT_DATE_ORDERED', 'Bestelldatum:');
define('EMAIL_TEXT_STATUS_UPDATE', 'Ihre Bestellung wurde auf den folgenden Status aktualisiert.' . "\n\n" . 'Neuer Status: %s' . "\n\n" . 'Bitte antworten Sie auf diese E-Mail, wenn Sie Fragen haben.' . "\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', 'Die Bemerkungen zu Ihrer Bestellung sind' . "\n\n%s\n\n");
define('ERROR_ORDER_DOES_NOT_EXIST', 'Fehler: Bestellung existiert nicht.');
define('SUCCESS_ORDER_UPDATED', 'Erfolg: Bestellung wurde erfolgreich aktualisiert.');
define('WARNING_ORDER_NOT_UPDATED', 'Achtung: Nichts ändern. Die Bestellung wurde nicht aktualisiert.');
define('SUCCESS_ORDER_DELETED', 'Erfolg: Bestellung wurde erfolgreich gelöscht.');
define('WARNING_ORDER_ALREADY_COMPLETED', 'Achtung: Bestellung bereits abgeschlossen. Es gibt nichts zu ändern. Wenn Sie das Konto ändern möchten, gehen Sie zum Abschnitt <b>Personalvermittler</b>.');
define('IMAGE_EDIT', 'Bestellung bearbeiten');
define('IMAGE_DELETE', 'Bestellung löschen');
define('IMAGE_ORDERS_INVOICE', 'Rechnung');
define('TABLE_HEADING_PURCHASED','Gekauft am');
?>