<?php

////// Autoload Classes (Requires PHP >= 5.2 ////////

function __autoload($class_name) {
  if ($class_name != "finfo") {
    require_once 'classes/' . $class_name . '.php';
  } // this if is to fix a bug with autoload and the upload.php class
}

//////////////////////////////
// If gettext isn't installed
// just return the string
//////////////////////////////

if (!function_exists("gettext")) {

  function _($string) {
    return $string;
  }

  function gettext($string) {
    return $string;
  }

}

function checkSession() {

  global $salt;

  if (isset($_SESSION['checkit'])) {

    if (md5($_SESSION['email']) . $salt == $_SESSION['checkit']) {
      $result = "ok";
    } else {
      $result = "failure";
    }
  } else {
    $result = "failure";
  }

  return $result;
}

/////////////////////
// Gets info about the user, based on IP or .htaccess, according to your config file
// This is called by control/includes/header.php, and control/login.php
/////////////////////////

function isCool($emailAdd="", $password="") {

  global $hname;
  global $uname;
  global $pword;
  global $dbName_SPlus;
  global $subcat;
  global $CpanelPath;
  global $PublicPath;
  global $debugger;
  global $salt;


  try {
    $dbc = new sp_DBConnector($uname, $pword, $dbName_SPlus, $hname);
  } catch (Exception $e) {
    echo $e;
  }

  $query = "SELECT staff_id, ip, fname, lname, email, user_type_id, ptags, extra
        FROM staff
        WHERE email = '" . scrubData($emailAdd, "email") . "' AND password = '" . scrubData($password) . "'";

  $result = MYSQL_QUERY($query);
  $numrows = mysql_num_rows($result);

  if ($debugger == "yes") {
    print "<p class=\"debugger\">$query<br /><strong>from</strong> isCool(), functions.php<br /></p>";
  }

  if ($numrows > 0) {

    $user = mysql_fetch_row($result);

    if (is_array($user)) {

//set session variables
      session_start();
      session_regenerate_id();
      session_start();

// Create session vars for the basic types
      $_SESSION['checkit'] = md5($user[4]) . $salt;
      $_SESSION['staff_id'] = $user[0];
      $_SESSION['ok_ip'] = $user[1];
      $_SESSION['fname'] = $user[2];
      $_SESSION['lname'] = $user[3];
      $_SESSION['email'] = $user[4];
      $_SESSION['user_type_id'] = $user[5];

// unpack our extra
      if ($user[7] != NULL) {
        $jobj = json_decode($user[7]);
        $_SESSION['css'] = $jobj->{'css'};
      }

// unpack our ptags
      $current_ptags = explode("|", $user[6]);

      foreach ($current_ptags as $value) {
        $_SESSION[$value] = 1;
      }

      $result = "success";
    }
  } else {

    $result = "failure";
  }

  return $result;
}

///////////////////////////
// A not-awesome way of deciding which jquery to load
// options:  tablesorter, datepicker, filetree, colorbox
///////////////////////////

function generatejQuery($use_jquery) {

  global $AssetPath;

// Always load jQuery core, ui, livequery
  $myjquery = "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js\"></script>\n
	<script type=\"text/javascript\" src=\"$AssetPath" . "jquery/jquery.livequery.min.js\"></script>\n";

// If there's not an array of values, send 'er back
  if (!is_array($use_jquery)) {
    return $myjquery;
  }

// Check to see what additional jquery files need to be loaded
  if (in_array("colorbox", $use_jquery)) {
    $myjquery .= "<script type=\"text/javascript\" src=\"$AssetPath" . "jquery/jquery.colorbox-min.js\"></script>\n
	<style type=\"text/css\">@import url($AssetPath" . "css/colorbox.css);</style>\n";
  }
  if (in_array("hover", $use_jquery)) {
    $myjquery .= "<script type=\"text/javascript\" src=\"$AssetPath" . "jquery/jquery.hoverIntent.js\"></script>\n";
  }

  if (in_array("ui", $use_jquery)) {
    $myjquery .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js\"></script>";
  }

  if (in_array("ui_styles", $use_jquery)) {
    $myjquery .= "<link rel=\"stylesheet\" href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/base/jquery-ui.css\" type=\"text/css\" media=\"all\" />";
  }

  if (in_array("tablesorter", $use_jquery)) {
    $myjquery .= "<script type=\"text/javascript\" src=\"$AssetPath" . "jquery/jquery.tablesorter.js\"></script>\n
		<script type=\"text/javascript\" src=\"$AssetPath" . "jquery/jquery.tablesorter.pager.js\"></script>\n";
  }

  return $myjquery;
}

