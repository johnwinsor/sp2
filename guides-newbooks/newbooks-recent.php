<?php

function check_url($url) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		$headers = curl_getinfo($ch);
		curl_close($ch);

		return $headers['http_code'];
}

require "inc/pgfeed/PGFeed.php";

$source="http://www.goodreads.com/review/list_rss/14996177";
$shelf="currently-reading";
$feed = $source . "?shelf=" . $shelf;

$p = new PGFeed;
$p->setOptions(0,200,1,NULL);
$p->parse($feed);
$channel = $p->getChannel();
$items = $p->getItems();     // gets news items
shuffle($items);


?>
<!DOCTYPE html>
<html lang="en">
<head>
		<meta charset="utf-8">

<!-- Le styles -->
	 <!-- <link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet"> -->


<style>

.carousel {
height: 100%;
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
min-height: 400px;
max-height: 400px;
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
	margin-left:auto;
	margin-right:auto;
	width:300px;
}

.carousel-caption {
	line-height: 20px;
	font-style: italic;
	margin-left:auto;
	margin-right:auto;
	text-align:center;
	font-weight: bold;
}

.bookauthor {
	position: relative;
	padding:10px 1px 1px 1px;
	margin-left:auto;
	margin-right:auto;
	width:300px;
	line-height: 15px;
	text-align: left;
}

.subjectheading{
	position: relative;
	padding:1px 1px 1px 1px;
	margin-left:auto;
	margin-right:auto;
	width:300px;
	line-height: 15px;
	text-align: left;
}

</style>



		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
</head>
<body>
<div id="myCarousel" class="carousel slide">
		<div class="carousel-inner">
		<?php
		$img = $items[0]["book_large_image_url"];
		$check_url_status = check_url($img);
		if ($check_url_status == '200') {
			if (!preg_match("/nocover/i", $img)) {
					print " <div class=\"item active\"><img src=\"" . $img . "\" alt=\"\">";
					print "<div class=\"carousel-caption\">" . $items[0]["title"] . "</div>";
					print "<div class=\"bookauthor\">by " . $items[0]["author_name"] . "</div>";
					print "<div class=\"subjectheading\">Call No.: " . $items[0]["user_review"] . "</div>";
					print "</div>";
			}
		}

		foreach (array_slice($items,1) as $i) {
			$img = $i["book_large_image_url"];
			$check_url_status = check_url($img);
			if ($check_url_status == '200') {
				if (!preg_match("/nocover/i", $img)) {
					print "<div class=\"item\"><img src=\"" . $img . "\" alt=\"\">";
					print "<div class=\"carousel-caption\">" . $i["title"] . "</div>";
					print "<div class=\"bookauthor\">by " . $i["author_name"] . "</div>";
					print "<div class=\"subjectheading\">Call No.: " . $i["user_review"] . "</div>";
					print "</div>";
				}
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
