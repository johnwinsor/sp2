1. CSS
    - default.css (assets/css/default.css)
        1. remove max-width from `#header_inner_wrap` and `#wrap`
        2. Tweak #leftcol and #rightcol
            - 65%/33%
        3. Remove the a:visited pseudoclass.
2. Good Reads RSS Feeds for New Books
    - Add GoodReads as a feed type
        - Edit "sp_Pluslet_Feed.php" - add NewBooks to the "$dd_array" array
            - $dd_array = array("Delicious", "RSS", "Flickr", "Twitter", "NewBooks");
    - PGFeed.php
        1. public function __construct() - Added "book_large_image_url" and "isbn" to itemElements array for Good Reads feed.
        2. Make sure "$this->strip = 0;" on line 236
    - feedme.php
        1. Disable existing print statement for the feed title in the default case of the switch statement.
             - //print "<li><a href=\"" . $i['link'] . "\" target=\"_blank\">" . $i['title'] . "</a>";
        2. Replace with a new 2 line print statement for printing Minerva title link and Good Reads large image
            - print "<li><a href=\"http://library.mills.edu/search/i?SEARCH=" . $i['isbn'] . "&sortdropdown=-&searchscope=6\">" . $i['title'] . "</a><br /><br />";
            - print "<img src=\"" . $i['book_large_image_url'] . "\"/></div>";
        3. Change the list type to an ordered list <ol>
3. Batch Importing Records (resources)
    - Use spreadsheet template - resource-import.csv
    - Tables
        - titles.csc
        - location.csv
        - rank.csv
        - location_title.csv
4. Tweak formatting of TOC in All Items by Source Pluslet
    - sp_Pluslet_1.php - remove the width style and change the class from toc1 to toc
        - <div style=\"float: left; width: 33%\" class=\"toc1\">
        - <div style=\"float: left; \" class=\"toc\">
5. Enable Proxy Functionality in Feeds
    - Public Facing Pages
        - feedme.php
            - grab proxy variable from POST (line 31)
                - `$proxy = $_POST["proxy"];`
            - embed the proxy into the link (lines 84-85)
                - `if ($proxy == 1) {
                        print "<li><a href=\"" . $proxyURL . $i['link'] . "\" target=\"_blank\">" . $i['title'] . "</a>";`
        - guide.php
            - Post the proxy variable to feedme.php - Add `proxy: feed[5]` to array (line 255)
    - Admin Page (proxy checkbox will update "extra" array with "proxy:1" in pluslet table in MySQL)
        - guide.js
            - Add "pshow_proxy" var to FEED case in preparePluslets function (line 274)
            - Add pshow_proxy to "pspecial" var array (lines 282-283)
            - add "proxy: feed[5]" to jquery find_feed function (line 748)
