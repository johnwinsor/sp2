<?php

/**
 *   @file sp_Guide
 *   @brief manage guide metadata
 *
 *   @author agdarby, rgilmour
 *   @date Jan 2011
 *   @todo better blunDer interaction, better message, maybe hide the blunder errors until the end
 */
class sp_Guide {

    private $_subject_id;
    private $_subject;
    private $_shortform;
    private $_description;
    private $_keywords;
    private $_active;
    private $_type;
    private $_extra;
    private $_message;

    public function __construct($subject_id="", $flag="") {

        global $use_disciplines;

        if ($flag == "" && $subject_id == "") {
            $flag = "empty";
        }

        switch ($flag) {
            case "empty":
                // Set default staffer, but nothing else; this will create an empty record
                $this->_staffers = array(0 => array($_SESSION["staff_id"], $_SESSION["fname"] . " " . $_SESSION["lname"]));
            	$this->_extra = array();
                $this->_discipliners = array();

                break;
            case "post":
                // prepare record for insertion or update
                // data stored in subject table
                $this->_subject_id = $_POST["subject_id"];
                $this->_subject = $_POST["subject"];
                $this->_shortform = $_POST["shortform"];
                $this->_description = $_POST["description"];
                $this->_keywords = $_POST["keywords"];
                $this->_active = $_POST["active"];
                $this->_type = $_POST["type"];
                $this->_shortform = $_POST["shortform"];
            	$this->_extra = $_POST['extra'];

                // data stored in staff_subject table
                $this->_staff_id = $_POST["staff_id"]; // array
                $this->_staff_count = count($this->_staff_id); // # of items in above array

                if ($use_disciplines == TRUE) {
                    //data stored in subject_discipline table
                    $this->_discipline_id = $_POST["discipline_id"]; // array
                    $this->_discipline_count = count($this->_discipline_id); // # of items in above array
                }


                break;
            case "delete":
                // kind of redundant, but just set up to delete appropriate tables?
                // $this->_staffers needed to see if they have permission to delete this record
                $this->_subject_id = $subject_id;

                self::getAssociatedStaff();

                //$this->_staffers = array(0 => array($_SESSION["staff_id"], $_SESSION["fname"] . " " . $_SESSION["lname"]));

                break;
            default:

                $this->_subject_id = $subject_id;

                /////////////
                // Get subject table info
                /////////////

                $querier = new sp_Querier();
                $q1 = "select subject_id, subject, active, shortform, description, keywords, type, extra from subject where subject_id = " . $this->_subject_id;
                $guideArray = $querier->getResult($q1);

                $this->_debug .= "<p>Subject query: $q1";
                // Test if these exist, otherwise go to plan B
                if ($guideArray == FALSE) {
                    $this->_message = _("There is no active record with that ID.  Why not create a new one?");
                } else {
                    $this->_subject = $guideArray[0]["subject"];
                    $this->_shortform = $guideArray[0]["shortform"];
                    $this->_description = $guideArray[0]["description"];
                    $this->_keywords = $guideArray[0]["keywords"];
                    $this->_active = $guideArray[0]["active"];
                    $this->_type = $guideArray[0]["type"];
                    $this->_extra = json_decode($guideArray[0]["extra"], true);
                }

                ///////////////////
                // Query Staff table
                // used to get our set of staff associated
                // ////////////////

                self::getAssociatedStaff();

                ///////////////////
                // Query Discipline table
                // used to get our set of disciplines associated
                // ////////////////
                if ($use_disciplines == TRUE) {
                    self::getAssociatedDisciplines();
                }

                break;
        }
    }

    public function getAssociatedStaff() {

        $querier2 = new sp_Querier();
        $q2 = "SELECT s.staff_id, CONCAT(fname, ' ', lname) as fullname FROM staff s, staff_subject ss WHERE s.staff_id = ss.staff_id AND ss.subject_id = " . $this->_subject_id;

        $this->_staffers = $querier2->getResult($q2);

        foreach ($this->_staffers as $value) {
            $this->_ok_staff[] = $value[0];
        }

        $this->_debug .= "<p>Staff query: $q2";
    }

    public function getAssociatedDisciplines() {

        $querier3 = new sp_Querier();
        $q3 = "SELECT d.discipline_id, d.discipline FROM discipline d, subject_discipline sd WHERE d.discipline_id = sd.discipline_id
        AND sd.subject_id = " . $this->_subject_id;

        $this->_discipliners = $querier3->getResult($q3);

        if ($this->_discipliners) {
            foreach ($this->_discipliners as $value) {
                $this->_ok_disciplines[] = $value[0];
            }
        }

        $this->_debug .= "<p>Disciplines query: $q3";
    }

