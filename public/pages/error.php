<?php
  session_start();
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);
?>

<!DOCTYPE html>
<html lang="pt-PT">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/css/error.css">
    <title>Inovatech | Falha...</title>
  </head>
  <body>
    <header class="header">
      <div class="header-logo">
        <img src="../images/inovatech_logo.png" alt="Inovatech Logo" class="logo-image">
      </div>
      <nav class="header-nav">
        <div class="header-nav-links">
          <a href="../index.php" class="nav-link">Início</a>
          <a href="../pages/protected/conferencias.php" class="nav-link">Conferências</a>
        </div>
        <div class="header-nav-buttons">
          <?php if ($isLoggedIn): ?>
            <a href="../pages/protected/profile.php" class="profile-link">
              <img 
                src="<?php echo !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '../images/default_profile.jpg'; ?>" 
                alt="Foto de Perfil" 
                class="profile-image"
              >
            </a>
          <?php else: ?>
            <a href="./login.php">
              <button id="signin">Iniciar Sessão</button>
            </a>
            <a href="./register.php">
              <button id="signup">Criar Conta</button>
            </a>
          <?php endif; ?>
        </div>
      </nav>  
    </header>
    <main class="main">
      <img src="../images/error404image.jpg" alt="">
      <h1 class="error-message">Ocorreu um erro! Tente novamente...</h1>
    </main>
  </body>
</html>