function noPermission($text) {
  $returnval = "<div id=\"maincontent\">
    <br /><br />
    <div class=\"box\" style=\"width: 50%;\">
    <p>$text</div>
    </div>";

  return $returnval;
}

/// cURL helper function
// used by video ingest module

function curl_get($url) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_TIMEOUT, 30);

  //added @ symbol to not display if curlopt_followlocation option cannot be set.
  //This function still works without it.
  @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  $return = curl_exec($curl);
  curl_close($curl);
  return $return;
}

///////////////
// Some truncation functions
// TruncByWord is for word-based trunc, Truncate for character-based
//////////////
// Found in talkback

function TruncByWord($phrase, $max_words) {
  $phrase_array = explode(' ', $phrase);
  if (count($phrase_array) > $max_words && $max_words > 0)
    $phrase = implode(' ', array_slice($phrase_array, 0, $max_words)) . '...';
  return $phrase;
}

/*
 * string Truncate ( string str , int length)
 * @param  string  str     string to truncate /abbreviate
 * @param  int     length  length to truncate /abbreviate to
 * @param  string  traling string to use for trailing on truncated strings
 * @return string  abbreviated string
 */

function Truncate($str, $length=10, $trailing=' [more]') {
// take off chars for the trailing
  $length-=strlen($trailing);
  if (strlen($str) > $length) {
// string exceeded length, truncate and add trailing dots
    return substr($str, 0, $length) . $trailing;
  } else {
// string was already short enough, return the string
    $res = $str;
  }

  return $res;
}

function stripP($text) {
///////////////////////////
// Fix FCKeditor madness!
// * check if it begins and ends with a P tag; if so, remove them
// this problem in FCKeditor 2.6.3; perhaps will be fixed in later versions
///////////////////////////

  $matcher = preg_match("/^<p>.*<\/p>$/", trim($text));

  if ($matcher == 1) {
// trim off those p tags!
    $text = preg_replace('/^<p>(.*)<\/p>$/', '$1', $text);
  }
  return $text;
}

function uploader2($temp_path, $target_path) {

  global $upload_whitelist;

  /* Verifiy File extension;  code modified from here:
    http://hungred.com/useful-information/secure-file-upload-check-list-php/ */

  $fileName = strtolower($_FILES['uploadedfile']['name']);

  if (!in_array(end(explode('.', $fileName)), $upload_whitelist)) {
    echo _("Invalid file type: not on whitelist of ok file types");
    exit(0);
  }

  /* Add the original filename to our target path.
    Result is "uploads/filename.extension" */

  $target_path_1 = $temp_path . basename($_FILES['uploadedfile']['name']);
  $_FILES['uploadedfile']['tmp_name'];


  $target_path_2 = $target_path . basename($_FILES['uploadedfile']['name']);

  if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path_2)) {
    return basename($_FILES['uploadedfile']['name']);
  } else {
    return "no";
  }
}

function getSubBoxes($prefix="", $trunc="", $all_subs=0) {

  $subs_option_boxes = "";

  if ($all_subs == "1") {
    $subs_query = "SELECT distinct subject_id, subject, type FROM subject ORDER BY type, subject";
  } else {
    $subs_query = "SELECT distinct s.subject_id, subject, type
            FROM subject s, staff_subject ss
            WHERE s.subject_id = ss.subject_id
            AND ss.staff_id = " . $_SESSION['staff_id'] . "
            ORDER BY type, subject";
  }


  $subs_result = MYSQL_QUERY($subs_query);

  $num_subs = mysql_num_rows($subs_result);

  if ($num_subs > 0) {

// create the option
    $current_type = "";
    $subs_option_boxes = "";

    while ($myrow = mysql_fetch_array($subs_result)) {
      $subs_id = $myrow["0"];
      $subs_name = $myrow["1"];
      $subs_type = $myrow["2"];

      if ($trunc) {
        $subs_name = Truncate($subs_name, $trunc, '');
      }

      if ($current_type != $subs_type) {

        $subs_option_boxes .= "<option value=\"\" style=\"background-color: #F6E3E7\">~~" . strtoupper($subs_type) . "~~</option>";
      }

      $subs_option_boxes .= "<option value=\"$prefix$subs_id\">$subs_name</option>";

      $current_type = $subs_type;
    }
  }

  return $subs_option_boxes;
}

