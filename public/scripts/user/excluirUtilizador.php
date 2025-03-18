<?php
  session_start();

  include("../../../config/databaseConnection.php");

  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;

  if (!$isAdmin) {
      die("Acesso negado.");
  }

  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $id = $_GET['id'];

      $query = "DELETE FROM utilizador WHERE id = ?";
      $stmt = mysqli_prepare($connectionDB, $query);

      mysqli_stmt_bind_param($stmt, 'i', $id);
      $result = mysqli_stmt_execute($stmt);

      if ($result) {
          header("Location: ../../pages/protected/gerir_utilizadores.php");
          exit();
      } else {
          header("Location: ../../pages/protected/gerir_utilizadores.php");
          exit();
      }
  } else {
      header("Location: ../../pages/protected/gerir_utilizadores.php");
      exit();
  }
?>
