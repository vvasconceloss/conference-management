<?php
  session_start();
  
  include("../../../config/databaseConnection.php");
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <title>Inovatech | Conferencias</title>
</head>
<body>
  <header class="header">
      <div class="header-logo">
        <img src="../../images/inovatech_logo.png" alt="Inovatech Logo" class="logo-image">
      </div>
      <nav class="header-nav">
        <div class="header-nav-links">
          <a href="../../index.php" class="nav-link">Início</a>
          <a href="./conferencias.php" class="nav-link">Conferências</a>
        </div>
        <div class="header-nav-buttons">
          <a href="../login.php">
            <button id="signin">Iniciar Sessão</button>
          </a>
          <a href="../register.php">
            <button id="signup">Criar Conta</button>
          </a>
        </div>
      </nav>  
    </header>
</body>
</html>