function getDBbySubBoxes($selected_sub) {

  $subs_option_boxes = "";
  $alphabet = "";

  $subs_query = "SELECT distinct subject_id, subject, type FROM subject WHERE type = 'Subject' ORDER BY subject";
  $subs_result = MYSQL_QUERY($subs_query);

  $num_subs = mysql_num_rows($subs_result);

  if ($num_subs > 0) {
    while ($myrow = mysql_fetch_array($subs_result)) {
      $subs_id = $myrow["0"];
      $subs_name = $myrow["1"];

      $subs_name = Truncate($subs_name, 50, '');

      $subs_option_boxes .= "<option value=\"databases.php?letter=bysub&amp;subject_id=$subs_id\"";
      if ($selected_sub == $subs_id) {
        $subs_option_boxes .= " selected=\"selected\"";
      }
      $subs_option_boxes .= ">" . _( $subs_name ) . "</option>";
    }
  }

  $alphabet .= " <select name=\"browser\" onChange=\"window.location=this.options[selectedIndex].value\">
  <option value=\"\" style=\"color: #ccc;\">- by subject -</option>
        $subs_option_boxes
        </select>";

  return $alphabet;
}

function changeMe($table, $flag, $item_id, $record_title, $staff_id) {

  global $dbName_SPlus;

  $record_title = TruncByWord($record_title, 15);

// Can be insert, update, delete; only the first creates a new record, so...
  if ($flag == "insert" || $flag == "delete") {
    $q = "insert into chchchanges (staff_id, ourtable, record_id, record_title, message)
        values(" . $staff_id . ", \"$table\", " . $item_id . ", \"" . $record_title . "\", \"$flag\")";

    $r = MYSQL_QUERY($q);
    if ($r) {
      return true;
    } else {
      return false;
    }
  } else {
// find out person who made last change to this record
    $qtest = "SELECT staff_id, chchchanges_id, message
        FROM `chchchanges`
        WHERE record_id = \"$item_id\" and ourtable = \"$table\" ORDER BY date_added DESC";

    $rtest = MYSQL_QUERY($qtest);

    $result = mysql_fetch_row($rtest);

// If there are no results, we need to insert a record
    if (!$result) {
      $q = "insert into chchchanges (staff_id, ourtable, record_id, record_title, message)
            values(" . $staff_id . ", \"$table\", " . $item_id . ", \"" . $record_title . "\", \"$flag\")";

      $r = MYSQL_QUERY($q);
      if ($r) {
        return true;
      } else {
        return false;
      }
    } else {
// If the editor is the same as last time & it's not the first record,
// just update the time; Otherwise, add a new entry to the table

      if (($result[0] == $staff_id) && ($result[2] != "insert")) {
// Editor is same as last guide updater, just update the time
        $q = "UPDATE chchchanges SET message = 'update', date_added = NOW() WHERE chchchanges_id = " . $result[1];
      } else {
//Editor is different, add entry to table
        $q = "insert into chchchanges (staff_id, ourtable, record_id, record_title, message)
                    values(" . $staff_id . ", \"$table\", " . $item_id . ", \"" . $record_title . "\", \"update\")";
      }
//print $q;

      $r = MYSQL_QUERY($q);
      if ($r) {
        return true;
      } else {
        return false;
      }
    }
  }
}

function lastModded($table, $record_id, $zero_message = 1, $show_email = 1) {
  $q = "SELECT email, DATE_FORMAT(date_added, '%b %D %Y') as last_modified
        FROM chchchanges c, staff s
        WHERE c.staff_id = s.staff_id
        AND ourtable = '$table'
        AND record_id = '$record_id'
        ORDER BY date_added DESC";
//print $q;
  $r = MYSQL_QUERY($q);
  $my_mod = mysql_fetch_row($r);

  if ($my_mod) {
    $val = $my_mod[1];
    if ($show_email == 1) {
      $val .= ", " . $my_mod[0];
    }
  } else {
    if ($zero_message == 1) {
      $val = _("Last modification date unknown.");
    } else {
      $val = "";
    }
  }

  return $val;
//return _("last modified") . " " . $my_mod[1] . " - " . $my_mod[0];
}

/*
 * scrubData
 * @param  mixed 		string	string to scrub
 * @param  string		type		options are: integer, text, ascii, richtext, email
 * @return string  	scrubbed string
 */

function scrubData($string, $type="text") {

  switch ($type) {
    case "text":
// magic quotes test
      if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
      }
      $string = strip_tags($string);
      $string = htmlspecialchars($string, ENT_QUOTES);
      break;
    case "richtext":
// magic quotes test
      if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
      }
      break;
    case "email":
// magic quotes test
      if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
      }
      //removes any tags protecting against javascript injection
      $string = strip_tags($string);

      //checks to see if the email is in valid email format, if not return a blank string
      if (!isValidEmailAddress($string)) {
        $string = '';
      }

      break;
    case "integer":
// this just makes it into a whole number; might not be a good solution...
      $string = round($string);
      break;
  }


  return $string;
}

