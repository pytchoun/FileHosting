<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
if (isset($_POST['edit-account'])) {
  editUserPassword();
}
?>
<body>
  <section class="d-flex align-items-center h-100">
    <div class="container">
      <div class="row mb-5">
        <div class="col-md-12">
          <h1 class="text-center">Gestion de mon profil</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <div class="card border border-account">
            <h5 class="card-header card-header-account text-center">Modifer le mot de passe</h5>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="card-body card-body-account">
                <div class="form-group">
                  <label for="oldPassword">Ancien mot de passe</label>
                  <input type="password" class="form-control" id="oldPassword" name="old-password" placeholder="Votre ancien mot de passe" required minlength="8" maxlength="20">
                  <span class="text-danger"><?php if(isset($_SESSION["errors"]["old_password"])) echo $_SESSION["errors"]["old_password"]; ?></span>
                </div>
                <div class="form-group">
                  <label for="editPassword">Nouveau mot de passe</label>
                  <input type="password" class="form-control" id="editPassword" name="new-password" placeholder="Votre nouveau mot de passe" required minlength="8" maxlength="20">
                  <span class="text-danger"><?php if(isset($_SESSION["errors"]["new_password"])) echo $_SESSION["errors"]["new_password"]; ?></span>
                </div>
                <div class="form-group">
                  <label for="editConfirmPassword">Confirmer nouveau mot de passe</label>
                  <input type="password" class="form-control" id="editConfirmPassword" name="confirm-new-password" placeholder="Confirmez votre nouveau mot de passe" required minlength="8" maxlength="20">
                  <span class="text-danger"><?php if(isset($_SESSION["errors"]["confirm_new_password"])) echo $_SESSION["errors"]["confirm_new_password"]; ?></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <p>
                  <a class="btn btn-outline-cyan btn-block" href="../account/index.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
                </p>
              </div>
              <div class="col-md-6">
                <p>
                  <button type="submit" class="btn btn-green btn-block" name="edit-account" style="margin-top:10px;"><i class="fas fa-check"></i> Modifier</button>
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <?php
  if (isset($_SESSION["errors"])) {
    unset($_SESSION["errors"]);
  }
  include '../assets/include/footer.php';
  ?>
</body>
