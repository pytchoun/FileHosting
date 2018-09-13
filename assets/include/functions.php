<?php

require "conf.inc.php";

// Fonction pour se connecter à la base de donnée
function connectDb() {
  try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PWD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    die("Erreur de connection: " . $e->getMessage() );
  }
  return $db;
}

// Fonction d'inscription d'un utilisateur sur le site
// Vérification des champs saisies, enregistrement dans la base de donnée
function registerUser() {
  $db = connectDb();
  $error = false;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
      $_SESSION["errors"]["name"] = "Un prénom est requis";
      $error = true;
    } else {
      $name = verifyInput($_POST["name"]);
      if (strlen($name) > 255) {
        $_SESSION["errors"]["name"] = "Votre prénom est trop grand";
        $error = true;
      }
    }

    if (empty($_POST["last-name"])) {
      $_SESSION["errors"]["last_name"] = "Un nom est requis";
      $error = true;
    } else {
      $last_name = verifyInput($_POST["last-name"]);
      if (strlen($last_name) > 255) {
        $_SESSION["errors"]["last_name"] = "Votre nom est trop grand";
        $error = true;
      }
    }

    if (empty($_POST["email"])) {
      $_SESSION["errors"]["email"] = "Un email est requis";
      $error = true;
    } else {
      $email = verifyInput($_POST["email"]);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["errors"]["email"] = "Format d'email invalide";
        $error = true;
      }
    }

    if (empty($_POST["password"])) {
      $_SESSION["errors"]["password"] = "Un mot de passe est requis";
      $error = true;
    } else {
      $password = verifyInput($_POST["password"]);
      if(!ctype_alnum($password)) {
        $_SESSION["errors"]["password"] = "Seules les lettres et les chiffres sont autorisés";
        $error = true;
      }
      if (strlen($_POST["password"]) < 8 or strlen($_POST["password"]) > 20) {
        $_SESSION["errors"]["password"] = "Min: 8 - Max: 20";
        $error = true;
      }
    }

    if (empty($_POST["confirm-password"])) {
      $_SESSION["errors"]["confirm_password"] = "Vous devez confirmer votre mot de passe";
      $error = true;
    } else {
      $confirm_password = verifyInput($_POST["confirm-password"]);
      if($_POST["confirm-password"] != $_POST["password"]) {
        $_SESSION["errors"]["confirm_password"] = "Les mots de passe ne correspondent pas";
        $error = true;
      }
    }
  }

  // Vérifier que l'email n'est pas déjà utilisé
  $query = $db->prepare("SELECT COUNT(email) FROM USER WHERE email=:email");
  $query->bindParam(':email', $email);
  $query->execute();
  $count = $query->fetch();
  if ($count[0] > 0) {
    $_SESSION["errors"]["email"] = "L'email est déjà utilisé";
    $error = true;
  }

  // Enregistrement du compte utilisateur dans la base de donnée
  if(!$error) {
    $query = $db->prepare("INSERT INTO USER (name, last_name, email, password, subscription, package, file_size_limit, current_storage_size, max_storage_size) VALUES (:name, :last_name, :email, :password, :subscription, :package, :file_size_limit, :current_storage_size, :max_storage_size)");
    $query->bindParam(':name', $name);
    $query->bindParam(':last_name', $last_name);
    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);
    $query->bindParam(':subscription', $subscription);
    $query->bindParam(':package', $package);
    $query->bindParam(':file_size_limit', $fileSizeLimit);
    $query->bindParam(':current_storage_size', $currentStorageSize);
    $query->bindParam(':max_storage_size', $maxStorageSize);

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $subscription = "Sans abonnement";
    $package = "Sans Package";
    $fileSizeLimit = "0";
    $currentStorageSize = "0";
    $maxStorageSize = "0";
    $query->execute();

    $_SESSION["accountCreated"] = 1;
    header("Location: login.php");
    exit;
  }
}

// Vérification de la saisie utilisateur : suppression des espaces en début et fin, suppression des antislashs, conversion des caractères spéciaux en entités HTML
function verifyInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// Fonction de connexion au site
function loginUser() {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$db = connectDb();

    // Sélection de l'utilisateur avec l'email saisi
		$query = $db->prepare("SELECT * FROM USER WHERE email=:email");
    $query->bindParam(':email', $_POST["email"]);
		$query->execute();
		$data = $query->fetch();

    // Le mot de passe saisie correspond avec celui en base de donnée
		if(password_verify($_POST["password"], $data["password"])) {

      $_SESSION["account"]["id"] = $data['id'];
      $_SESSION["account"]["name"] = $data['name'];
      $_SESSION["account"]["last_name"] = $data['last_name'];
      $_SESSION["account"]["email"] = $data['email'];
      $_SESSION["account"]["subscription"] = $data['subscription'];
      $_SESSION["account"]["begin_subscription"] = $data['begin_subscription'];
      $_SESSION["account"]["end_subscription"] = $data['end_subscription'];
      $_SESSION["account"]["package"] = $data['package'];
      $_SESSION["account"]["begin_package"] = $data['begin_package'];
      $_SESSION["account"]["end_package"] = $data['end_package'];

      header('Location: /FileHosting/account/index.php');
      exit;
    } else {
      $_SESSION["errors"]["login"] = 1;
    }
  }
}

