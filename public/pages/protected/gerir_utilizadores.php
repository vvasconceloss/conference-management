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

  $query = "SELECT id, nome, email, isEstrangeiro FROM utilizador ORDER BY nome ASC";
  $result = mysqli_query($connectionDB, $query);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../styles/global.css">
  <link rel="stylesheet" href="../../styles/css/utilizadores.css">
  <title>Inovatech | Utilizadores</title>
  <script src="https://kit.fontawesome.com/15df1461d5.js" crossorigin="anonymous"></script>
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
    <h1><?php echo mysqli_num_rows($result); ?> Utilizadores</h1>
    <input type="text" id="search" placeholder="Pesquisar utilizador..." onkeyup="filtrarUtilizadores()">
    <table>
      <thead>
        <tr>
          <th hidden>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Estrangeiro</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody id="tabela-utilizadores">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td hidden><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nome']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo $row['isEstrangeiro'] ? 'Sim' : 'Não'; ?></td>
            <td>
              <button onclick="editarUtilizador(<?php echo $row['id']; ?>)"><i class="fa-solid fa-pen"></i></button>
              <button onclick="excluirUtilizador(<?php echo $row['id']; ?>)"><i class="fa-solid fa-trash"></i></button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
  <script>
    function filtrarUtilizadores() {
      let input = document.getElementById('search').value.toLowerCase();
      let rows = document.querySelectorAll('#tabela-utilizadores tr');
      rows.forEach(row => {
        let nome = row.cells[1].textContent.toLowerCase();
        let email = row.cells[2].textContent.toLowerCase();
        row.style.display = (nome.includes(input) || email.includes(input)) ? '' : 'none';
      });
    }
    function excluirUtilizador(id) {
      if (confirm('Tem certeza que deseja excluir este utilizador?')) {
        window.location.href = `../../scripts/user/excluirUtilizador.php?id=${id}`;
      }
    }
    function editarUtilizador(id) {
      window.location.href = `editar_utilizador.php?id=${id}`;
    }
  </script>
</body>
</html>