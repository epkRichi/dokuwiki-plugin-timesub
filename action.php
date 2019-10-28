<?php
/**  
 * Action Plugin for the timesub plugin
 * @author  Frank Schiebel
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
       
        if ($tsaction == "getplan")
        {
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