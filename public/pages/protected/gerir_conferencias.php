<?php
  session_start();
  include("../../../config/databaseConnection.php");

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';
  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  $querySemanas = "SELECT DISTINCT DATE_FORMAT(data, '%Y-%m-%d') AS semana FROM conferencia ORDER BY data ASC";
  $resultSemanas = mysqli_query($connectionDB, $querySemanas);
  $semanasDisponiveis = mysqli_fetch_all($resultSemanas, MYSQLI_ASSOC);

  $queryCategorias = "SELECT * FROM categoria";
  $resultCategorias = mysqli_query($connectionDB, $queryCategorias);
  $categorias = mysqli_fetch_all($resultCategorias, MYSQLI_ASSOC);

  $semanaSelecionada = $_GET['semana'] ?? ($semanasDisponiveis[0]['semana'] ?? date('Y-m-d'));
  $categoriaSelecionada = $_GET['categoria'] ?? '';

  $dataSelecionada = new DateTime($semanaSelecionada);

  $inicioSemana = clone $dataSelecionada;
  $inicioSemana->modify('Monday this week');

  $fimSemana = clone $inicioSemana;
  $fimSemana->modify('Sunday this week');

  $query = "SELECT c.*, cat.titulo AS categoria_titulo, cat.id AS categoria_id
            FROM conferencia c
            LEFT JOIN categoria_has_conferencia chc ON c.id = chc.conferencia_id
            LEFT JOIN categoria cat ON chc.categoria_id = cat.id
            WHERE c.data BETWEEN ? AND ?";

  if (!empty($categoriaSelecionada)) {
      $query .= " AND cat.id = ?";
  }
  $query .= " ORDER BY c.data ASC";

  $stmt = mysqli_prepare($connectionDB, $query);
  if (!empty($categoriaSelecionada)) {
      mysqli_stmt_bind_param($stmt, "sss", $inicioSemanaFormatted, $fimSemanaFormatted, $categoriaSelecionada);
  } else {
      mysqli_stmt_bind_param($stmt, "ss", $inicioSemanaFormatted, $fimSemanaFormatted);
  }
  $inicioSemanaFormatted = $inicioSemana->format('Y-m-d');
  $fimSemanaFormatted = $fimSemana->format('Y-m-d');
  
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $conferencias = [];
  while ($row = mysqli_fetch_assoc($result)) {
      $conferencias[$row['id']]['info'] = $row;
      if (!empty($row['categoria_titulo'])) {
          $conferencias[$row['id']]['categorias'][] = $row['categoria_titulo'];
      }
  }

  mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/conferencias.css">
  <script src="https://kit.fontawesome.com/15df1461d5.js" crossorigin="anonymous"></script>
  <title>Inovatech | Conferências</title>
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
    <h1>Conferências</h1>
    <form method="GET" action="" id="filtroForm">
      <label for="semana">Selecione a semana:</label>
      <select name="semana" id="semana" onchange="filtrarAutomaticamente()">
        <?php foreach ($semanasDisponiveis as $semana): ?>
          <option value="<?php echo $semana['semana']; ?>" <?php echo ($semanaSelecionada == $semana['semana']) ? 'selected' : ''; ?>>
            <?php echo date('d/m/Y', strtotime($semana['semana'])); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <label for="categoria">Categoria:</label>
      <select name="categoria" id="categoria" onchange="filtrarAutomaticamente()">
        <option value="">Todas</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?php echo $cat['id']; ?>" <?php echo ($categoriaSelecionada == $cat['id']) ? 'selected' : ''; ?>>
            <?php echo $cat['titulo']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <section class="section-button-create">
      <a href="./criar_conferencia.php">
        <button class="botao-criar">Criar Conferência</button>
      </a>
    </section>
    <div class="conferencias-container">
      <?php if (!empty($conferencias)): ?>
        <?php foreach ($conferencias as $row): ?>
          <a id="conferencia-link" href="detalhes_conferencia.php?id=<?php echo $row['info']['id']; ?>">
            <div class="conferencia-card">
              <div class="conferencia-info">
                <span class="data"> <?php echo date('d-m-Y', strtotime($row['info']['data'])); ?> </span>
                <span class="hora"> <?php echo date('H:i', strtotime($row['info']['data'])); ?> </span>
              </div>
              <div class="conferencia-detalhes">
                <?php if (!empty($row['categorias'])): ?>
                  <p><?php echo implode(', ', $row['categorias']); ?></p>
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($row['info']['titulo']); ?></h2>
                <p><?php echo htmlspecialchars($row['info']['descricao']); ?></p>
                <div class="botoes-admin">
                  <a href="./editar_conferencia.php?id=<?php echo $row['info']['id']; ?>">
                    <button><i class="fa-solid fa-pencil"></i></button>
                  </a>
                  <a href="../../scripts/conferencias/excluirConferencia.php?id=<?php echo $row['info']['id']; ?>" onclick="return confirmarExclusao();">
                    <button><i class="fa-solid fa-trash"></i></button>
                  </a>
                </div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="sem-conferencias">Nenhuma conferência encontrada para esta semana.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
<script>
  function filtrarAutomaticamente() {
    document.getElementById('filtroForm').submit();
  }

  function confirmarExclusao() {
    return confirm("Tem certeza que deseja excluir esta conferência?");
  }
</script>
</html>