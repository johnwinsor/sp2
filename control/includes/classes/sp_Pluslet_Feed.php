<?php

/**
 *   @file sp_Pluslet_Feed
 *   @brief 
 *
 *   @author agdarby
 *   @date Feb 2011
 *   @todo 
 */

/* JW - Added proxy for feed URLS - Lines 106-114 */

class sp_Pluslet_Feed extends sp_Pluslet {

    public function __construct($pluslet_id, $flag="", $subject_id, $isclone=0) {
        parent::__construct($pluslet_id, $flag, $subject_id, $isclone);

        $this->_editable = TRUE;
    }

    public function output($action="", $view="public") {

        // public vs. admin
        parent::establishView($view);

        if ($this->_extra != "") {

            $jobj = json_decode($this->_extra);
            $feed_type = $jobj->{'feed_type'};
            $num_items = $jobj->{'num_items'};
            $show_desc = $jobj->{'show_desc'};
            $show_feed = $jobj->{'show_feed'};
             /* JW - Added proxy for feed URLS */
            $proxy = $jobj->{'proxy'};
            
            if ($num_items == "") { $num_items = 5; }
        }

        if ($action == "edit") {

            //////////////////////
            // New or Existing?
            //////////////////////

            if ($this->_pluslet_id) {
                $current_id = $this->_pluslet_id;
                $this->_pluslet_id_field = "pluslet-" . $this->_pluslet_id;
                $this->_pluslet_name_field = "";
                $this->_title = "<input type=\"text\" class=\"required_field\" id=\"pluslet-update-title-$this->_pluslet_id\" value=\"$this->_title\" size=\"$title_input_size\" />";
                $this_instance = "pluslet-update-body-$this->_pluslet_id";
            } else {
                $new_id = rand(10000, 100000);
                $current_id = $new_id;
                $this->_pluslet_bonus_classes = "unsortable";
                $this->_pluslet_id_field = $new_id;
                $this->_pluslet_name_field = "new-pluslet-Feed";
                $this->_title = "<input type=\"text\" class=\"required_field $clone\" id=\"pluslet-new-title-$new_id\" name=\"new_pluslet_title\" value=\"$this->_title\" size=\"$title_input_size\" />";
                $this_instance = "pluslet-new-body-$new_id";
            }

               if ($num_items == "") { $num_items = 5; }
            // some translations . . .
            $vYes = _("Yes");
            $vNo = _("No");
            $vRss1 = _("Display");
            $vRss2 = _("items<br />Show descriptions?");

            global $title_input_size; // alter size based on column
            // Generate our dropdown
            $dd_name = "feed_type-$current_id";
            /* JW - Added NewBooks for GoodReads. Removed all others except RSS
            $dd_array = array("Delicious", "RSS", "Flickr", "Twitter", "NewBooks"); */
            $dd_array = array("RSS", "NewBooks");

            $typeMe = new sp_Dropdown($dd_name, $dd_array, $feed_type);
            $feed_type_dd = $typeMe->display();

            $this->_body = "<br /><input type=\"text\" name=\"$this_instance\" class=\"required_field\" value=\"$this->_body\" size=\"$title_input_size\" />
            $feed_type_dd        
            <br />
            " . _("Enter RSS feed or GoodReads Shelf Name");
            $this->_body .= "
            
            <p style=\"font-size: 11px;padding-top: 3px;\">
            $vRss1 <input type=\"text\" name=\"displaynum-$current_id\" value=\"$num_items\" size=\"1\" />
            $vRss2 <input name=\"showdesc-$current_id\" type=\"radio\" value=\"1\"";

            if ($show_desc == 1) {
                $this->_body .= " checked=\"checked\"";
            }
            $this->_body .= "> $vYes <input name=\"showdesc-$current_id\" type=\"radio\" value=\"0\" ";
            if ($show_desc == 0) {
                $this->_body .= " checked=\"checked\"";
            }
            $this->_body .= " /> $vNo<br />
    Show feed source? <input name=\"showfeed-$current_id\" type=\"radio\" value=\"1\"";
            if ($show_feed == 1) {
                $this->_body .= " checked=\"checked\"";
            }
            $this->_body .= "> $vYes <input name=\"showfeed-$current_id\" type=\"radio\" value=\"0\" ";
            if ($show_feed == 0) {
                $this->_body .= " checked=\"checked\"";
            }
            $this->_body .= " /> $vNo<br />
    Proxy URL? <input name=\"proxy-$current_id\" type=\"radio\" value=\"1\"";
            if ($proxy == 1) {
                $this->_body .= " checked=\"checked\"";
            }
            $this->_body .= "> $vYes <input name=\"proxy-$current_id\" type=\"radio\" value=\"0\" ";
            if ($proxy == 0) {
                $this->_body .= " checked=\"checked\"";
            }
            $this->_body .= " /> $vNo</p>

<div>\n";

            parent::startPluslet();
            print $this->_body;
            parent::finishPluslet();

            return;
        } else {
            // Note we hide the Feed parameters in the name field
            $vRSSLoading = _("Loading ...");
            $feed = trim($this->_body);
            /* JW - Added proxy for feed URLS */
            $this->_body = "<p class=\"find_feed\" name=\"$feed|$num_items|$show_desc|$show_feed|$feed_type|$proxy\">$vRSSLoading</p>";

            parent::assemblePluslet();

            return $this->_pluslet;
        }
    }

}

?>