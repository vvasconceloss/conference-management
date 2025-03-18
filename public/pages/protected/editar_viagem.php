<?php
  session_start();
  include("../../../config/databaseConnection.php");

  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  if (!$isAdmin) {
      die("Acesso negado.");
  }

  if (!isset($_GET['id'])) {
      die("ID da viagem não especificado.");
  }

  $viagemId = intval($_GET['id']);

  $queryViagem = "
      SELECT viagem.id AS viagem_id, viagem.origem, viagem.data_partida, viagem.data_chegada, 
            utilizador.id AS utilizador_id, utilizador.nome AS utilizador_nome, 
            hospedagem.id AS hospedagem_id, hospedagem.nome AS hospedagem_nome
      FROM viagem
      LEFT JOIN utilizador ON viagem.utilizador_id = utilizador.id
      LEFT JOIN hospedagem ON utilizador.hospedagem_id = hospedagem.id
      WHERE viagem.id = $viagemId
  ";

  $resultViagem = mysqli_query($connectionDB, $queryViagem);

  if (!$resultViagem || mysqli_num_rows($resultViagem) === 0) {
      die("Viagem não encontrada.");
  }

  $viagem = mysqli_fetch_assoc($resultViagem);

  $queryHospedagens = "SELECT id, nome FROM hospedagem WHERE hospedagem.id != 4";
  $resultHospedagens = mysqli_query($connectionDB, $queryHospedagens);
  $hospedagens = mysqli_fetch_all($resultHospedagens, MYSQLI_ASSOC);

  $paisesUE = [
      "Áustria", "Bélgica", "Bulgária", "Croácia", "Chipre", "República Checa", "Dinamarca", 
      "Estónia", "Finlândia", "França", "Alemanha", "Grécia", "Hungria", "Irlanda", 
      "Itália", "Letónia", "Lituânia", "Luxemburgo", "Malta", "Países Baixos", "Polónia", 
      "Portugal", "Roménia", "Eslováquia", "Eslovénia", "Espanha", "Suécia", "Ucrânia"
  ];

  $contribuidores = ["Empresa A", "Empresa B", "Empresa C", "Outro"];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $origem = mysqli_real_escape_string($connectionDB, $_POST['origem']);
      $dataPartida = mysqli_real_escape_string($connectionDB, $_POST['data_partida']);
      $dataChegada = mysqli_real_escape_string($connectionDB, $_POST['data_chegada']);
      $hospedagemId = intval($_POST['hospedagem_id']);
      $contribuidor = mysqli_real_escape_string($connectionDB, $_POST['contribuidor']);
      $dataDeslocamento = mysqli_real_escape_string($connectionDB, $_POST['data_deslocamento']);

      $queryUpdate = "
          UPDATE viagem
          SET origem = '$origem', 
              data_partida = '$dataPartida', 
              data_chegada = '$dataChegada'
          WHERE id = $viagemId
      ";

      if (mysqli_query($connectionDB, $queryUpdate)) {
          $queryUpdateHospedagem = "
              UPDATE utilizador
              SET hospedagem_id = $hospedagemId
              WHERE id = {$viagem['utilizador_id']}
          ";
          mysqli_query($connectionDB, $queryUpdateHospedagem);

          header("Location: ./gerir_viagens.php");
          exit();
      } else {
          $erro = "Erro ao atualizar a viagem: " . mysqli_error($connectionDB);
      }
  }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/css/editar_viagem.css">
    <script src="https://kit.fontawesome.com/15df1461d5.js" crossorigin="anonymous"></script>
    <title>Editar Viagem | Inovatech</title>
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
                <?php if (isset($_SESSION['user_login'])): ?>
                    <div class="profile-dropdown">
                        <button class="profile-button">
                            <span class="profile-initial"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></span>
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
        <section>
            <h1>Editar Viagem</h1>
            <?php if (isset($erro)): ?>
                <div class="erro"><?php echo $erro; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="utilizador_nome">Utilizador:</label>
                    <input type="text" id="utilizador_nome" value="<?php echo htmlspecialchars($viagem['utilizador_nome']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="origem">Origem:</label>
                    <select id="origem" name="origem" required>
                        <?php foreach ($paisesUE as $pais): ?>
                            <option value="<?php echo htmlspecialchars($pais); ?>" <?php echo ($pais === $viagem['origem']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pais); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_partida">Data de Partida:</label>
                    <input type="datetime-local" id="data_partida" name="data_partida" value="<?php echo date('Y-m-d\TH:i', strtotime($viagem['data_partida'])); ?>" required>
                </div>
                <div class="form-group">
                    <label for="data_chegada">Data de Chegada:</label>
                    <input type="datetime-local" id="data_chegada" name="data_chegada" value="<?php echo date('Y-m-d\TH:i', strtotime($viagem['data_chegada'])); ?>" required>
                </div>
                <div class="form-group">
                    <label for="hospedagem_id">Hospedagem:</label>
                    <select id="hospedagem_id" name="hospedagem_id" required>
                        <?php foreach ($hospedagens as $hospedagem): ?>
                            <option value="<?php echo $hospedagem['id']; ?>" <?php echo ($hospedagem['id'] == $viagem['hospedagem_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($hospedagem['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hospedagem_id">Hospedagem:</label>
                    <select id="hospedagem_id" name="hospedagem_id" required>
                        <?php foreach ($hospedagens as $hospedagem): ?>
                            <option value="<?php echo $hospedagem['id']; ?>" <?php echo (isset($hospedagemId) && $hospedagemId == $hospedagem['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($hospedagem['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contribuidor">Contribuidor:</label>
                    <select id="contribuidor" name="contribuidor" required>
                        <?php foreach ($contribuidores as $contribuidor): ?>
                            <option value="<?php echo htmlspecialchars($contribuidor); ?>" <?php echo (isset($contribuidor) && $contribuidor === $contribuidor) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($contribuidor); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_deslocamento">Data do Deslocamento:</label>
                    <input type="datetime-local" id="data_deslocamento" name="data_deslocamento" value="<?php echo isset($dataDeslocamento) ? date('Y-m-d\TH:i', strtotime($dataDeslocamento)) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                    <a href="./gerir_viagens.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>