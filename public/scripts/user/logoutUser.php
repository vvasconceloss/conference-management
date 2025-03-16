<?php
  session_start();
  
  if (isset($_SESSION['user_login'])) {
    session_unset();
    session_destroy();
    setcookie("user_login", "", time() - 3600, "/");
    header("Location: ../../pages/login.php");
    exit();
  }
?>