// Vérifier si l'utilisateur est connecté au site
function isConnected() {
	if(!empty($_SESSION["account"]["email"])) {
		$db = connectDb();
		$query = $db->prepare("SELECT id FROM USER WHERE email=:email");
    $query->bindParam(':email', $_SESSION["account"]["email"]);
    $query->execute();

		if($query->rowCount()) {
			return true;
		} else {
			return false;
		}
	}
	return false;
}

// Fonction de déconnexion au site
function logoutUser() {
  unset($_SESSION["account"]);
	header('Location: /FileHosting/index.php');
  exit;
}

// Fonction d'édition des données nom, prénom et adresse email de l'utilisateur
function editUser() {
  $db = connectDb();
  $error = false;

   if(isset($_POST['email']) AND !empty($_POST['email'])) {
      $email = verifyInput($_POST["email"]);

      // Vérifier si l'email est déjà utilisé par un autre utilisateur
      $query = $db->prepare("SELECT COUNT(email) FROM USER WHERE email=:email AND id<>:id");
      $query->bindParam(':email', $email);
      $query->bindParam(':id', $_SESSION["account"]["id"]);
      $query->execute();
      $count = $query->fetch();
      if ($count[0] > 0) {
        $_SESSION["errors"]["email"] = "L'email est déjà utilisé";
        $error = true;
      }

      if (filter_var($email, FILTER_VALIDATE_EMAIL) AND $error == false) {
        $query = $db->prepare("UPDATE USER SET email=:email WHERE id=:id");
        $query->bindParam(':email', $email);
        $query->bindParam(':id', $_SESSION["account"]["id"]);
        $query->execute();

        $_SESSION["account"]["email"] = $email;
        $_SESSION["updated"]["email"] = 1;
      }
   }

   if(isset($_POST['name']) AND !empty($_POST['name'])) {
      $name = verifyInput($_POST["name"]);

      if (strlen($name) > 255) {
        $_SESSION["errors"]["name"] = "Votre prénom est trop grand";
        $error = true;
      }

      if ($error == false) {
        $query = $db->prepare("UPDATE USER SET name=:name WHERE id=:id");
        $query->bindParam(':name', $name);
        $query->bindParam(':id', $_SESSION["account"]["id"]);
        $query->execute();

        $_SESSION["account"]["name"] = $name;
        $_SESSION["updated"]["name"] = 1;
      }
   }

   if(isset($_POST['last-name']) AND !empty($_POST['last-name'])) {
      $last_name = verifyInput($_POST["last-name"]);

      if (strlen($last_name) > 255) {
        $_SESSION["errors"]["last-name"] = "Votre nom est trop grand";
        $error = true;
      }

      if ($error == false) {
        $query = $db->prepare("UPDATE USER SET last_name=:last_name WHERE id=:id");
        $query->bindParam(':last_name', $last_name);
        $query->bindParam(':id', $_SESSION["account"]["id"]);
        $query->execute();

        $_SESSION["account"]["last_name"] = $last_name;
        $_SESSION["updated"]["last_name"] = 1;
      }
   }

   if(empty($_POST['email']) AND empty($_POST['name']) AND empty($_POST['last-name'])) {
     $_SESSION["updated"]["noUpdate"] = 1;
   }

   if ($error == false) {
     header('Location: /FileHosting/account/index.php');
     exit;
   }
}

