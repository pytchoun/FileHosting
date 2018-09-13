<?php
session_start();
include "../include/functions.php";

$email = verifyInput($_POST["email"]);
$folderId = $_POST["folder"];
$myParentFolderId = $_POST["myParentFolder"];

if ($email != null) {
  $db = connectDb();
  // Sélectionner l'utilisateur cible du partage
  $query = $db->prepare("SELECT id FROM USER WHERE email=:email AND id<>:id");
  $query->bindParam(':email', $email);
  $query->bindParam(':id', $_SESSION["account"]["id"]);
  $query->execute();
  $count = $query->fetch();

  if ($count > 0) {
    // Vérifions que le dossier n'est pas déjà partagé pour cet utilisateur
    $query = $db->prepare("SELECT id_folder, id_user_guest FROM FOLDER_SHARE WHERE id_folder=:id_folder AND id_user_guest=:id_user_guest");
    $query->bindParam(':id_folder', $folderId);
    $query->bindParam(':id_user_guest', $idUserGuest);
    $idUserGuest = $count[0];
    $query->execute();
    $search = $query->fetch();

    if ($search == 0) {
      // Partageons le dossier en mettant à jour la base de donnée
      $query = $db->prepare("INSERT INTO FOLDER_SHARE (id_folder, id_parent_folder, id_user_host, id_user_guest) VALUES (:id_folder, :id_parent_folder, :id_user_host, :id_user_guest)");
      $query->bindParam(':id_folder', $folderId);
      $query->bindParam(':id_parent_folder', $myParentFolderId);
      $query->bindParam(':id_user_host', $_SESSION["account"]["id"]);
      $query->bindParam(':id_user_guest', $idUserGuest);
      $query->execute();
      $_SESSION["shareFolder"]["success"] = 1;
    } else {
      $_SESSION["shareFolder"]["duplicate"] = 1;
    }
  } else {
    $_SESSION["shareFolder"]["error"] = 1;
  }
} else {
  $_SESSION["shareFolder"]["empty"] = 1;
}


if (isset($_SESSION["shareFolder"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["shareFolder"]["error"])) {
      echo "L'email est introuvable.";
    }
    if (isset($_SESSION["shareFolder"]["success"])) {
      echo "Le dossier a été partagé.";
    }
    if (isset($_SESSION["shareFolder"]["duplicate"])) {
      echo "Le dossier est déjà partagé.";
    }
    if (isset($_SESSION["shareFolder"]["empty"])) {
      echo "La saisie est vide.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["shareFolder"])) {
  unset($_SESSION["shareFolder"]);
}
?>
