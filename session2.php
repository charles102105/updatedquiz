<?php
session_start();
if (!empty($_SESSION['username']))
{
print "Welcome ". $_SESSION['username'];
}
else
   exit ("Terminated <a href=session1.php>Login First </a>");
?>
<p><a href=session3.php>logout</a>
    hello world