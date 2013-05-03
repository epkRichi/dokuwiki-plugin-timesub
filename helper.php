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

    // Aktualisiere die paene aus dem zip
    $this->_unZipArchive();
    // hole die vertretungen für den übergebenen tag und die anzeigeart
    // @return array
    $substrows = $this->_timesubGetSubsForDate("08.05.2013","tdyntextlehrer");

    return $html;

    $planfilesTested = array();
    foreach ($planfileIDs as $planfile) {
    $planfile = mediaFN($planfile);
        if(file_exists($planfile) && !is_dir($planfile)) {
            $planfilesTested[] = $planfile;
        }
    }

    //html = $this->_timesubCreateMenu($planfilesTested);

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
function _timesubGetSubsForDate ($datumkurz,$dbtable) {
    global $conf;

    $infile = mediaFN(cleanID($this->getConf('extract_target').":timesub-".$dbtable));
    $contents = io_readFile($infile,false);
    $lines = explode("\n",$contents);
    $rows = array();
    foreach($lines as $line) {
        chop($line);
        $row = unserialize($line);
        if ($row['Datumkurz'] == "$datumkurz") {
            $rows[] = $row;
        }
    }

    return $rows;
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
        if ($files) {
            $mdbfilename = $this->_postProcessFiles($directory, $files);
            if (file_exists($mdbfilename)) {
                $this->_timesubMdb2Csv($mdbfilename);
            } elseif ($this->getConf('debug')) {
                msg("Error: $mdbfilename not found!",-1);
            }
        }
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
            if (!is_dir(dir)){
                io_mkdir_p($dir);
            }
            rename($this->tmpdir.'/'.$fn_old, $dir.'/'.$fn_new);
            $mdbfilename = $dir.'/'.$fn_new;
            chmod($dir.'/'.$fn_new, $conf['fmode']);
            if ($this->getConf('debug')) {
                msg("Extracted: $dir/$fn_new", 1);
            }
        }
        return $mdbfilename;
    }
}


/**
 * Converts the timesub mdb-Database to csv-Files
 *
 * @author Frank Schiebel <frank@linuxmuster.net>
 */
function _timesubMdb2Csv($mdbfile) {
    global $conf;

    if (!file_exists($mdbfile)) {
        msg("Database file $mdbfile nor found", -1);
        return;
    }
    if ($this->getConf('debug')) {
        msg("Reading future events from $mdbfile");
    }

    // FIXME to config
    $tables_to_convert = "TDynTextAula TDynTextLehrer TStatTextAula TStatTextLehrer";
    $tables_to_convert = explode(" ",$tables_to_convert);

    // get data from tables an serialize it
    foreach($tables_to_convert as $table) {
        // this will not work on all webspaces, you need
        // to have mdbtools installed, but so be it (works for me ;))
        $csv = popen("/usr/bin/mdb-export -q \\\" -X \\\\ " . escapeshellarg($mdbfile) . " " . escapeshellarg($table), "r");
        // get headerline a keys to array
        $header = fgetcsv($csv);
        // some variables
        $rows = array();
        $savestring = "";
        // now go through the lines an save every substitution that lies
        // in den future to the seralized data storage, forget the rest...
        while($row=fgetcsv($csv)) {
            $row = array_combine($header, $row);
            $timestamp =  strtotime($row['Datumkurz']);
            if ($timestamp > time()) {
                $savestring .= serialize($row) . "\n";
            }
        }
        // wtrite data to file
        $outfile = mediaFN(cleanID($this->getConf('extract_target').":timesub-".$table));
        io_saveFile($outfile,$savestring);

    }
}


}




// vim:ts=4:sw=4:et:
