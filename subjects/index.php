<?php
/**
 *   @file index.php
 *   @brief Display the subject guides splash page
 *
 *   @author adarby
 *   @date mar 2011
 */

$use_jquery = array("ui");

include("../control/includes/config.php");
include("../control/includes/functions.php");

$page_title = $resource_name;
$description = "The best stuff for your research.  No kidding.";
$keywords = "research, databases, subjects, search, find";

try {
    $dbc = new sp_DBConnector($uname, $pword, $dbName_SPlus, $hname);
} catch (Exception $e) {
    echo $e;
}

$guide_path = "guide.php?subject=";

if (isset($_GET['type']) && in_array(($_GET['type']), $guide_types)) {

    // use the submitted value
    $view_type = scrubData($_GET['type']);
} else {
    $view_type = "all";
}

///////////////////////
// Have they done a search?

$search = "";

if (isset($_POST["search"])) {
    $search = scrubData($_POST["search"]);
}

// set up our checkboxes for guide types
if (count($guide_types) > 1) {
    $tickboxes = "<ul>";

    foreach ($guide_types as $key) {
        $tickboxes .= "<li><input type=\"checkbox\" id=\"show-" . ucfirst($key) . "\" name=\"show$key\"";
        if ($view_type == "all" || $view_type == $key) {
            $tickboxes .= " checked=\"checked\"";
        }
        $tickboxes .= "/>" . ucfirst($key) . " Guides</li></li>\n";
    }

    $tickboxes .= "</ul>";
} else {
    $tickboxes = "";
}

// Get the subjects for jquery autocomplete
$suggestibles = "";  // init

$q = "select subject, shortform from subject where active = '1' order by subject";

$r = MYSQL_QUERY($q);

//initialize $suggestibles
$suggestibles = '';

while ($myrow = mysql_fetch_array($r)) {
    $item_title = trim($myrow[0]);


	if(!isset($link))
	{
		$link = '';
	}

    $suggestibles .= "{text:\"" . htmlspecialchars($item_title) . "\", url:\"$link$myrow[1]\"}, ";

}

$suggestibles = trim($suggestibles, ', ');


// Get our newest guides

$q2 = "select subject, subject_id, shortform from subject where active = '1' order by subject_id DESC limit 0,5";

$r2 = MYSQL_QUERY($q2);

$newest_guides = "<ul>\n";

while ($myrow2 = mysql_fetch_array($r2)) {
    $guide_location = $guide_path . $myrow2[2];
    $newest_guides .= "<li><a href=\"$guide_location\">" . trim($myrow2[0]) . "</a></li>\n";
}

$newest_guides .= "</ul>\n";


// Get our newest databases

$qnew = "SELECT title, location, access_restrictions FROM title t, location_title lt, location l WHERE t.title_id = lt.title_id AND l.location_id = lt.location_id AND eres_display = 'Y' order by t.title_id DESC limit 0,5";

$rnew = mysql_query($qnew);

$newlist = "<ul>\n";
while ($myrow = mysql_fetch_array($rnew)) {
    $db_url = "";

    // add proxy string if necessary
    if ($myrow[2] != 1) {
        $db_url = $proxyURL;
    }

    $newlist .= "<li><a href=\"$db_url$myrow[1]\">$myrow[0]</a></li>\n";
}
$newlist .= "</ul>\n";

// List guides function -- no other page uses it ? //

