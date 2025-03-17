<?php
  session_start();

  include("../../../config/databaseConnection.php");

  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';

  if (!$isLoggedIn) {
      header("Location: ../login.php");
      exit();
  }

  if ($stmt = mysqli_prepare($connectionDB, "SELECT nome, email FROM usuarios WHERE login = ? LIMIT 1")) {
      mysqli_stmt_bind_param($stmt, 's', $userLogin);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      if ($row = mysqli_fetch_assoc($result)) {
          $userName = $row['nome'];
          $userLogin = $row['email'];
      } else {
          die("Erro: Usuário não encontrado.");
      }
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao recuperar dados do usuário.");
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/profile.css">
  <title>Inovatech | Perfil</title>
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
        <?php if ($isAdmin): ?>
          <a href="./admin.php" class="nav-link">Administração</a>
        <?php endif; ?>
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
  <main class="profile-main">
    <section class="profile-section">
      <div class="profile-header">
        <div class="profile-icon">
          <span class="profile-initial-large"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
        </div>
        <h1 class="profile-name"><?php echo $userName; ?></h1>
      </div>
      <div class="profile-info">
        <h2 class="info-title">Informações do Perfil</h2>
        <form action="../../scripts/user/updateProfile.php" method="post" class="info-form">
          <div class="form-group">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" value="<?php echo $userName; ?>" class="form-input">
          </div>
          <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo $userLogin; ?>" class="form-input">
          </div>
          <div class="form-group">
            <label for="password">Nova Senha:</label>
            <input type="password" id="password" name="password" class="form-input">
          </div>
          <button type="submit" class="form-button">Atualizar Perfil</button>
        </form>
      </div>
    </section>
  </main>
</body>
</html>