<?php
    session_start();
    include("../../../config/databaseConnection.php");

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die("Erro: ID do utilizador não foi especificado.");
    }

    $userId = intval($_GET['id']);

    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
    $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

    if (!$isLoggedIn) {
        header("Location: ../login.php");
        exit();
    }

    $userLogin = $_SESSION['user_login'] ?? $_COOKIE['user_login'] ?? '';

    $stmt = mysqli_prepare($connectionDB, "SELECT id, nome FROM utilizador WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $userLogin);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $loggedUserId = $row['id'];
        $userNameLogged = $row['nome'];
    } else {
        die("Erro: Usuário não encontrado.");
    }
    mysqli_stmt_close($stmt);

    if (!$isAdmin && $loggedUserId !== $userId) {
        die("Erro: Você não tem permissão para editar este perfil.");
    }

    $stmt = mysqli_prepare($connectionDB, "SELECT nome, email, isEstrangeiro, isParticipante FROM utilizador WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $userName = $row['nome'];
        $userEmail = $row['email'];
        $estrangeiro = $row['isEstrangeiro'];
        $participante = $row['isParticipante'];
    } else {
        die("Erro: Usuário não encontrado.");
    }
    mysqli_stmt_close($stmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? $userName;
        $email = $_POST['email'] ?? $userEmail;
        $password = $_POST['password'] ?? '';
        $estrangeiro = isset($_POST['estrangeiro']) ? intval($_POST['estrangeiro']) : $estrangeiro;
        $participante = isset($_POST['participante']) ? intval($_POST['participante']) : $participante;

        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($connectionDB, "UPDATE utilizador SET senha = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $passwordHash, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        $stmt = mysqli_prepare($connectionDB, "UPDATE utilizador SET nome = ?, email = ?, isEstrangeiro = ?, isParticipante = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssiiii', $name, $email, $estrangeiro, $participante, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: gerir_utilizadores.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/css/profile.css">
    <title>Inovatech | Editar Perfil</title>
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
                    <span class="profile-initial"><?php echo strtoupper(substr($userNameLogged, 0, 1)); ?></span>
                </button>
                <div class="dropdown-content">
                    <a href="./profile.php?id=<?php echo $loggedUserId; ?>" class="dropdown-link">Perfil</a>
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
            <h2 class="info-title">Editar Perfil</h2>
            <form action="editar_utilizador.php?id=<?php echo $userId; ?>" method="post" class="info-form">
                <div class="form-group">
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" class="form-input">
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" class="form-input">
                </div>
                <div class="form-group">
                    <label for="password">Nova Senha:</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Deixe em branco para manter a senha atual">
                </div>
                <div class="form-group">
                    <label for="estrangeiro">Estrangeiro</label>
                    <select name="estrangeiro" id="estrangeiro" class="form-input">
                        <option value="1" <?php echo $estrangeiro ? 'selected' : ''; ?>>Sim</option>
                        <option value="0" <?php echo !$estrangeiro ? 'selected' : ''; ?>>Não</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="participante">Participante</label>
                    <select name="participante" id="participante" class="form-input">
                        <option value="1" <?php echo $participante ? 'selected' : ''; ?>>Sim</option>
                        <option value="0" <?php echo !$participante ? 'selected' : ''; ?>>Não</option>
                    </select>
                </div>
                <button type="submit" class="form-button">Atualizar Perfil</button>
            </form>
        </div>
    </section>
</main>
</body>
</html>