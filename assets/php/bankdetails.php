<?php
session_start();
include "../include/functions.php";

$currentYear = date("Y");

// Demander l'affichage du numéro de la carte bancaire de l'utilisateur
$myCreditCardNumber = showMyCreditCardNumber();

$userChoice = $_POST["value"];
if ($userChoice == 1) { ?>
  <div class="row">
    <div class="col-md-12">
      <h6>Coordonnée bancaire</h6>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <img src="/FileHosting/assets/image/illustration/cb.png" width="150" height="30" alt="Mode de paiement accepté">
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="form-group col-md-4">
          <label for="card-number">Numéro de la carte</label>
          <input disabled class="form-control" id="card-number" type="text" name="card-number" value="<?php echo $myCreditCardNumber; ?>" minlength="13" maxlength="22" placeholder="Numéro de la carte" required>
        </div>
        <div class="form-group col-md-3">
          <label for="card-security-code">Cryptogramme visuel</label>
          <input class="form-control" id="card-security-code" type="text" name="card-security-code" minlength="3" maxlength="4" placeholder="Cryptogramme visuel" required>
        </div>
        <div class="form-group col-md-2">
          <label>Expiration : Mois</label>
          <select class="form-control" name="card-month" required>
            <option disabled selected value="">--Mois--</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label>Expiration : Année</label>
          <select class="form-control" name="card-year" required>
            <option disabled selected value="">--Année--</option>
            <?php
            for ($i=0; $i < 15; $i++) {
              echo "<option value='$currentYear'>$currentYear</option>";
              $currentYear++;
            }
            ?>
          </select>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <p>
        <a class="btn btn-outline-cyan btn-block" href="../account/index.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
      </p>
    </div>
    <div class="col-md-3 offset-md-7">
      <p>
        <button type="submit" class="btn btn-purple btn-block" name="pay-subscription" style="margin-top:10px;"><i class="fas fa-shopping-cart"></i> Procéder au paiement</button>
      </p>
    </div>
  </div>
<?php } elseif ($userChoice == 2) { ?>
  <div class="row">
    <div class="col-md-12">
      <h6>Coordonnée bancaire</h6>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <img src="/FileHosting/assets/image/illustration/cb.png" width="150" height="30" alt="Mode de paiement accepté">
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="form-group col-md-4">
          <label for="card-number">Numéro de la carte</label>
          <input class="form-control" id="card-number" type="text" name="card-number" minlength="13" maxlength="22" placeholder="Numéro de la carte" required>
        </div>
        <div class="form-group col-md-3">
          <label for="card-security-code">Cryptogramme visuel</label>
          <input class="form-control" id="card-security-code" type="text" name="card-security-code" minlength="3" maxlength="4" placeholder="Cryptogramme visuel" required>
        </div>
        <div class="form-group col-md-2">
          <label>Expiration : Mois</label>
          <select class="form-control" name="card-month" required>
            <option disabled selected value="">--Mois--</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label>Expiration : Année</label>
          <select class="form-control" name="card-year" required>
            <option disabled selected value="">--Année--</option>
            <?php
            for ($i=0; $i < 15; $i++) {
              echo "<option value='$currentYear'>$currentYear</option>";
              $currentYear++;
            }
            ?>
          </select>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <p>
        <a class="btn btn-outline-cyan btn-block" href="../account/index.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
      </p>
    </div>
    <div class="col-md-3 offset-md-7">
      <p>
        <button type="submit" class="btn btn-purple btn-block" name="pay-subscription-with-new-payment" style="margin-top:10px;"><i class="fas fa-shopping-cart"></i> Procéder au paiement</button>
      </p>
    </div>
  </div>
<?php } ?>
