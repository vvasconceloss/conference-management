<?php
    session_start();
    include("../../../config/databaseConnection.php");

    $isEstrangeiro = isset($_POST['estrangeiro']) ? 1 : 0;
    $hospedagemId = $isEstrangeiro ? 1 : 4;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $isEstrangeiro = $_POST['isEstrangeiro'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sqlQuery = "INSERT INTO utilizador (nome, email, pass, isEstrangeiro, hospedagem_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connectionDB, $sqlQuery);

        mysqli_stmt_bind_param($stmt, 'sssii', $nome, $email, $password, $isEstrangeiro, $hospedagemId);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../../pages/protected/gerir_utilizadores.php?success=1");
        } else {
            header("Location: ../../pages/protected/gerir_utilizadores.php?error=1");
        }
        mysqli_stmt_close($stmt);
    }
?>