// Fonction d'édition du mot de passe de l'utilisateur
function editUserPassword() {
  $db = connectDb();
  $error = false;

  if(isset($_POST['old-password']) AND !empty($_POST['old-password']) AND isset($_POST['new-password']) AND !empty($_POST['new-password']) AND isset($_POST['confirm-new-password']) AND !empty($_POST['confirm-new-password'])) {
     $old_password = verifyInput($_POST["old-password"]);
     $new_password = verifyInput($_POST["new-password"]);
     $confirm_new_password = verifyInput($_POST["confirm-new-password"]);

     if(ctype_alnum($old_password) AND ctype_alnum($new_password) AND ctype_alnum($confirm_new_password)) {
     		$query = $db->prepare("SELECT * FROM USER WHERE id=:id");
        $query->bindParam(':id', $_SESSION["account"]["id"]);
     		$query->execute();
     		$data = $query->fetch();

        if(password_verify($_POST["old-password"], $data["password"])) {
          if (strlen($_POST["new-password"]) >= 8 AND strlen($_POST["new-password"]) <= 20) {
            if($_POST["confirm-new-password"] != $_POST["new-password"]) {
              $_SESSION["errors"]["confirm_new_password"] = "Les mots de passe ne correspondent pas";
              $error = true;
            }
            if ($error == false) {
              $password = password_hash($_POST["new-password"], PASSWORD_DEFAULT);
              $query = $db->prepare("UPDATE USER SET password=:password WHERE id=:id");
              $query->bindParam(':password', $password);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();

              $_SESSION["updated"]["password"] = 1;
            }
          } else {
            $_SESSION["errors"]["new_password"] = "Min: 8 - Max: 20";
            $error = true;
          }
        } else {
          $_SESSION["errors"]["old_password"] = "L'ancien mot de passe ne correspondent pas";
          $error = true;
        }
     } else {
       $_SESSION["errors"]["new_password"] = "Seules les lettres et les chiffres sont autorisés";
       $error = true;
     }
  }

   if(empty($_POST['old-password']) AND empty($_POST['new-password']) AND empty($_POST['confirm-new-password'])) {
     $_SESSION["updated"]["noUpdate"] = 1;
   }

   if ($error == false) {
     header('Location: /FileHosting/account/index.php');
     exit;
   }
}

// Fonction montrant la description de l'abonnement de l'utilisateur
function showSubscriptionDescription($subscription) {
  $db = connectDb();
  $query = $db->prepare("SELECT description FROM SUBSCRIPTION WHERE name=:name");
  $query->bindParam(':name', $subscription);
  $query->execute();
  $result = $query->fetch();
  return $result;
}

// Fonction pour enregistrer la carte bancaire de l'utilisateur
function saveCreditCard() {
  $db = connectDb();

  if(isset($_POST['card-number']) AND !empty($_POST['card-number']) AND isset($_POST['card-security-code']) AND !empty($_POST['card-security-code']) AND isset($_POST['card-month']) AND !empty($_POST['card-month']) AND isset($_POST['card-year']) AND !empty($_POST['card-year'])) {
     $card_number = verifyInput($_POST["card-number"]);
     $card_security_code = verifyInput($_POST["card-security-code"]);

     if (is_numeric($_POST["card-number"]) AND is_numeric($_POST["card-security-code"])) {
       if (strlen($_POST["card-number"]) >= 13 AND strlen($_POST["card-number"]) <= 22 AND strlen($_POST["card-security-code"]) >= 3 AND strlen($_POST["card-security-code"]) <= 4) {
         $query = $db->prepare("SELECT COUNT(id_user) FROM CREDIT_CARD WHERE id_user=:id_user");
         $query->bindParam(':id_user', $_SESSION["account"]["id"]);
         $query->execute();
         $count = $query->fetch();

         if ($count[0] > 0) {
           $insert_credit_card = $db->prepare("UPDATE CREDIT_CARD SET card_number=:card_number, card_security_code=:card_security_code, card_month=:card_month, card_year=:card_year WHERE id_user=:id_user");
           $insert_credit_card->bindParam(':id_user', $_SESSION["account"]["id"]);
           $insert_credit_card->bindParam(':card_number', $card_number);
           $insert_credit_card->bindParam(':card_security_code', $card_security_code);
           $insert_credit_card->bindParam(':card_month', $_POST["card-month"]);
           $insert_credit_card->bindParam(':card_year', $_POST["card-year"]);
           $insert_credit_card->execute();
         } else {
           $insert_credit_card = $db->prepare("INSERT INTO CREDIT_CARD (id_user, card_number, card_security_code, card_month, card_year) VALUES (:id_user, :card_number, :card_security_code, :card_month, :card_year)");
           $insert_credit_card->bindParam(':id_user', $_SESSION["account"]["id"]);
           $insert_credit_card->bindParam(':card_number', $card_number);
           $insert_credit_card->bindParam(':card_security_code', $card_security_code);
           $insert_credit_card->bindParam(':card_month', $_POST["card-month"]);
           $insert_credit_card->bindParam(':card_year', $_POST["card-year"]);
           $insert_credit_card->execute();
         }
         $_SESSION["creditCard"]["creditCardAdded"] = 1;
       } else {
         $_SESSION["creditCard"]["creditCardWrongNumberInput"] = 1;
       }
     } else {
       $_SESSION["creditCard"]["creditCardWrongInput"] = 1;
     }
  } else {
    $_SESSION["creditCard"]["creditCardNoInput"] = 1;
  }
}

