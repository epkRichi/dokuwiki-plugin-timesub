<?php
/**
 * DokuWiki Plugin timesub (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Frank Schiebel <frank@ua25.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_timesub extends DokuWiki_Syntax_Plugin {    
    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 222;
    }


    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{timesub>.+?\}\}',$mode,'plugin_timesub');
        $this->Lexer->addSpecialPattern('\{\{timesubmenu>.+?\}\}',$mode,'plugin_timesub');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler){

        $match = substr($match, 2, -2);
        list($type, $match) = split('>', $match, 2);
        list($input, $options) = split('#', $match, 2);
        return array($type, $input, $options);

    }

    public function render($mode,  Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;
        if (!$myhf =& plugin_load('helper', 'timesub')) return false;

        // disable caching
        $renderer->info['cache'] = false;

        $type = $data[0];
        $optiondata= $data[1];


        $timesubday = 0;
        if ( isset($_REQUEST['timesubday'])) {
            $timesubday = $_REQUEST['timesubday'];
        }

        if ($type == "timesub" ) {
            $renderer->doc .= $myhf->displayTimesub($timesubday,$optiondata);
        }

        return true;
    }
}

// vim:ts=4:sw=4:et:
