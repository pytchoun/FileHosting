<?php
session_start();
include "../include/functions.php";

$db = connectDb();
$fileId = $_POST["file"];
$newFolder = $_POST["newFolder"];

if ($newFolder != null) {
  // Récupérons l'emplacement du fichier et son nom
  $query = $db->prepare("SELECT name, target FROM FILE WHERE id=:id");
  $query->bindParam(':id', $fileId);
  $query->execute();
  $fileOldTarget = $query->fetch();

  if ($newFolder == 0) {
    // Le fichier est déplacé à la racine
    $target_dir = "../uploads/";
    $target_user = $_SESSION["account"]["id"]."/";
    $target_folder = $target_dir . $target_user;
    $fileTarget["target"] = $target_folder;
    $target = $target_folder . fixeAccent($fileOldTarget["name"]);
  } else {
    // Récupérons l'emplacement du dossier d'accueil
    $query = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
    $query->bindParam(':id', $newFolder);
    $query->execute();
    $fileTarget = $query->fetch();

    $target = $fileTarget["target"] . "/" . fixeAccent($fileOldTarget["name"]);
  }

  if (file_exists("../" . $fileTarget["target"] . "/" . fixeAccent($fileOldTarget["name"]))) {
    $_SESSION["file"]["exist"] = 1;
  } else {
    rename("../" . $fileOldTarget["target"], "../" . $fileTarget["target"] . "/" . fixeAccent($fileOldTarget["name"]));

    // Mis à jour du fichier dans la base de donnée
    $query = $db->prepare("UPDATE FILE SET id_folder=:id_folder, target=:target WHERE id=:id");
    $query->bindParam(':id_folder', $newFolder);
    $query->bindParam(':target', $target);
    $query->bindParam(':id', $fileId);
    $query->execute();
    $_SESSION["file"]["moved"] = 1;
  }
} else {
  $_SESSION["file"]["empty"] = 1;
}


if (isset($_SESSION["file"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["file"]["moved"])) {
      echo "Fichier déplacé avec succès.";
    }
    if (isset($_SESSION["file"]["empty"])) {
      echo "Aucun dossier sélectionné.";
    }
    if (isset($_SESSION["file"]["exist"])) {
      echo "Un fichier portant ce nom existe déjà dans ce dossier.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["file"])) {
  unset($_SESSION["file"]);
}
?>
