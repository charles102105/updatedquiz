<?php
session_start();
$_SESSION['username']="shasha";
$_SESSION['year']=1950;
print "Hello ". $_SESSION['username'];
?>
<p><a href=session2.php>login Now</a>