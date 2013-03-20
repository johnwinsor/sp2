<?php
/**
 *   @file search.php
 *   @brief Search results page
 *
 *   @author adarby
 *   @date feb, 2011
 *   @todo text translations
 */
$subcat = "home";
$page_title = "Search Results";

//print_r($_REQUEST);

include("includes/header.php");

// Set our default intro text (which will be overwritten if there has been a search)
$intro = _("<p>Please search for something with the box above.</p>");

if (isset($_POST["searchterm"])) {

    $searcher = mysql_real_escape_string(scrubData($_POST["searchterm"]));

    // Search all our categories
    // Guides, Records, Staff, FAQs, TalkBack
    // Only for people with records permission
    if ($_SESSION["records"] == 1) {
        $querier1 = new sp_Querier();
        $q1 = "SELECT title_id, title FROM title WHERE title LIKE '%" . $searcher . "%' ORDER BY title";
        $recordsArray = $querier1->getResult($q1);

        $querier2 = new sp_Querier();
        $q2 = "SELECT subject_id, subject FROM subject WHERE subject LIKE '%" . $searcher . "%' ORDER BY subject";
        $guidesArray = $querier2->getResult($q2);
    }

    // Only for admins
    if ($_SESSION["admin"] == 1) {
        $querier3 = new sp_Querier();
        $q3 = "SELECT staff_id, CONCAT(fname, ' ', lname) as fullname FROM staff WHERE (fname LIKE '%$searcher%') OR (lname LIKE '%$searcher%') ORDER BY lname";
        $staffArray = $querier3->getResult($q3);
    }

    // only for talkbackers
    if ($_SESSION["talkback"] == 1) {
        $querier4 = new sp_Querier();
        $q4 = "SELECT talkback_id, LEFT(question, 300) FROM talkback WHERE (question LIKE '%$searcher%') OR (answer LIKE '%$searcher%') ORDER BY question";
        $talkbackArray = $querier4->getResult($q4);
    }

    // only for faqers
    if ($_SESSION["faq"] == 1) {
        $querier5 = new sp_Querier();
        $q5 = "SELECT faq_id, LEFT(question, 300) FROM faq WHERE (question LIKE '%$searcher%') OR (answer LIKE '%$searcher%') OR (keywords LIKE '%$searcher%') ORDER BY question";
        $faqArray = $querier5->getResult($q5);
    }

    // Let's get started

    $search_types = array("records", "guides", "faq", "staff", "talkback");

    $records_takemeto = "records/record.php?record_id=";
    $guides_takemeto = "guides/guide.php?subject_id=";
    $staff_takemeto = "admin/user.php?staff_id=";
    $talkback_takemeto = "talkback/talkback.php?talkback_id=";
    $faq_takemeto = "faq/faq.php?faq_id=";

    $intro = _("<p>There are no results in your search for <strong>$searcher</strong>.</p>");


    $colour1 = "#fff";
    $colour2 = "#F6E3E7";
    $colour3 = "highlight";

	$search_result = '';

    foreach ($search_types as $key) {
        $row_count = 0;
        $currentArray = $key . "Array";
        global $$currentArray;
        $currentTakemeto = $key . "_takemeto";
        global $$currentTakemeto;

        if ($$currentArray) {
            $intro = ""; // clear out the intro
            $search_result .= "<h3>" . ucfirst($key) . "</h3>";

            foreach ($$currentArray as $value) {
                $row_colour = ($row_count % 2) ? $colour1 : $colour2;
                $search_result .= "<div style=\"background-color:$row_colour ; padding: 2px;\" class=\"striper\">
            &nbsp;&nbsp;<img src=\"$IconPath/required.png\" alt=\"bullet\" />  <a  href=\"" . $$currentTakemeto . $value[0] . "\">$value[1]</a></div>";
                $row_count++;
            }
        }
    }
}
?>

<br />
<div style="float: left;  width: 50%;">
    <h2 class="bw_head"><?php print _("Search Results"); ?></h2>
    <div class="box">
<?php print $intro . $search_result; ?>
    </div>

</div>


<?php
include("includes/footer.php");
?>

