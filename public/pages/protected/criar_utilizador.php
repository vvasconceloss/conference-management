<?php
  session_start();
  include("../../../config/databaseConnection.php");

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';
  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  if (!$isAdmin) {
      header("Location: ../conferencias.php");
      exit();
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/css/utilizadores.css?v=<?php echo time(); ?>">
    <script src="https://kit.fontawesome.com/15df1461d5.js" crossorigin="anonymous"></script>
    <title>Inovatech | Adicionar Utilizador</title>
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
                <?php if ($isLoggedIn): ?>
                    <div class="profile-dropdown">
                        <button class="profile-button">
                            <span class="profile-initial"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                        </button>
                        <div class="dropdown-content">
                            <a href="./profile.php" class="dropdown-link">Perfil</a>
                            <a href="../../scripts/user/logoutUser.php" class="dropdown-link">Terminar Sessão</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../login.php"><button id="signin">Iniciar Sessão</button></a>
                    <a href="../register.php"><button id="signup">Criar Conta</button></a>
                <?php endif; ?>
            </div>
        </nav>  
    </header>
    <main>
        <h1>Adicionar Utilizador</h1>
        <form method="POST" action="../../scripts/user/criarUtilizador.php" class="form-criar-utilizador">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="isEstrangeiro">Estrangeiro:</label>
                <select id="isEstrangeiro" name="isEstrangeiro" required>
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="isAdmin">Administrador:</label>
                <select id="isAdmin" name="isAdmin" required>
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="form-group">
                <label for="isParticipante">Participante:</label>
                <select id="isParticipante" name="isParticipante" required>
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="botao-criar">Criar Utilizador</button>
                <a href="./gerir_utilizadores.php" class="botao-cancelar">Cancelar</a>
            </div>
        </form>
    </main>
</body>
</html>