<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
if (isset($_POST['edit-account'])) {
  editUser();
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
            <h5 class="card-header card-header-account text-center">Modifer mes informations personnelles</h5>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="card-body card-body-account">
                <div class="form-group">
                  <label for="editLastName">Nom</label>
                  <input type="text" class="form-control" id="editLastName" name="last-name" placeholder="<?php echo $_SESSION["account"]["last_name"]; ?>">
                  <span class="text-danger"><?php if(isset($_SESSION["errors"]["last-name"])) echo $_SESSION["errors"]["last-name"]; ?></span>
                </div>
                <div class="form-group">
                  <label for="editName">Prénom</label>
                  <input type="text" class="form-control" id="editName" name="name" placeholder="<?php echo $_SESSION["account"]["name"]; ?>">
                  <span class="text-danger"><?php if(isset($_SESSION["errors"]["name"])) echo $_SESSION["errors"]["name"]; ?></span>
                </div>
                <div class="form-group">
                  <label for="editEmail">Adresse email</label>
                  <input type="email" class="form-control" id="editEmail" name="email" placeholder="<?php echo $_SESSION["account"]["email"]; ?>">
                  <span class="text-danger"><?php if(isset($_SESSION["errors"]["email"])) echo $_SESSION["errors"]["email"]; ?></span>
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
