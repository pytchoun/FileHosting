<?php
include 'assets/include/head.php';
if (isset($_SESSION["account"]["email"])) {
  header('Location: /FileHosting/account/index.php');
}
if (isset($_POST['sign-up'])) {
  registerUser();
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
          <div class="card">
            <div id="login-form">
              <h5 class="card-header text-center login-form-header">Inscription</h5>
              <div class="card-body login-form-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                  <div class="form-row">
                    <div class="form-group col">
                      <label for="signUpName">Prénom</label>
                      <input type="text" class="form-control" id="signUpName" name="name" value="<?php if(isset($_POST["name"])) echo $_POST["name"]; ?>" placeholder="Votre prénom" required>
                      <span class="text-danger"><?php if(isset($_SESSION["errors"]["name"])) echo $_SESSION["errors"]["name"]; ?></span>
                    </div>
                    <div class="form-group col">
                      <label for="signUpLastName">Nom</label>
                      <input type="text" class="form-control" id="signUpLastName" name="last-name" value="<?php if(isset($_POST["last-name"])) echo $_POST["last-name"]; ?>" placeholder="Votre nom" required>
                      <span class="text-danger"><?php if(isset($_SESSION["errors"]["last_name"])) echo $_SESSION["errors"]["last_name"]; ?></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="signUpEmail">Adresse email</label>
                    <input type="email" class="form-control" id="signUpEmail" name="email" value="<?php if(isset($_POST["email"])) echo $_POST["email"]; ?>" placeholder="Votre adresse email" required>
                    <span class="text-danger"><?php if(isset($_SESSION["errors"]["email"])) echo $_SESSION["errors"]["email"]; ?></span>
                  </div>
                  <div class="form-row">
                    <div class="form-group col">
                      <label for="signUpPassword">Mot de passe</label>
                      <input type="password" class="form-control" id="signUpPassword" name="password" placeholder="Votre mot de passe" required minlength="8" maxlength="20">
                      <span class="text-danger"><?php if(isset($_SESSION["errors"]["password"])) echo $_SESSION["errors"]["password"]; ?></span>
                    </div>
                    <div class="form-group col">
                      <label for="signUpConfirmPassword">Confirmation du mot de passe</label>
                      <input type="password" class="form-control" id="signUpConfirmPassword" name="confirm-password" placeholder="Confirmez votre mot de passe" required minlength="8" maxlength="20">
                      <span class="text-danger"><?php if(isset($_SESSION["errors"]["confirm_password"])) echo $_SESSION["errors"]["confirm_password"]; ?></span>
                    </div>
                  </div>
                  <small class="form-text text-muted pb-3" id="passwordHelpBlock">
                    Votre mot de passe doit comporter entre 8 et 20 caractères, contenir des lettres et des chiffres et ne doit pas contenir d'espaces, de caractères spéciaux ou d'emoji.
                  </small>
                  <div class="row">
                    <div class="col-md-6">
                      <p>
                        <button type="submit" class="btn btn-orange btn-block" name="sign-up">S'inscrire</button>
                      </p>
                    </div>
                    <div class="col-md-6">
                      <p>
                        <a class="btn btn-purple btn-block" href="login.php" style="color: white;">Se connecter</a>
                        <small id="loginHelp" class="form-text text-muted text-center">Déjà un compte ? Cliquez ici.</small>
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
  include 'assets/include/footer.php';
  ?>
</body>
