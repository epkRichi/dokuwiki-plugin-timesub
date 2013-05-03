<?php
/**
 * DokuWiki Plugin timesub (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Frank Schiebel <frank@linuxmuster.net>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

class  helper_plugin_timesub extends DokuWiki_Plugin {

function getMethods(){
    $result = array();
    $result[] = array(
      'name'   => 'timesub',
      'desc'   => 'Displays substitution tables from time/time substitute',
      'params' => array(
      'infile' => 'string',
      'outfile' => 'string',
      'number (optional)' => 'integer'),
      'return' => array('pages' => 'array'),
    );
    // and more supported methods...
    return $result;
  }

/**
  * Wrapper funktion for displaying the plan page
  *
  * This function checks the given configuration, builds an array of
  * verified plan files and creates the html output which is returned to
  * the plugins sysntax component.
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  * @param in $timesubday daynumber to decide wich plan to display 0,1,2,3...
  * @return string
  */
function displayTimesub($timesubday) {
    global $conf;

    return;

    // Aktualisiere die paene aus dem zip
    $this->_unZipArchive();


    $planfilesTested = array();
    foreach ($planfileIDs as $planfile) {
    $planfile = mediaFN($planfile);
        if(file_exists($planfile) && !is_dir($planfile)) {
            $planfilesTested[] = $planfile;
        }
    }

    $html = $this->_timesubCreateMenu($planfilesTested);

    if(!isset($planfilesTested[$timesubday])) {
        msg("Für den angegebenen Tag ist kein Plan hinterlegt.");
        return;
    }

    if(!file_exists($planfilesTested[$timesubday])) {
        msg("Datei existiert nicht:" . $planfilesTested[$timesubday] .". Passen Sie die Konfiguration an");
        return;
    }

    $html .= $this->_timesubReadHtml($planfilesTested[$timesubday]);

    return $html;

}

/**
  * Reads timesub html files
  *
  * This function reads the output of timesub info-modul html files
  * and creates a userfriendly table for displaying in dokuwiki
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  * @param string $infile filename to read html from
  * @return string
  */
function _timesubReadHtml ($infile) {
    global $conf;

    $html_output = "Plan";
    return $html_output;
}

/**
  * Create navigation menu to all plans
  *
  * This function reads every given planfile to determine the
  * real date of the plan and creates a menu with the dates to
  * click on, linked to the corresponding plan files
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  * @param array $infiles array with verified filenames to read
  * @return string
  */
function _timesubCreateMenu($infiles) {
    global $conf;
    global $ID;

    $returnhtml .= "Menu";
    return $returnhtml;

}

/**
  * Unzip uploaded archive file
  *
  * The plans have to be uploaded to the server as a zip file. This function
  * extracts the plan file according to the plugin configuration, so that 
  * later on the plans can be displayed.
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  * @return boolean
  */
function _unZipArchive() {

    global $conf;

    $upload_file = cleanID($this->getConf('upload_filename'));
    $upload_filepath = str_replace(":","/",$upload_file);
    $upload_filepath = str_replace("//","/", $conf['savedir'] . "/media/" . $upload_filepath);
    $zip_file = $upload_filepath;

    $directory = cleanID($this->getConf('extract_target'));;
    $directory = str_replace(":","/",$directory);
    $directory = str_replace("//","/", $conf['savedir'] . "/media/" . $directory);
    if ($this->getConf('debug')) {
        msg("Trying to extract zip-file: $zip_file");
        msg("Destination directory: $directory");
    }

    $dir = io_mktmpdir();
    if($dir) {
        $this->tmpdir = $dir;
    } else {
        msg('Failed to create tmp dir, check permissions of cache/ directory', -1);
        return false;
    }

    // failed to create tmp dir stop here
    if(!$this->tmpdir) return false;

    // include ZipLib
    require_once(DOKU_INC."inc/ZipLib.class.php");
    //create a new ZipLib class
    $zip = new ZipLib;

    //attempt to open the archive file
    $result = $zip->Extract($zip_file,$this->tmpdir);

    if($result) {
        $files = $zip->get_List($zip_file);
        $this->_postProcessFiles($directory, $files);
        return true;
    } else {
        return false;
    }
}

/**
 * Checks the mime type and fixes the permission and filenames of the
 * extracted files. Taken from Michel Kliers archiveupload plugin.
 *
 * @author Michael Klier <chi@chimeric.de>
 * @author Frank Schiebel <frank@linuxmuster.net>
 */
function _postProcessFiles($dir, $files) {
    global $conf;
    global $lang;

    require_once(DOKU_INC.'inc/media.php');

    $dirs     = array();
    $tmp_dirs = array();

    foreach($files as $file) {
        $fn_old = $file['filename'];            // original filename
        $fn_new = str_replace('/',':',$fn_old); // target filename
        $fn_new = str_replace(':', '/', cleanID($fn_new));

        if(substr($fn_old, -1) == '/') { 
            // given file is a directory
            io_mkdir_p($dir.'/'.$fn_new);
            chmod($dir.'/'.$fn_new, $conf['dmode']);
            array_push($dirs, $dir.'/'.$fn_new);
            array_push($tmp_dirs, $this->tmpdir.'/'.$fn_old);
        } else {
            // move the file
            // FIXME check for success ??
            rename($this->tmpdir.'/'.$fn_old, $dir.'/'.$fn_new);
            chmod($dir.'/'.$fn_new, $conf['fmode']);
            if ($this->getConf('debug')) {
                msg("Extracted: $dir/$fn_new", 1);
            }

        }
    }
}

}

// vim:ts=4:sw=4:et:
