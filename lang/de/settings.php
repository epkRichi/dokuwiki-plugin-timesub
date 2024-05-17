<?php
/**
 * english language file for timesub plugin
 *
 * @author Frank Schiebel <frank@ua25.de>
 */

$lang['headertable_aula'] = "Name der Datenbanktabelle, welche die statischen Infos im Kopf des Plans für die Anzeige in der Aula enthält.";
$lang['headertable_lehrer'] = "Name der Datenbanktabelle, welche die statischen Infos im Kopf des Plans für die Anzeige im Lehrerzimmer enthält.";
$lang['substtable_aula'] = "Name der Datenbanktabelle, welche die Informationen zu den Vertretungen für die Anzeige in der Aula enthält.";
$lang['substtable_lehrer'] = "Name der Datenbanktabelle, welche die Informationen zu den Vertretungen für die Anzeige im Lehrerzimmer enthält.";
$lang['curl_uploadsecret'] = 'Passwort, das zum Hochladen der Vertretungspläne per CURL nötig ist.<br />curl -k -F secret="geheim" -F filedata=@plans.zip https://SERVER/portfolio/curlupload.php';
$lang['upload_filename'] = 'Der Dateiname mit vollständigen DokuWiki Pfad, als der der Plan hochgeladen werden soll.';
$lang['extract_target'] = 'DokuWiki Namespace, in den das hochgeladene Archiv ausgepackt werden soll.';
$lang['debug'] = 'Ausgaben zur Fehlersuche an/ausschalten';
$lang['dbfields_order_lehrer'] = "Reihenfolge der Datenbankfelder in der Lehrerzimer-Tabelle, so dass sie zur Kopfzeile der Anzeigetabelle passt: <em>Lehrer, Std., Klasse, Fach, Raum, für, Bemerkung</em>";
$lang['dbfields_order_aula'] = "Reihenfolge der Datenbankfelder in der Aula-Tabelle, so dass sie zur Kopfzeile der Anzeigetabelle passt: <em>Klasse, Std., Lehrer/Fach, vertr. durch, Fach, Raum, Bemerkung</em>";
$lang['tsinternet_filename'] = "Name der Datenbankdatei mit den Vertretungen (Groß/Kleinschreibung beachten!)";
$lang['saveconftocachedir'] = "Ersetzugsliste im Cache-Verzeichnis speichern?";
$lang['make_json_available'] = "Soll eine JSON Version des Planes ausgegeben werden?";
$lang['json_access_keys'] = "Welche Keys sollen einen Zugriff auf die JSON Version erlauben?";
$lang['enable_debug_timestamp'] = "Alternativen Zeitstempel zum debuggen verwenden?";
$lang['debug_timestamp'] = "Zeitstempel, der statt dem aktuellen Zeitpunkt verwendet werden soll (YYYY-MM-DD). Der Zeitstempel wird benutzt, damit Vertretungspläne für veragngene Tage nicht angezeigt werden.";

//Setup VIM: ex: et ts=4 :
