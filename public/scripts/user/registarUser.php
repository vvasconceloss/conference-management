<?php
include("../../../config/databaseConnection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['nome'], $_POST['email'], $_POST['password'], $_POST['confirmar'])) {
        die("Todos os campos devem ser preenchidos.");
    }

    $nomeParticipante = mysqli_real_escape_string($connectionDB, trim($_POST['nome']));
    $emailParticipante = mysqli_real_escape_string($connectionDB, trim($_POST['email']));
    $password = $_POST['password'];
    $confirmarPassword = $_POST['confirmar'];

    if (empty($password) || empty($confirmarPassword)) {
        die("As senhas não podem estar vazias.");
    }

    if ($password !== $confirmarPassword) {
        die("As senhas não coincidem. Tente novamente.");
    }

    $sqlCheckUser = "SELECT id FROM utilizador WHERE email = ?";
    
    if ($stmt = mysqli_prepare($connectionDB, $sqlCheckUser)) {
        mysqli_stmt_bind_param($stmt, "s", $emailParticipante);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            header("Location: ../../index.php");
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Erro ao verificar o e-mail.");
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $sqlQuery = "INSERT INTO utilizador (nome, email, pass) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($connectionDB, $sqlQuery)) {
        mysqli_stmt_bind_param($stmt, "sss", $nomeParticipante, $emailParticipante, $passwordHash);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../../pages/login.php");
            exit();
        } else {
            echo "Erro ao efetuar o registo do utilizador: " . mysqli_error($connectionDB);
        }
        mysqli_stmt_close($stmt);
    } else {
        die("Erro ao preparar a consulta.");
    }

    mysqli_close($connectionDB);
}
?>
