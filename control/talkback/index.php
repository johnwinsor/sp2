<?php
/**
 *   @file index.php
 *   @brief handles RUD (Read, Update, Delete) for talkback module.
 *   Note that C (create) is handled only from a talkback submission from
 *   public website
 *
 *   @author adarby
 *   @date march 2011
 */
$subcat = "talkback";
$page_title = "Talk Back Admin";
$default_limit = 25; // # of items to show by default; you can change this!

include("../includes/header.php");

/////////////////
// See if a filter has been added
////////////////

if (isset($_GET["tbtag"]) && $_GET["tbtag"] != "") {
  $set_tag = scrubData($_GET["tbtag"]);
} else {
  $set_tag = "";
}

//////////////////////
// LIMITS
// Set up limit for use in page; in sql; and acceptable range of limits
// 
//////////////////////

// See if user has submitted limit
if (isset($_GET["show"]) && $_GET["show"] != "") {
  $set_limit = scrubData($_GET["show"]);

  // Now we set things up for use in our query
  switch ($_GET["show"]) {
    case "10":
    case "25":
    case "50":
      $our_sql_limit = "LIMIT 0," . $_GET["show"];
      $set_limit = $_GET["show"];
      break;
    case "all":
      $our_sql_limit = "";
      $set_limit = "all";
      break;
    default:
      $our_sql_limit = "LIMIT 0, 25";
      $set_limit = $default_limit; 
  }
} else {
  $set_limit = $default_limit; 
  $our_sql_limit = "LIMIT 0, 25";
}

// set up which numbers one can choose from
$num_filters = array("10", "25", "50", "all");  // you can change this

// Now put together the show limit for SQL purposes
$our_limit = ""; // init

// Determine what to show:
// Loop through all available tags; determine which is IT

$our_filter = ""; // init -- used in limit URL
$filters = ""; // init 

// let's add the default no filter to our array
$prepend_array = array("All" => "All");

$all_tbtags = $prepend_array + $all_tbtags;

foreach ($all_tbtags as $key => $value) {
  if (isset($_GET["tbtag"]) && $key == $_GET["tbtag"]) {
    $tag_class = "ctag-on";
    $our_filter = $key;
  } else {
    $tag_class = "ctag-off";
  }
  $filters .= " <span class=\"$tag_class\"><a href=\"index.php?tbtag=$key&show=$set_limit\" class=\"filter_results\">$key</a></span>";
}

// layout for our # to show

$show_links = ""; // init

foreach ($num_filters as $value) {
  if ($value == $set_limit) {
    $tag_class = "ctag-on";
  } else {
    $tag_class = "ctag-off";
    
  }
  $show_links .= " <span class=\"$tag_class\"><a href=\"index.php?tbtag=$our_filter&show=$value\" class=\"filter_results\">$value</a></span>";
}

/////////////////////
// Query
// First make sure there is a tb tag
///////////

if (isset($_GET["tbtag"]) && $_GET["tbtag"] != '' && $_GET["tbtag"] != 'All' ) {
  $sql_where = "WHERE tbtags LIKE '%" . $set_tag . "%'";
} else {
  $sql_where = "";
}

$querierTBYES = new sp_Querier();
$qTBYES = "SELECT talkback_id, question, q_from, date_submitted, DATE_FORMAT(date_submitted, '%b %D %Y') as date_formatted, answer, a_from, display, last_revised_by, tbtags
    FROM talkback
    $sql_where
    ORDER BY date_submitted DESC
    $our_sql_limit";

//print $qTBYES;

$tbArrayYes = $querierTBYES->getResult($qTBYES);

$tb_yes_answer = genTalkBacks($tbArrayYes, 1);


/////////////////
// Show Results
////////////////
?>

<br />
<div style="float: left;  width: 70%;">
<h2 class="bw_head"><?php print _("View and Answer TalkBacks"); ?></h2>
<div class="box no_overflow">
  <p><?php print _("Show:") . $show_links; ?> </p>
  <p><?php print _("Filter:") . $filters; ?></p>
  <br /><br />
  <?php print $tb_yes_answer; ?>
</div>
</div>
<div style="float: right; width: 28%;margin-left: 10px;"><h2 class="bw_head"><?php print _("About TalkBack"); ?></h2>
<div class="box">
<p><?php print _("TalkBack questions come via the web form on the public TalkBack page. An email should be sent to the admin when a new one arrives, and they may be answered here."); ?></p>
<br />
    <ul>
    <li><a target="_blank" href="<?php print $TalkBackPath; ?>"><?php print _("TalkBack Public Page"); ?></a></li>
</ul>
</div>

</div>

<?php


include("../includes/footer.php");

/////////////////
// genTalkBacks
// format our tb data
////////////////

function genTalkBacks($tbArray, $show_response = 1) {
  global $IconPath;

  $row_count1 = 0;
  $row_count2 = 0;
  $colour1 = "evenrow";
  $colour2 = "oddrow";
  $tb_answer = "";

  if (!is_array($tbArray)) {
    return "<strong>" . _("Alas, there are no items with this tag.") . "</strong>";
  }
  
  foreach ($tbArray as $key=>$value) {
    $tb_tagger = "";
    $row_colour = ($row_count2 % 2) ? $colour1 : $colour2;

    if ($value[2]) {
      $q_from = $value[2];
    } else {
      $q_from = _("Anonymous");
    }

    if ($value["answer"] == '') {
      $row_colour = "tb_highlight";
      $tb_tagger = "<span class=\"ctag-on\">" . $value["tbtags"] . "</span>";
    }
    
    if (isset($show_response) && $show_response == 0) {
      $first_div_width = "90%";
      $last_mod_tb = "";
    } else {
      $first_div_width = "45%";
      $short_answer = stripslashes(htmlspecialchars_decode(TruncByWord($value["answer"], 15)));
      $last_mod_tb = lastModded("talkback", $value["talkback_id"], 0, 1);
    }

    $short_question = Truncate($value["question"], 200);


    if ($last_mod_tb) {
      $mod_line = _("--") . $last_mod_tb;
    } else {
      $mod_line = "";
    }
    
    $tb_answer .= "
            <div style=\"clear: both; float: left;  padding: 3px 5px; width: 98%;\" class=\"striper $row_colour\">
                <div style=\"float: left; width: 32px; max-width: 5%;\"><a class=\"showmedium-reloader\" style=\"color: #333;\" href=\"talkback.php?talkback_id=$value[0]&amp;wintype=pop\"><img src=\"$IconPath/pencil.png\" alt=\"edit\" width=\"16\" height=\"16\" /></a></div>
                <div style=\"float: left; width: $first_div_width;\">
                 $tb_tagger<strong>Q:</strong> $short_question <span style=\"color: #666; font-size: 10px;\">($q_from, $value[date_formatted])</span>
                </div>";

    if (isset($show_response) && $show_response == 1) {
      $tb_answer .= "<div style=\"float: left; width: 45%; margin-left: 4%;\">
                 <strong>A:</strong> $short_answer <span style=\"color: #666; font-size: 10px;\">$mod_line</span>
                </div>
            ";
    }
    $tb_answer .= "</div>";


    $row_count2++;
  }
  return $tb_answer;
}
?>