<?php
  session_start();
  include("../../../config/databaseConnection.php");

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';
  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  $conferenciaId = $_GET['id'] ?? null;

  if (!$conferenciaId) {
      die("Conferência não especificada.");
  }

  $queryConferencia = "
    SELECT c.*, cat.titulo AS categoria_titulo 
    FROM conferencia c
    LEFT JOIN categoria_has_conferencia chc ON c.id = chc.conferencia_id
    LEFT JOIN categoria cat ON chc.categoria_id = cat.id
    WHERE c.id = ?";

  $conferencia = [];
  if ($stmt = mysqli_prepare($connectionDB, $queryConferencia)) {
      mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
      mysqli_stmt_execute($stmt);
      $resultConferencia = mysqli_stmt_get_result($stmt);
      $conferencia = mysqli_fetch_assoc($resultConferencia);
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao obter os detalhes da conferência.");
  }

  $duracao = $conferencia['duracao'];

  $queryParticipantes = "
      SELECT u.nome AS utilizador, u.email AS email
      FROM conferencia_has_utilizador chu
      JOIN utilizador u ON chu.utilizador_id = u.id
      LEFT JOIN conferencia_has_orador cho ON cho.utilizador_id = u.id AND cho.conferencia_id = chu.conferencia_id
      WHERE chu.conferencia_id = ? AND cho.utilizador_id IS NULL";

  $participantes = [];
  if ($stmt = mysqli_prepare($connectionDB, $queryParticipantes)) {
      mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
      mysqli_stmt_execute($stmt);
      $resultParticipantes = mysqli_stmt_get_result($stmt);
      $participantes = mysqli_fetch_all($resultParticipantes, MYSQLI_ASSOC);
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao obter os participantes da conferência.");
  }

  $queryOradores = "
      SELECT u.nome AS utilizador, u.email AS email
      FROM conferencia_has_orador cho
      JOIN utilizador u ON cho.utilizador_id = u.id
      WHERE cho.conferencia_id = ?";

  $oradores = [];
  if ($stmt = mysqli_prepare($connectionDB, $queryOradores)) {
      mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
      mysqli_stmt_execute($stmt);
      $resultOradores = mysqli_stmt_get_result($stmt);
      $oradores = mysqli_fetch_all($resultOradores, MYSQLI_ASSOC);
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao obter os oradores da conferência.");
  }

  $queryRecursos = "
      SELECT r.tipo, r.nome, r.url
      FROM recurso r
      WHERE r.conferencia_id = ?";

  $recursos = [];
  if ($stmt = mysqli_prepare($connectionDB, $queryRecursos)) {
      mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
      mysqli_stmt_execute($stmt);
      $resultRecursos = mysqli_stmt_get_result($stmt);
      $recursos = mysqli_fetch_all($resultRecursos, MYSQLI_ASSOC);
      mysqli_stmt_close($stmt);
  } else {
      die("Erro ao obter os recursos da conferência.");
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comentario'])) {
      $comentario = mysqli_real_escape_string($connectionDB, $_POST['comentario']);
      $userId = $_SESSION['user_id'] ?? 0;

      if ($comentario) {
          $insertComentario = "
              INSERT INTO comentario (comentario, conferencia_id, utilizador_id)
              VALUES (?, ?, ?)";
          if ($stmt = mysqli_prepare($connectionDB, $insertComentario)) {
              mysqli_stmt_bind_param($stmt, "sii", $comentario, $conferenciaId, $userId);
              if (mysqli_stmt_execute($stmt)) {
                  echo "Comentário enviado com sucesso!";
              } else {
                  echo "Erro ao enviar comentário.";
              }
              mysqli_stmt_close($stmt);
          } else {
              echo "Erro ao preparar o comentário.";
          }
      }
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/detalhes_conferencia.css">
  <title>Inovatech | Detalhes da Conferência</title>
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
          <a href="../login.php">
            <button id="signin">Iniciar Sessão</button>
          </a>
          <a href="../register.php">
            <button id="signup">Criar Conta</button>
          </a>
        <?php endif; ?>
      </div>
    </nav>  
  </header>
  <main>
    <h1><?php echo htmlspecialchars($conferencia['titulo']); ?></h1>
    <div class="conferencia-detalhes">
      <p><?php echo htmlspecialchars($conferencia['descricao']); ?></p>
      <p><strong>Data:</strong> <?php echo date('d-m-Y H:i', strtotime($conferencia['data'])); ?></p>
      <p><strong>Duração:</strong> <?php echo htmlspecialchars($duracao); ?></p>
      <p><strong>Categoria:</strong> <?php echo htmlspecialchars($conferencia['categoria_titulo']); ?></p>
    </div>
    <div class="oradores-container">
      <h2>Oradores</h2>
      <?php if (!empty($oradores)): ?>
        <ul>
          <?php foreach ($oradores as $orador): ?>
            <li><?php echo htmlspecialchars($orador['utilizador']); ?> - <?php echo htmlspecialchars($orador['email']); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Nenhum orador encontrado.</p>
      <?php endif; ?>
    </div>
    <div class="participantes-container">
      <h2>Participantes</h2>
      <?php if (!empty($participantes)): ?>
        <ul>
          <?php foreach ($participantes as $participante): ?>
            <li><?php echo htmlspecialchars($participante['utilizador']); ?> - <?php echo htmlspecialchars($participante['email']); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Nenhum participante encontrado.</p>
      <?php endif; ?>
    </div>
    <div class="recursos-container">
      <h2>Recursos</h2>
      <?php if (!empty($recursos)): ?>
        <ul>
          <?php foreach ($recursos as $recurso): ?>
            <li><strong><?php echo htmlspecialchars($recurso['tipo']); ?>:</strong> <a href="<?php echo htmlspecialchars($recurso['url']); ?>" target="_blank"><?php echo htmlspecialchars($recurso['nome']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Nenhum recurso disponível.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