// just to handle all the error messages
function blunDer($message, $type = 1) {
  print "<h2>" . _("Someone Has Blundered!") . "</h2>";
  if ($type == 0) {
    print "<p>" . _("(But probably not you.)") . "</p>";
  }
  print "<p>$message</p>\n";
  print _("<p>Please contact the <a href=\"mailto:$administrator\">Administrator</a></p>");
  include("../includes/footer.php");
  exit();
}

//////////////
// Erstwhile guide_functions.php
////////////////


function findDescOverride($subject_id, $title_id) {
  $query = "SELECT description_override FROM rank WHERE subject_id = '$subject_id' AND title_id = '$title_id'";
  $result = mysql_query($query);
  $override_text = mysql_fetch_row($result);
  if ($override_text[0] != "") {
    return $override_text[0];
  }
}

function showDocIcon($extension) {
  switch ($extension) {
    case "avi":
    case "mpg":
    case "mpeg":
    case "mp4":
    case "mov":
    case "wmv":
      return "film.png";
      break;
    case "doc":
    case "rtf":
    case "docx":
      return "doc.png";
      break;
    case "fla":
      return "flash.png";
      break;
    case "bmp":
    case "gif":
    case "jpg":
    case "jpeg":
    case "png";
    case "tif":
    case "tiff":
      return "picture.png";
      break;
    case "m4p":
    case "mp3":
    case "ogg":
    case "wav":
      return "music.png";
      break;
    case "pdf":
      return "pdf.png";
      break;
    case "ppt":
      return "ppt.png";
      break;
    case "psd":
      return "psd.png";
      break;
    case "swf":
      return "flash.png";
      break;
    case "txt":
      return "txt.png";
      break;
    case "xls":
    case "xlsx":
      return "xls.png";
      break;
    case "xml":
      return "code.png";
      break;
    case "zip":
      return "zip.png";
      break;
  }
}

