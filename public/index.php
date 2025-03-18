<?php
  session_start();

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';
  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/global.css">
  <link rel="stylesheet" href="./styles/css/index.css">
  <link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon">
  <title>Inovatech | Gestão de Conferências</title>
</head>
<body>
  <header class="header">
    <div class="header-logo">
      <img src="./images/inovatech_logo.png" alt="Inovatech Logo" class="logo-image">
    </div>
    <nav class="header-nav">
      <div class="header-nav-links">
        <a href="./index.php" class="nav-link">Início</a>
        <a href="./pages/protected/conferencias.php" class="nav-link">Conferências</a>
        <?php if ($isAdmin): ?>
          <a href="./pages/protected/admin.php" class="nav-link">Administração</a>
        <?php endif; ?>
      </div>
      <div class="header-nav-buttons">
        <?php if ($isLoggedIn): ?>
          <div class="header-nav-buttons">
            <div class="profile-dropdown">
              <button class="profile-button">
                <span class="profile-initial"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
              </button>
              <div class="dropdown-content">
                <a href="./pages/protected/profile.php" class="dropdown-link">Perfil</a>
                <a href="./scripts/user/logoutUser.php" class="dropdown-link">Terminar Sessão</a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a href="./pages/login.php">
            <button id="signin">Iniciar Sessão</button>
          </a>
          <a href="./pages/register.php">
            <button id="signup">Criar Conta</button>
          </a>
        <?php endif; ?>
      </div>
    </nav>  
  </header>
  <main class="main">
    <section class="main-section">
      <h2 class="section-1-subtitle">InovaTech: Fazemos a diferença</h2>
      <h1 class="section-1-title">Gestor de Conferências</h1>
    </section>
  </main>
  <footer class="footer">
    <h3 class="footer-copyright">copyright &copy; 2025 <a href="https://github.com/vvasconceloss" target="_blank">Victor Vasconcelos</a> and <a href="https://github.com/JLGG2007" target="_blank">Juan Garcia</a></h3>
    <h3 class="footer-copyright">Escola Secundária de Santo André - Gestão e Programação de Sistemas de Informação</h3>
  </footer>
</body>
</html>