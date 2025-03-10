<!DOCTYPE html>
<html lang="pt-PT">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/css/register.css">
    <title>Inovatech | Criar Conta</title>
  </head>
  <body>
    <header class="header">
      <div class="header-logo">
        <img src="../images/inovatech_logo.png" alt="Inovatech Logo" class="logo-image">
      </div>
      <nav class="header-nav">
        <div class="header-nav-links">
          <a href="../index.php" class="nav-link">Início</a>
          <a href="../pages/protected/conferencias.php" class="nav-link">Conferências</a>
        </div>
        <div class="header-nav-buttons">
          <a href="./login.php">
            <button id="signin">Iniciar Sessão</button>
          </a>
          <a href="./register.php">
            <button id="signup">Criar Conta</button>
          </a>
        </div>
      </nav>  
    </header>
    <main class="main">
      <section class="main-section-register">
        <h2 class="section-register-title">Bem-vindo(a) a plataforma! Crie sua conta</h2>
        <form class="form-register" method="post" action="../scripts/user/registarUser.php">
          <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" viewBox="0 0 16 16">
              <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
            </svg>
            <input autocomplete="off" placeholder="Nome" name="nome" class="input-field" type="text">
          </div>
          <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
            </svg>
            <input placeholder="Email" name="email" class="input-field" type="text">
          </div>
          <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
            </svg>
            <input placeholder="Password" name="password" class="input-field" type="password">
          </div>
          <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
            </svg>
            <input placeholder="Confirmar Password" name="confirmar" class="input-field" type="password">
          </div>
          <button class="button-register" type="submit">Criar Conta</button>
        </form>
        <h3 class="login-text">Já tem uma conta? <a href="./login.html">Inicie sua sessão</a></h3>
      </section>
    </main>
  </body>
</html>