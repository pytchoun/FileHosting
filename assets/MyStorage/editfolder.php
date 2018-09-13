<?php
session_start();
include "../include/functions.php";

$folderId = $_POST["folder"];
$folderName = $_POST["name"];
$folderName = verifyInput($folderName);

$db = connectDb();
// Récupérons l'emplacement du dossier
$query = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
$query->bindParam(':id', $folderId);
$query->execute();
$dataTarget = $query->fetch();

$directory = pathinfo($dataTarget['target'],PATHINFO_DIRNAME);
$oldFolderName = pathinfo($dataTarget['target'],PATHINFO_BASENAME);
$oldTarget = $dataTarget['target'];
$target = $directory . "/" . fixeAccent($folderName);

if ($folderName != null) {
  if (file_exists("../" . $target) AND "../" . $target != "../" . $oldTarget) {
    $_SESSION["folder"]["exist"] = 1;
  } else {
    // Récupérons la liste de ses dossiers fils
    $itemsFolderList = folderList($folderId);

    // Récupération des fichiers et dossiers pour mettre à jour leur chemin d'accès
    $selectTargetFile = $db->prepare("SELECT id, target FROM FILE WHERE id_folder=:id_folder");
    $updateTargetFile = $db->prepare("UPDATE FILE SET target=:target WHERE id_folder=:id_folder AND id=:id");
    $selectTargetFolder = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
    $updateTargetFolder = $db->prepare("UPDATE FOLDER SET target=:target WHERE id=:id");

    foreach ($itemsFolderList as $item) {
      $selectTargetFile->bindParam(':id_folder', $item["id"]);
      $selectTargetFile->execute();
      $fileTarget = $selectTargetFile->fetchAll();
      foreach ($fileTarget as $oldTargetFile) {
        $thisTargetFile = $oldTargetFile['target'];
        $thisIdFile = $oldTargetFile['id'];

        $updateTargetFile->bindParam(':id_folder', $item["id"]);
        $updateTargetFile->bindParam(':id', $thisIdFile);
        $updateTargetFile->bindParam(':target', $thisTargetFile);
        $thisTargetFile = str_replace($oldFolderName, fixeAccent($folderName), $thisTargetFile);
        $updateTargetFile->execute();
      }

      $selectTargetFolder->bindParam(':id', $item["id"]);
      $selectTargetFolder->execute();
      $folderTarget = $selectTargetFolder->fetchAll();
      foreach ($folderTarget as $oldTargetFolder) {
        $thisTargetFolder = $oldTargetFolder['target'];

        $updateTargetFolder->bindParam(':id', $item["id"]);
        $updateTargetFolder->bindParam(':target', $thisTargetFolder);
        $thisTargetFolder = str_replace($oldFolderName, fixeAccent($folderName), $thisTargetFolder);
        $updateTargetFolder->execute();
      }
    }

    // Récupérons les fichiers du dossier courant pour mettre à jour le chemin d'accès
    $selectTargetFile = $db->prepare("SELECT id, target FROM FILE WHERE id_folder=:id_folder");
    $selectTargetFile->bindParam(':id_folder', $folderId);
    $selectTargetFile->execute();
    $fileTarget = $selectTargetFile->fetchAll();

    $updateTargetFile = $db->prepare("UPDATE FILE SET target=:target WHERE id_folder=:id_folder AND id=:id");
    foreach ($fileTarget as $oldTargetFile) {
      $thisTargetFile = $oldTargetFile['target'];
      $thisIdFile = $oldTargetFile['id'];

      $updateTargetFile->bindParam(':target', $thisTargetFile);
      $updateTargetFile->bindParam(':id', $thisIdFile);
      $updateTargetFile->bindParam(':id_folder', $folderId);
      $thisTargetFile = str_replace($oldFolderName, fixeAccent($folderName), $thisTargetFile);
      $updateTargetFile->execute();
    }

    // Mis à jour du chemin d'accès du dossier
    $query = $db->prepare("UPDATE FOLDER SET name=:name, target=:target WHERE id=:id");
    $query->bindParam(':name', $folderName);
    $query->bindParam(':target', $target);
    $query->bindParam(':id', $folderId);
    $query->execute();

    rename("../" . $oldTarget, "../" . $target);

    $_SESSION["folder"]["name"] = 1;
  }
} else {
  $_SESSION["folder"]["error"] = 1;
}


if (isset($_SESSION["folder"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["folder"]["name"])) {
      echo "Mise à jour du nom du dossier.";
    }
    if (isset($_SESSION["folder"]["exist"])) {
      echo "Un dossier portant ce nom existe déjà dans ce dossier.";
    }
    if (isset($_SESSION["folder"]["error"])) {
      echo "Le dossier a besoin d'un nom.";
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
