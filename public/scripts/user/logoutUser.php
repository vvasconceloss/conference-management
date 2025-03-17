<?php
  session_start();

  if (isset($_SESSION['user_login'])) {
      session_unset();
      session_destroy();

      if (isset($_COOKIE['user_login'])) {
          setcookie("user_login", "", time() - 3600, "/");
      }

      header("Location: ../../pages/login.php");
      exit();
  } else {
      header("Location: ../../pages/login.php");
      exit();
  }
?>