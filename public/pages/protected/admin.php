<?php
  session_start();

  include("../../../config/databaseConnection.php");

  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  if (!$isLoggedIn || !$isAdmin) {
      header("Location: ../login.php");
      exit();
  }

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'Admin';

  $queryUsers = "SELECT COUNT(*) as total_users FROM utilizador";
  if ($stmt = mysqli_prepare($connectionDB, $queryUsers)) {
      mysqli_stmt_execute($stmt);
      $resultUsers = mysqli_stmt_get_result($stmt);
      $totalUsers = mysqli_fetch_assoc($resultUsers)['total_users'];
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao obter dados de utilizadores.");
  }

  $queryConferences = "SELECT COUNT(*) as total_conferences FROM conferencia";
  if ($stmt = mysqli_prepare($connectionDB, $queryConferences)) {
      mysqli_stmt_execute($stmt);
      $resultConferences = mysqli_stmt_get_result($stmt);
      $totalConferences = mysqli_fetch_assoc($resultConferences)['total_conferences'];
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao obter dados de conferências.");
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/admin.css">
  <title>Inovatech | Admin</title>
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
        <a href="./admin.php" class="nav-link active">Administração</a>
      </div>
      <div class="header-nav-buttons">
        <div class="profile-dropdown">
          <button class="profile-button">
            <span class="profile-initial"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
          </button>
          <div class="dropdown-content">
            <a href="./profile.php" class="dropdown-link">Perfil</a>
            <a href="../../scripts/user/logoutUser.php" class="dropdown-link">Terminar Sessão</a>
          </div>
        </div>
      </div>
    </nav>  
  </header>
  <main class="admin-main">
    <section class="admin-overview">
      <h1 class="admin-title">Painel de Administração</h1>
      <div class="admin-cards">
        <div class="admin-card">
          <h2 class="card-title">Utilizadores</h2>
          <p class="card-value"><?php echo $totalUsers; ?></p>
          <a href="./gerir_utilizadores.php" class="card-button">Gerir Utilizadores</a>
        </div>
        <div class="admin-card">
          <h2 class="card-title">Conferências</h2>
          <p class="card-value"><?php echo $totalConferences; ?></p>
          <a href="./gerir_conferencias.php" class="card-button">Gerir Conferências</a>
        </div>
        <div class="admin-card">
          <h2 class="card-title">Viagens</h2>
          <p class="card-value">0</p>
          <a href="./gerir_viagens.php" class="card-button">Gerir Viagens</a>
        </div>
      </div>
    </section>
  </main>
  <footer class="footer">
    <h3 class="footer-copyright">copyright &copy; 2025 <a href="https://github.com/vvasconceloss" target="_blank">Victor Vasconcelos</a> and <a href="https://github.com/JLGG2007" target="_blank">Juan Garcia</a></h3>
    <h3 class="footer-copyright">Escola Secundária de Santo André - Gestão e Programação de Sistemas de Informação</h3>
  </footer>
</body>
</html>