<?php
/**
 * DokuWiki Plugin timesub (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Frank Schiebel <frank@linuxmuster.net>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'admin.php';

class admin_plugin_timesub extends DokuWiki_Admin_Plugin {

public function getMenuSort() { return FIXME; }
public function forAdminOnly() { return false; }

public function handle() {
    if($_POST['timesubreplacements']){
        if(io_saveFile($this->_getsavedir().'/timesub-replacements.conf',$_POST['timesubreplacements'])){
            msg($this->getLang('saved'),1);
        }
    }
}

public function html() {
    global $lang;
    ptln('<h1>' . $this->getLang('menu') . '</h1>');
    echo $this->locale_xhtml('intro');
    ptln('<form action="" method="post">');
    ptln('<input type="hidden" name="do" value="admin" />');
    ptln('<input type="hidden" name="page" value="timesub" />');
    ptln('<textarea class="edit" rows="15" cols="80" style="height: 300px" name="timesubreplacements">');
    ptln(formtext(io_readFile($this->_getsavedir().'/timesub-replacements.conf')));
    ptln('</textarea><br />');
    ptln('<input type="submit" value="'.$lang['btn_save'].'" class="button" />');
    ptln('</form>');
}

/**
  * get savedir
  */
function _getsavedir() {
    global $conf;
    if ( $this->getConf('saveconftocachedir') ) {
        return rtrim($conf['savedir'],"/") . "/cache";
    } else {
        return dirname(__FILE__);
    }
}

}

// vim:ts=4:sw=4:et:
