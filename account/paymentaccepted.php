<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
?>
<body>
  <section class="d-flex align-items-center h-75">
    <div class="container">
      <div class="row mb-5">
        <div class="col-md-12">
          <h1 class="text-center">Paiement accepté</h1>
        </div>
      </div>
      <div class="row mb-5">
        <div class="col-md-12 text-center">
          <i class="fas fa-thumbs-up text-info fa-10x"></i>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <p class="text-center">Votre paiement a été enregistré.</p>
          <p class="text-center text-muted">Merci de votre confiance.</p>
          <p class="text-center">
            <a class="btn btn-green btn-lg" href="../account/index.php" style="margin-top:10px; color:white;"><img src="/FileHosting/assets/image/illustration/account.svg" alt="Mon compte FileHosting" height="24"> Mon compte</a>
          </p>
        </div>
      </div>
    </div>
  </section>
  <?php
  include '../assets/include/footer.php';
  ?>
</body>