function showIcons($ctags, $showtext = 0) {
  global $PublicPath;
  global $IconPath;
  global $AssetPath;
  $icons = "";
if ($ctags != "") {
  foreach ($ctags as $value) {
    switch ($value) {
      case "restricted":
        $icons .= "<img src=\"$IconPath/lock.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . _("Restricted Resource") . "\" title=\"" . _("Restricted Resource") . "\" /> ";
        if ($showtext == 1) {
          $icons .= " = " . _("Restricted resource") . "<br />";
        }
        break;
      case "unrestricted":
        $icons .= "<img src=\"$IconPath/lock_unlock.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . _("Unrestricted Resource") . "\" title=\"" . _("Unrestricted Resource") . "\" /> ";
        if ($showtext == 1) {
          $icons .= " = " . _("Unrestricted resource") . "<br />";
        }
        break;
      case "full_text":
        $icons.= " <img src=\"$IconPath/full_text.gif\" border=\"0\" alt=\"" . _("Some full text available") . "\" title=\"" . _("Some full text available") . "\" />";
        if ($showtext == 1) {
          $icons .= " = " . _("Some full text") . "<br />";
        }
        break;
        // jw - added paid_ejournal tag (also had to edit confiig.php)
      case "Paid_Journals":
        $icons.= " <img src=\"$IconPath/ejournal.gif\" border=\"0\" alt=\"" . _("eJournal") . "\" title=\"" . _("eJournal") . "\" />";
        if ($showtext == 1) {
          $icons .= " = " . _("eJournal") . "<br />";
        }
        break;
      case "openurl":
        $icons .= "<img src=\"$IconPath/openurl.png\" width=\"32\" height=\"13\" border=\"0\" alt=\"openURL\" title=\"openURL\" /> ";
        if ($showtext == 1) {
          $icons .= " = " . _("OpenURL enabled") . "<br /><br />";
        }
        break;
      case "images":
        $icons.= " <img src=\"$IconPath/camera.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . _("Resource contains images") . "\" title=\"" . _("Resource contains images") . "\" />";
        if ($showtext == 1) {
          $icons .= " = " . _("Images") . "<br />";
        }
        break;
      case "video":
        $icons.= " <img src=\"$IconPath/television.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . _("Resource contains video") . "\" title=\"" . _("Resource contains video") . "\" />";
        if ($showtext == 1) {
          $icons .= " = " . _("Video files") . "<br />";
        }
        break;
      case "audio":
        $icons.= " <img src=\"$IconPath/sound.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . _("Resource contains audio") . "\" title=\"" . _("Resource contains audio") . "\" />";
        if ($showtext == 1) {
          $icons .= " = " . _("Audio files") . "<br />";
        }
        break;
    }
  }
}

  return $icons;
}

function seeRecentChanges($staff_id, $limit=10) {

  global $IconPath;
  global $CpanelPath;
  $recent_activity = "";

  if ($staff_id) {

    $sq2 = "SELECT ourtable, record_id, record_title, message, date_added
        FROM chchchanges
        WHERE staff_id = '" . $staff_id . "'
        GROUP BY record_title, message, ourtable
        ORDER BY date_added DESC
        LIMIT 0, $limit";
  } else {
    $sq2 = "SELECT ourtable, record_id, record_title, message, date_added, CONCAT( fname, ' ', lname ) AS fullname
        FROM chchchanges, staff
        WHERE chchchanges.staff_id = staff.staff_id
        GROUP BY record_title, message, ourtable
        ORDER BY date_added DESC
        LIMIT 0 , $limit";
  }


  //print $sq2;

  $sr2 = MYSQL_QUERY($sq2);

  $num_rows = mysql_num_rows($sr2);

  $row_count = 0;
  $colour1 = "oddrow";
  $colour2 = "evenrow";

  if ($num_rows != 0) {

    while ($myrow2 = mysql_fetch_array($sr2)) {

      $row_colour = ($row_count % 2) ? $colour1 : $colour2;

      $intro = "";
      $message = $myrow2["3"];

      switch ($myrow2["0"]) {
        case "guide":
          $intro = _("Research Guides") . " $message";
          $linkit = $CpanelPath . "guides/guide.php?subject_id=$myrow2[1]";
          break;
        case "faq":
          $intro = _("FAQ") . " $message";
          $linkit = $CpanelPath . "faq/faq.php?faq_id=$myrow2[1]";
          break;
        case "talkback":
          $intro = _("Talk Back") . " $message";
          $linkit = $CpanelPath . "talkback/talkback.php?talkback_id=$myrow2[1]";
          break;
        case "subject":
          switch ($message) {
            case "insert":
              $intro = _("Research Guide created");
              break;
            case "update":
              $intro = _("Research Guide metadata");
              break;
            case "delete":
              $intro = _("Research Guide deleted");
              break;
          }

          $linkit = $CpanelPath . "guides/guide.php?subject_id=$myrow2[1]";
          break;
        case "record":
          $intro = _("Record") . " $message";
          $linkit = $CpanelPath . "records/record.php?record_id=$myrow2[1]";
          break;
        case "staff_details":
          $intro = _("Staff Details updated.");
          break;
        case "staff":
          switch ($message) {
            case "insert":
              $intro = _("New User Added");
              $linkit = $CpanelPath . "admin/user.php?staff_id=$myrow2[1]";
              break;
            case "update":
              $intro = _("User Information updated");
              $linkit = $CpanelPath . "admin/user.php?staff_id=$myrow2[1]";
              break;
            case "delete":
              $intro = _("User deleted");
              break;
          }

          break;
        case "video":
          $intro = _("Video") . " $message";
          $linkit = $CpanelPath . "videos/video.php?video_id=$myrow2[1]";
          break;
      }

      $recent_activity .= "<div style=\"clear: both; padding: 3px 5px;\" class=\"$row_colour\"> <img src=\"$IconPath/required.png\" alt=\"bullet\" /> $intro";
      if ($myrow2["2"] != "") {
        $recent_activity .= ": <a href=\"$linkit\" style=\"color: #333;\" title=\"Took place: $myrow2[4]\">$myrow2[2]</a>";
      }

      if (!$staff_id) {
        $recent_activity .= " <span style=\"color: #666; font-size: 10px;\">$myrow2[5]</span>";
      }
      $recent_activity .= "</div>";
      $row_count++;
    }
  }
  return $recent_activity;
}

