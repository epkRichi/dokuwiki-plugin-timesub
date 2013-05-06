<?php
/**
 * Default settings for the timesub plugin
 *
 * @author Frank Schiebel <frank@linuxmuster.net>
 */

//$conf['fixme']    = 'FIXME';
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
$conf['saveconftocachedir'] = '1';
$conf['debug'] = '1';

