<?php 
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

require "PGFeed.php";
$p = new PGFeed;
$p->setOptions(0,30,0,NULL);

$source = $_GET["feed"];
//$source = "http://www.goodreads.com/review/list_rss/14996177?key=b649cd7986154243d9b1da2ff2afed0bbe4d800e&shelf=history-new-books";
$p->parse($source);
$channel = $p->getChannel();

$items = $p->getItems();     // gets news items

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

<!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

    <div id="myCarousel" class="carousel slide carousel-fade">
        <div class="carousel-inner">
            <div class="item active">
                    <a href="http://library.mills.edu/search/i?SEARCH=<?php echo $items[0]["isbn"];?>&sortdropdown=-&searchscope=6" target="_parent"><img src="<?php echo $items[0]["book_large_image_url"];?>" alt=""></a>
                    <!-- <div class="carousel-caption">
                          <p><?php echo $items[0]["title"];?></p>
                    </div> -->
            </div>
            <?php
            foreach (array_slice($items,1) as $i) {
                print "<div class=\"item\"><a href=\"http://library.mills.edu/search/i?SEARCH=" . $i["isbn"] . "&sortdropdown=-&searchscope=6\" target=\"_parent\"><img src=\"" . $i["book_large_image_url"] . "\" alt=\"\"></a></div>";
            }
            ?>
        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a> <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
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