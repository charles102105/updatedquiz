<?php
session_start();
if (!empty($_SESSION['username']))
{
print "Thank you choosing this page ". $_SESSION['username'];
session_destroy();
}
else
  header('location:login.php');
?>