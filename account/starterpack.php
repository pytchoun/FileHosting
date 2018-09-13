<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
if (isset($_POST['pay-subscription'])) {
  paySubscription(2, 10, 16106127360, 52428800);
}
if (isset($_POST['pay-subscription-with-new-payment'])) {
  paySubscriptionWithNewPayment(2, 10, 16106127360, 52428800);
}
$db = connectDb();

// Demander l'affichage du numéro de la carte bancaire de l'utilisateur
$myCreditCardNumber = showMyCreditCardNumber();

$beginSubscription = date('Y-m-d h:i:s');
// Obtenir l'abonnement et la date de fin de validité de l'abonnement de l'utilisateur
$query = $db->prepare("SELECT end_subscription, subscription FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$status = $query->fetch();
?>
<body>
  <section>
    <div class="container">
      <div class="row my-5">
        <div class="col-md-12">
          <h1 class="text-center">Mon abonnement FileHosting</h1>
          <?php
          if (isset($_SESSION["creditCard"]["creditCardWrongNumberInput"]) OR isset($_SESSION["creditCard"]["creditCardWrongInput"])) {
            ?>
            <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
              <?php
              if (isset($_SESSION["creditCard"]["creditCardWrongNumberInput"])) {
                echo "Vous devez saisir entre 13 et 22 chiffre.";
              }
              if (isset($_SESSION["creditCard"]["creditCardWrongInput"])) {
                echo "Vous devez saisir des chiffres.";
              }
              ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          if (isset($_SESSION["creditCard"]["creditCardNoInput"])) {
            ?>
            <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
              Vous n'avez rien saisie.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          if (isset($_SESSION["creditCard"]["declinedPayment"])) {
            ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
              Le cryptogramme ne correspond pas avec le numéro de la carte bancaire.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <?php
      if ($status[0] > $beginSubscription AND $status[1] == "Starter") { ?>
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-info text-center" role="alert">
              FileHosting vous remercie de vouloir renouveler votre abonnement.
            </div>
          </div>
        </div>
      <?php } elseif ($status[0] > $beginSubscription) { ?>
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-warning text-center" role="alert">
              Changer votre abonnement avant la date d'expiration de celui-ci résultera en la perte du temps restant de ce dernier.
            </div>
          </div>
        </div>
      <?php } ?>
      <div class="row">
        <div class="col-md-12">
          <h5>Récapitulatif de la commande</h5>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Nom de l'abonnement</th>
                  <th>Durée</th>
                  <th>Prix</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Starter</td>
                  <td>1 mois</td>
                  <td>10€<span class="text-muted">/mois TTC</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="row my-3">
        <div class="col-md-12">
          <h5>Informations de facturation</h5>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <h6>Choix du mode de paiement</h6>
          <div class="form-group">
            <?php
            $DoIHaveACreditCard = DoIHaveACreditCard($_SESSION["account"]["id"]);
            if ($DoIHaveACreditCard[0] > 0) { ?>
              <select class="form-control" name="card-month" onchange="bankDetails(this.value)" required>
                <option disabled selected value="">--Mode de paiement--</option>
                <option value="1"><?php echo $myCreditCardNumber; ?></option>
                <option value="2">Nouveau mode de paiement</option>
              </select>
            <?php } else { ?>
              <select class="form-control" name="card-month" onchange="bankDetails(this.value)" required>
                <option disabled selected value="">--Mode de paiement--</option>
                <option disabled value="1">Aucune carte bancaire enregistré</option>
                <option value="2">Nouveau mode de paiement</option>
              </select>
            <?php } ?>
          </div>
        </div>
      </div>
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div id="show-bank-details">
          <!-- AJAX -->
          <div class="row">
            <div class="col-md-2">
              <p>
                <a class="btn btn-outline-cyan btn-block" href="subscription.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
              </p>
            </div>
            <div class="col-md-3 offset-md-7">
              <p>
                <button disabled type="button" class="btn btn-purple btn-block" style="margin-top:10px;"><i class="fas fa-shopping-cart"></i> Procéder au paiement</button>
              </p>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
  <?php
  if (isset($_SESSION["creditCard"])) {
    unset($_SESSION["creditCard"]);
  }
  include '../assets/include/footer.php';
  ?>
</body>
