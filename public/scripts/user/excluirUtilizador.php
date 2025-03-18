<?php
  session_start();
  include("../../../config/databaseConnection.php");

  if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
      header("Location: ./conferencias.php");
      exit();
  }

  if (isset($_GET['id'])) {
      $id = intval($_GET['id']);

      $queryDeleteRecursos = "DELETE FROM recurso WHERE conferencia_id = ?";
      $stmtRecursos = mysqli_prepare($connectionDB, $queryDeleteRecursos);
      mysqli_stmt_bind_param($stmtRecursos, "i", $id);
      mysqli_stmt_execute($stmtRecursos);
      mysqli_stmt_close($stmtRecursos);

      $queryDeleteUtilizadores = "DELETE FROM conferencia_has_utilizador WHERE conferencia_id = ?";
      $stmtUtilizadores = mysqli_prepare($connectionDB, $queryDeleteUtilizadores);
      mysqli_stmt_bind_param($stmtUtilizadores, "i", $id);
      mysqli_stmt_execute($stmtUtilizadores);
      mysqli_stmt_close($stmtUtilizadores);

      $queryDeleteCategorias = "DELETE FROM categoria_has_conferencia WHERE conferencia_id = ?";
      $stmtCategorias = mysqli_prepare($connectionDB, $queryDeleteCategorias);
      mysqli_stmt_bind_param($stmtCategorias, "i", $id);
      mysqli_stmt_execute($stmtCategorias);
      mysqli_stmt_close($stmtCategorias);

      $queryDelete = "DELETE FROM conferencia WHERE id = ?";
      $stmt = mysqli_prepare($connectionDB, $queryDelete);
      mysqli_stmt_bind_param($stmt, "i", $id);
      mysqli_stmt_execute($stmt);

      if (mysqli_stmt_affected_rows($stmt) > 0) {
          mysqli_stmt_close($stmt);
          header("Location: ../../pages/protected/conferencias.php?msg=Conferência+excluída+com+sucesso");
          exit();
      } else {
          mysqli_stmt_close($stmt);
          header("Location: ../../pages/protected/conferencias.php?msg=Erro+ao+excluir+conferência");
          exit();
      }
  } else {
      header("Location: ../../pages/protected/conferencias.php?msg=ID+inválido");
      exit();
  }
?>