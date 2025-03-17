<?php
    session_start();

    include("../../../config/databaseConnection.php");

    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
    $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);

    $userLogin = $_SESSION['user_login'] ?? '';

    if (!$isLoggedIn) {
        header("Location: ../login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email)) {
            echo "<script>alert('O nome e o e-mail são obrigatórios!');</script>";
            exit();
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                echo "<script>alert('A senha deve ter pelo menos 6 caracteres.');</script>";
                exit();
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $userLogin = mysqli_real_escape_string($connectionDB, $userLogin);
        $name = mysqli_real_escape_string($connectionDB, $name);
        $email = mysqli_real_escape_string($connectionDB, $email);

        $query = "UPDATE utilizador SET nome = ?, email = ?, pass = ? WHERE email = ?";

        if ($stmt = mysqli_prepare($connectionDB, $query)) {
            if (!empty($password)) {
                mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashedPassword, $userLogin);
            } else {
                $queryNoPassword = "UPDATE utilizador SET nome = ?, email = ? WHERE email = ?";
                if ($stmt = mysqli_prepare($connectionDB, $queryNoPassword)) {
                    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $userLogin);
                }
            }

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['user_name'] = $name; 
                $_SESSION['user_login'] = $email;

                echo "<script>alert('Perfil atualizado com sucesso!');</script>";
                header("Location: ../../index.php");
                exit();
            } else {
                echo "<script>alert('Erro ao atualizar o perfil: " . mysqli_error($connectionDB) . "');</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Erro ao preparar a consulta: " . mysqli_error($connectionDB) . "');</script>";
        }
    }

    mysqli_close($connectionDB);
?>
