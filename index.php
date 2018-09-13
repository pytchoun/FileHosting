<?php
include 'assets/include/head.php';
?>
<body>
  <section class="h-75 d-flex align-items-center" id="landing-page">
    <?php
    if (isset($_SESSION["accountDeleted"])) { ?>
      <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
        Votre compte est supprimé.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php
    }
    ?>
    <div class="container">
      <div class="row">
        <div class="col-md-12 mb-5">
          <h1 class="text-center">L'hébergement de qualité</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <img src="assets/image/illustration/speed.svg" alt="Activation instantanée de votre hébergement" height="80" class="mx-auto d-block">
          <h5 class="text-center" style="padding-top:20px;">Activation instantanée</h5>
          <p class="text-justify">
            Dès votre inscription terminée, automatiquement, vos identifiants sont valides. Ainsi, vous pourrez profiter de votre abonnement sans tarder.
          </p>
        </div>
        <div class="col-md-4">
          <img src="assets/image/illustration/money.svg" alt="14 jours garantie ou argent remis de votre hébergement" height="80" class="mx-auto d-block">
          <h5 class="text-center" style="padding-top:20px;">14 jours garantie ou argent remis</h5>
          <p class="text-justify">
            Possibilité d'obtenir un remboursement complet durant les 7 jours suivant l'achat de votre abonnement.
          </p>
        </div>
        <div class="col-md-4">
          <img src="assets/image/illustration/save.svg" alt="Sauvegardes aux 12 heures de votre hébergement" height="80" class="mx-auto d-block">
          <h5 class="text-center" style="padding-top:20px;">Sauvegardes aux 12 heures</h5>
          <p class="text-justify">
            Vos données sont sauvegardées aux 12 heures dans un second datacenter et ce, de manière sécuritaire et conforme avec la règlementation générale sur la protection des données.
          </p>
        </div>
      </div>
    </div>
  </section>
  <section class="bg-info" style="color:white;">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <img src="assets/image/illustration/host_your_file.png" alt="Activation instantanée de votre hébergement" height="300">
        </div>
        <div class="col-md-6 align-self-center text-center">
          <h5>Qu'est que c'est ?</h5>
          <p>
            FileHosting c'est votre espace de stockage dans le cloud. Hébergez vos fichiers et accédez y ou que vous soyez.
            Nous vous offrons une large capacité de stockage et notre hébergement est de haute disponibilité.
          </p>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5" style="background-color: #2ec3bd; color: white;">
    <div class="container">
      <div class="row">
        <div class="col-md-4 text-center">
          <i class="fas fa-cloud fa-2x"></i>
          <h5 style="padding-top:20px;">Un véritable espace</h5>
          <p>
            Optez pour un espace de stockage jusqu'à 1 TO.
          </p>
        </div>
        <div class="col-md-4 text-center">
          <i class="fas fa-upload fa-2x"></i>
          <h5 style="padding-top:20px;">Hébergez vos fichiers</h5>
          <p>
            Hébergez vos fichiers et partagez les avec qui vous voulez.
          </p>
        </div>
        <div class="col-md-4 text-center">
          <i class="fas fa-share-alt fa-2x"></i>
          <h5 style="padding-top:20px;">Pargez vos fichiers</h5>
          <p>
            Une fois vos fichiers hébergés vous pouvez les partagez avec qui vous voulez.
          </p>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5">
    <div class="container">
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
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section style="padding-top: 150px; padding-bottom: 150px;  background-color: white;" class="shadow-lg">
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2 text-center">
          <h3>Vivez l'expérience de l'hébergement cloud dès maintenant !</h3>
        </div>
        <div class="col-md-8 offset-md-2 text-center">
          <p class="text-muted">
            <?php if (isConnected()) { ?>
              Accéder à votre compte et profitez directement de nos services.
            <?php } else { ?>
              Inscrivez-vous et profitez directement de nos services.
            <?php } ?>
          </p>
          <p>
            <?php if (isConnected()) { ?>
              <a class="btn btn-green btn-lg" href="/FileHosting/account/index.php" style="border-radius: 999px; color: white;">Mon compte</a>
            <?php } else { ?>
              <a class="btn btn-green btn-lg" href="register.php" style="border-radius: 999px; color: white;">Inscrivez-vous</a>
            <?php } ?>
          </p>
        </div>
      </div>
    </div>
  </section>
  <?php
  if (isset($_SESSION["accountDeleted"])) {
    unset($_SESSION["accountDeleted"]);
  }
  include 'assets/include/footer.php';
  ?>
</body>