	public function addExtra($key, $value)
	{
		if($key != '')
		{
			$this->_extra[$key] = $value;
		}
	}

    public function outputMetadataForm($wintype="") {

        global $wysiwyg_desc;
        global $IconPath;
        global $guide_types;
        global $use_disciplines;

        //print "<pre>";print_r($this->_staffers); print "</pre>";

        $action = htmlentities($_SERVER['PHP_SELF']) . "?subject_id=" . $this->_subject_id;

        if ($wintype != "") {
            $action .= "&wintype=pop";
        }

        if ($this->_subject_id) {
            $guide_title_line = _("Edit Existing Guide Metadata");
        } else {
            $guide_title_line = _("Create New Guide");
        }

        echo "
<form action=\"" . $action . "\" method=\"post\" id=\"new_record\" accept-charset=\"UTF-8\">
<input type=\"hidden\" name=\"subject_id\" value=\"" . $this->_subject_id . "\" />
<div style=\"float: left; margin-right: 20px;\">
<h2 class=\"bw_head\">$guide_title_line</h2>
<div class=\"box\">
<span class=\"record_label\">" . _("Guide") . "</span><br />
<input type=\"text\" name=\"subject\" id=\"record_title\" size=\"50\" class=\"required_field\" value=\"" . $this->_subject . "\">
<br /><br />
<span class=\"record_label\">" . _("Short Form") . "</span><br />
<input type=\"text\" name=\"shortform\" id=\"record_shortform\" size=\"20\" class=\"required_field\" value=\"" . $this->_shortform . "\">
<br />
<span class=\"smaller\">* " . _("Short label for subject--minus spaces, ampersands, etc.") . "</span>
<br />
<br />
<span class=\"record_label\">" . _("Type of Guide") . "</span><br />
";

/////////////////////
// Guide types dropdown
/////////////////////

        $guideMe = new sp_Dropdown("type", $guide_types, $this->_type, "50");
        $guide_string = $guideMe->display();

/////////////////////
// Is Live
////////////////////

        $is_live = "<span class=\"record_label\">" . _("Publish Guide (publicly accessible)?") . "</span><br />
<input name=\"active\" type=\"radio\" value=\"1\"";
        if ($this->_active == 1) {
            $is_live .= " checked=\"checked\"";
        }
        $is_live .= " /> " . _("Yes") . " &nbsp;&nbsp;&nbsp; <input name=\"active\" type=\"radio\" value=\"0\"";
        if ($this->_active == 0) {
            $is_live .= " checked=\"checked\"";
        }
        $is_live .= " /> " . _("No") . "
<br style=\"clear: both;\" /><br />";

        ///////////////////
        // Screen layout
        // /////////////

        if (!empty($this->_extra)) {

            $jobj = $this->_extra;
            $main_col_size = $jobj['maincol'];
            
        } else {
            $main_col_size = "0-8-4";
        	
        }
        $screen_layout = "<span class=\"record_label\">" . _("Column widths (add up to 12):") . " <span id=\"main_col_width\">$main_col_size</span></span><br />
        <div id=\"slider\"></div>
        <input type=\"hidden\" id=\"extra\" name=\"extra[maincol]\" value=\"$main_col_size\" />";
        

/////////////////////
// Guide types dropdown
/////////////////////

        $guideMe = new sp_Dropdown("type", $guide_types, $this->_type, 50);
        $guide_types_string = $guideMe->display();

        echo "
$guide_types_string
<br /><br />
$is_live
$screen_layout
</div>
</div>
<!-- right hand column -->
<div style=\"float: left;min-width: 200px;\">
	<div id=\"record_buttons\" class=\"box\">
		<input type=\"submit\" name=\"submit_record\" class=\"save_button\" value=\"" . _("Save Now") . "\">";

        // if a) it's not a new record, and  b) we're an admin or c) we are listed as a librarian for this guide, show delete button
        // make sure they're allowed to delete

        if ($this->_subject_id != "") {
            if (in_array($_SESSION["staff_id"], $this->_ok_staff) || $_SESSION["admin"] == 1) {
                echo "<input type=\"submit\" name=\"delete_record\" class=\"delete_button\" value=\"" . _("Delete Forever!") . "\">";
            }
            $last_mod = _("Last modified: ") . lastModded("guide", $this->_subject_id);
            echo "<div id=\"last_edited\">$last_mod</div>";
        }

        echo "</div>";

        /////////////////
        // Staffers
        /////////////////
        $staffer_list = "";

        if ($this->_staffers == FALSE) {
            // No results
            $staffer_list = "";
        } else {
            // loop through results
            foreach ($this->_staffers as $value) {

                $staffer_list .= self::outputStaff($value);
            }
        }

        $qStaff = "select staff_id, CONCAT(fname, ' ', lname) as fullname FROM staff WHERE ptags LIKE '%records%' ORDER BY lname, fname";

        $querierStaff = new sp_Querier();
        $staffArray = $querierStaff->getResult($qStaff);

        $staffMe = new sp_Dropdown("staff_id[]", $staffArray, "", "50", "--Select--");
        $staff_string = $staffMe->display();

        /////////////////
        // Disciplines
        /////////////////

        if ($use_disciplines == TRUE) {

            $discipliner_list = "";

            if ($this->_discipliners == FALSE) {
                // No results
                $discipliner_list = "";
            } else {
                // loop through results
                foreach ($this->_discipliners as $value) {

                    $discipliner_list .= self::outputDisciplines($value);
                }
            }

            $qDiscipline = "select discipline_id, discipline FROM discipline ORDER BY discipline";

            $querierDiscipline = new sp_Querier();
            $disciplineArray = $querierStaff->getResult($qDiscipline);

            $disciplineMe = new sp_Dropdown("discipline_id[]", $disciplineArray, "", "50", "--Select--");
            $discipline_string = $disciplineMe->display();
        }

        echo "
<h2 class=\"bw_head\">" . _("Staff") . "</h2>
   <div class=\"box no_overflow\" id=\"staff_menu\">
   $staff_string
		<div id=\"item_list\">$staffer_list</div> <!-- staff inserted here -->
   </div>
   <h2 class=\"bw_head\">" . _("Metadata") . "</h2>
   <div class=\"box\"  id=\"metadata_menu\">

   <span class=\"record_label\">" . _("Description") . "</span><br />
<textarea name=\"description\" id=\"record_description\" class=\"\" cols=\"35\" rows=\"2\">" . $this->_description . "</textarea>
<br />
<br />
<span class=\"record_label\">" . _("Keywords (separate with commas)") . "</span><br />
<input type=\"text\" name=\"keywords\" id=\"record_keywords\" size=\"40\" class=\"\" value=\"" . $this->_keywords . "\">
<br />
<br />";

if ($use_disciplines == TRUE) {

    echo "<span class=\"record_label\">" . _("Parent Disciplines") . "</span><br />
$discipline_string
<div id=\"discipline_list\">$discipliner_list</div> <!-- disciplines inserted here -->
</div>";

    }


echo "</div>
</form>";
    }

