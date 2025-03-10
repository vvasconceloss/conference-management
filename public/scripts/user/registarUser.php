<?php
    include("../../../config/databaseConnection.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['nome'], $_POST['email'],  $_POST['password'], $_POST['confirmar'])) {
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

    $sqlCheckUser = "SELECT id FROM utilizadores WHERE email = '$emailParticipante'";
    $result = mysqli_query($connectionDB, $sqlCheckUser);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../../index.html");
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $sqlQuery = "INSERT INTO utilizadores (nome, email, pass) VALUES ('$nomeParticipante', '$emailParticipante', '$passwordHash')";

    if (mysqli_query($connectionDB, $sqlQuery)) {
        header("Location: ../../pages/login.html");
        exit();
    } else {
        echo "Erro ao efetuar o registo do utilizador: " . mysqli_error($connectionDB);
    }

    mysqli_close($connectionDB);
    }
?>