<?php
session_start();
require "functions.php";
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>FileHosting</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/FileHosting/assets/css/stylesheet.css">
  </head>
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color:#19709e;">
      <div class="container">
        <a class="navbar-brand" href="/FileHosting/index.php" style="color:white;">
          <img src="/FileHosting/assets/image/logo/FileHosting.png" width="30" height="30" alt="Logo FileHosting"> FileHosting
        </a>
        <button style="background-color:#5A408F;" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarToggler">
          <ul class="navbar-nav">
            <?php if (isConnected()) { ?>
              <li class="nav-item">
                <a class="nav-link" href="/FileHosting/account/index.php" style="color:white;">
                  <img class="align-middle" src="/FileHosting/assets/image/illustration/account.svg" alt="Accéder à mon profil" height="24"> Bienvenue <span style="color:#FFAE38;"><?php echo $_SESSION["account"]["name"]; ?></span> !
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/FileHosting/account/logout.php" style="color:white;">
                  <i class="fas fa-sign-out-alt text-warning align-middle" style="font-size: 20px;"></i> Déconnexion
                </a>
              </li>
            <?php } else { ?>
              <li class="nav-item">
                <a class="nav-link" href="/FileHosting/login.php" style="color:white;">
                  <img class="align-middle" src="/FileHosting/assets/image/illustration/account.svg" alt="Me connecter sur FileHosting" height="24"> Connexion
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/FileHosting/register.php" style="color:white;">
                  <i class="fas fa-sign-in-alt text-warning align-middle" style="font-size: 20px;"></i> Inscription
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </nav>
  </header>