    public function outputStaff($value) {
        global $IconPath;

        $ourstaff = "
        <div class=\"selected_item_wrapper\">
                <div class=\"selected_item\">
                        <input name=\"staff_id[]\" value=\"$value[0]\" type=\"hidden\" />
                        $value[1]<br />
                </div>
                <div class=\"selected_item_options\">
                        <img src=\"$IconPath/delete.png\" class=\"delete_item\" alt=\"" . _("delete") . "\" title=\"" . _("delete") . "\" border=\"0\">
                </div>
        </div>";

        return $ourstaff;
    }

    public function outputDisciplines($value) {
        global $IconPath;

        $ourdisciplines = "
        <div class=\"selected_item_wrapper\">
                <div class=\"selected_item\">
                        <input name=\"discipline_id[]\" value=\"$value[0]\" type=\"hidden\" />
                        $value[1]<br />
                </div>
                <div class=\"selected_item_options\">
                        <img src=\"$IconPath/delete.png\" class=\"delete_item\" alt=\"" . _("delete") . "\" title=\"" . _("delete") . "\" border=\"0\">
                </div>
        </div>";

        return $ourdisciplines;
    }

    public function deleteRecord() {

        // make sure they're allowed to delete
        //print "<p> session staff = " . $_SESSION["staff_id"] . " -- staff_id = " . $this->_staffers[0][0];
       // print "<pre>";
        //print_r($this->_ok_staff);
        if (!in_array($_SESSION["staff_id"], $this->_ok_staff) && $_SESSION["admin"] != "1") {
            $this->_message = _("You do not have permission to delete this guide.");
            return FALSE;
        }

        // Delete the records from staff and staff_subject tables
        $q = "DELETE subject,staff_subject FROM subject LEFT JOIN staff_subject ON subject.subject_id = staff_subject.subject_id WHERE subject.subject_id = '" . $this->_subject_id . "'";

        $delete_result = mysql_query($q);

        $this->_debug = "<p>Del query: $q";

        if (mysql_affected_rows() != 0) {
            $q2 = "DELETE pluslet, pluslet_subject FROM pluslet LEFT JOIN pluslet_subject ON pluslet.pluslet_id = pluslet_subject.pluslet_id WHERE type != 'Special' AND subject_id = '" . $this->_subject_id . "'";
            $delete_result2 = mysql_query($q2);
            $this->_debug .= "<p>Del query 2: $q2";
        } else {
            // message
            $this->_message = _("There was a problem with your delete (stage 1 of 2).");
            return FALSE;
        }

        if ($delete_result2) {
            // message
            if ($_GET["wintype"] == "pop") {
                $this->_message = _("Thy will be done.  Offending Guide (and associated boxes) deleted.  Close window to continue.");
            } else {
                $this->_message = _("Thy will be done.  Offending Guide (and associated boxes) deleted.");
            }

            // /////////////////////
            // Alter chchchanges table
            // table, flag, item_id, title, staff_id
            ////////////////////

            $updateChangeTable = changeMe("guide", "delete", $this->_subject_id, $this->_subject, $_SESSION['staff_id']);

            return TRUE;
        } else {
            // message
            $this->_message = _("There was a problem with your delete (stage 2 of 2).");
            return FALSE;
        }
    }

