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

    $conferenciaId = $_GET['id'] ?? null;

    if (!$conferenciaId) {
        header("Location: ./conferencias.php");
        exit();
    }

    $queryConferencia = "SELECT * FROM conferencia WHERE id = ?";
    $stmt = mysqli_prepare($connectionDB, $queryConferencia);
    mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $conferencia = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$conferencia) {
        header("Location: ./conferencias.php");
        exit();
    }

    $queryCategorias = "SELECT * FROM categoria";
    $resultCategorias = mysqli_query($connectionDB, $queryCategorias);
    $categorias = mysqli_fetch_all($resultCategorias, MYSQLI_ASSOC);

    $queryCategoriasSelecionadas = "SELECT categoria_id FROM categoria_has_conferencia WHERE conferencia_id = ?";
    $stmt = mysqli_prepare($connectionDB, $queryCategoriasSelecionadas);
    mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
    mysqli_stmt_execute($stmt);
    $resultCategoriasSelecionadas = mysqli_stmt_get_result($stmt);
    $categoriasSelecionadas = array_column(mysqli_fetch_all($resultCategoriasSelecionadas, MYSQLI_ASSOC), 'categoria_id');
    mysqli_stmt_close($stmt);

    $queryOradores = "SELECT id, nome FROM utilizador WHERE id IN (SELECT utilizador_id FROM conferencia_has_orador WHERE conferencia_id = ?)";
    $stmt = mysqli_prepare($connectionDB, $queryOradores);
    mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
    mysqli_stmt_execute($stmt);
    $resultOradores = mysqli_stmt_get_result($stmt);
    $oradoresSelecionados = array_column(mysqli_fetch_all($resultOradores, MYSQLI_ASSOC), 'id');
    mysqli_stmt_close($stmt);

    $queryParticipantes = "SELECT id, nome FROM utilizador WHERE isParticipante = 1";
    $resultParticipantes = mysqli_query($connectionDB, $queryParticipantes);
    $participantes = mysqli_fetch_all($resultParticipantes, MYSQLI_ASSOC);

    $queryParticipantesSelecionados = "SELECT utilizador_id FROM conferencia_has_utilizador WHERE conferencia_id = ?";
    $stmt = mysqli_prepare($connectionDB, $queryParticipantesSelecionados);
    mysqli_stmt_bind_param($stmt, "i", $conferenciaId);
    mysqli_stmt_execute($stmt);
    $resultParticipantesSelecionados = mysqli_stmt_get_result($stmt);
    $participantesSelecionados = array_column(mysqli_fetch_all($resultParticipantesSelecionados, MYSQLI_ASSOC), 'utilizador_id');
    mysqli_stmt_close($stmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = mysqli_real_escape_string($connectionDB, $_POST['titulo']);
        $descricao = mysqli_real_escape_string($connectionDB, $_POST['descricao']);
        $data = mysqli_real_escape_string($connectionDB, $_POST['data']);
        $duracao = mysqli_real_escape_string($connectionDB, $_POST['duracao']);
        $categoriasSelecionadas = $_POST['categorias'] ?? [];
        $oradoresSelecionados = $_POST['oradores'] ?? [];
        $participantesSelecionados = $_POST['participantes'] ?? [];

        $queryUpdate = "UPDATE conferencia SET titulo=?, descricao=?, data=?, duracao=? WHERE id=?";
        $stmt = mysqli_prepare($connectionDB, $queryUpdate);
        mysqli_stmt_bind_param($stmt, "ssssi", $titulo, $descricao, $data, $duracao, $conferenciaId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_query($connectionDB, "DELETE FROM categoria_has_conferencia WHERE conferencia_id = $conferenciaId");
        foreach ($categoriasSelecionadas as $categoriaId) {
            $stmt = mysqli_prepare($connectionDB, "INSERT INTO categoria_has_conferencia (categoria_id, conferencia_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $categoriaId, $conferenciaId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_query($connectionDB, "DELETE FROM conferencia_has_orador WHERE conferencia_id = $conferenciaId");
        foreach ($oradoresSelecionados as $oradorId) {
            $stmt = mysqli_prepare($connectionDB, "INSERT INTO conferencia_has_orador (utilizador_id, conferencia_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $oradorId, $conferenciaId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_query($connectionDB, "DELETE FROM conferencia_has_participante WHERE conferencia_id = $conferenciaId");
        foreach ($participantesSelecionados as $participanteId) {
            $stmt = mysqli_prepare($connectionDB, "INSERT INTO conferencia_has_participante (utilizador_id, conferencia_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $participanteId, $conferenciaId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
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
      <title>Inovatech | Editar Conferência</title>
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
    <h1>Editar Conferência</h1>
      <form method="POST" action="">
              <div class="form-group">
                  <label for="titulo">Título:</label>
                  <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($conferencia['titulo']); ?>" required>
              </div>
              <div class="form-group">
                  <label for="descricao">Descrição:</label>
                  <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($conferencia['descricao']); ?></textarea>
              </div>
              <div class="form-group">
                  <label for="data">Data e Hora:</label>
                  <input type="datetime-local" id="data" name="data" value="<?php echo htmlspecialchars($conferencia['data']); ?>" required>
              </div>
              <div class="form-group">
                  <label for="duracao">Duração:</label>
                  <input type="time" id="duracao" name="duracao" value="<?php echo htmlspecialchars($conferencia['duracao']); ?>" required>
              </div>
              <div class="form-group">
                <label for="oradores">Oradores:</label>
                <select id="oradores" name="oradores[]" multiple>
                  <?php foreach ($participantes as $orador): ?>
                    <option value="<?php echo $orador['id']; ?>" <?php echo in_array($orador['id'], $oradoresSelecionados) ? 'selected' : ''; ?>>
                      <?php echo $orador['nome']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="participantes">Participantes:</label>
                <select id="participantes" name="participantes[]" multiple>
                    <?php
                    $queryParticipantes = "SELECT id, nome FROM utilizador WHERE isParticipante = 1";
                    $resultParticipantes = mysqli_query($connectionDB, $queryParticipantes);
                    while ($participante = mysqli_fetch_assoc($resultParticipantes)) {
                        echo "<option value='{$participante['id']}'>{$participante['nome']}</option>";
                    }
                    ?>
                </select>
              </div>
              <div class="form-group">
                <label for="categorias">Categorias:</label>
                <select id="categorias" name="categorias[]" multiple>
                  <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo in_array($cat['id'], $categoriasSelecionadas) ? 'selected' : ''; ?>>
                  <?php echo $cat['titulo']; ?>
                  </option>
                <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="botao-criar">Criar Conferência</button>
          </form>
    </main>
  </body>
  </html>