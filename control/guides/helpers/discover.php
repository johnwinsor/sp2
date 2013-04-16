<?php

/**
 *   @file
 *   @brief Search/Browse results returned via jquery, find_results.php
 *
 *   @author adarby
 *   @date
 *   @todo
 */

$subcat = "guides";
$page_title = "Find Stuff";

$no_header = "yes";

include("../../includes/header.php");

$all_subs = "";
$our_subject_id = "";

if (isset($_GET["subject_id"])) {
  $our_subject_id = scrubData($_GET["subject_id"], "int");
}

// get list of all subjects with pluslets, to use later
$q3 = "SELECT s.shortform, s.subject FROM pluslet p, subject s, pluslet_subject ps
WHERE p.pluslet_id = ps.pluslet_id
AND s.subject_id = ps.subject_id
group by s.subject";

//print $q2;

$r3 = mysql_query($q3);

while($myrow = mysql_fetch_array($r3)) {
	$sub_title = Truncate($myrow[1], 50, '');
	$all_subs .= "<option value=\"$myrow[0]\">$sub_title</option>";
}


print "
<div id=\"maincontent\">
<form action=\"discover.php\" method=\"post\" id=\"target\">

<div style=\"float: left; width: 60%;\">
	<h2>Browse</h2>
	<div class=\"box\">
	<select name=\"all_subs\" id=\"all_subs\">
	<option value=\"\" style=\"font-size: 9pt;\">" . _("- Browse Boxes -") . "</option>

	$all_subs
</select>
</div>

</div>
<div style=\"float: left;  width: 35%; margin-left: 3%;\">
	<h2>Search</h2>
	<div class=\"box\">
	 <input type=\"text\" id=\"search_terms\" name=\"search\" />
	 <input type=\"submit\" value=\"" . _("Go!") . "\" name=\"searcher\" id=\"searcho\" />
	 </div>
</div>
</form>
<div class=\"box no_overflow\" style=\"clear: both;\">
<div id=\"results\"></div>
</div>
";



?>

<script type="text/javascript" language="javascript">
$(document).ready(function(){

	var thisguide = '<?php print $our_subject_id; ?>';
	$("#all_subs").change(function() {
		var desired_guide = $("select option:selected").val();
		$("#results").fadeIn(3000).load("find_results.php", {shortform: desired_guide, guide_id: thisguide});
	});

	$('form').submit(function() {
		var terms = $("#search_terms").val();
		$("#results").fadeIn(3000).load("find_results.php", {search_terms: terms, guide_id: thisguide });
	  	return false;
	});


	$("img[name*=add-]").livequery('click', function(event) {
		var item_id = $(this).attr("name").split("-");
		// make these vars available to the parent file, guide.php
		parent.addItem = item_id[1];
		parent.addItemType = item_id[2];
		parent.jQuery.colorbox.close();
		return false;
	});


});
</script>