    public function insertRecord() {

        // Make sure there isn't a guide with this title or shortform already
        $is_dupe = self::dupeCheck();

        if ($is_dupe == TRUE) {
            $this->_message = _("There is already a guide with this SHORTFORM.  The shortform must be unique.");
            return;
        }

        //////////////////
        // Encode our extra as json
        /////////////////

    	$json_extra = json_encode($this->_extra);

        /////////////////////
        // update subject table
        /////////////////////

        $qInsertSubject = "INSERT INTO subject (subject, shortform, description, keywords, active, type, extra) VALUES (
	  '" . mysql_real_escape_string(scrubData($this->_subject, "text")) . "',
	  '" . mysql_real_escape_string(scrubData($this->_shortform, "text")) . "',
      '" . mysql_real_escape_string(scrubData($this->_description, "text")) . "',
      '" . mysql_real_escape_string(scrubData($this->_keywords, "text")) . "',
	  '" . mysql_real_escape_string(scrubData($this->_active, "integer")) . "',
	  '" . mysql_real_escape_string(scrubData($this->_type, "text")) . "',
          '" . mysql_real_escape_string($json_extra) . "'
          )";

        $rInsertSubject = mysql_query($qInsertSubject);

        $this->_subject_id = mysql_insert_id();

        $this->_debug = "<p>1. insert subject: $qInsertSubject</p>";
        if (!$rInsertSubject) {
            echo blunDer("We have a problem with the title query: $qInsertSubject");
        }

        /////////////////////
        // insert into staff_subject
        ////////////////////

        self::modifySS();

        /////////////////////
        // insert into subject_discipline
        ////////////////////

        self::modifySD();

        // /////////////////////
        // Alter chchchanges table
        // table, flag, item_id, title, staff_id
        ////////////////////

        $updateChangeTable = changeMe("guide", "insert", $this->_subject_id, $this->_subject, $_SESSION['staff_id']);

