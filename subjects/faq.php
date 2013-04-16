<?php
/**
 *   @file faq.php
 *   @brief Display faqs
 *
 *   @author adarby
 *   @date Sep 28, 2009
 *   @todo The interface for this is pretty lacklustre.  Make it better!
 */

$description = "A searchable, sortable list of Frequently Asked Questions";
$keywords = "FAQ, FAQs, help, questions";

include("../control/includes/config.php");
include("../control/includes/functions.php");

$use_jquery = array("ui");

//initialized passed variables
$postvar_coll_id = '';
$postvar_subject_id = '';
$postvar_faq_id = '';

if(isset($_REQUEST['coll_id']))
{
	$postvar_coll_id = $_REQUEST['coll_id'];
}

if(isset($_REQUEST['subject_id']))
{
	$postvar_subject_id = $_REQUEST['subject_id'];
}

if(isset($_REQUEST['faq_id']))
{
	$postvar_faq_id = $_REQUEST['faq_id'];
}

// This is the id of the collection that will appear by default
$default_faqpage_id = "1";

// This is the introductory text on the landing page.  If someone is doing a search, a browse by subject
// or looking at a FAQ collection, this text is overwritten in the if ($displaytype == "search") section
// at around line 125 below.  Put in the different intro text there, if you want.

$intro = _("<p>You can <strong>search</strong> the FAQs, <strong>browse</strong> them <strong>by subject</strong>, see <strong>collections</strong> of FAQs (i.e., different groupings of FAQs for different purposes/audiences), or browse the Basic FAQs, below.</p>");

try {
    $dbc = new sp_DBConnector($uname, $pword, $dbName_SPlus, $hname);
} catch (Exception $e) {
    echo $e;
}  

// init our faq result.  
$faq_result = "";


// Make sure the GET and POST data is clean/appropriate
if (isset($_REQUEST["coll_id"])) {
    $postvar_coll_id = scrubData($_REQUEST["coll_id"], "integer");
} else {
  $postvar_coll_id = "";
}

if (isset($_POST['subject_id'])) {
    $postvar_subject_id = scrubData($_POST["subject_id"], "integer");
} else {
  $postvar_subject_id = "";
}

if (isset($_REQUEST['faq_id'])) {
    $postvar_faq_id = scrubData($_REQUEST["faq_id"], "integer");
} else {
  $postvar_faq_id = "";
}


if(isset($_POST['searchterm']))
{
	$search_clause = scrubData($_POST['searchterm']);
}else
{
	$search_clause = '';
}

////////////////
// Get list of subjects for sidebar
///////////////

$querier2 = new sp_Querier();
$q2 = "select distinct s.subject_id, s.subject
    from faq f, faq_subject fs, subject s 
    WHERE f.faq_id = fs.faq_id 
    AND fs.subject_id = s.subject_id
    AND active = '1'
    ORDER BY subject";

$oursubs = $querier2->getResult($q2);

$guideMe = new sp_Dropdown("subject_id", $oursubs, $postvar_subject_id, "40");
$guide_string = $guideMe->display();

/* Set local variables */
$suggestion_text = '';


if (isset($_REQUEST['searchterm']) && $_REQUEST['searchterm'] && $_REQUEST['searchterm'] != $suggestion_text) {

    $displaytype = "search";
    $page_title = "Library FAQs: Search Results";
} elseif (isset($_GET['page']) && $_GET['page'] == "all") {
    $displaytype = "all";
    $page_title = "Show All FAQs";
} elseif ($postvar_subject_id != "") {
    $displaytype = "bysubject";
    $page_title = "FAQs by Subject";
} elseif ($postvar_coll_id != "") {
    $displaytype = "collection";

    // Get the name of the collection
    $query = "SELECT name, description FROM faqpage WHERE faqpage_id = '$postvar_coll_id'";
    $result = MYSQL_QUERY($query);
    $name = mysql_fetch_row($result);

    $page_title = "FAQS: $name[0]";
    $intro = stripslashes(htmlspecialchars_decode($name[1]));
} elseif ($postvar_faq_id != "") {
    $displaytype = "single";
    $page_title = "Library FAQs";
} else {
    $displaytype = "splashpage";
    $page_title = "Library FAQs";
}

include("includes/header.php");

if ($displaytype == "search") {

    $full_query = "SELECT faq_id, question, answer, keywords
	FROM `faq`
	WHERE (question like '%" . mysql_real_escape_string($search_clause) . "%' OR answer like '%" . mysql_real_escape_string($search_clause) . "%' OR keywords like '%" . mysql_real_escape_string($search_clause) . "%')
	Group BY question";

    $intro = "<p>Search for <strong>$search_clause</strong>.</p>";
} elseif ($displaytype == "all") {

    $full_query = "SELECT distinct faq_id, question, answer, keywords
	FROM `faq`
	ORDER BY question";

    $intro = "";
} elseif ($displaytype == "bysubject") {

    $full_query = "SELECT f.faq_id, question, answer, f.keywords, subject
	FROM `faq` f, faq_subject fs, subject s
	WHERE f.faq_id = fs.faq_id
	AND fs.subject_id = s.subject_id
	AND s.subject_id = '$postvar_subject_id'
	ORDER BY question";

    $intro = "";
} elseif ($displaytype == "single") {

    $full_query = "SELECT faq_id, question, answer, keywords
	FROM `faq`
	WHERE faq_id = '$postvar_faq_id'";

    $intro = "";
} elseif ($displaytype == "collection") {

    $full_query = "SELECT f.faq_id, question, answer, keywords
	FROM faq f, faq_faqpage ff, faqpage fp
	WHERE f.faq_id = ff.faq_id
	AND fp.faqpage_id = ff.faqpage_id
	AND fp.faqpage_id = '$postvar_coll_id'
	ORDER BY fp.name, question";

    $intro = "";
} else {

    // This is the default

    $full_query = "SELECT f.faq_id, question, answer, keywords
	FROM faq f, faq_faqpage ff, faqpage fp
	WHERE f.faq_id = ff.faq_id
	AND fp.faqpage_id = ff.faqpage_id
	AND fp.faqpage_id = '$default_faqpage_id'
	ORDER BY f.question";
}

