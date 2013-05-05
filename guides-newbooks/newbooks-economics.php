<?php 

ini_set('display_errors','On');
error_reporting(E_ALL);

require "inc/pgfeed/PGFeed.php";
$p = new PGFeed;
$p->setOptions(0,30,0,NULL);

$source="http://www.goodreads.com/review/list_rss/14996177";
// $shelf= $_GET["shelf"];
$shelf="economics-new-books";
$feed = $source . "?shelf=" . $shelf;
//print $feed;
$p->parse($feed);
$channel = $p->getChannel();

$items = $p->getItems();     // gets news items

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

<!-- Le styles -->
   <!-- <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet"> -->
  
 
    <style>
body {
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 14px;
  margin: 0px;
  height: 700px;
  background-color: black;
}

.carousel {
height: 700px;
position: relative;
margin-bottom: 0;
padding-bottom: 0;
}

.carousel-inner {
overflow: hidden;
position: relative;
margin: 0px auto;
}

.item img {
min-height: 500px;
margin: 0px auto;
padding:10px;
border:1px solid black;
background:white;
-ms-interpolation-mode: bicubic;
}

.carousel {
  position: relative;
  line-height: 1;
}
.carousel-inner {
  overflow: hidden;
  width: 100%;
  position: relative;
}
.carousel .item {
  display: none;
  position: relative;
  -webkit-transition: 0.6s ease-in-out left;
  -moz-transition: 0.6s ease-in-out left;
  -o-transition: 0.6s ease-in-out left;
  transition: 0.6s ease-in-out left;
}
.carousel .item > img {
  display: block;
  line-height: 1;
}
.carousel .active,
.carousel .next,
.carousel .prev {
  display: block;
}
.carousel .active {
  left: 0;
}
.carousel .next,
.carousel .prev {
  position: absolute;
  top: 0;
  width: 100%;
}
.carousel .next {
  left: 100%;
}
.carousel .prev {
  left: -100%;
}
.carousel .next.left,
.carousel .prev.right {
  left: 0;
}
.carousel .active.left {
  left: -100%;
}
.carousel .active.right {
  left: 100%;
}

.carousel-caption { 
  position: relative;
  padding:1px 1px 1px 1px;
  color: white;
  margin-left:auto;
  margin-right:auto;
  width:650px;
}

.carousel-caption h2,
.carousel-caption p {
  line-height: 30px;
  font-style: italic;
  width:600px;
  margin-left:auto;
  margin-right:auto;
  text-align:center;
  font-weight: normal;
}

.bookauthor {
  position: relative;
  padding:1px 1px 1px 1px;
  color: white;
  margin-left:auto;
  margin-right:auto;
  width:600px;
  line-height: 15px;
  text-align: right;
}

.subjectheading{
   text-align:center;
  color: white;

}

</style>



    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<div class="subjectheading"><h2>New Books in Economics</h2></div>
<div id="myCarousel" class="carousel slide">
    <div class="carousel-inner">
    <?php 
    $img = $items[0]["book_large_image_url"];
    if (!preg_match("/nocover/i", $img)) {
        print " <div class=\"item active\"><img src=\"" . $img . "\" alt=\"\">";
        print "<div class=\"carousel-caption\"><h2>" . $items[0]["title"] . "</h2></div>";
        print "<div class=\"bookauthor\">by " . $items[0]["author_name"] . "</div>";
        print "<div class=\"bookauthor\">Call No.: " . $items[0]["user_review"] . "</div>";
        print "</div>";
    }
        foreach (array_slice($items,1) as $i) {
            $img = $i["book_large_image_url"];
            if (!preg_match("/nocover/i", $img)) {
                print "<div class=\"item\"><img src=\"" . $img . "\" alt=\"\">";
                print "<div class=\"carousel-caption\"><h2>" . $i["title"] . "</h2></div>";
                print "<div class=\"bookauthor\">by " . $i["author_name"] . "</div>";
                print "<div class=\"bookauthor\">Call No.: " . $i["user_review"] . "</div>";
                print "</div>";
            }
        }
        ?>
    </div>
</div>

<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>

    <script type="text/javascript">
    $('.carousel').carousel({
            interval: 6000
    })
</script>

</body>
</html>
