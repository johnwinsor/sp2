
</div>
</div>
</div>

<div id="footer">

<p class="close" align="center">

<?php
    if (isset($last_mod) && $last_mod != "") {
        print _("Revised: ") . $last_mod;
    } else {
        print _("This page maintained by: ") . "<a href=\"mailto:$administrator_email\">
$administrator</a>";
    }

?>
<br />
Powered by <a href="http://www.subjectsplus.com/">SubjectsPlus</a>
<br /><br />
</p>
<!-- end footer div -->
</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40625016-1', 'mills.edu');
  ga('send', 'pageview');

</script>
</body>
<script src="<?php print $AssetPath; ?>jquery/bootstrap.js"></script>
</html>


