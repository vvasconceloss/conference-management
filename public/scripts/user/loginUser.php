<?php
    session_start();

    include("../../../config/databaseConnection.php");

    if (isset($_SESSION['user_login'])) {
        header("Location: ../../pages/protected/conferencias.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['email'], $_POST['password']) || empty($_POST['email']) || empty($_POST['password'])) {
            die("Todos os campos devem ser preenchidos.");
        }

        $email = mysqli_real_escape_string($connectionDB, trim($_POST['email']));
        $password = $_POST['password'];

        $query = "SELECT id, nome, email, pass, isAdmin, isParticipante, isEstrangeiro, pais, isOrador FROM utilizador WHERE email = ?";
        
        if ($stmt = mysqli_prepare($connectionDB, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $id, $nome, $emailBD, $passBD, $isAdmin, $isParticipante, $isEstrangeiro, $pais, $isOrador);
                mysqli_stmt_fetch($stmt);

                if (password_verify($password, $passBD)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $nome;
                    $_SESSION['user_login'] = $emailBD;
                    $_SESSION['isAdmin'] = ($isAdmin == 1);

                    setcookie("user_login", $emailBD, time() + (86400 * 30), "/");

                    header("Location: ../../index.php");
                    exit();
                } else {
                    echo "<script>alert('E-mail ou senha incorretos!'); window.location.href='../../entrar.html';</script>";
                }
            } else {
                echo "<script>alert('Utilizador não existe!'); window.location.href='../../entrar.html';</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            die("Erro na preparação da consulta.");
        }

        mysqli_close($connectionDB);
    }
?>