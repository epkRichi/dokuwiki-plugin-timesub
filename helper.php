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

function getMethods() {
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
  * @author Frank Schiebel <frank@linuxmuster.net>
  * @param string $timesubday date in format dd.mm.yyyy
  * @param string $displaytarget one of lehrer/aula
  * @return string $html html formatted output
  */
function displayTimesub($timesubday,$displaytarget) {
    global $conf;

    // Aktualisiere die plaene aus dem zip
    $this->_unZipArchive();
    // zuweisung db tabelle(n) -> displaytarget
    if($displaytarget == "lehrer") {
        $substtable = strtolower($this->getConf('substtable_lehrer'));
        $headertable = strtolower($this->getConf('headertable_lehrer'));
    } else {
        $substtable = strtolower($this->getConf('substtable_aula'));
        $headertable = strtolower($this->getConf('headertable_aula'));
    }
    // hole das datum aller tage, für die vertretungen vorliegen
    $dates = $this->_timesubGetDatesAvailable($substtable);
    // setze als default das datum des nächsten tages, 
    // für den es vertretungen gibt
    if (!in_array($timesubday, $dates)) {
        $timesubday = array_keys($dates);
        $timesubday = $timesubday[0];
    }
    // baue das menü zusammen
    $html = $this->_timesubCreateMenu($dates,$timesubday);
    // lese die serialisierten vertretungsplandaten 
    // für vertretungen und header
    $substrows = $this->_timesubGetLinesForDate($timesubday,$substtable);
    // make replacements
    $substrows = $this->_makeReplacements($substrows);
    $headerrows = $this->_timesubGetLinesForDate($timesubday,$headertable);
    // bette die ergebnisse in html ein
    $html .= $this->_timesubCreateHeadertable($headerrows,$displaytarget);
    $html .= $this->_timesubCreateTable($substrows,$displaytarget);

    return $html;

}

/**
  * Creates html-table for given displaytarget
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  *
  * @param array    $substitutions array with substitutions, indexed by mdb fieldnames
  * @param string   $displaytarget "lehrer" or "aula"
  * @return string
  */
function _timesubCreateTable ($substitutions,$displaytarget) {

    if (count($substitutions) == 0 ) {
        $html = "Es liegen keine Vertretungseinträge vor.";
        return $html;
    }

    if ($displaytarget == "lehrer" ) {
        $fields = $this->getConf('dbfields_order_lehrer');
        $fields = explode(",",$fields);
        $html  = "<table class=\"timesub\">";
        $html .= "<tr><th>Lehrer</th>";
        $html .= "<th>Std.</th>";
        $html .= "<th>Klasse</th>";
        $html .= "<th>Fach</th>";
        $html .= "<th>Raum</th>";
        $html .= "<th>für</th>";
        $html .= "<th>Bemerkung</th></tr>";
    } else {
        $fields = $this->getConf('dbfields_order_aula');
        $fields = explode(",",$fields);
        $html  = "<table class=\"timesub\">";
        $html .= "<tr><th>Klasse</th>";
        $html .= "<th>Std.</th>";
        $html .= "<th>Lehrer/Fach</th>";
        $html .= "<th>vertr. durch</th>";
        $html .= "<th>Fach</th>";
        $html .= "<th>Raum</th>";
        $html .= "<th>Bemerkung</th></tr>";
    }

    $last = "";
    foreach($substitutions as $subst) {
        if ($last != $subst['F1']) {
            $trclass = $trclass == "class=\"two\"" ? "class=\"one\"" : "class=\"two\"";
        }
        $last = $subst['F1'];
        $html .= "<tr $trclass>";
        foreach ($fields as $field) {
            $field = trim($field);
            $html .= "<td>" . $subst[$field] . "</td>";
        }
        $html .= "</tr>";

    }
    $html .= "</table>";
    return $html;
}

function _timesubCreateHeadertable($datarow,$displaytarget) {

    if (count($datarow) == 0 ) {
        $html = "Es liegen keine Informationen zum Vertretungsplan vor.<br />";
        return $html;
    }

    $data = $datarow[0];

    if ($displaytarget == "lehrer" ) {
        $html  = "<h1>" . $data['Ueberschrift'] . " " . $data['Datumlang'] ."</h1>";
        $html .= "<div class=\"printtime\">" . $data['Aushangort'] . "/" . $data['Druckdatum'] . "</div>";
        $html .= "<table class=\"timesub\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
        $html .= "<tr><td class=\"header\">Abwesende Klassen:</td>";
        $html .= "<td>" . $data['AbwKlassen']. "</td></tr>";
        $html .= "<tr><td class=\"header\">Abwesende Kurse:</td>";
        $html .= "<td>" . $data['AbwKurse']. "</td></tr>";
        $html .= "<tr><td class=\"header\">Abwesende Lehrer:</td>";
        $html .= "<td>" . $data['AbwLehrer']. "</td></tr>";
        $html .= "<tr><td class=\"header\">Blockierte Räume:</td>";
        $html .= "<td>" . $data['FehlRäume']. "</td></tr>";
        $html .= "<tr><td class=\"header bittebeachten\">Bitte beachten:</td>";
        $html .= "<td class=\"bittebeachten\">" . $data['BitteBeachten']. "</td></tr>";
        $html .= "</table>";

    } else {
        // eigentlich unnötig, aber für künftige anpassungen
        // unterscheide ich lehrer und aula bei der ausgabe
        $html  = "<h1>" . $data['Ueberschrift'] . " " . $data['Datumlang'] ."</h1>";
        $html .= "<div class=\"printtime\">" . $data['Aushangort'] . "/" . $data['Druckdatum'] . "</div>";
        $html .= "<table class=\"timesub\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
        $html .= "<tr><td class=\"header\">Abwesende Klassen:</td>";
        $html .= "<td>" . $data['AbwKlassen']. "</td></tr>";
        $html .= "<tr><td class=\"header\">Abwesende Kurse:</td>";
        $html .= "<td>" . $data['AbwKurse']. "</td></tr>";
        $html .= "<tr><td class=\"header\">Abwesende Lehrer:</td>";
        $html .= "<td>" . $data['AbwLehrer']. "</td></tr>";
        $html .= "<tr><td class=\"header\">Blockierte Räume:</td>";
        $html .= "<td>" . $data['FehlRäume']. "</td></tr>";
        $html .= "<tr><td class=\"header bittebeachten\">Bitte beachten:</td>";
        $html .= "<td class=\"bittebeachten\">" . $data['BitteBeachten']. "</td></tr>";
        $html .= "</table>";
    }

    return $html;

}

/**
  * Reads serialized timesub database files
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  *
  * @param string $datumkurz date to get substitutions for (format dd.mm.yyyy)
  * @param string $dbtable database table file to read from (lowercase dokuwiki style)
  * @return array substitutions for given day
  */
function _timesubGetLinesForDate ($datumkurz,$dbtable) {
    global $conf;

    $infile = mediaFN(cleanID($this->getConf('extract_target').":timesub-".$dbtable));
    $contents = io_readFile($infile,false);
    $lines = explode("\n",$contents);
    $rows = array();
    foreach($lines as $line) {
        chop($line);
        $row = unserialize($line);
        if (isset($row['Datumkurz']) && $row['Datumkurz'] == "$datumkurz") {
            print "Push";
            $rows[] = $row;
        }
    }

    asort($rows);
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
  * @param array $dates array with verified dates for wich substs are available
  * @param string $timesubday date in format dd.mm.yyyy
  * @return string
**/
function _timesubCreateMenu($dates,$timesubday) {
    global $ID;

    $html = "<div class=\"timesubmenu\">";
    foreach($dates as $key=>$shortdate) {
        if ( $key == $timesubday ) {
            $aclass = " class=\"tstoday\"";
        } else {
            $aclass = "";
        }
        $html .=  "<a " . $aclass . " href=\"".wl($ID,"timesubday=$key")."\">".$key."</a>";
    }
    $html .= "</div>";

    return $html;

}

/**
  * Gets the dates for wich substituions are availables
  *
  * @author Frank Schiebel <frank@linuxmuster.net>
  * @param string $dbtable to decide if we read for "aula" or "lehrer"
  * @return array $dated sorted array with valid dates
**/
function _timesubGetDatesAvailable($dbtable) {
    global $conf;

    $infile = mediaFN(cleanID($this->getConf('extract_target').":timesub-".$dbtable));
    $contents = io_readFile($infile,false);
    $lines = explode("\n",$contents);
    $dates = array();
    foreach($lines as $line) {
        chop($line);
        $row = unserialize($line);
        $timestamp =  strtotime($row['Datumkurz']);
        $todaystamp = strtotime(date('Y-m-d'));
        if ($timestamp >= $todaystamp) {
            $dates[$row['Datumkurz']] = $row['Datumkurz'];
        }
    }
    asort($dates);
    return $dates;
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
                $this->_timesubMdb2Serialized($mdbfilename);
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
 * Fixes the permission and filenames of the
 * extracted files. Taken from Michel Kliers archiveupload plugin.
 *
 * @author Michael Klier <chi@chimeric.de>
 * @author Frank Schiebel <frank@linuxmuster.net>
 * @param string $dir directory to extract files to
 * @param array  $files array width filelist from archive
 * @return string filename of timesubs ts-internet.mdb file
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
            if (!is_dir($dir)) {
                io_mkdir_p($dir);
            }
            rename($this->tmpdir.'/'.$fn_old, $dir.'/'.$fn_new);
            if ( strpos($fn_old,$this->getConf('tsinternet_filename')) !== FALSE ) {
                $mdbfilename = $dir.'/'.$fn_new;
            }
            chmod($dir.'/'.$fn_new, $conf['fmode']);
            if ($this->getConf('debug')) {
                msg("Extracted: $dir/$fn_new", 1);
            }
        }
    }
    if ($this->getConf('debug')) {
        msg("Using $mdbfilename as database", 1);
    }
    return $mdbfilename;
}


