<?php
session_start();
include "../include/functions.php";

$db = connectDb();
$folderName = $_POST["name"];
$myFolder = $_POST["folder"];
$folderOk = 1;

if ($myFolder != 0) {
  // Récupérons l'emplacement du dossier
  $query = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
  $query->bindParam(':id', $myFolder);
  $query->execute();
  $folderTarget = $query->fetch();

  $target_dir = "../" . $folderTarget["target"]."/";
  $target_dir_db = $folderTarget["target"]."/";
  $target_user = $_SESSION["account"]["id"]."/";
  $target_folder = $target_dir . fixeAccent($folderName);
  $target_folder_db = $target_dir_db . fixeAccent($folderName);
} else {
  $target_dir = "../../uploads/";
  $target_dir_db = "../uploads/";
  $target_user = $_SESSION["account"]["id"]."/";
  $target_folder = $target_dir . $target_user . fixeAccent($folderName);
  $target_folder_db = $target_dir_db . $target_user . fixeAccent($folderName);
}

// Vérifier si le dossier à un nom
if (empty($_POST["name"])) {
  $folderOk = 0;
  $_SESSION["folder"]["name"] = 1;
}

// Vérifier si le dossier existe déjà
if (file_exists($target_folder)) {
  $folderOk = 0;
  $_SESSION["folder"]["exist"] = 1;
} else {
  mkdir($target_folder, 0777, true);
}

if ($folderOk == 1) {
  // Insertion du dossier en base de donnée
  $query = $db->prepare("INSERT INTO FOLDER (id_user, id_folder, name, target) VALUES (:id_user, :id_folder, :name, :target)");
  $query->bindParam(':id_user', $_SESSION["account"]["id"]);
  $query->bindParam(':id_folder', $myFolder);
  $query->bindParam(':name', $folderName);
  $query->bindParam(':target', $target_folder_db);
  $query->execute();
  $_SESSION["folder"]["success"] = 1;
} else {
  $_SESSION["folder"]["error"] = 1;
}


if (isset($_SESSION["folder"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["folder"]["error"])) {
      echo "Une erreur est apparue.<br>";
    }
    if (isset($_SESSION["folder"]["exist"])) {
      echo "Le dossier existe déjà.<br>";
    }
    if (isset($_SESSION["folder"]["name"])) {
      echo "Le dossier a besoin d'un nom.<br>";
    }
    if (isset($_SESSION["folder"]["success"])) {
      echo "Le dossier a été enregistré.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["folder"])) {
  unset($_SESSION["folder"]);
}
?>
