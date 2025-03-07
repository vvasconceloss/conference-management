<?php
  $password = "";
  $username = "root";
  $serverName = "localhost";
  $databaseName = "conferencia_db";

  $connectionDB = mysqli_connect($serverName, $username, $password, $databaseName);

  if (!$connectionDB) {
    $_SESSION["errorServer"] = "An unexpected error occurred. Please try again later.";
    error_log("Database connection error: " . mysqli_connect_error());

    if (!headers_sent()) {
      header("Location:../public/pages/error.html");
      exit();
    }
  }
?>