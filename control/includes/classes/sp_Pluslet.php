<?php

/**
 *   @file sp_Guide
 *   @brief manage guide metadata
 *
 *   @author agdarby, rgilmour
 *   @date Jan 2011
 *   @todo better blunDer interaction, better message, maybe hide the blunder errors until the end
 */
class sp_Pluslet {

    protected $_pluslet_id;
    protected $_title;
    protected $_body;
    protected $_clone;
    protected $_type;
    protected $_extra;
    protected $_pluslet_bonus_classes = "";
    protected $_pluslet_name_field = "";
    protected $_pluslet_body_bonus_classes = "";
    protected $_editable = "";
    protected $_icons = "";

    public function __construct($pluslet_id="", $flag="", $subject_id = "", $isclone = 0) {

        $this->_pluslet_id = $pluslet_id;

        if (!$this->_pluslet_id) {
            return;
        }

        $this->_pluslet_id_field = "pluslet-" . $this->_pluslet_id; // default css id of item

        $this->_subject_id = $subject_id;
        $this->_isclone = $isclone;

        // By default we make pluslets editable (in admin), this is overriden in some pluslet types
        $this->_editable = TRUE;

        /////////////
        // Get pluslet table info
        /////////////

        $querier = new sp_Querier();
        $q1 = "SELECT pluslet_id, title, body, clone, type, extra FROM pluslet WHERE pluslet_id = " . $this->_pluslet_id;

        //print $q1;
        $plusletArray = $querier->getResult($q1);

        $this->_debug .= "<p>Pluslet query: $q1";
        // Test if these exist, otherwise go to plan B
        if ($plusletArray == FALSE) {
            $this->_message = _("There is no box with that ID.  Why not create a new one?");
        } else {
            $this->_title = $plusletArray[0]["title"];
            $this->_body = $plusletArray[0]["body"];
            $this->_clone = $plusletArray[0]["clone"];
            $this->_type = $plusletArray[0]["type"];
            $this->_extra = $plusletArray[0]["extra"];
        }


        // Now if it's a clone, empty out the pluslet_id
        if ($this->_isclone == 1) {
            $this->_pluslet_id = "";
        }


    }

    protected function establishView($view) {
        global $IconPath;

        switch ($view) {
            case "admin":
                $helptext = _("Help");
                $this->_icons = "<img src=\"$IconPath/help.png\" border=\"0\" title=\"$helptext\" class=\"help-$this->_type\" alt=\"" . _("help") . "\" /> <a class=\"togglebody\"><img src=\"$IconPath/toggle_small.png\" border=\"0\" alt=\"" . _("toggle me") . "\" title=\"" . _("toggle me") . "\" /></a>";

                // If editable, give the pencil icon
                if ($this->_editable == TRUE) {
                    // Deal with All Items by Source type (#1 in db)
                    if ($this->_pluslet_id == 1) {
                        $this->_icons .= " <a class=\"showmedium\" href=\"manage_items.php?subject_id=$this->_subject_id&amp;wintype=pop\"><img src=\"$IconPath/pencil.png\" border=\"0\" alt=\"" . _("Edit") . "\" title=\"" . _("Edit") . "\" /></a>";
                    } else {
                        $this->_icons .= " <a id=\"edit-$this->_pluslet_id-$this->_type\"><img src=\"$IconPath/pencil.png\" border=\"0\" alt=\"" . _("Edit") . "\" title=\"" . _("Edit") . "\" /></a>";
                    }
                }
                // Everyone gets a delete button in the admin
                $this->_icons .= " <a id=\"delete-$this->_pluslet_id\"><img src=\"$IconPath/delete.png\" alt=\"" . _("Remove item from this guide") . "\" title=\"" . _("Remove item from this guide") . "\" border=\"0\" /></a>";

                // Show the item id --it's handy for debugging
                $this->_visible_id = "<span class=\"smallgrey\">$this->_pluslet_id</span>";

                // get our relative path to the legacy fixed_pluslet folder
                $this->_relative_asset_path = "../../assets/";

                break;
            default:
                // Default is the public view
                // don't show the item id
                $this->_visible_id = "";

                // get our relative path to the legacy fixed_pluslet folder
                $this->_relative_asset_path = "../assets/";

                break;
        }
    }