/**
 * Converts the timesub mdb-Database to serialized arrays,
 * one file per db table
 *
 * @author Frank Schiebel <frank@linuxmuster.net>
 *
 * @param string filename of ts-internet.mdb
 */
function _timesubMdb2Serialized($mdbfile) {
    global $conf;

    if (!file_exists("/usr/bin/mdb-export")) {
        msg("mdb-export command: /usr/bin/mdb-export not found", -1);
        return;
    }

    if (!file_exists($mdbfile)) {
        msg("Database file $mdbfile not found", -1);
        return;
    }
    if ($this->getConf('debug')) {
        msg("Reading future events from $mdbfile");
    }

    // get tables to convert to serialized arrays from  config
    $tables_to_convert = array();
    $tables_to_convert[] = $this->getConf('headertable_aula');
    $tables_to_convert[] = $this->getConf('headertable_lehrer');
    $tables_to_convert[] = $this->getConf('substtable_aula');
    $tables_to_convert[] = $this->getConf('substtable_lehrer');

    // get data from tables an serialize it
    foreach($tables_to_convert as $table) {
        // this will not work on all webspaces, you need
        // to have mdbtools installed, but so be it (works for me ;))
        $table = trim($table);
        $csv = popen("/usr/bin/mdb-export -q \\\" -X \\\\ " . escapeshellarg($mdbfile) . " " . escapeshellarg($table), "r");
        // get headerline a keys to array
        $header = fgetcsv($csv);
        // some variables
        $rows = array();
        $number_of_substs_in_future = 0;
        $savestring = "";
        // now go through the lines and save every substitution that lies
        // in the future to the seralized data storage, forget the rest...
        while($row=fgetcsv($csv)) {
            $row = array_combine($header, $row);
            $timestamp =  strtotime($row['Datumkurz']);
            $todaystamp = strtotime(date('Y-m-d'));
            if ($timestamp >= $todaystamp) {
                // throw away "Bitte beachten" in RTF format: this
                // messes the serialized data completely up...
                unset($row['BitteRTF']);
                $savestring .= serialize($row) . "\n";
                $number_of_substs_in_future++;
            }
        }
        // wtrite data to file
        $outfile = mediaFN(cleanID($this->getConf('extract_target').":timesub-".$table));
        if ($this->getConf('debug')) {
            msg("Writing serialized data to $outfile ($number_of_substs_in_future row(s))");
        }
        io_saveFile($outfile,$savestring);
    }
}

