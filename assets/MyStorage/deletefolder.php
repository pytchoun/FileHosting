<?php
session_start();
include "../include/functions.php";

$folderId = $_POST["folder"];
$totalFolderSize = 0;

$db = connectDb();
// Récupérons l'emplacement du dossier
$query = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
$query->bindParam(':id', $folderId);
$query->execute();
$result = $query->fetch();

// Récupérons la taille de tous les fichiers dans le dossier
$query = $db->prepare("SELECT size FROM FILE WHERE id_folder=:id_folder");
$query->bindParam(':id_folder', $folderId);
$query->execute();
$fileSize = $query->fetchAll();
foreach ($fileSize as $size) {
  $totalFolderSize = $totalFolderSize + $size["size"];
}

// Récupérons la liste de ses dossiers fils
$itemsFolderList = folderList($folderId);

// Supprimons tous les dossiers et fichiers dans ce dossier
$queryDeleteItem = $db->prepare("DELETE FROM FILE WHERE id_folder=:id_folder;
  DELETE FROM FILE_SHARE WHERE id_folder=:id_folder;
  DELETE FROM FOLDER WHERE id=:id_folder;
  DELETE FROM FOLDER_SHARE WHERE id_folder=:id_folder;
  DELETE FROM FOLDER_SHARE WHERE id_parent_folder=:id_folder");
// Récupérons la taille de tous les fichiers dans ce dossier
$queryFileSize = $db->prepare("SELECT size FROM FILE WHERE id_folder=:id_folder");

foreach ($itemsFolderList as $item) {
  $queryFileSize->bindParam(':id_folder', $item["id"]);
  $queryFileSize->execute();
  $fileSize = $queryFileSize->fetchAll();
  foreach ($fileSize as $size) {
    $totalFolderSize = $totalFolderSize + $size["size"];
  }
  $queryDeleteItem->bindParam(':id_folder', $item["id"]);
  $queryDeleteItem->execute();
  $queryDeleteItem->closeCursor();
}

// Supprimons le dossier et ses fichiers
$query = $db->prepare("DELETE FROM FOLDER WHERE id=:id;
  DELETE FROM FOLDER_SHARE WHERE id_folder=:id;
  DELETE FROM FOLDER_SHARE WHERE id_parent_folder=:id;
  DELETE FROM FILE WHERE id_folder=:id;
  DELETE FROM FILE_SHARE WHERE id_folder=:id");
$query->bindParam(':id', $folderId);
$query->execute();

// Récupérons la taille de stockage de l'utilisateur
$query = $db->prepare("SELECT current_storage_size FROM USER WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->execute();
$dataSize = $query->fetch();

// Mis à jour de la taille de stockage de l'utilisateur
$query = $db->prepare("UPDATE USER SET current_storage_size=:current_storage_size WHERE id=:id");
$query->bindParam(':id', $_SESSION["account"]["id"]);
$query->bindParam(':current_storage_size', $currentStorageSize);
$currentStorageSize = $dataSize["current_storage_size"] - $totalFolderSize;
$query->execute();

// Supprimons le dossier du serveur
deleteFolderRecursive("../" . $result["target"]);

$_SESSION["delete"]["success"] = 1;


if (isset($_SESSION["delete"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["delete"]["success"])) {
      echo "Dossier supprimé avec succès.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["delete"])) {
  unset($_SESSION["delete"]);
}
?>