function getHeadshot($email, $pic_size="medium") {

  $name_id = explode("@", $email);
  $lib_image = "_" . $name_id[0];
  global $AssetPath;

  $headshot = "<img src=\"" . $AssetPath . "" . "/users/$lib_image/headshot.jpg\" alt=\"$email\" title=\"$email\"";
  switch ($pic_size) {
    case "small":
      $headshot .= " width=\"50\"";
      break;
    case "medium":
      $headshot .= " width=\"70\"";
      break;
  }

  $headshot .= " class=\"staff_photo\" align=\"left\" />";
  return $headshot;
}

// Display staff images
function showStaff($email, $picture=1, $pic_size="medium", $link_name = 0) {
  global $tel_prefix;
  global $mod_rewrite;

  $q = "SELECT fname, lname, title, tel, email FROM staff WHERE email = '$email'";

  $r = MYSQL_QUERY($q);

  $row_count = mysql_num_rows($r);

  if ($row_count == 0) {
    return;
  }

  while ($myrow = mysql_fetch_array($r)) {

    if ($link_name == 1) {
      $email = $myrow["email"];
      $name_id = explode("@", $email);

      if ($mod_rewrite == 1) {
        $linky = "staff_details.php?name=" . $name_id[0];
      } else {
        $linky = "staff_details.php?name=" . $name_id[0];
      }
      $full_name = "<a href=\"$linky\">" . $myrow[0] . " " . $myrow[1] . "</a>";
    } else {
      $full_name = $myrow[0] . " " . $myrow[1];
    }


    $staffer = "<p style=\"clear: both;\">";
    $staffer .= getHeadshot($email, $pic_size);
    $staffer .= "<strong>$full_name</strong><br />$myrow[2]<br />$tel_prefix$myrow[3]<br /><a href=\"mailto:$myrow[4]\">$myrow[4]</a></p>";
  }

  return $staffer;
}

function getDBbyTypeBoxes($selected_type = "") {

  $types_option_boxes = "";
  $alphabet = "";
  global $all_ctags;

  sort($all_ctags);

    foreach ($all_ctags as $tag) {

      $new_tag = ucwords(preg_replace('/_/', ' ', $tag));

      $types_option_boxes .= "<option value=\"databases.php?letter=bytype&amp;type=$tag\"";

      if ($selected_type == $tag) {
        $types_option_boxes .= " selected=\"selected\"";
      }
      $types_option_boxes .= ">" . _( $new_tag ) . "</option>";
    }


  $alphabet .= " <select name=\"browser\" onChange=\"window.location=this.options[selectedIndex].value\">
  <option value=\"\">- by format -</option>
    <option value=\"databases.php?letter=bytype\">" . _("List All Format Types") . "</option>
        $types_option_boxes
        </select>";

  return $alphabet;
}

