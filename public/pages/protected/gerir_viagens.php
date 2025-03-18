<?php
  session_start();
  include("../../../config/databaseConnection.php");

  $userLogin = $_SESSION['user_login'] ?? '';
  $userName = $_SESSION['user_name'] ?? 'username';
  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

  if (!$isAdmin) {
      die("Acesso negado.");
  }

  $queryViagens = "
  SELECT utilizador.id AS utilizador_id, utilizador.nome AS utilizador_nome, 
         viagem.id AS viagem_id, viagem.origem, viagem.data_chegada, viagem.data_partida,
         hospedagem.nome AS hospedagem_nome
  FROM utilizador
  LEFT JOIN viagem ON utilizador.id = viagem.utilizador_id
  LEFT JOIN hospedagem ON utilizador.hospedagem_id = hospedagem.id
  ORDER BY utilizador.nome ASC
  ";

  $resultViagem = mysqli_query($connectionDB, $queryViagens);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/viagens.css">
  <script src="https://kit.fontawesome.com/15df1461d5.js" crossorigin="anonymous"></script>
  <title>Inovatech | Gestão de Viagens</title>
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
  <section>
    <h1>Viagens</h1>
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Origem</th>
          <th>Data de Chegada</th>
          <th>Data de Partida</th>
          <th>Hospedagem</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($resultViagem)): ?>
          <?php
            $viagemDefinida = !empty($row['viagem_id']);
            $classeLinha = $viagemDefinida ? '' : 'sem-viagem';
            $hospedagem = $viagemDefinida ? htmlspecialchars($row['hospedagem_nome']) : '-----';
          ?>
          <tr class="<?php echo $classeLinha; ?>">
            <td><?php echo htmlspecialchars($row['utilizador_nome']); ?></td>
            <td><?php echo $viagemDefinida ? htmlspecialchars($row['origem']) : '-----'; ?></td>
            <td><?php echo $viagemDefinida ? date('d-m-Y H:i', strtotime($row['data_chegada'])) : '-----'; ?></td>
            <td><?php echo $viagemDefinida ? date('d-m-Y H:i', strtotime($row['data_partida'])) : '-----'; ?></td>
            <td><?php echo $hospedagem; ?></td>
            <td>
              <?php if ($viagemDefinida): ?>
                <a href="editar_viagem.php?id=<?php echo $row['viagem_id']; ?>">
                  <i class="fa-solid fa-pen"></i>
                </a>
                <a href="remover_viagem.php?id=<?php echo $row['viagem_id']; ?>" onclick="return confirm('Tem certeza que deseja remover esta viagem?');">
                  <i class="fa-solid fa-trash"></i>
                </a>
              <?php else: ?>
                <span class="sem-viagem-acoes">
                  <a href="editar_viagem.php?id=<?php echo $row['utilizador_id']; ?>">
                    <i class="fa-solid fa-plane-departure"></i>
                  </a>
                </span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </section>
  </main>
</body>
</html>