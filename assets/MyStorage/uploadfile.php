<?php
session_start();
include "../include/functions.php";

$myFolder = $_POST["folder"];
$fileName = $_FILES['upload-file']['name'];
$fileTmpName = $_FILES['upload-file']['tmp_name'];
$fileSize = $_FILES['upload-file']['size'];
$fileType = $_FILES['upload-file']['type'];
$uploadOk = 1;

$db = connectDb();
if ($myFolder != 0) {
  // Récupérons l'emplacement du dossier
  $query = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
  $query->bindParam(':id', $myFolder);
  $query->execute();
  $folderTarget = $query->fetch();

  $target_dir = "../" . $folderTarget["target"]."/";
  $target_dir_db = $folderTarget["target"]."/";
  $target_user = $_SESSION["account"]["id"]."/";
  $target_file = $target_dir . fixeAccent(basename($fileName));
  $target_file_db = $target_dir_db . fixeAccent(basename($fileName));
} else {
  $target_dir = "../../uploads/";
  $target_dir_db = "../uploads/";
  $target_user = $_SESSION["account"]["id"]."/";
  $target_file = $target_dir . $target_user . fixeAccent(basename($fileName));
  $target_file_db = $target_dir_db . $target_user . fixeAccent(basename($fileName));
}

// Récupérons la taille du stockage de l'utilisateur
$query = $db->prepare("SELECT current_storage_size, max_storage_size, file_size_limit FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$dataSize = $query->fetch();
$currentStorageSize = $fileSize + $dataSize["current_storage_size"];

if ($currentStorageSize > $dataSize["max_storage_size"]) {
  $uploadOk = 0;
  $_SESSION["file"]["limit"] = 1;
}

// Vérifier si le fichier existe déjà
if (file_exists($target_file)) {
  $uploadOk = 0;
  $_SESSION["file"]["exist"] = 1;
}

if ($fileSize > $dataSize["file_size_limit"]) {
  $uploadOk = 0;
  $_SESSION["file"]["size"] = 1;
}

if ($uploadOk == 1) {
  if ($myFolder != 0) {
    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
  } else {
    if (!file_exists($target_dir.$target_user)) {
      mkdir($target_dir.$target_user, 0777, true);
    }
  }
  if (move_uploaded_file($fileTmpName, $target_file)) {
    // Insertion du fichier en base de donnée
    $query = $db->prepare("INSERT INTO FILE (id_user, id_folder, name, type, size, target) VALUES (:id_user, :id_folder, :name, :type, :size, :target)");
    $query->bindParam(':id_user', $_SESSION["account"]["id"]);
    $query->bindParam(':id_folder', $myFolder);
    $query->bindParam(':name', $fileName);
    $query->bindParam(':type', $fileType);
    $query->bindParam(':size', $fileSize);
    $query->bindParam(':target', $target_file_db);
    $query->execute();

    $_SESSION["file"]["success"] = 1;

    // Mis à jour de la taille de stockage de l'utilisateur
    $query = $db->prepare("UPDATE USER SET current_storage_size=:current_storage_size WHERE id=:id");
    $query->bindParam(':id', $_SESSION["account"]["id"]);
    $query->bindParam(':current_storage_size', $currentStorageSize);
    $query->execute();
  } else {
    $uploadOk = 0;
    $_SESSION["file"]["upload_error"] = 1;
  }
} else {
  $_SESSION["file"]["error"] = 1;
}


if (isset($_SESSION["file"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["file"]["error"])) {
      echo "Une erreur est apparue.<br>";
    }
    if (isset($_SESSION["file"]["exist"])) {
      echo "Le fichier existe déjà.<br>";
    }
    if (isset($_SESSION["file"]["size"])) {
      echo "Le fichier est trop lourd.<br>";
    }
    if (isset($_SESSION["file"]["success"])) {
      echo "Le fichier a été enregistré.";
    }
    if (isset($_SESSION["file"]["upload_error"])) {
      echo "Le fichier n'a pas pu être enregistré.";
    }
    if (isset($_SESSION["file"]["limit"])) {
      echo "Vous avez atteint votre limite de stockage.";
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