function getLetters($table, $selected = "A", $numbers = 1) {

  $selected = scrubData($selected);

  $selected_subject = "";
  if (isset($_GET["subject_id"])) {
    $selected_subject = intval($_GET["subject_id"]);
  }

  $selected_type = "";
  if (isset($_GET["type"])) {
    $selected_type = $_GET["type"];
  }

  $showsearch = 0;
  $abc_link = "";

// If it's an array, just plunk that stuff in //

  if (is_array($table)) {
    $letterz = $table;
    $showsearch = 0;
  } else {

    $shownew = 1;
    $extras = "";

    switch ($table) {
      case "databases":
        $lq = "SELECT distinct UCASE(left(title,1)) AS initial
                    FROM location l, location_title lt, title t
                    WHERE l.location_id = lt.location_id AND lt.title_id = t.title_id
                    AND eres_display = 'Y'
                    AND left(title,1) REGEXP '[A-Z]'
                    ORDER BY initial";
        $abc_link = "databases.php";
        $shownew = 0;
        break;
    }

//print $lq;

    $lr = MYSQL_QUERY($lq);

    while ($mylets = mysql_fetch_row($lr)) {
      $letterz[] = $mylets[0];
    }
    if ($numbers == 1) {
      $letterz[] = "Num";
    }

    $letterz[] = "All";

    if (!$selected) {
      $selected = "ALL";
    }
  }

  $alphabet = "<div id=\"letterhead\" align=\"center\">";

  foreach ($letterz as $value) {
    if ($value == $selected) {
      $alphabet .= "<span id=\"selected_letter\">$value</span> ";
    } else {
      $alphabet .= "<a href=\"$abc_link?letter=$value\">$value</a>";
    }
  }

  if ($table == "databases") {
    $alphabet .= getDBbyTypeBoxes($selected_type);
    $alphabet .= getDBbySubBoxes($selected_subject);
  }

  if ($showsearch != 0) {
    $alphabet .= "<input type=\"text\" id=\"letterhead_suggest\" size=\"30\"  />";
  }


  $alphabet .= "</div>";

  return $alphabet;
}

function prepareTH($array) {

  $th = "
<table width=\"98%\" class=\"item_listing\" cellspacing=\"0\" cellpadding=\"3\">
<tr>";

  foreach ($array as $key => $value) {
    $th .= "<th>$value</th>";
  }

  $th .= "</tr>";

  return $th;
}

function isValidEmailAddress($lstrPotentialEmail) {
  $lstrExpression = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i';

  $lintMatch = preg_match($lstrExpression, $lstrPotentialEmail);

  if ($lintMatch > 0) {
    return true;
  }

  return false;
}

/**
 * displayLogoOnlyHeader() - this function displays a logo only header
 *
 * @return void
 */
function displayLogoOnlyHeader()
{
	//find where control folder is and get assest URL to display logo
	$lstrURL = $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

	$lobjSplit = explode( '/', $lstrURL );

	for( $i=(count($lobjSplit) - 1); $i >=0; $i-- )
	{
		if($lobjSplit[$i] == 'control')
		{
			unset($lobjSplit[$i]);
			$lstrURL = implode( '/' , $lobjSplit );
			break;
		}else
		{
			unset($lobjSplit[$i]);
		}
	}

	//display logo only header
	?>
		<div id="header">
		   <div style="width: 100%; text-align: left;"><img src="<?php echo 'http://' .$lstrURL . '/assets/'; ?>images/admin/logo_small.png"  border="0" class="logo" width="136" height="28" /></div>
		</div>
		<?php
}

/**
 * isInstalled() - this funtion determines whether or not SujectsPlus is installed
 *
 * @return boolean
 */