function listGuides($search = "", $type="all") {

    $andclause = "";
    global $guide_path;

    if ($search != "") {
        $search = scrubData($search);
        $andclause .= " AND subject LIKE '%" . mysql_escape_string($search) . "%'";
    }

    if ($type != "all") {
        $andclause .= " AND type='" . mysql_escape_string($type) . "'";
    }

    $q = "SELECT shortform, subject, type FROM subject WHERE active = '1' " . $andclause . " ORDER BY subject";
    $r = MYSQL_QUERY($q);
    //print $q;
    $row_count = 0;
    $colour1 = "oddrow";
    $colour2 = "evenrow";

    while ($myrow = mysql_fetch_array($r)) {

        $row_colour = ($row_count % 2) ? $colour1 : $colour2;

        $guide_location = $guide_path . $myrow[0];

        print "<div class=\"zebra $row_colour type-$myrow[2]\" style=\"height: 1.5em;\">
		 <div style=\"float: left; width: 83%;\"><a href=\"$guide_location\">" . htmlspecialchars_decode($myrow[1]) . "</a></div>
		 <div style=\"float: left; width: 15%;\">$myrow[2]</div>
		 </div>\n";
        $row_count++;
    }

}

////////////////////////////
// Now we are finally read to display the page
////////////////////////////

include("includes/header.php");

//////////////////////
// To Respond or Not
// Setup our columns
if ($is_responsive == TRUE) {
  $ldiv = "class=\"span8\"";
  $rdiv = "class=\"span4\"";
} else {
  $ldiv = "id=\"leftcol\"";
  $rdiv = "id=\"rightcol\"";
}

?>
<div <?php print $ldiv; ?>>
    <div class="pluslet">
        <div class="titlebar">
            <div class="titlebar_text"><?php print _("Find Research Guides"); ?></div>
        </div>
        <div class="pluslet_body">
            <p style="margin: 0;"><?php print _("Find the research guide of your dreams"); ?></p>
            <form action="search.php"  method="post" autocomplete="off">
<?php
$input_box = new sp_CompleteMe("quick_search", "search.php", "guide.php?subject=", "Quick Search", "guides", 40);
$input_box->displayBox();
?>
            </form>
            <br />
        </div>
    </div>
    <div class="pluslet" style="">
        <div class="titlebar">
            <div class="titlebar_text" style="width: 90%;"><?php print _("Browse Research Guides"); ?></div>
        </div>
        <div class="pluslet_body">
            <div style="text-align: center;" id="letterhead">
                <?php echo $tickboxes ?>
            </div>
            <?php listGuides($search, $view_type); ?>
        </div>
    </div>
</div>

    <div  <?php print $rdiv; ?>>
        <div class="pluslet">
            <div class="titlebar">
                <div class="titlebar_text"><?php print _("Newest Guides"); ?></div>
            </div>
            <div class="pluslet_body"> <?php print $newest_guides; ?> </div>
        </div>
        <!-- start pluslet -->
        <div class="pluslet">
            <div class="titlebar">
                <div class="titlebar_text"><?php print _("Newest Databases"); ?></div>
            </div>
            <div class="pluslet_body"> <?php print $newlist; ?> </div>
        </div>
        <!-- end pluslet -->
        <!-- start pluslet -->
        <div class="pluslet">
            <div class="titlebar">
                <div class="titlebar_text"><?php print _("New Books"); ?></div>
            </div>
            <div><iframe scrolling="no" style="overflow:hidden;width:100%;height:600px;" frameborder="0" border="0" src="../guides-newbooks/newbooks-recent.php"></iframe></div>
        </div>
        <!-- end pluslet -->
        <br />

    </div>

<?php
///////////////////////////
// Load footer file
///////////////////////////

include("includes/footer.php");

?>

<script type="text/javascript" language="javascript">
    $(document).ready(function(){

        // add rowstriping
        stripeR();

        $("[id*=show]").live("change", function() {

            var showtype_id = $(this).attr("id").split("-");
            //alert("u clicked: " + showtype_id[1]);
            unStripeR();
            $(".type-" + showtype_id[1]).toggle();
            stripeR();

        });

        function stripeR() {
            $(".zebra").not(":hidden").filter(":even").addClass("evenrow");
            $(".zebra").not(":hidden").filter(":odd").addClass("oddrow");
        }

        function unStripeR () {
            $(".zebra").removeClass("evenrow");
            $(".zebra").removeClass("oddrow");
        }

    });
</script>
