<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php print $page_title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="Description" content="<?php if (isset($description)) {print $description;} ?>" />
<meta name="Keywords" content="<?php if (isset($keywords)) {print $keywords;} ?>" />
<meta name="Author" content="" />
<?php if (isset($is_responsive) && $is_responsive == TRUE) {
    echo '<link type="text/css" media="all" rel="stylesheet" href="' . $AssetPath . 'css/bootstrap.css">';
}
?>

<link type="text/css" media="screen" rel="stylesheet" href="<?php print $AssetPath; ?>css/default.css">
<!-- <link type="text/css" media="print" rel="stylesheet" href="<?php print $AssetPath; ?>css/print.css"> -->
<link type="text/css" media="screen" rel="stylesheet" href="<?php print $AssetPath; ?>css/custom.css">

<?php // Load our jQuery libraries + some css
if (isset($use_jquery)) { print generatejQuery($use_jquery);
?>
    <script type="text/javascript" language="javascript">
    // Used for autocomplete

        $.fn.defaultText = function(value){

            var element = this.eq(0);
            element.data('defaultText',value);

            element.focus(function(){
                if(element.val() == value){
                    element.val('').removeClass('defaultText');
                }
            }).blur(function(){
                if(element.val() == '' || element.val() == value){
                    element.addClass('defaultText').val(value);

                }
            });

            return element.blur();
        }
    </script>
<?php
}
?>
</head>

<body>
<div class="container-fluid">
    <div class="row-fluid header">
        <div class="span12">
            <div class="navbar right">
                <div>
                    <ul class="nav">
                        <li><a href="http://library.mills.edu/patroninfo">My Minerva Login</a></li>
                        <li><a href="http://www.mills.edu/academics/library/exceptions.php">Library Hours</a></li>
                        <li><a href="http://library.mills.edu/">Minerva Catalog Home</a></li>
                    </ul>
                </div>
            </div>
            <div class="topLogoSmall">
                <div class="mills">
                    <a href="http://www.mills.edu" tabindex="10"><img src="http://library.mills.edu/screens/Mills_logo_280_scaled.png" height="49" width="170" alt="Mills College Library"></a>
                </div>
                <div>
                    <span class="topLogoSmallText"><a href="http://www.mills.edu/academics/library/index.php">F.W. Olin Library:</a> <a href="http://library.mills.edu/">Minerva</a></span>
                </div>
            </div>
            <div class="navbar">
                <ul class="nav" role="navigation">
                    <li class="dropdown">
                        <a id="drop1" href="http://www.mills.edu/academics/library/index.php" role="button" class="dropdown-toggle" data-toggle="dropdown">Library Information <b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                            <li>
                                <a tabindex="-1" href="http://library.mills.edu/suggest">Suggestions</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="http://www.mills.edu/academics/library/library_information/news.php">Library News</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="http://www.mills.edu/academics/library/library_departments/circulation.php#renew">Renewals</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="http://www.mills.edu/academics/library/library_departments/circulation.php#fines">Fines and Payments</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="http://library.mills.edu/help" id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown">Search Help <b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
                            <li>
                                <a tabindex="-1" href="http://library.mills.edu/help#searching">Seearching</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="http://library.mills.edu/help#searchtips">Advanced Keyword Search Tips</a>
                            </li>
                            <li>
                                <a tabindex="-1" href="http://library.mills.edu/help#prefsearch">Saving your searches</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="http://library.mills.edu/screens/connect.html">Connect with a Librarian</a>
                    </li>
                    <li>
                        <a href="http://www.mills.edu/academics/library/library_information/faq.php">FAQ</a>
                    </li>
                    <li>
                        <a href="<?php print $BaseURL; ?>subjects/index.php">Study Guides</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row-fluid searchbar">
        <div class="span12">
            <select name="specializedSearch" id="specializedSearch">
                                    <option selected="selected" value=" ">
                                        Search Options
                                    </option>
                                    <option value="http://library.mills.edu/search/X">
                                        Advanced Keyword
                                    </option>
                                    <option value="http://library.mills.edu/search/t">
                                        Title
                                    </option>
                                    <option value="http://library.mills.edu/search/a">
                                        Author
                                    </option>
                                    <option value="http://library.mills.edu/search/q">
                                        Author and Title
                                    </option>
                                    <option value="http://library.mills.edu/search/d">
                                        Subject
                                    </option>
                                    <option value="http://library.mills.edu/search/s">
                                        Periodical Title
                                    </option>
                                    <option value="http://library.mills.edu/search/r">
                                        Course Reserve
                                    </option>
                                    <option value="http://library.mills.edu/search/y">
                                        Electronic Resource
                                    </option>
                                    <option value="http://library.mills.edu/search/c">
                                        Call Number
                                    </option>
                                    <option value="http://library.mills.edu/search/i">
                                        ISBN/ISSN/Music No.
                                    </option>
                                    <option value="http://library.mills.edu/search/l">
                                        LCCN
                                    </option>
                                    <option value="http://library.mills.edu/search/f">
                                        Publication No.
                                    </option>
                                    <option value="http://library.mills.edu/search/g">
                                        Sudocs No.
                                    </option>
                                    <option value="http://library.mills.edu/search/o">
                                        Utility No.
                                    </option>
                                    <option value="http://library.mills.edu/search/b">
                                        Barcode
                                    </option>
            </select>
            <input type="image" src="http://library.mills.edu/screens/ico_go_orange.png" class="searchNavBut" value="Go" onclick="GotoURL();">
        </div>
    </div>


<div class="row-fluid" id="header">
    <div id="header_inner_wrap">
  	<div><h1><?php print $page_title; ?></h1></div>
	<!-- <div id="header-tools" style="float: right;">
	  <a href="http://www.addthis.com/bookmark.php?v=250&pub=xa-4a5cb30866f1511b" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()"><img src="http://s7.addthis.com/static/btn/lg-bookmark-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a5cb30866f1511b"></script>
	</div> -->
    </div>
</div>
<div class="row-fluid" id="maincontent">
    <div style="padding-left: 10px; padding-right: 10px;">