// Fonction montrant les 4 derniers chiffres du numéro de la carte bancaire à son utilisateur
function showMyCreditCardNumber() {
  $db = connectDb();
  $query = $db->prepare("SELECT card_number FROM CREDIT_CARD WHERE id_user=:id_user");
  $query->bindParam(':id_user', $_SESSION["account"]["id"]);
  $query->execute();
  $myCreditCardNumber = $query->fetch();
  $myCreditCardLastNumber = substr($myCreditCardNumber[0], -4);
  $myCreditCardNumberSize = strlen($myCreditCardNumber[0]) -4;
  $myCreditCardHideNumber = "";
  for ($i=0; $i < $myCreditCardNumberSize; $i++) {
    $myCreditCardHideNumber = $myCreditCardHideNumber."*";
  }
  $secretCardNumber = $myCreditCardHideNumber.$myCreditCardLastNumber;
  return $secretCardNumber;
}

// Fonction cachant le cryptogramme de la carte bancaire de l'utilisateur en affichant des *
function showMyCreditCardSecurityCode() {
  $db = connectDb();
  $query = $db->prepare("SELECT card_security_code FROM CREDIT_CARD WHERE id_user=:id_user");
  $query->bindParam(':id_user', $_SESSION["account"]["id"]);
  $query->execute();
  $myCreditCardSecurityCode = $query->fetch();
  $myCreditCardSecurityCodeSize = strlen($myCreditCardSecurityCode[0]);
  $myCreditCardSecurityCodeHideNumber = "";
  for ($i=0; $i < $myCreditCardSecurityCodeSize; $i++) {
    $myCreditCardSecurityCodeHideNumber = $myCreditCardSecurityCodeHideNumber."*";
  }
  $secretCardSecurityCode = $myCreditCardSecurityCodeHideNumber;
  return $secretCardSecurityCode;
}

// Vérifier si l'utilisateur a enregistré une carte bancaire
function DoIHaveACreditCard($id) {
  $db = connectDb();
  $query = $db->prepare("SELECT COUNT(id_user) FROM CREDIT_CARD WHERE id_user=:id_user");
  $query->bindParam(':id_user', $id);
  $query->execute();
  $result = $query->fetch();
  return $result;
}

// Supprimer la carte bancaire de l'utilisateur
function deleteCreditCard() {
  $db = connectDb();
  $query = $db->prepare("DELETE FROM CREDIT_CARD WHERE id_user=:id_user");
  $query->bindParam(':id_user', $_SESSION["account"]["id"]);
  $query->execute();
  $_SESSION["creditCard"]["creditCardDeleted"] = 1;
  header('Location: /FileHosting/account/index.php');
  exit;
 }

