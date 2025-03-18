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
    <link rel="stylesheet" href="../styles/css/register.css">
    <title>Inovatech | Criar Conta</title>
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
      <section class="main-section-register">
        <h2 class="section-register-title">Bem-vindo(a) a plataforma! Crie sua conta</h2>
        <form class="form-register" method="post" action="../scripts/user/registarUser.php">
          <div class="field">
            <input autocomplete="off" placeholder="Nome" name="nome" class="input-field" type="text">
          </div>
          <div class="field">
            <input placeholder="Email" name="email" class="input-field" type="text">
          </div>
          <div class="field">
            <input placeholder="Password" name="password" class="input-field" type="password">
          </div>
          <div class="field">
            <input placeholder="Confirmar Password" name="confirmar" class="input-field" type="password">
          </div>
          <div class="checkbox-field">
            <input type="checkbox" id="estrangeiro" name="estrangeiro">
            <label for="estrangeiro">Estrangeiro</label>
          </div>
          <button class="button-register" type="submit">Criar Conta</button>
        </form>
        <h3 class="login-text">Já tem uma conta? <a href="./login.php">Inicie sua sessão</a></h3>
      </section>
    </main>
  </body>
</html>