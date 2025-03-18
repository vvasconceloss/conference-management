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

  if ($stmt = mysqli_prepare($connectionDB, "SELECT nome, email, isEstrangeiro, isParticipante FROM utilizador WHERE email = ? LIMIT 1")) {
      mysqli_stmt_bind_param($stmt, 's', $userLogin);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      if ($row = mysqli_fetch_assoc($result)) {
          $userName = $row['nome'];
          $userLogin = $row['email'];
          $estrangeiro = $row['isEstrangeiro'];
          $participante = $row['isParticipante'];
      } else {
          die("Erro: Usuário não encontrado.");
      }
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao recuperar dados do usuário.");
  }

  $viagem = [];
  if ($stmt = mysqli_prepare($connectionDB, "SELECT * FROM viagem WHERE utilizador_id = (SELECT id FROM utilizador WHERE email = ?)")) {
      mysqli_stmt_bind_param($stmt, 's', $userLogin);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      if ($row = mysqli_fetch_assoc($result)) {
          $viagem = $row;
      }
      mysqli_stmt_close($stmt);
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
        <h1 class="profile-name"><?php echo htmlspecialchars($userName); ?></h1>
      </div>
      <div class="profile-info">
        <h2 class="info-title">Informações do Perfil</h2>
        <form action="../../scripts/user/updateProfile.php" method="post" class="info-form">
          <div class="form-group">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" class="form-input">
          </div>
          <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userLogin); ?>" class="form-input">
          </div>
          <div class="form-group">
            <label for="password">Nova Senha:</label>
            <input type="password" id="password" name="password" class="form-input">
          </div>
          <div class="form-group">
            <label for="estrangeiro">Estrangeiro</label>
            <select name="estrangeiro" id="estrangeiro" class="form-input" disabled>
              <option value="1" <?php echo $estrangeiro ? 'selected' : ''; ?>>Sim</option>
              <option value="0" <?php echo !$estrangeiro ? 'selected' : ''; ?>>Não</option>
            </select>
          </div>
          <div class="form-group">
            <label for="participante">Participante</label>
            <select name="participante" id="participante" class="form-input" disabled>
              <option value="1" <?php echo $participante ? 'selected' : ''; ?>>Sim</option>
              <option value="0" <?php echo !$participante ? 'selected' : ''; ?>>Não</option>
            </select>
          </div>
          <button type="submit" class="form-button">Atualizar Perfil</button>
        </form>
      </div>
    </section>
    <section class="profile-conferencia">
      <h2 class="info-title">Minhas Conferências</h2>
        <div class="conferencias-list">
          <?php if (!empty($conferenciasParticipando)): ?>
              <ul>
                  <?php foreach ($conferenciasParticipando as $conferencia): ?>
                      <li>
                          <a class="link-conferencia-profile" href="detalhes_conferencia.php?id=<?php echo $conferencia['id']; ?>">
                              <h3><?php echo htmlspecialchars($conferencia['titulo']); ?></h3>
                              <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($conferencia['data'])); ?></p>
                              <p><?php echo htmlspecialchars($conferencia['descricao']); ?></p>
                          </a>
                      </li>
                  <?php endforeach; ?>
              </ul>
          <?php else: ?>
              <p>Você não está inscrito em nenhuma conferência.</p>
          <?php endif; ?>
        </div>
    </section>
    <section class="profile-viagem">
      <h2 class="info-title">Informações de Viagem e Deslocamento</h2>
      <?php if (!empty($viagem)): ?>
          <div class="viagem-info">
              <p><strong>Origem:</strong> <?php echo htmlspecialchars($viagem['origem']); ?></p>
              <p><strong>Data de Partida:</strong> <?php echo date('d/m/Y', strtotime($viagem['data_partida'])); ?></p>
              <p><strong>Data de Retorno:</strong> <?php echo date('d/m/Y', strtotime($viagem['data_chegada'])); ?></p>
          </div>
      <?php else: ?>
          <p>Nenhuma informação de viagem ou deslocamento encontrada.</p>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>