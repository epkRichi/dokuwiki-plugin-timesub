<?php
/**
 * Default settings for the timesub plugin
 *
 * @author Frank Schiebel <frank@ua25.de>
 */

$conf['headertable_aula']    = 'TStatTextAula';
$conf['headertable_lehrer']    = 'TStatTextLehrer';
$conf['substtable_aula']    = 'TDynTextAula';
$conf['substtable_lehrer']    = 'TDynTextLehrer';
$conf['dbfields_order_lehrer']  = "F1,F2,F3,F4,F5,F6,F7";
$conf['dbfields_order_aula']  = "F1,F2,F3,F4,F5,F6,F7";
$conf['tsinternet_filename'] = "TS-Internet.mdb";
$conf['curl_uploadsecret'] = '';
$conf['upload_filename'] = 'timesub:incoming:timesub.zip';
$conf['extract_target'] = 'timesub:plans';
$conf['make_json_available'] = '0';
$conf['json_access_keys'] = '';
$conf['saveconftocachedir'] = '1';
$conf['debug'] = '1';
$conf['enable_debug_timestamp'] = '0';
$confg['debug_timestamp'] = '';
