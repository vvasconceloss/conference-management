<?php
  session_start();
  
  include("../../../config/databaseConnection.php");

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';
  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  $queryUltimaConferencia = "SELECT data FROM conferencias ORDER BY data DESC LIMIT 1";
  $resultUltimaConferencia = mysqli_query($connectionDB, $queryUltimaConferencia);
  $ultimaConferencia = mysqli_fetch_assoc($resultUltimaConferencia);

  $semanaSelecionada = isset($_GET['semana']) ? $_GET['semana'] : ($ultimaConferencia ? $ultimaConferencia['data'] : date('Y-m-d'));
  $dataSelecionada = new DateTime($semanaSelecionada);

  $inicioSemana = clone $dataSelecionada;
  $inicioSemana->modify('Monday this week');

  $fimSemana = clone $inicioSemana;
  $fimSemana->modify('Sunday this week');

  $query = "SELECT * FROM conferencias ORDER BY data ASC";
  $result = mysqli_query($connectionDB, $query);

  $conferenciasDaSemana = [];
  $conferenciasPorSemana = [];

  while ($row = mysqli_fetch_assoc($result)) {
    $dataConferencia = new DateTime($row['data']);

    $inicioSemanaConferencia = clone $dataConferencia;
    $inicioSemanaConferencia->modify('Monday this week');
    $fimSemanaConferencia = clone $inicioSemanaConferencia;
    $fimSemanaConferencia->modify('Sunday this week');

    $intervaloSemana = $inicioSemanaConferencia->format('Y-m-d') . ' a ' . $fimSemanaConferencia->format('Y-m-d');
    $conferenciasPorSemana[$intervaloSemana][] = $row;

    if ($dataConferencia >= $inicioSemana && $dataConferencia <= $fimSemana) {
      $conferenciasDaSemana[] = $row;
    }
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/conferencias.css">
  <title>Inovatech | Conferencias</title>
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
    <h1>Conferências da Semana</h1>
    <div class="filtro-semana">
      <form method="GET" action="" id="filtroForm">
        <label for="semana">Selecione a semana:</label>
        <select name="semana" id="semana" onchange="filtrarAutomaticamente()">
          <?php
            foreach ($conferenciasPorSemana as $intervaloSemana => $conferencias) {
              list($inicioSemanaOption, $fimSemanaOption) = explode(' a ', $intervaloSemana);
              $selected = ($inicioSemanaOption == $inicioSemana->format('Y-m-d')) ? 'selected' : '';
              echo "<option value='$inicioSemanaOption' $selected>$intervaloSemana</option>";
            }
          ?>
        </select>
      </form>
    </div>
    <div class="conferencias-container">
      <?php if (!empty($conferenciasDaSemana)): ?>
        <?php foreach ($conferenciasDaSemana as $row): ?>
          <?php
            $dataHora = $row['data'];
            $dataHoraFim = $row['duracao'];
            $dataFormatada = date('d-m-y', strtotime($dataHora));
            $horaFormatada = date('H:i', strtotime($dataHora));

            $inicio = new DateTime($dataHora);
            $fim = new DateTime($dataHoraFim);
            $intervalo = $inicio->diff($fim);
            $duracaoFormatada = $intervalo->format('%hh%Imin');
          ?>
          <div class="conferencia-card">
            <div class="conferencia-info">
              <div class="conferencia-data">
                <span class="hora"><?php echo htmlspecialchars($horaFormatada); ?></span>
                <span class="data"><?php echo htmlspecialchars($dataFormatada); ?></span>
              </div>
              <div class="conferencia-duracao">
                <span><?php echo htmlspecialchars($duracaoFormatada); ?> de duração</span>
              </div>
            </div>
            <div class="conferencia-detalhes">
              <h2><?php echo htmlspecialchars($row['titulo']); ?></h2>
              <p><?php echo htmlspecialchars($row['descricao']); ?></p>
            </div>
          </div>
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
  </script>
</html>