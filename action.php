<?php
/**  
 * Action Plugin for the timesub plugin
 * @author  Frank Schiebel <frank@ua25.de>
 * 
 */

if (!defined('DOKU_INC')) 
{    
    die();
}
class action_plugin_timesub extends DokuWiki_Action_Plugin
{
    function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('AUTH_LOGIN_CHECK', 'AFTER', $this, 'dw_start');
    }


    function dw_start(&$event, $param)
    {

        global $INPUT, $USERINFO;

        //get tsaction
        $tsaction = $INPUT->get->str('timesub');

        
        
        if ($tsaction == "getplan" && $this->getConf('make_json_available') ) {
            // access control via get token 
            // get accesstoken
            $tstoken = $INPUT->get->str('timesubtoken');
            
            // not token, no data
            if ( $tstoken === '' ) exit;
            
            // get all valid tokens from config
            //$allvalidtokens = confToHash(DOKU_CONF . "displix.auth.php");
            $allvalidtokens = explode("\n",$this->getConf('json_access_keys'));
            $allvalidtokens = array_map('trim', $allvalidtokens);
            $allvalidtokens = array_filter($allvalidtokens, function($v) { return $v != ''; });
            if( ! count($allvalidtokens) > 0 ) { exit; }
            
            $tokenkey = array_search($tstoken, $allvalidtokens);
            // no valid token? exit!
            if ( $tokenkey === false ) exit;
               
            if (!$myhf =& plugin_load('helper', 'timesub')) return false;
            $timesubday=0;
            $json = $myhf->displayTimesubJSON($timesubday,"lehrer");
            //print "<pre>";
            print($json);
            //print "</pre>";
        }

        // stop dokuwiki if a tsaction was given
        if ($tsaction != "" ) 
        {
            $event->preventDefault();
            $event->stopPropagation();
            exit;
        }
        
    }  

}