    protected function assemblePluslet($hide_titlebar=0) {

        $this->_pluslet = "<a name=\"box-" . $this->_pluslet_id . "\"></a>";;

        // if we're using a simple pluslet, things are diff
        // we use $this->_visible_id to make sure this is only on the frontend
        
        if ($hide_titlebar == 1 && $this->_visible_id == "") {
            $this->_pluslet .= "<div class=\"pluslet_simple no_overflow\">" . htmlspecialchars_decode($this->_body);
            // this div closed outside of if/else
        } else {
            $this->_pluslet .= "<div class=\"pluslet $this->_pluslet_bonus_classes\" id=\"$this->_pluslet_id_field\" name=\"$this->_pluslet_name_field\">
            <div class=\"titlebar\">
            <div class=\"titlebar_text\">$this->_title $this->_visible_id</div>
            <div class=\"titlebar_options\">$this->_icons</div>
            </div>";

            if ($this->_body != "") {
                $this->_pluslet .= "<div class=\"pluslet_body $this->_pluslet_body_bonus_classes\">
                            " . htmlspecialchars_decode($this->_body) . "
                    </div>";
            }

        }
            

        
        $this->_pluslet .= "</div>";
    }

    ///////////////////
    // The next two functions are for when the pluslet needs to appear live
    // without being stored in a variable, for instance, when using ckeditor
    ///////////////////

    protected function startPluslet() {

        echo "
        <div class=\"pluslet $this->_pluslet_bonus_classes\" id=\"$this->_pluslet_id_field\" name=\"$this->_pluslet_name_field\">
            <div class=\"titlebar\">
                <div class=\"titlebar_text\">$this->_title $this->_visible_id</div>
                <div class=\"titlebar_options\">$this->_icons</div>
            </div>
        <div class=\"pluslet_body $this->_pluslet_body_bonus_classes\">";
    }

    protected function finishPluslet() {

        echo "</div>
            </div>";
    }

