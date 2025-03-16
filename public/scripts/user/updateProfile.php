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

      $userLogin = mysqli_real_escape_string($connectionDB, $userLogin);
      $name = mysqli_real_escape_string($connectionDB, $name);
      $email = mysqli_real_escape_string($connectionDB, $email);
      $password = mysqli_real_escape_string($connectionDB, $password);

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $query = "UPDATE utilizadores SET nome = '$name', email = '$email', pass = '$hashedPassword' WHERE email = '$userLogin'";
      if (mysqli_query($connectionDB, $query)) {
          $_SESSION['user_name'] = $name; 
          $_SESSION['user_login'] = $email;

          echo "<script>alert('Perfil atualizado com sucesso!');</script>";
          
          header("Location: ../../index.php");
          exit();
      } else {
          echo "<script>alert('Erro ao atualizar o perfil: " . mysqli_error($connectionDB) . "');</script>";
      }
  }

  $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
  $isLoggedIn = isset($_SESSION['user_login']) || isset($_COOKIE['user_login']);
  $userLogin = $_SESSION['user_login'] ?? '';
?>