// Paiement d'un abonnement avec la carte bancaire de l'utilisateur enregistré sur son compte
 function paySubscription($subscription, $price, $storageSize, $fileSizeLimit) {
   $db = connectDb();
   $billingDate = date('Y-m-d');
   $beginSubscription = date('Y-m-d h:i:s');
   $endSubscription = date('Y-m-d h:i:s', time() + 30 * 24 * 60 * 60);

   if(isset($_POST['card-security-code']) AND !empty($_POST['card-security-code']) AND isset($_POST['card-month']) AND !empty($_POST['card-month']) AND isset($_POST['card-year']) AND !empty($_POST['card-year'])) {
      $card_security_code = verifyInput($_POST["card-security-code"]);

      if (is_numeric($_POST["card-security-code"])) {
        if (strlen($_POST["card-security-code"]) >= 3 AND strlen($_POST["card-security-code"]) <= 4) {

          $query = $db->prepare("SELECT card_security_code FROM CREDIT_CARD WHERE id_user=:id_user");
          $query->bindParam(':id_user', $_SESSION["account"]["id"]);
          $query->execute();
          $result = $query->fetch();

          if ($_POST["card-security-code"] == $result[0]) {
            $query = $db->prepare("INSERT INTO BILL (id_user, id_subscription, billing_date, price) VALUES (:id_user, :id_subscription, :billing_date, :price)");
            $query->bindParam(':id_user', $_SESSION["account"]["id"]);
            $query->bindParam(':id_subscription', $subscription);
            $query->bindParam(':billing_date', $billingDate);
            $query->bindParam(':price', $price);
            $query->execute();

            $query = $db->prepare("SELECT max_storage_size FROM USER WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $myStorage = $query->fetch();

            if ($myStorage[0] < $storageSize) {
              $query = $db->prepare("UPDATE USER SET max_storage_size=:max_storage_size WHERE id=:id");
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->bindParam(':max_storage_size', $storageSize);
              $query->execute();
            }

            $query = $db->prepare("SELECT name FROM SUBSCRIPTION WHERE id=:id");
            $query->bindParam(':id', $subscription);
            $query->execute();
            $result = $query->fetch();

            $query = $db->prepare("SELECT subscription FROM USER WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $answer = $query->fetch();

            if ($result[0] == $answer[0]) {
              $query = $db->prepare("SELECT end_subscription FROM USER WHERE id=:id");
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
              $status = $query->fetch();

              if ($status[0] > $beginSubscription) {
                $endSubscription = strtotime($status[0]);
                $endSubscription = strtotime("1 month", $endSubscription);
                $endSubscription = date('Y-m-d h:i:s', $endSubscription);

                $query = $db->prepare("UPDATE USER SET subscription=:subscription, end_subscription=:end_subscription WHERE id=:id");
                $query->bindParam(':subscription', $result[0]);
                $query->bindParam(':end_subscription', $endSubscription);
                $query->bindParam(':id', $_SESSION["account"]["id"]);
                $query->execute();
              } else {
                $query = $db->prepare("UPDATE USER SET subscription=:subscription, begin_subscription=:begin_subscription, end_subscription=:end_subscription WHERE id=:id");
                $query->bindParam(':subscription', $result[0]);
                $query->bindParam(':begin_subscription', $beginSubscription);
                $query->bindParam(':end_subscription', $endSubscription);
                $query->bindParam(':id', $_SESSION["account"]["id"]);
                $query->execute();
                $_SESSION["account"]["begin_subscription"] = $beginSubscription;
              }
            } else {
              $query = $db->prepare("UPDATE USER SET subscription=:subscription, begin_subscription=:begin_subscription, end_subscription=:end_subscription, file_size_limit=:file_size_limit WHERE id=:id");
              $query->bindParam(':subscription', $result[0]);
              $query->bindParam(':begin_subscription', $beginSubscription);
              $query->bindParam(':end_subscription', $endSubscription);
              $query->bindParam(':file_size_limit', $fileSizeLimit);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
              $_SESSION["account"]["begin_subscription"] = $beginSubscription;
            }
            $_SESSION["account"]["subscription"] = $result[0];
            $_SESSION["account"]["end_subscription"] = $endSubscription;
            header("Location: /FileHosting/account/paymentaccepted.php");
            exit;
          } else {
            $_SESSION["creditCard"]["declinedPayment"] = 1;
          }
        } else {
          $_SESSION["creditCard"]["creditCardWrongNumberInput"] = 1;
        }
      } else {
        $_SESSION["creditCard"]["creditCardWrongInput"] = 1;
      }
   } else {
     $_SESSION["creditCard"]["creditCardNoInput"] = 1;
   }
 }

// Paiement d'un abonnement avec une carte bancaire de l'utilisateur qui n'est pas enregistré sur son compte
 function paySubscriptionWithNewPayment($subscription, $price, $storageSize, $fileSizeLimit) {
   $db = connectDb();
   $billingDate = date('Y-m-d');
   $beginSubscription = date('Y-m-d h:i:s');
   $endSubscription = date('Y-m-d h:i:s', time() + 30 * 24 * 60 * 60);

   if(isset($_POST['card-number']) AND !empty($_POST['card-number']) AND isset($_POST['card-security-code']) AND !empty($_POST['card-security-code']) AND isset($_POST['card-month']) AND !empty($_POST['card-month']) AND isset($_POST['card-year']) AND !empty($_POST['card-year'])) {
      $card_number = verifyInput($_POST["card-number"]);
      $card_security_code = verifyInput($_POST["card-security-code"]);

      if (is_numeric($_POST["card-number"]) AND is_numeric($_POST["card-security-code"])) {
        if (strlen($_POST["card-number"]) >= 13 AND strlen($_POST["card-number"]) <= 22 AND strlen($_POST["card-security-code"]) >= 3 AND strlen($_POST["card-security-code"]) <= 4) {

          $query = $db->prepare("INSERT INTO BILL (id_user, id_subscription, billing_date, price) VALUES (:id_user, :id_subscription, :billing_date, :price)");
          $query->bindParam(':id_user', $_SESSION["account"]["id"]);
          $query->bindParam(':id_subscription', $subscription);
          $query->bindParam(':billing_date', $billingDate);
          $query->bindParam(':price', $price);
          $query->execute();

          $query = $db->prepare("SELECT max_storage_size FROM USER WHERE id=:id");
          $query->bindParam(':id', $_SESSION["account"]["id"]);
          $query->execute();
          $myStorage = $query->fetch();

          if ($myStorage[0] < $storageSize) {
            $query = $db->prepare("UPDATE USER SET max_storage_size=:max_storage_size WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->bindParam(':max_storage_size', $storageSize);
            $query->execute();
          }

          $query = $db->prepare("SELECT name FROM SUBSCRIPTION WHERE id=:id");
          $query->bindParam(':id', $subscription);
          $query->execute();
          $result = $query->fetch();

          $query = $db->prepare("SELECT subscription FROM USER WHERE id=:id");
          $query->bindParam(':id', $_SESSION["account"]["id"]);
          $query->execute();
          $answer = $query->fetch();

          if ($result[0] == $answer[0]) {
            $query = $db->prepare("SELECT end_subscription FROM USER WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $status = $query->fetch();

            if ($status[0] > $beginSubscription) {
              $endSubscription = strtotime($status[0]);
              $endSubscription = strtotime("1 month", $endSubscription);
              $endSubscription = date('Y-m-d h:i:s', $endSubscription);

              $query = $db->prepare("UPDATE USER SET subscription=:subscription, end_subscription=:end_subscription WHERE id=:id");
              $query->bindParam(':subscription', $result[0]);
              $query->bindParam(':end_subscription', $endSubscription);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
            } else {
              $query = $db->prepare("UPDATE USER SET subscription=:subscription, begin_subscription=:begin_subscription, end_subscription=:end_subscription WHERE id=:id");
              $query->bindParam(':subscription', $result[0]);
              $query->bindParam(':begin_subscription', $beginSubscription);
              $query->bindParam(':end_subscription', $endSubscription);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
              $_SESSION["account"]["begin_subscription"] = $beginSubscription;
            }
          } else {
            $query = $db->prepare("UPDATE USER SET subscription=:subscription, begin_subscription=:begin_subscription, end_subscription=:end_subscription, file_size_limit=:file_size_limit WHERE id=:id");
            $query->bindParam(':subscription', $result[0]);
            $query->bindParam(':begin_subscription', $beginSubscription);
            $query->bindParam(':end_subscription', $endSubscription);
            $query->bindParam(':file_size_limit', $fileSizeLimit);
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $_SESSION["account"]["begin_subscription"] = $beginSubscription;
          }
          $_SESSION["account"]["subscription"] = $result[0];
          $_SESSION["account"]["end_subscription"] = $endSubscription;
          header("Location: /FileHosting/account/paymentaccepted.php");
          exit;
        } else {
          $_SESSION["creditCard"]["creditCardWrongNumberInput"] = 1;
        }
      } else {
        $_SESSION["creditCard"]["creditCardWrongInput"] = 1;
      }
   } else {
     $_SESSION["creditCard"]["creditCardNoInput"] = 1;
   }
 }

// Paiement d'un package avec la carte bancaire de l'utilisateur enregistré sur son compte
 function payPackage($package, $price, $storageSize) {
   $db = connectDb();
   $billingDate = date('Y-m-d');
   $beginPackage = date('Y-m-d h:i:s');
   $endPackage = date('Y-m-d h:i:s', time() + 30 * 24 * 60 * 60);

   if(isset($_POST['card-security-code']) AND !empty($_POST['card-security-code']) AND isset($_POST['card-month']) AND !empty($_POST['card-month']) AND isset($_POST['card-year']) AND !empty($_POST['card-year'])) {
      $card_security_code = verifyInput($_POST["card-security-code"]);

      if (is_numeric($_POST["card-security-code"])) {
        if (strlen($_POST["card-security-code"]) >= 3 AND strlen($_POST["card-security-code"]) <= 4) {

          $query = $db->prepare("SELECT card_security_code FROM CREDIT_CARD WHERE id_user=:id_user");
          $query->bindParam(':id_user', $_SESSION["account"]["id"]);
          $query->execute();
          $result = $query->fetch();

          if ($_POST["card-security-code"] == $result[0]) {
            $query = $db->prepare("INSERT INTO BILL (id_user, id_subscription, billing_date, price) VALUES (:id_user, :id_subscription, :billing_date, :price)");
            $query->bindParam(':id_user', $_SESSION["account"]["id"]);
            $query->bindParam(':id_subscription', $package);
            $query->bindParam(':billing_date', $billingDate);
            $query->bindParam(':price', $price);
            $query->execute();

            $query = $db->prepare("UPDATE USER SET max_storage_size=:max_storage_size WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->bindParam(':max_storage_size', $storageSize);
            $query->execute();

            $query = $db->prepare("SELECT name FROM SUBSCRIPTION WHERE id=:id");
            $query->bindParam(':id', $package);
            $query->execute();
            $result = $query->fetch();

            $query = $db->prepare("SELECT package FROM USER WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $answer = $query->fetch();

            if ($result[0] == $answer[0]) {
              $query = $db->prepare("SELECT end_package FROM USER WHERE id=:id");
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
              $status = $query->fetch();

              if ($status[0] > $beginPackage) {
                $endPackage = strtotime($status[0]);
                $endPackage = strtotime("1 month", $endPackage);
                $endPackage = date('Y-m-d h:i:s', $endPackage);

                $query = $db->prepare("UPDATE USER SET package=:package, end_package=:end_package WHERE id=:id");
                $query->bindParam(':package', $result[0]);
                $query->bindParam(':end_package', $endPackage);
                $query->bindParam(':id', $_SESSION["account"]["id"]);
                $query->execute();
              } else {
                $query = $db->prepare("UPDATE USER SET package=:package, begin_package=:begin_package, end_package=:end_package WHERE id=:id");
                $query->bindParam(':package', $result[0]);
                $query->bindParam(':begin_package', $beginPackage);
                $query->bindParam(':end_package', $endPackage);
                $query->bindParam(':id', $_SESSION["account"]["id"]);
                $query->execute();
                $_SESSION["account"]["begin_package"] = $beginPackage;
              }
            } else {
              $query = $db->prepare("UPDATE USER SET package=:package, begin_package=:begin_package, end_package=:end_package WHERE id=:id");
              $query->bindParam(':package', $result[0]);
              $query->bindParam(':begin_package', $beginPackage);
              $query->bindParam(':end_package', $endPackage);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
              $_SESSION["account"]["begin_package"] = $beginPackage;
            }
            $_SESSION["account"]["package"] = $result[0];
            $_SESSION["account"]["end_package"] = $endPackage;
            header("Location: /FileHosting/account/paymentaccepted.php");
            exit;
          } else {
            $_SESSION["creditCard"]["declinedPayment"] = 1;
          }
        } else {
          $_SESSION["creditCard"]["creditCardWrongNumberInput"] = 1;
        }
      } else {
        $_SESSION["creditCard"]["creditCardWrongInput"] = 1;
      }
   } else {
     $_SESSION["creditCard"]["creditCardNoInput"] = 1;
   }
 }

// Paiement d'un package avec une carte bancaire de l'utilisateur qui n'est pas enregistré sur son compte
 function payPackageWithNewPayment($package, $price, $storageSize) {
   $db = connectDb();
   $billingDate = date('Y-m-d');
   $beginPackage = date('Y-m-d h:i:s');
   $endPackage = date('Y-m-d h:i:s', time() + 30 * 24 * 60 * 60);

   if(isset($_POST['card-number']) AND !empty($_POST['card-number']) AND isset($_POST['card-security-code']) AND !empty($_POST['card-security-code']) AND isset($_POST['card-month']) AND !empty($_POST['card-month']) AND isset($_POST['card-year']) AND !empty($_POST['card-year'])) {
      $card_number = verifyInput($_POST["card-number"]);
      $card_security_code = verifyInput($_POST["card-security-code"]);

      if (is_numeric($_POST["card-number"]) AND is_numeric($_POST["card-security-code"])) {
        if (strlen($_POST["card-number"]) >= 13 AND strlen($_POST["card-number"]) <= 22 AND strlen($_POST["card-security-code"]) >= 3 AND strlen($_POST["card-security-code"]) <= 4) {

          $query = $db->prepare("INSERT INTO BILL (id_user, id_subscription, billing_date, price) VALUES (:id_user, :id_subscription, :billing_date, :price)");
          $query->bindParam(':id_user', $_SESSION["account"]["id"]);
          $query->bindParam(':id_subscription', $package);
          $query->bindParam(':billing_date', $billingDate);
          $query->bindParam(':price', $price);
          $query->execute();

          $query = $db->prepare("UPDATE USER SET max_storage_size=:max_storage_size WHERE id=:id");
          $query->bindParam(':id', $_SESSION["account"]["id"]);
          $query->bindParam(':max_storage_size', $storageSize);
          $query->execute();

          $query = $db->prepare("SELECT name FROM SUBSCRIPTION WHERE id=:id");
          $query->bindParam(':id', $package);
          $query->execute();
          $result = $query->fetch();

          $query = $db->prepare("SELECT package FROM USER WHERE id=:id");
          $query->bindParam(':id', $_SESSION["account"]["id"]);
          $query->execute();
          $answer = $query->fetch();

          if ($result[0] == $answer[0]) {
            $query = $db->prepare("SELECT end_package FROM USER WHERE id=:id");
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $status = $query->fetch();

            if ($status[0] > $beginPackage) {
              $endPackage = strtotime($status[0]);
              $endPackage = strtotime("1 month", $endPackage);
              $endPackage = date('Y-m-d h:i:s', $endPackage);

              $query = $db->prepare("UPDATE USER SET package=:package, end_package=:end_package WHERE id=:id");
              $query->bindParam(':package', $result[0]);
              $query->bindParam(':end_package', $endPackage);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
            } else {
              $query = $db->prepare("UPDATE USER SET package=:package, begin_package=:begin_package, end_package=:end_package WHERE id=:id");
              $query->bindParam(':package', $result[0]);
              $query->bindParam(':begin_package', $beginPackage);
              $query->bindParam(':end_package', $endPackage);
              $query->bindParam(':id', $_SESSION["account"]["id"]);
              $query->execute();
              $_SESSION["account"]["begin_package"] = $beginPackage;
            }
          } else {
            $query = $db->prepare("UPDATE USER SET package=:package, begin_package=:begin_package, end_package=:end_package WHERE id=:id");
            $query->bindParam(':package', $result[0]);
            $query->bindParam(':begin_package', $beginPackage);
            $query->bindParam(':end_package', $endPackage);
            $query->bindParam(':id', $_SESSION["account"]["id"]);
            $query->execute();
            $_SESSION["account"]["begin_package"] = $beginPackage;
          }
          $_SESSION["account"]["package"] = $result[0];
          $_SESSION["account"]["end_package"] = $endPackage;
          header("Location: /FileHosting/account/paymentaccepted.php");
          exit;
        } else {
          $_SESSION["creditCard"]["creditCardWrongNumberInput"] = 1;
        }
      } else {
        $_SESSION["creditCard"]["creditCardWrongInput"] = 1;
      }
   } else {
     $_SESSION["creditCard"]["creditCardNoInput"] = 1;
   }
 }

// Affichage de la taille des fichiers en unité lisible par l'utilisateur
function human_filesize($bytes, $decimals = 2) {
  $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

// Suppression des accents dans les fichiers
function fixeAccent($chaine) {
  $chaine = str_replace(["À", "À", "Â", "Ã", "Ä", "Å"], "A", $chaine);
  $chaine = str_replace(["à", "á", "â", "ã", "ä", "å"], "a", $chaine);
  $chaine = str_replace(["È", "É", "Ê", "Ë"], "E", $chaine);
  $chaine = str_replace(["é", "è", "ê", "ë"], "e", $chaine);
  $chaine = str_replace(["Ì", "Í", "Î", "Ï"], "I", $chaine);
  $chaine = str_replace(["ì", "í", "î", "ï"], "i", $chaine);
  $chaine = str_replace(["Ò", "Ó", "Ô", "Õ", "Ö"], "O", $chaine);
  $chaine = str_replace(["ð", "ò", "ó", "ô", "ö"], "o", $chaine);
  $chaine = str_replace(["Ù", "Ú", "Û", "Ü"], "U", $chaine);
  $chaine = str_replace(["ù", "ú", "û", "ü"], "u", $chaine);
  $chaine = str_replace(["Ç"], "C", $chaine);
  $chaine = str_replace(["ç"], "c", $chaine);
  return $chaine;
}

// Obtenir tous les dossiers fils du dossier saisi
function folderList($searchFolder) {
  $db = connectDb();
  $items = [];
  $query = $db->prepare("SELECT id FROM FOLDER WHERE id_folder=:id_folder");

  function loop($searchFolder, $query, &$items) {
    $query->bindParam(':id_folder', $searchFolder);
    $query->execute();
    $results = $query->fetchAll();

    foreach($results as $result) {
      $items[] = $result;
      loop($result['id'], $query, $items);
    }
  }

  loop($searchFolder, $query, $items);
  return $items;
}

// Supprimer un dossier du serveur
function deleteFolderRecursive($dir) {
  foreach(scandir($dir) as $file) {
    if ('.' === $file || '..' === $file) {
      continue;
    }
    if (is_dir("$dir/$file")) {
      deleteFolderRecursive("$dir/$file");
    } else {
      unlink("$dir/$file");
    }
  }
  rmdir($dir);
}

// Supprimer toutes les données de l'utilisateur du site
function deleteUser() {
  $root = realpath($_SERVER["DOCUMENT_ROOT"]);
  $db = connectDb();
  $query = $db->prepare("SELECT password FROM USER WHERE id=:id");
  $query->bindParam(':id', $_SESSION["account"]["id"]);
  $query->execute();
  $data = $query->fetch();

  if(password_verify($_POST["password"], $data["password"])) {
    if(file_exists($root.'/FileHosting/uploads/'.$_SESSION["account"]["id"])) {
      deleteFolderRecursive($root.'/FileHosting/uploads/'.$_SESSION["account"]["id"]);
    }

    $query = $db->prepare("DELETE FROM USER WHERE id=:id;
      DELETE FROM CREDIT_CARD WHERE id_user=:id;
      DELETE FROM FILE_SHARE WHERE id_user_host=:id;
      DELETE FROM FILE_SHARE WHERE id_user_guest=:id;
      DELETE FROM FILE WHERE id_user=:id;
      DELETE FROM FOLDER WHERE id_user=:id;
      DELETE FROM FOLDER_SHARE WHERE id_user_host=:id;
      DELETE FROM FOLDER_SHARE WHERE id_user_guest=:id");
    $query->bindParam(':id', $_SESSION["account"]["id"]);
    $query->execute();

    unset($_SESSION["account"]);

    $_SESSION["accountDeleted"] = 1;

    header('Location: /FileHosting/index.php');
    exit;
  } else {
    $_SESSION["wrongPassword"] = 1;
  }
}

?>
