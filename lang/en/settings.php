<?php
/**
 * english language file for timesub plugin
 *
 * @author Frank Schiebel <frank@ua25.de>
 */

$lang['headertable_aula'] = "Name of the database table which contains the static information of the header of the student version of the plan.";
$lang['headertable_lehrer'] = "Name of the database table which contains the static information of the header of the teacher version of the plan.";
$lang['substtable_aula'] = "Name of the database table which contains the substitutions for the student version of the plan.";
$lang['substtable_lehrer'] = "Name of the database table which contains the substitutions for the teacher version of the plan.";
$lang['curl_uploadsecret'] = 'Password that\'s needed for uploading the substitution plan via CURL.<br />curl -k -F secret="superstrongpassword" -F filedata=@plans.zip https://SERVER/portfolio/curlupload.php';
$lang['upload_filename'] = 'The complete DokuWiki path, including the filename, to where the uploaded plan should be saved.';
$lang['extract_target'] = 'DokuWiki namespace to which the uploaded archive should be extracted.';
$lang['debug'] = 'Enable debug messages.';
$lang['dbfields_order_lehrer'] = "Order of the database fields in the teacher table so that it matches the table header: <em>Lehrer, Std., Klasse, Fach, Raum, für, Bemerkung</em>";
$lang['dbfields_order_aula'] = "Order of the database fields in the student table so that it matches the table header: <em>Klasse, Std., Lehrer/Fach, vertr. durch, Fach, Raum, Bemerkung</em>";
$lang['tsinternet_filename'] = "Name of the uploaded database file (case sensitive!)";
$lang['saveconftocachedir'] = "Store replacement list in the cache directory?";
$lang['make_json_available'] = "Should a json version of the plan made available?";
$lang['json_access_keys'] = "Access keys for the json version of the plan:";
$lang['enable_debug_timestamp'] = "Use an alternative timestamp for debugging purposes?";
$lang['debug_timestamp'] = "Timestamp to be used instead of the current timestamp (YYYY-MM-DD). The timestamp is used to prevent displaying substitution plans for old days.";

//Setup VIM: ex: et ts=4 :
