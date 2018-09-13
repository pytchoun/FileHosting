<?php
session_start();
include "../include/functions.php";

$email = verifyInput($_POST["email"]);
$fileId = $_POST["file"];
$folderId = $_POST["folder"];

if ($email != null) {
  $db = connectDb();
  // Sélectionner l'utilisateur cible du partage
  $query = $db->prepare("SELECT id FROM USER WHERE email=:email AND id<>:id");
  $query->bindParam(':email', $email);
  $query->bindParam(':id', $_SESSION["account"]["id"]);
  $query->execute();
  $count = $query->fetch();

  if ($count > 0) {
    // Vérifions que le fichier n'est pas déjà partagé pour cet utilisateur
    $query = $db->prepare("SELECT id_file, id_user_guest FROM FILE_SHARE WHERE id_file=:id_file AND id_user_guest=:id_user_guest");
    $query->bindParam(':id_file', $fileId);
    $query->bindParam(':id_user_guest', $idUserGuest);
    $idUserGuest = $count[0];
    $query->execute();
    $search = $query->fetch();

    if ($search == 0) {
      // Partageons le fichier en mettant à jour la base de donnée
      $query = $db->prepare("INSERT INTO FILE_SHARE (id_file, id_folder, id_user_host, id_user_guest) VALUES (:id_file, :id_folder, :id_user_host, :id_user_guest)");
      $query->bindParam(':id_file', $fileId);
      $query->bindParam(':id_folder', $folderId);
      $query->bindParam(':id_user_host', $_SESSION["account"]["id"]);
      $query->bindParam(':id_user_guest', $idUserGuest);
      $query->execute();
      $_SESSION["shareFile"]["success"] = 1;
    } else {
      $_SESSION["shareFile"]["duplicate"] = 1;
    }
  } else {
    $_SESSION["shareFile"]["error"] = 1;
  }
} else {
  $_SESSION["shareFile"]["empty"] = 1;
}


if (isset($_SESSION["shareFile"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["shareFile"]["error"])) {
      echo "L'email est introuvable.";
    }
    if (isset($_SESSION["shareFile"]["success"])) {
      echo "Le fichier a été partagé.";
    }
    if (isset($_SESSION["shareFile"]["duplicate"])) {
      echo "Le fichier est déjà partagé.";
    }
    if (isset($_SESSION["shareFile"]["empty"])) {
      echo "La saisie est vide.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["shareFile"])) {
  unset($_SESSION["shareFile"]);
}
?>