if (isset($debugger) && $debugger == "yes") {
    print "<p class=\"debugger\">$full_query<br /><strong>from</strong> this file</p>";
}

$full_result = MYSQL_QUERY($full_query);

$result_count = mysql_num_rows($full_result);

if ($result_count != 0) {

$index = "";
$results = "";
    $row_count = 1;
	$index = '';
    $results = '';

    while ($myrow = mysql_fetch_array($full_result)) {

        
        
        $show_row_count = "";
        $faq_id = $myrow["0"];
        $question = stripslashes(htmlspecialchars_decode($myrow["1"]));
        $answer = stripslashes(htmlspecialchars_decode($myrow["2"]));
        $answer = preg_replace('/<\/?div.*?>/ ', '', $answer);
        $keywords = $myrow["3"];

        if ($result_count > 1) {
            $index .= "<div class=\"zebra\" style=\"min-height: 1.5em; width: 97%;\"><a href=\"#faq-$row_count\">$question</a></div>\n";
            $show_row_count = $row_count . ". ";
        }
        $results .= "<a name=\"faq-$row_count\"></a>\n
		<div class=\"pluslet_simple\">\n
		<h2>$show_row_count$question</h2>\n

		$answer

		</div>\n
";

        // Add 1 to the row count, for the "even/odd" row striping

        $row_count++;
    }
} else {

    $results = "<div class=\"pluslet\">\n
<div class=\"titlebar\"><div class=\"titlebar_text\">" . _("No Results") . "</div></div>\n
<div class=\"pluslet_body\">\n"
    . _("There were no FAQs found for this query.") .
"</div>\n
</div>\n";
}

$collections_query = "SELECT f.faqpage_id, name
FROM faqpage f, faq_faqpage ff
WHERE f.faqpage_id = ff.faqpage_id
GROUP BY name";

// print $collections_query;

$collections_result = MYSQL_QUERY($collections_query);

// create the option
$coll_items = "<li><a href=\"faq.php?page=all\">All</a></li>";

while ($myrow1 = mysql_fetch_array($collections_result)) {
    $coll_id = $myrow1["0"];
    $coll_name = $myrow1["1"];

    $coll_items .= "<li><a href=\"faq.php?coll_id=$coll_id\">$coll_name</a></li>";
}

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
<?php
//$num_faqs = $row_count - 1;
// print "<p style=\"background-color: #ffffcf;\"><strong>Note:</strong>  $num_faqs FAQs displayed.  Search <strong>all FAQs</strong> with the boxes to the right.</p><br />";


if (isset($index)) {

    print "
		<div class=\"pluslet_simple\">

		<div class=\"faq_filter\"><a href=\"#rdiv\">" . _("Note:  Not all FAQs displayed.  Search or browse for more ") . " &raquo;</a></div>
		$index\n";
    print "</div><br /><br />";
}


print $results; ?>
</div>
<div <?php print $rdiv; ?>>
  <a name="rdiv"></a>
    <div class="pluslet">
        <div class="titlebar">
            <div class="titlebar_text"><?php print _("Search FAQs"); ?></div>
        </div>
        <div class="pluslet_body" style="padding-right: 0; margin-right: 0;">
            <form action="faq.php" method="post" autocomplete="off">
                <p>
                    <?php
                    $input_box = new sp_CompleteMe("quick_search", "faq.php", "faq.php?faq_id=", "Quick Search", "faq", 40);
                    $input_box->displayBox();
                    ?>

                    <br />
                    <input type="checkbox" name="showsuggest" checked="checked" id="suggestornot" />
                    show suggestions </p>
            </form>
        </div>
    </div>
    <br />
    <div class="pluslet">
        <div class="titlebar">
            <div class="titlebar_text"><?php print _("Browse FAQs by Subject"); ?></div>
        </div>
        <div class="pluslet_body" style="padding-right: 0; margin-right: 0;">
            <form action="faq.php" method="post">
<?php print $guide_string; ?>
                <input type="submit" value="go" class="form_button" />
            </form>
        </div>
    </div>
    <br />
    <div class="pluslet">
        <div class="titlebar">
            <div class="titlebar_text"><?php print _("Browse FAQs by Collection"); ?></div>
        </div>
        <div class="pluslet_body">
            <ul>
<?php print $coll_items; ?>
            </ul>
        </div>
    </div>

</div>

<?php

include("includes/footer.php");

?>
<script type="text/javascript">

    $(document).ready(function(){

        function stripeR(container) {
            $(".zebra:even").addClass("evenrow");
            $(".zebra:odd").addClass("oddrow");
        }

        stripeR();

    });

</script>