/**
 * Replaces strings in the substtable according to config settings
 *
 * @author Frank Schiebel <frank@linuxmuster.net>
 *
 * @param array $substitutions array width substitutions, indexed by db-fieldname * @return array $substitutions
 */
function _makeReplacements($substitutions) {

    $replacements = confToHash($this->_getsavedir().'/timesub-replacements.conf');
    foreach($replacements as $replacement) {
        list($needle, $rtext,$cssclass) = explode("|",$replacement);
        $needle = trim($needle);
        $rtext = trim($rtext);
        $cssclass = trim($cssclass);
        if (!isset($cssclass)) {
            $substitutions = $this->_str_replace_deep($needle, $rtext, $substitutions);
        } else {
            $substitutions = $this->_str_replace_deep($needle, $this->_htmlTagText("$rtext","span","$cssclass"), $substitutions);
        }
    }
    return $substitutions;

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

/**
 *
 */

function _str_replace_deep($search, $replace, $subject)
{
    if (is_array($subject))
    {
        foreach($subject as &$oneSubject)
            $oneSubject = $this->_str_replace_deep($search, $replace, $oneSubject);
        unset($oneSubject);
        return $subject;
    } else {
        return str_replace($search, $replace, $subject);
    }
}

/**
  *
  */

function _htmlTagText($text,$htmltag,$cssclasses) {
    $html  = "<".$htmltag . " class=\"".$cssclasses."\">" . $text ."</$htmltag>";
    return $html;

}


}


// vim:ts=4:sw=4:et:
