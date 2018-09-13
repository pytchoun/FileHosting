<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
$db = connectDb();
// Obtenir l'abonnement actuel de l'utilisateur
$query = $db->prepare("SELECT subscription FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$status = $query->fetch();
?>
<body>
  <section>
    <div class="container">
      <div class="row my-5">
        <div class="col-md-12">
          <h1 class="text-center">Gestion de mon abonnement</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
            <table class="table table-borderless table-filehosting table-striped-filehosting table-hover-filehosting text-center">
              <thead class="thead-filehosting">
                <tr>
                  <th></th>
                  <th>Starter</th>
                  <th>Premium</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td>
                  <td>Démarrage parfait</td>
                  <td>La puissance de l'hébergement cloud</td>
                </tr>
                <tr>
                  <td><b>Accès à votre espace de stockage</b></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                </tr>
                <tr>
                  <td><b>Hébergez vos fichiers</b></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                </tr>
                <tr>
                  <td><b>Partagez vos fichiers</b></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                </tr>
                <tr>
                  <td><b>Stockage évolutif</b></td>
                  <td><i class="fas fa-times-circle fa-2x text-muted"></i></td>
                  <td><i class="fas fa-check-circle fa-2x text-success"></i></td>
                </tr>
                <tr>
                  <td><b>Espace de stockage</b></td>
                  <td>15 GB</td>
                  <td>50 GB (jusqu'à 100 GB)</td>
                </tr>
                <tr>
                  <td><b>Taille limite par fichier</b></td>
                  <td>50 MB</td>
                  <td>100 MB</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td></td>
                  <td>10€<span class="text-muted">/mois TTC</span></td>
                  <td>20€<span class="text-muted">/mois TTC</span></td>
                </tr>
                <tr>
                  <td></td>
                  <td>
                    <?php
                    if ($status[0] == "Starter") { ?>
                      <p>
                        <a class="btn btn-lg btn-green" href="starterpack.php" style="margin-top:10px; color:white;"><i class="fas fa-check"></i> Renouveler</a>
                      </p>
                    <?php } else { ?>
                      <p>
                        <a class="btn btn-lg btn-orange" href="starterpack.php" style="margin-top:10px; color:white;"><i class="fas fa-check"></i> S'abonner</a>
                      </p>
                    <?php } ?>
                  </td>
                  <td>
                    <?php
                    if ($status[0] == "Premium") { ?>
                      <p>
                        <a class="btn btn-lg btn-green" href="premiumpack.php" style="margin-top:10px; color:white;"><i class="fas fa-check"></i> Renouveler</a>
                      </p>
                    <?php } else { ?>
                      <p>
                        <a class="btn btn-lg btn-orange" href="premiumpack.php" style="margin-top:10px; color:white;"><i class="fas fa-check"></i> S'abonner</a>
                      </p>
                    <?php } ?>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="row">
            <div class="col-md-2">
              <p>
                <a class="btn btn-outline-cyan btn-block" href="../account/index.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php
  include '../assets/include/footer.php';
  ?>
</body>
