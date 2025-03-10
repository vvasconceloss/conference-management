<?php
  include("../../../config/databaseConnection.php");

  if (isset($_SESSION['user_login'])) {
    header("Location: ../../pages/protected/conferencias.php");
    exit();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email'], $_POST['password'])) {
        die("Todos os campos devem ser preenchidos.");
    }

    $email = mysqli_real_escape_string($connectionDB, trim($_POST['email']));
    $password = $_POST['password'];

    $query = "SELECT id, email, pass FROM utilizadores WHERE email = '$email'";
    $resultado = mysqli_query($connectionDB, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $utilizador = mysqli_fetch_assoc($resultado);
        
        if (password_verify($password, $utilizador['pass'])) {
            $_SESSION['user_id'] = $utilizador['id'];
            $_SESSION['user_login'] = $utilizador['email'];

            setcookie("user_login", $utilizador['email'], time() + (86400 * 30), "/");

            header("Location: ../../index.html");
            exit();
        } else {
            echo "<script>alert('E-mail ou senha incorretos!'); window.location.href='../../entrar.html';</script>";
        }
    } else {
        echo "<script>alert('Utilizador não existe!'); window.location.href='../../entrar.html';</script>";
    }

    mysqli_close($connectionDB);
  }
?>