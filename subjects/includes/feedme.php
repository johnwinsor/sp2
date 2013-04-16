<?php

/**
 *   @file feedme.php
 *   @brief Decide what to do with an RSS-like request (could be flickr, delicious, rss)
 *
 *   @author adarby
 *   @date
 *   @todo
 */

/* JW - Added proxy for feed URLS */
include("../../control/includes/config.php");

$source = $_POST["feed"];
// tweak the source if it's a delicious feed
if ($_POST["type"] == "Delicious") {
    // http://feeds.delicious.com/v2/{format}/{username}/{tag[+tag+...+tag]}
    $source = "http://feeds.delicious.com/v2/rss/" . $source . "";
}
//print_r($_POST);
if ($_POST["count"]) {
    $count = $_POST["count"];
} else {
    $count = 5;
}

$show_desc = $_POST["show_desc"];
$show_feed = $_POST["show_feed"];
/* JW - Added proxy for feed URLS */

if (isset($_POST["proxy"])) {
    $proxy = $_POST["proxy"];
}

include("../../control/includes/classes/PGFeed.php");

//print "show feed = $show_feed; count = $count; show desc = $show_desc";

if ($_POST["type"] != "NewBooks") {

    $p = new PGFeed;

    $p->setOptions(0, $count, 0, 7200);
    $p->parse($source);
    $channel = $p->getChannel(); // gets data about the feed as a whole
    $items = $p->getItems();     // gets news items
    //$text = tr("vFeedSource", "source:");
    $our_feed .= "source: <a href=\"$channel[link]\">$channel[link]</a>";

    switch ($_POST["type"]) {

        case "Flickr":

            // displays linked thumbnails from a Flickr feed
            foreach ($items as $i) {
                echo "<div style=\"display: inline; margin-right: 5px; \"<a href=\"" . $i['link'] . "\" target=\"_blank\"><img src=\"" .
                $i["thumbnailURL"] . "\" height=\"" .
                $i['thumbnailHeight'] . "\" width=\"" .
                $i['thumbnailWidth'] . "\" title=\"$i[title]\" border=\"0\"/></a></div>\n";
            }
            break;

        case "Delicious":

            print "<ul>";
            // prints the linked title for each item
            foreach ($items as $i) {
                echo "<li><a href=\"" . $i['link'] . "\" target=\"_blank\">" . $i['title'] . "</a>";
                if ($show_desc == 1) {
                    print "<br />" . $i['description'];
                }
                print "</li>";
            }
            print "</ul>\n";
            //$p->dump();
            break;



        //case "rss":
        default:
            print "<ol>";
            // prints the linked title for each item
            foreach ($items as $i) {
                /* JW - Added proxy for feed URLS */
                if ($proxy == 1) {
                    print "<li><a href=\"" . $proxyURL . $i['link'] . "\" target=\"_blank\">" . $i['title'] . "</a>";
                } else {
                    print "<li><a href=\"" . $i['link'] . "\" target=\"_blank\">" . $i['title'] . "</a>";
                }
                if ($show_desc == 1) {
                    print "<br />" . $i['description'];
                }
                print "</li><br />";
            }
            print "</ol>\n";
            //$p->dump();
            break;
    }

    if ($show_feed == 1) {
        print "<p>$our_feed</p>";
    }

} else {
    //print $source;
    print "<iframe class=\"nbiframe\"  src=\"" . $BaseURL . "newbooks/newbooks.php?shelf=$source\" scrolling=\"no\"></iframe>";

//     print "<div class=\"gr\"><span style=\"color: #382110\">my read shelf:</span><br/><a href=\"http://www.goodreads.com/review/list/14996177?shelf=read\" title=\"Millslib's book recommendations, liked quotes, book clubs, book trivia, book lists (read shelf)\"><img border=\"0\" alt=\"Millslib's book recommendations, liked quotes, book clubs, book trivia, book lists (read shelf)\" src=\"http://www.goodreads.com/images/badge/badge1.jpg\"></a></div>";

// .gr {
// vertical-align: bottom;
// padding-bottom: 0;
// margin-bottom: 0;
// margin-left: auto;
// margin-right: auto;
// width: 50%;
// }



}

?>