function isInstalled()
{
	global $hname;
	global $uname;
	global $pword;
	global $dbName_SPlus;

	try {
		$dbc = new sp_DBConnector($uname, $pword, $dbName_SPlus, $hname);
	} catch (Exception $e) {
		echo $e;
	}

	//does key SubjectsPlus tables exist query
	$lstrQuery = 'SHOW TABLES LIKE \'staff%\'';

	$rscResults = MYSQL_QUERY( $lstrQuery );
	$lintRowCount = mysql_num_rows( $rscResults );

	//no key SubjectsPlus tables exists
	if( $lintRowCount == 0 ) return FALSE;
	return TRUE;
}

/**
 * isUpdated() - this funtion determines whether or not SujectsPlus is updated to 2.0
 *
 * @return boolean
 */
function isUpdated()
{
	global $hname;
	global $uname;
	global $pword;
	global $dbName_SPlus;

	try {
		$dbc = new sp_DBConnector($uname, $pword, $dbName_SPlus, $hname);
	} catch (Exception $e) {
		echo $e;
	}

	//does key SubjectsPlus 2.0 tables exist query
	$lstrQuery = 'SHOW TABLES LIKE \'discipline\'';

	$rscResults = MYSQL_QUERY( $lstrQuery );
	$lintRowCount = mysql_num_rows( $rscResults );

	//no key SubjectsPlus 2.0 tables exists
	if( $lintRowCount == 0 ) return FALSE;
	return TRUE;
}

/**
 * getAssetPath() - returns path of asset folder. Used for when wanting to get
 * asset path when configuration AssetPath is not set yet (e.g. Installation)
 *
 * @return string
 */
function getAssetPath()
{
	$lstrPath = dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR;

	return $lstrPath;
}

/**
 * getControlPath() - returns path of control folder. Used for when wanting to get
 * control path when configuration CpanelPath is not set yet (e.g. Installation)
 *
 * @return string
 */
function getControlPath()
{
	$lstrPath = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR;

	return $lstrPath;
}

/**
 * getAssetURL() - returns url of asset folder. SUed for when wanting to get
 * asset url when configuration AssestURL is not set yet (e.g. Installation).
 *
 * @return string
 */
function getAssetURL()
{
	$lstrURL = $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

	$lobjSplit = explode( '/', $lstrURL );

	for( $i=(count($lobjSplit) - 1); $i >=0; $i-- )
	{
		if($lobjSplit[$i] == 'control' || $lobjSplit[$i] == 'subjects')
		{
			unset($lobjSplit[$i]);
			$lstrURL = implode( '/' , $lobjSplit );
			break;
		}else
		{
			unset($lobjSplit[$i]);
		}
	}

	return 'http://' . $lstrURL . '/assets/';
}

/**
 * getControlRL() - returns url of control folder. SUed for when wanting to get
 * control url when configuration CPanelURL is not set yet (e.g. Installation).
 *
 * @return string
 */
function getControlURL()
{
	$lstrURL = $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

	$lobjSplit = explode( '/', $lstrURL );

	for( $i=(count($lobjSplit) - 1); $i >=0; $i-- )
	{
		if($lobjSplit[$i] == 'subjects')
		{
			unset($lobjSplit[$i]);
			$lstrURL = implode( '/' , $lobjSplit );
			$lstrURL = 'http://' . $lstrURL . '/control/';
			break;
		}elseif($lobjSplit[$i] == 'control')
		{
			$lstrURL = implode( '/' , $lobjSplit );
			$lstrURL = 'http://' . $lstrURL . '/';
			break;
		}else
		{
			unset($lobjSplit[$i]);
		}
	}

	return $lstrURL;
}

/**
 * getRewriteBase() - this function will find the base for SubjectsPlus so that
 * a .htaccess file use the rewrite base
 *
 * @return string
 */
function getRewriteBase()
{
	$lstrURI = $_SERVER[ 'REQUEST_URI' ];

	$lobjSplit = explode( '/', $lstrURI );

	for( $i=(count($lobjSplit) - 1); $i >=0; $i-- )
	{
		if($lobjSplit[$i] == 'control')
		{
			unset($lobjSplit[$i]);
			$lstrRewriteBase = implode( '/' , $lobjSplit );
			$lstrRewriteBase = $lstrRewriteBase . '/';
			break;
		}else
		{
			unset($lobjSplit[$i]);
		}
	}

	return $lstrRewriteBase;
}

?>