    protected function tokenizeText() {
        global $proxyURL;
        global $PublicPath;
        global $FAQPath;
        global $UserPath;
        global $IconPath;
        global $open_string;
        global $close_string;
        global $open_string_kw;
        global $close_string_kw;
        global $open_string_cn;
        global $close_string_cn;
        global $open_string_bib;

        $icons = "";
        //$target = "target=\"_" . $target . "\"";
        $target = "";
        $tokenized = "";

        $parts = preg_split('/{{|}}/', $this->_body);
        if (count($parts) > 1) { // there are tokens in $body
            foreach ($parts as $part) {
                if (preg_match('/^dab},\s?{\d+},\s?{.+},\s?{[01]{2}$/', $part) || preg_match('/^faq},\s?{(\d+,)*\d+$/', $part) || preg_match('/^cat},\s?{.+},\s?{.*},\s?{\w+$/', $part) || preg_match('/^fil},\s?{.+},\s?{.+$/', $part)) { // $part is a properly formed token
                    $fields = preg_split('/},\s?{/', $part);
                    $prefix = substr($part, 0, 3);
                    switch ($prefix) {
                        case "faq":
                            $query = "SELECT faq_id, question FROM `faq` WHERE faq_id IN(" . $fields[1] . ") ORDER BY question";
                            $result = mysql_query($query);
                            $tokenized.= "<ul>";
                            while ($myrow = mysql_fetch_row($result)) {
                                $tokenized.= "<li><a href=\"$FAQPath" . "?faq_id=$myrow[0]\" $target>" . stripslashes(htmlspecialchars_decode($myrow[1])) . "</a></li>";
                            }
                            $tokenized.= "</ul>";
                            break;
                        case "fil":
                            $ext = explode(".", $fields[1]);
                            $i = count($ext)-1;
                            $our_icon = showDocIcon($ext[$i]);
                            $file = "$UserPath/$fields[1]";
                            $tokenized.= "<a href=\"$file\" $target>$fields[2]</a> <img style=\"position:relative; top:.3em;\" src=\"$IconPath/$our_icon\" alt=\"$ext[$i]\" />";
                            break;
                        case "cat":
                            $pretext = "";
                            switch ($fields[3]) {
                                case "subject":
                                    $cat_url = $open_string . $fields[1] . $close_string;
                                    $pretext = $fields[2] . " ";
                                    $linktext = $fields[1];
                                    break;
                                case "keywords":
                                    $cat_url = $open_string_kw . $fields[1] . $close_string_kw;
                                    $linktext = $fields[2];
                                    break;
                                case "call_num":
                                    $cat_url = $open_string_cn . $fields[1] . $close_string_cn;
                                    $linktext = $fields[2];
                                    break;
                                case "bib":
                                    $cat_url = $open_string_bib . $fields[1];
                                    $linktext = $fields[2];
                                    break;
                            }
                            $tokenized.= "$pretext<a href=\"$cat_url\" $target>$linktext</a>";
                            break;
                        case "dab":
                            //print_r($fields);
                            $description = "";
                            ///////////////////
                            // Check for icons or descriptions in fields[3]
                            // 00 = neither; 10 = icons no desc; 01 = desc no icons; 11 = both
                            ///////////////////
                            if (isset($fields["3"])) {
                                switch ($fields["3"]) {
                                    case "00":
                                        $show_icons = "";
                                        $show_desc = "";
                                        $show_rank = 0;
                                        break;
                                    case "10":
                                        $show_icons = "yes";
                                        $show_desc = "";
                                        $show_rank = 0;
                                        break;
                                    case "01":
                                        $show_icons = "";
                                        $show_desc = 1;
                                        $icons = "";
                                        break;
                                    case "11":
                                        $show_icons = "yes";
                                        $show_desc = 1;
                                        break;
                                }
                            }
                            $query = "SELECT location, access_restrictions, format, ctags, helpguide, citation_guide, description, call_number, t.title
                                    FROM location l, location_title lt, title t
                                    WHERE l.location_id = lt.location_id
                                    AND lt.title_id = t.title_id
                                    AND t.title_id = $fields[1]";
                            //print $query . "<br /><br />";
                            $result = mysql_query($query);

                            while ($myrow = mysql_fetch_row($result)) {
                                // eliminate final line breaks -- offset fixed 11/15/2011 agd
                                $myrow[6] = preg_replace('/(<br \/>)+/', '', $myrow[6]);
                                // See if it's a web format
                                if ($myrow[2] == 1) {
                                    if ($myrow[1] == 1) {
                                        $url = $myrow[0];
                                        $rest_icons = "unrestricted";
                                    } else {
                                        $url = $proxyURL . $myrow[0];
                                        $rest_icons = "restricted";
                                    }

                                    $current_ctags = explode("|", $myrow[3]);

                                    // add our $rest_icons info to this array at the beginning
                                    array_unshift($current_ctags, $rest_icons);

                                    if ($show_icons == "yes") {
                                        $icons = showIcons($current_ctags);
                                    }

                                    if ($show_desc == 1) {
                                        // if we know the subject_id, good; for public, must look up
                                    	$subject_id = '';
                                        if (isset($_GET["subject_id"])) {
                                            $subject_id = $_GET["subject_id"];
                                        } elseif (isset($_GET["subject"])) {
                                            $q1 = "SELECT subject_id FROM subject WHERE shortform = '" . $_GET["subject"] . "'";

                                            $r1 = mysql_query($q1);
                                            $subject_id = mysql_fetch_row($r1);
                                            $subject_id = $subject_id[0];
                                        }

                                        $override = findDescOverride($subject_id, $fields[1]);
                                        // if they do want to display the description:
                                        if ($override != "") {
                                            // show the subject-specific "description_override" if it exists
                                            $description = "<br />" . scrubData($override);
                                        } else {
                                            $description = "<br />" . scrubData($myrow[6]);
                                        }
                                        //$description = "<br />$myrow[9]";
                                    }
                                    $tokenized.= "<a href=\"$url\" $target>$myrow[8]</a> $icons $description";
                                } else {
                                    // It's print
                                    $format = "other";
                                    if ($show_icons == "yes") {
                                        $icons = showIcons($current_ctags);
                                    }
                                    if ($show_desc != "") {
                                        $description = "<br />$myrow[6]";
                                    }

                                    // Simple Print (2), or Print with URL (3)
                                    if ($myrow[2] == 3) {
                                        $tokenized.= "<em>$myrow[8]</em><br />" . _("") . "
                                        <a href=\"$myrow[0]\" $target>$myrow[7]</a>
                                        $icons $description";
                                    } else {

                                        // check if it's a url
                                        if (preg_match('/^(https?|www)/', $myrow[0])) {
                                            $tokenized.= "<a href=\"$myrow[0]\" $target>$myrow[8]</a> $icons $description";
                                        } else {
                                            $tokenized.= "$myrow[8] <em>$myrow[0]</em> $icons $description";
                                        }
                                    }
                                }
                            }
                            break;
                    }
                } elseif (preg_match('/{|}/', $part) && preg_match('/\bdab\b|\bfaq\b|\bcat\b|\bfil\b/', $part)) { // looks kinda like a token
                    $tokenized.= "<span style='background-color:yellow'>BROKEN TOKEN: " . $part . "</span>";
                } else {
                    $tokenized.= $part;
                }
            } // end foreach
        } else {
            $this->_body = $this->_body;
            return;
        }
        $this->_body = $tokenized;
    }

    function getRecordId() {
        return $this->_pluslet_id;
    }

    function getExtraInfo() {
        return $this->_extra;
    }

    function deBug() {
        print $this->_debug;
    }

}

?>
