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

  $queryCategorias = "SELECT * FROM categoria";
  $resultCategorias = mysqli_query($connectionDB, $queryCategorias);
  $categorias = mysqli_fetch_all($resultCategorias, MYSQLI_ASSOC);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $titulo = mysqli_real_escape_string($connectionDB, $_POST['titulo']);
      $descricao = mysqli_real_escape_string($connectionDB, $_POST['descricao']);
      $data = mysqli_real_escape_string($connectionDB, $_POST['data']);
      $duracao = mysqli_real_escape_string($connectionDB, $_POST['duracao']);
      $categoriasSelecionadas = $_POST['categorias'] ?? [];
      $oradoresSelecionados = $_POST['oradores'] ?? [];
      $participantesSelecionados = $_POST['participantes'] ?? [];

      $queryInserirConferencia = "INSERT INTO conferencia (titulo, descricao, data, duracao) VALUES (?, ?, ?, ?)";
      $stmt = mysqli_prepare($connectionDB, $queryInserirConferencia);
      mysqli_stmt_bind_param($stmt, "ssss", $titulo, $descricao, $data, $duracao);
      mysqli_stmt_execute($stmt);
      $conferenciaId = mysqli_insert_id($connectionDB);
      mysqli_stmt_close($stmt);

      if (!empty($categoriasSelecionadas)) {
          $queryAssociarCategoria = "INSERT INTO categoria_has_conferencia (categoria_id, conferencia_id) VALUES (?, ?)";
          foreach ($categoriasSelecionadas as $categoriaId) {
              $stmt = mysqli_prepare($connectionDB, $queryAssociarCategoria);
              mysqli_stmt_bind_param($stmt, "ii", $categoriaId, $conferenciaId);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_close($stmt);
          }
      }

      if (!empty($oradoresSelecionados)) {
          $queryAssociarOrador = "INSERT INTO conferencia_has_orador (conferencia_id, utilizador_id) VALUES (?, ?)";
          foreach ($oradoresSelecionados as $oradorId) {
              $stmt = mysqli_prepare($connectionDB, $queryAssociarOrador);
              mysqli_stmt_bind_param($stmt, "ii", $conferenciaId, $oradorId);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_close($stmt);
          }
      }

      if (!empty($participantesSelecionados)) {
          $queryAssociarParticipante = "INSERT INTO conferencia_has_utilizador (conferencia_id, utilizador_id) VALUES (?, ?)";
          foreach ($participantesSelecionados as $utilizadorId) {
              $stmt = mysqli_prepare($connectionDB, $queryAssociarParticipante);
              mysqli_stmt_bind_param($stmt, "ii", $conferenciaId, $utilizadorId);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_close($stmt);
          }
      }

      header("Location: ./conferencias.php");
      exit();
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/css/conferencias.css">
    <link rel="stylesheet" href="../../styles/css/criar_conferencia.css">
    <script src="https://kit.fontawesome.com/15df1461d5.js" crossorigin="anonymous"></script>
    <title>Inovatech | Criar Conferência</title>
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
        <h1>Criar Conferência</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" required></textarea>
            </div>
            <div class="form-group">
                <label for="data">Data e Hora:</label>
                <input type="datetime-local" id="data" name="data" required>
            </div>
            <div class="form-group">
                <label for="duracao">Duração:</label>
                <input type="time" id="duracao" name="duracao" required>
            </div>
            <div class="form-group">
                <label>Oradores:</label>
                <?php
                  $queryOradores = "SELECT id, nome FROM utilizador";
                  $resultOradores = mysqli_query($connectionDB, $queryOradores);
                  while ($orador = mysqli_fetch_assoc($resultOradores)) {
                      echo "<label><input type='checkbox' name='oradores[]' value='{$orador['id']}'> {$orador['nome']}</label><br>";
                  }
                ?>
            </div>
            <div class="form-group">
              <label for="participantes">Participantes:</label>
              <div class="form-group">
                <?php
                  $queryParticipantes = "SELECT id, nome FROM utilizador WHERE isParticipante = 1";
                  $resultParticipantes = mysqli_query($connectionDB, $queryParticipantes);
                  while ($participante = mysqli_fetch_assoc($resultParticipantes)) {
                      echo "<label><input type='checkbox' name='participantes[]' value='{$participante['id']}'> {$participante['nome']}</label><br>";
                  }
                ?>
                </div>
            </div>
            <div class="form-group">
                <label for="categorias">Categorias:</label>
                <select id="categorias" name="categorias[]" multiple>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['titulo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="botao-criar">Criar Conferência</button>
        </form>
    </main>
</body>
</html>