        // message
        $this->_message = _("Thy Will Be Done.") . " <a href=\"guide.php?subject_id=" . $this->_subject_id . "\">" . _("View Your Guide") . "</a>";
    }

    public function updateRecord() {

        // Check to make sure the title or shortform haven't been changed to create dupes
        $is_dupe = self::dupeCheck();

        if ($is_dupe == TRUE) {
            $this->_message = _("There is already a guide with this SHORTFORM.  The shortform must be unique.");
            return;
        }

        //print_r($_POST);

        //////////////////
        // Encode our extra as json
        /////////////////

    	$json_extra = json_encode($this->_extra);


        /////////////////////
        // update subject table
        /////////////////////

        $qUpSubject = "UPDATE subject SET subject = '" . mysql_real_escape_string(scrubData($this->_subject, "text")) . "',
	  shortform = '" . mysql_real_escape_string(scrubData($this->_shortform, "text")) . "',
      description = '" . mysql_real_escape_string(scrubData($this->_description, "text")) . "',
      keywords = '" . mysql_real_escape_string(scrubData($this->_keywords, "text")) . "',
	  active = '" . mysql_real_escape_string(scrubData($this->_active, "integer")) . "',
	  type = '" . mysql_real_escape_string(scrubData($this->_type, "text")) . "',
      extra = '" . mysql_real_escape_string($json_extra) . "'
          WHERE subject_id = " . scrubData($this->_subject_id, "integer");

        $rUpSubject = mysql_query($qUpSubject);

        $this->_debug = "<p>1. update title: $qUpSubject</p>";
        if (!$rUpSubject) {
            print "affected rows = " . mysql_affected_rows();
            echo blunDer("We have a problem with the title query: $qUpSubject");
        }

        /////////////////////
        // clear staff_subject
        /////////////////////

        $qClearSS = "DELETE FROM staff_subject WHERE subject_id = " . $this->_subject_id;

        $rClearSS = mysql_query($qClearSS);

        $this->_debug .= "<p>2. clear staff_subject: $qClearSS</p>";

        if (!$rClearSS) {
            echo blunDer("We have a problem with the clear staff_subject query: $qClearSS");
        }

        /////////////////////
        // insert into staff_subject
        ////////////////////

        self::modifySS();

        /////////////////////
        // clear subject_discipline
        /////////////////////

        $qClearSD = "DELETE FROM subject_discipline WHERE subject_id = " . $this->_subject_id;

        $rClearSD = mysql_query($qClearSD);

        $this->_debug .= "<p>2. clear subject_discipline: $qClearSD</p>";

        if (!$rClearSD) {
            echo blunDer("We have a problem with the clear subject_discipline query: $qClearSD");
        }

        /////////////////////
        // insert into subject_discipline
        ////////////////////

        self::modifySD();

        // /////////////////////
        // Alter chchchanges table
        // table, flag, item_id, title, staff_id
        ////////////////////

        $updateChangeTable = changeMe("guide", "update", $this->_subject_id, $this->_subject, $_SESSION['staff_id']);

        // message
        $this->_message = _("Thy Will Be Done.  Guide updated.");
    }

    function updateExtra() {

        // Encode our extra as json
        $json_extra = json_encode($this->_extra);

        /////////////////////
        // update subject table
        /////////////////////

        $qUpExtra = "UPDATE subject 
        SET extra = '" . mysql_real_escape_string($json_extra) . "'
          WHERE subject_id = " . scrubData($this->_subject_id, "integer");

        $rUpExtra = mysql_query($qUpExtra);

        $this->_debug = "<p>1. update title: $qUpExtra</p>";
        if (!$rUpExtra) {
            print "affected rows = " . mysql_affected_rows();
            echo blunDer("We have a problem with the title query: $qUpExtra");
        }
    }

    function modifySS() {

        $de_duped = array_unique($this->_staff_id);

        foreach ($de_duped as $value) {
            if (is_numeric($value)) {
                $qUpSS = "INSERT INTO staff_subject (staff_id, subject_id) VALUES (
				'" . scrubData($value, "integer") . "',
				'" . scrubData($this->_subject_id, "integer") . "')";

                $rUpSS = mysql_query($qUpSS);

                $this->_debug .= "<p>3. (insert staff_subject loop) : $qUpSS</p>";
                if (!$rUpSS) {
                    echo blunDer("We have a problem with the staff_subject query: $qUpSS");
                }
            }
        }
    }

    function modifySD() {
        global $use_disciplines;
        if ($use_disciplines != TRUE) { return;}
        
        $de_duped = array_unique($this->_discipline_id);

        foreach ($de_duped as $value) {
            if (is_numeric($value)) {
                $qUpSD = "INSERT INTO subject_discipline (subject_id, discipline_id) VALUES (
                '" . scrubData($this->_subject_id, "integer") . "',
                '" . scrubData($value, "integer") . "')";

                $rUpSD = mysql_query($qUpSD);

                $this->_debug .= "<p>3. (insert subject_discipline loop) : $qUpSD</p>";
                if (!$rUpSD) {
                    echo blunDer("We have a problem with the subject_discipline query: $qUpSD");
                }
            }
        }
    }

    function prepareDisciplines() {
        // get all disciplines currently showing in discipline list
        // de-dupe

    }

    function dupeCheck() {
        // returns TRUE is there is already an item with that subject or shortform
        if ($this->_subject_id == "") {
            // INSERT
            $qcheck = "SELECT shortform FROM subject WHERE shortform = '" . mysql_real_escape_string(scrubData($this->_shortform)) . "'";
        } else {
            // UPDATE
            $qcheck = "SELECT shortform FROM subject WHERE shortform = '" . mysql_real_escape_string(scrubData($this->_shortform)) . "' AND subject_id != '" . $this->_subject_id . "'";
        }
        //print $qcheck;

        $rcheck = MYSQL_QUERY($qcheck);

        $this->_debug .= "<p>Dupe check: $qcheck</p>";

        if (mysql_num_rows($rcheck) == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function getMessage() {
        return $this->_message;
    }

    function getRecordId() {
        return $this->_subject_id;
    }

    function deBug() {
        print $this->_debug;
    }

}

?>
