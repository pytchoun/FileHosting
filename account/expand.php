<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
$db = connectDb();
// Obtenir la taille du stockage actuelle et maximum de l'utilisateur
$query = $db->prepare("SELECT current_storage_size, max_storage_size FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$storageSize = $query->fetch();
$currentStorageSize = human_filesize($storageSize[0]);
$maxStorageSize = human_filesize($storageSize[1]);
if ($storageSize[1] != 0) {
  $progressBarValue = ($storageSize[0] / $storageSize[1]) * 100;
} else {
  $progressBarValue = 0;
}

// Obtenir la date de fin de validité de l'abonnement de l'utilisateur
$query = $db->prepare("SELECT end_subscription FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$status = $query->fetch();

$timeNow = date('Y-m-d h:i:s');
if ($status[0] < $timeNow) {
  $obsoleteSubscription = true;
} else {
  $obsoleteSubscription = false;
}
?>
<body>
  <section>
    <div class="container">
      <div class="row my-5">
        <div class="col-md-12">
          <h1 class="text-center">Espace de stockage FileHosting</h1>
          <?php
          if ($_SESSION["account"]["subscription"] != "Premium" || $obsoleteSubscription == true) {
            ?>
            <div class="alert alert-info text-center" role="alert">
              Sans abonnement Premium actif vous ne pouvez pas faire évoluer votre limite de stockage.
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <p>Stockage total</p>
          <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width:<?php echo $progressBarValue."%"; ?>;" role="progressbar" aria-valuenow="<?php echo $storageSize[0]; ?>" aria-valuemin="0" aria-valuemax="<?php echo $storageSize[1]; ?>"></div>
          </div>
          <p><?php echo $currentStorageSize; ?> utilisés</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 offset-md-4">
          <div class="card border border-account text-center">
            <div class="card-body card-body-account">
              <h5 class="card-title"><?php echo $maxStorageSize; ?></h5>
              <p class="card-text">Forfait actuel</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-5 mb-2">
        <div class="col-md-12">
          <h5 class="text-center">Solution de stockage supplémentaire</h5>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="card-deck text-center">
            <div class="card border border-account">
              <div class="card-body card-header-account">
                <h5 class="card-title">Package 1 : 25 GB</h5>
                <p>
                  <a class="btn btn-sm btn-purple btn-block" href="package1.php" style="margin-top:10px; color:white;">7€/mois TTC</a>
                </p>
              </div>
            </div>
            <div class="card border border-account">
              <div class="card-body card-header-account">
                <h5 class="card-title">Package 2 : 50 GB</h5>
                <p>
                  <a class="btn btn-sm btn-purple btn-block" href="package2.php" style="margin-top:10px; color:white;">12€/mois TTC</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-md-2">
          <p>
            <a class="btn btn-outline-cyan btn-block" href="../account/index.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
          </p>
        </div>
      </div>
    </div>
  </section>
  <?php
  include '../assets/include/footer.php';
  ?>
</body>
