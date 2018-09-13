<?php
include 'assets/include/head.php';
if (isset($_SESSION["account"]["email"])) {
  header('Location: /FileHosting/account/index.php');
}
if (isset($_POST['sign-in'])) {
  loginUser();
}
?>
<body>
  <section class="d-flex align-items-center h-100">
    <div class="container">
      <div class="row mb-5">
        <div class="col-md-12">
          <h1 class="text-center">Accéder à mon espace FileHosting</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <?php
          if (isset($_SESSION["accountCreated"])) {
          ?>
          <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            Votre compte est créé, vous pouvez vous connecter.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php
          }
          if (isset($_SESSION["errors"]["login"])) {
          ?>
          <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
            Compte introuvable.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php
          }
          ?>
          <div class="card">
            <div id="login-form">
              <h5 class="card-header text-center login-form-header">Connexion</h5>
              <div class="card-body login-form-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                  <div class="form-group">
                    <label for="signInEmail">Adresse email</label>
                    <input type="email" class="form-control" id="signInEmail" name="email" placeholder="Votre adresse email" required>
                  </div>
                  <div class="form-group">
                    <label for="signInPassword">Mot de passe</label>
                    <input type="password" class="form-control" id="signInPassword" name="password" placeholder="Votre mot de passe" required minlength="8" maxlength="20">
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <p>
                        <button type="submit" class="btn btn-purple btn-block" name="sign-in">Se connecter</button>
                      </p>
                    </div>
                    <div class="col-md-6">
                      <p>
                        <a class="btn btn-orange btn-block" href="register.php" style="color: white;">S'inscrire</a>
                        <small id="loginHelp" class="form-text text-muted text-center">Pas de compte ? Cliquez ici.</small>
                      </p>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php
  if (isset($_SESSION["errors"])) {
    unset($_SESSION["errors"]);
  }
  if (isset($_SESSION["accountCreated"])) {
    unset($_SESSION["accountCreated"]);
  }
  include 'assets/include/footer.php';
  ?>
</body>
