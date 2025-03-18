<?php
  session_start();
  include("../../../config/databaseConnection.php");

  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  if (!$isAdmin) {
      die("Acesso negado.");
  }

  if (!isset($_GET['id'])) {
      die("ID da viagem não especificado.");
  }

  $viagemId = intval($_GET['id']);

  $queryDelete = "DELETE FROM viagem WHERE id = $viagemId";

  if (mysqli_query($connectionDB, $queryDelete)) {
      header("Location: ../../pages/protected/gerir_viagens.php");
      exit();
  } else {
      die("Erro ao excluir a viagem: " . mysqli_error($connectionDB));
  }
?>