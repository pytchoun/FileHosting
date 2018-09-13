<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
if (isset($_POST['save-credit-card'])) {
  saveCreditCard();
}
if (isset($_POST['delete-credit-card'])) {
  deleteCreditCard();
}
if (isset($_POST['delete-account'])) {
  deleteUser();
}

$currentYear = date("Y");
$timeNow = date('Y-m-d h:i:s');

// Demander l'affichage des données de la carte bancaire de l'utilisateur
$myCreditCardNumber = showMyCreditCardNumber();
$myCreditCardSecurityCode = showMyCreditCardSecurityCode();

// Vérifier si l'utilisateur possède une carte bancaire enregistré
$DoIHaveACreditCard = DoIHaveACreditCard($_SESSION["account"]["id"]);

// Afficher la description de l'abonnement de l'utilisateur
$description = showSubscriptionDescription($_SESSION["account"]["subscription"]);

$db = connectDb();
// Obtenir la date de fin de validité de l'abonnement de l'utilisateur
$query = $db->prepare("SELECT end_subscription, end_package FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$status = $query->fetch();

if ($status[0] == null) {
  $endSubscription = "N/A";
} else {
  $endSubscription = strtotime($status[0]);
  $endSubscription = date("d/m/Y", $endSubscription);
  if ($status[0] < $timeNow) {
    $obsoleteSubscription = true;
  } else {
    $obsoleteSubscription = false;
  }
}
if ($status[1] == null) {
  $endPackage = "N/A";
} else {
  $endPackage = strtotime($status[1]);
  $endPackage = date("d/m/Y", $endPackage);
}

// Obtenir la taille du stockage actuelle et maximum de l'utilisateur
$query = $db->prepare("SELECT current_storage_size, max_storage_size FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$storageSize = $query->fetch();
$currentStorageSize = human_filesize($storageSize[0]);
$maxStorageSize = human_filesize($storageSize[1]);

// Obtenir le nombre de fichier de l'utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM FILE WHERE id_user=:id_user");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->execute();
$fileNumber = $query->fetch();

// Obtenir le nombre de dossier de l'utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM FOLDER WHERE id_user=:id_user");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->execute();
$folderNumber = $query->fetch();

// Obtenir le nombre de fichier partagé par l'utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM FILE_SHARE WHERE id_user_host=:id_user_host");
$query->bindParam(':id_user_host', $_SESSION["account"]["id"]);
$query->execute();
$fileHosted = $query->fetch();

// Obtenir le nombre de dossier partagé par l'utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM FOLDER_SHARE WHERE id_user_host=:id_user_host");
$query->bindParam(':id_user_host', $_SESSION["account"]["id"]);
$query->execute();
$folderHosted = $query->fetch();

// Obtenir le nombre de fichier partagé par un utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM FILE_SHARE WHERE id_user_guest=:id_user_guest");
$query->bindParam(':id_user_guest', $_SESSION["account"]["id"]);
$query->execute();
$fileShared = $query->fetch();

// Obtenir le nombre de dossier partagé par un utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM FOLDER_SHARE WHERE id_user_guest=:id_user_guest");
$query->bindParam(':id_user_guest', $_SESSION["account"]["id"]);
$query->execute();
$folderShared = $query->fetch();
?>
<body>
  <section>
    <div class="container" style="padding-bottom: 20px;">
      <div class="row mt-5">
        <div class="col-md-12">
          <h1 class="text-center">Mon profil FileHosting</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <h3 class="text-center border-bottom border-dark rounded-bottom h-account">Informations personnelles</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
          if (isset($_SESSION["updated"])) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
              <?php
              if (isset($_SESSION["updated"]["email"])) {
                echo "Votre email a été modifié.<br>";
              }
              if (isset($_SESSION["updated"]["name"])) {
                echo "Votre nom a été modifié.<br>";
              }
              if (isset($_SESSION["updated"]["last_name"])) {
                echo "Votre prénom a été modifié.<br>";
              }
              if (isset($_SESSION["updated"]["password"])) {
                echo "Votre mot de passe a été modifié.";
              }
              if (isset($_SESSION["updated"]["noUpdate"])) {
                echo "Vous n'avez rien modifié.";
              }
              ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          if (isset($_SESSION["wrongPassword"])) { ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
              Le mot de passe ne correspond pas.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          if (isset($_SESSION["creditCard"]["creditCardAdded"]) OR isset($_SESSION["creditCard"]["creditCardDeleted"])) {
            ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
              <?php
              if (isset($_SESSION["creditCard"]["creditCardAdded"])) {
                echo "Votre carte bancaire a été enregistré.";
              }
              if (isset($_SESSION["creditCard"]["creditCardDeleted"])) {
                echo "Votre carte bancaire a été supprimé.";
              }
              ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
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
          if (isset($_SESSION["access"]["forbidden"])) {
            ?>
            <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
              Vous ne pouvez pas accéder à votre espace de stockage sans abonnement actif.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          if (isset($_SESSION["access"]["unauthorized"])) {
            ?>
            <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
              Vous ne pouvez pas acheter un Package sans l'abonnement Premium actif.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
          }
          ?>
          <div class="card-deck">
            <div class="card border border-account">
              <h5 class="card-header card-header-account text-center">Aperçu de mon compte</h5>
              <div class="card-body card-body-account">
                <div class="table-responsive">
                  <table class="table table-borderless text-center">
                    <tbody>
                      <tr>
                        <td>Prénom</td>
                        <td><?php echo $_SESSION["account"]["name"]; ?></td>
                      </tr>
                      <tr>
                        <td>Nom</td>
                        <td><?php echo $_SESSION["account"]["last_name"]; ?></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><?php echo $_SESSION["account"]["email"]; ?></td>
                      </tr>
                      <tr>
                        <td>Abonnement</td>
                        <td><?php echo $_SESSION["account"]["subscription"]; ?></td>
                      </tr>
                      <tr>
                        <td>Package</td>
                        <td><?php echo $_SESSION["account"]["package"]; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="card border border-account">
              <h5 class="card-header card-header-account text-center">Carte bancaire</h5>
              <div class="card-body card-body-account">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                  <div class="row">
                    <div class="form-group col-md-7">
                      <label for="card-number">Numéro de la carte</label>
                      <input class="form-control" id="card-number" type="text" name="card-number" value="<?php echo $myCreditCardNumber; ?>" minlength="13" maxlength="22" placeholder="Numéro de la carte" required>
                    </div>
                    <div class="form-group col-md-5">
                      <label for="card-security-code">Cryptogramme visuel</label>
                      <input class="form-control" id="card-security-code" type="text" name="card-security-code" value="<?php echo $myCreditCardSecurityCode; ?>" minlength="3" maxlength="4" placeholder="Cryptogramme visuel" required>
                    </div>
                  </div>
                  <div class="row my-3">
                    <div class="col-md-12">
                      <label>Date d'expiration</label>
                    </div>
                    <div class="form-group col-md-6">
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
                    <div class="form-group col-md-6">
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
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <p>
              <a class="btn btn-green btn-block" href="edit.php" style="margin-top:10px; color:white;"><i class="fas fa-user"></i> Modifier le profil</a>
            </p>
          </div>
          <div class="col-md-3">
            <p>
              <a class="btn btn-cyan btn-block" href="password.php" style="margin-top:10px; color:white;"><i class="fas fa-unlock"></i> Changer le mot de passe</a>
            </p>
          </div>
          <div class="col-md-3">
            <p>
              <button type="submit" class="btn btn-teal btn-block" name="save-credit-card" style="margin-top:10px;"><i class="fas fa-save"></i> Sauvegarder ma carte</button>
            </p>
          </div>
        </form>
        <div class="col-md-3">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <p>
              <?php
              if ($DoIHaveACreditCard[0] > 0) {
                $answer = "";
              } else {
                $answer = "disabled";
              }
              ?>
              <button type="submit" class="btn btn-purple btn-block" name="delete-credit-card" style="margin-top:10px;" <?php echo $answer; ?>><i class="far fa-trash-alt"></i> Supprimer ma carte</button>
            </p>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <h3 class="text-center border-bottom border-dark rounded-bottom h-account">Abonnement</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <div class="card border border-account">
            <h5 class="card-header card-header-account text-center">Forfait : <?php echo $_SESSION["account"]["subscription"]; ?></h5>
            <div class="card-body card-body-account">
              <p style="margin:0;">
                <?php
                echo $description[0];
                ?>
              </p>
            </div>
            <div class="card-footer card-footer-account text-center">
              <p style="margin:0;">
                Fin de validité : <?php echo $endSubscription; ?>
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p>
                <a class="btn btn-green btn-block" href="subscription.php" style="margin-top:10px; color:white;"><i class="fas fa-exchange-alt"></i> Changer d'abonnement</a>
              </p>
            </div>
            <div class="col-md-6">
              <p>
                <a class="btn btn-cyan btn-block" href="history.php" style="margin-top:10px; color:white;"><i class="fas fa-history"></i> Historique</a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <h3 class="text-center border-bottom border-dark rounded-bottom h-account">Espace de stockage</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <?php
          if ($_SESSION["account"]["subscription"] == "Sans abonnement" || $obsoleteSubscription == true) {
            ?>
            <div class="alert alert-info text-center" role="alert">
              Sans abonnement actif vous ne pouvez pas accéder à votre espace de stockage.
            </div>
            <?php
          }
          ?>
          <div class="card border border-account">
            <h5 class="card-header card-header-account text-center">Aperçu de mon stockage</h5>
            <div class="card-body card-body-account">
              <div class="table-responsive">
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <td>Espace total</td>
                      <td><?php echo $maxStorageSize; ?></td>
                    </tr>
                    <tr>
                      <td>Espace utilisé</td>
                      <td><?php echo $currentStorageSize; ?></td>
                    </tr>
                    <tr>
                      <td>Nombre de fichiers</td>
                      <td><?php echo $fileNumber[0]; ?></td>
                    </tr>
                    <tr>
                      <td>Nombre de dossiers</td>
                      <td><?php echo $folderNumber[0]; ?></td>
                    </tr>
                    <tr>
                      <td>Nombre de fichiers partagés avec moi</td>
                      <td><?php echo $fileShared[0]; ?></td>
                    </tr>
                    <tr>
                      <td>Nombre de dossiers partagés avec moi</td>
                      <td><?php echo $folderShared[0]; ?></td>
                    </tr>
                    <tr>
                      <td>Nombre de partage de fichiers</td>
                      <td><?php echo $fileHosted[0]; ?></td>
                    </tr>
                    <tr>
                      <td>Nombre de partage de dossiers</td>
                      <td><?php echo $folderHosted[0]; ?></td>
                    </tr>
                    <tr>
                      <td>Mon Package</td>
                      <td><?php echo $_SESSION["account"]["package"]; ?></td>
                    </tr>
                    <tr>
                      <td>Fin de validité du Package</td>
                      <td><?php echo $endPackage; ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p>
                <a class="btn btn-green btn-block" href="expand.php" style="margin-top:10px; color:white;"><i class="fab fa-cloudscale"></i> Acheter plus d'espace</a>
              </p>
            </div>
            <div class="col-md-6">
              <p>
                <a class="btn btn-cyan btn-block" href="storage.php" style="margin-top:10px; color:white;"><i class="fas fa-cloud"></i> Accéder à mon espace</a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <a class="text-danger btn float-right" role="button" data-toggle="modal" data-target="#deleteAccount">
            <i class="far fa-trash-alt"></i> Supprimer le compte
          </a>
          <div class="modal fade" id="deleteAccount" tabindex="-1" role="dialog" aria-labelledby="deleteAccountTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="deleteAccountTitle">Supprimer le compte</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p class="text-justify">
                    Êtes-vous certain de vouloir supprimer votre compte ? Ceci entrainera la perte de toutes vos données sur le site. Pour supprimer votre compte entrez votre mot de passe ci-dessous.
                  </p>
                  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="form-group col">
                      <input class="form-control" id="your-password" type="password" name="password" minlength="8" maxlength="20" placeholder="Mot de passe" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <div class="col-md-6">
                      <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Annuler</button>
                    </div>
                    <div class="col-md-6">
                      <button type="submit" class="btn btn-orange btn-block" name="delete-account"><i class="fas fa-heartbeat"></i> Confirmer</button>
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
  if (isset($_SESSION["updated"])) {
    unset($_SESSION["updated"]);
  }
  if (isset($_SESSION["creditCard"])) {
    unset($_SESSION["creditCard"]);
  }
  if (isset($_SESSION["wrongPassword"])) {
    unset($_SESSION["wrongPassword"]);
  }
  if (isset($_SESSION["access"])) {
    unset($_SESSION["access"]);
  }
  include '../assets/include/footer.php';
  ?>
</body>
