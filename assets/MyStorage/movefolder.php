<?php
session_start();
include "../include/functions.php";

$db = connectDb();
$folderId = $_POST["folder"];
$newFolder = $_POST["newFolder"];
$linkToFolderToHome = $newFolder;
$moveFolderOk = true;

if ($newFolder != null) {
  // Sélectionner le dossier à déplacer
  $query = $db->prepare("SELECT name, target FROM FOLDER WHERE id=:id");
  $query->bindParam(':id', $folderId);
  $query->execute();
  $selectThisFolder = $query->fetch();
  $thisFolderTarget = $selectThisFolder["target"];
  $thisFolderName = $selectThisFolder["name"];

  if ($newFolder == 0) {
    // Le dossier est déplacé à la racine
    $targetDirectory = "../uploads/";
    $targetUser = $_SESSION["account"]["id"]."/";
    $targetFolder = $targetDirectory . $targetUser;
    $newFolderTarget = $targetFolder;
    $target = $targetFolder . fixeAccent($thisFolderName);
  } else {
    // Sélectionner le dossier d'accueil
    $query = $db->prepare("SELECT target FROM FOLDER WHERE id=:id");
    $query->bindParam(':id', $newFolder);
    $query->execute();
    $selectNewFolder = $query->fetch();
    $newFolderTarget = $selectNewFolder["target"];

    // Le dossier est déplacé dans un autre dossier
    $target = $newFolderTarget . "/" . fixeAccent($thisFolderName);

    // Le dossier d'accueil passe t-il par le dossier à déplacer ?
    $query = $db->prepare("SELECT id_folder FROM FOLDER WHERE id=:id");
    while ($linkToFolderToHome != 0) {
      $query->bindParam(':id', $linkToFolderToHome);
      $query->execute();
      $idFromThisFolder = $query->fetch();
      $itemToRename[] = $idFromThisFolder;
      $linkToFolderToHome = $idFromThisFolder["id_folder"];
      if ($linkToFolderToHome == $folderId) {
        $moveFolderOk = false;
        break;
      }
    }
  }

  if ($moveFolderOk == true) {
    // Un dossier portant le nom du dossier à déplacer existe t-il dans le dossier d'accueil ?
    if (file_exists("../" . $newFolderTarget . "/" . fixeAccent($thisFolderName))) {
      $_SESSION["folder"]["exist"] = 1;
    } else {
      // Sélectionner tous les dossiers fils
      $itemsFolderList = folderList($folderId);

      // Sélectionner tous les dossiers et fichiers fils
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
          $thisTargetFile = str_replace($thisFolderTarget, $target, $thisTargetFile);
          $updateTargetFile->execute();
        }

        $selectTargetFolder->bindParam(':id', $item["id"]);
        $selectTargetFolder->execute();
        $folderTarget = $selectTargetFolder->fetchAll();
        foreach ($folderTarget as $oldTargetFolder) {
          $thisTargetFolder = $oldTargetFolder['target'];

          $updateTargetFolder->bindParam(':id', $item["id"]);
          $updateTargetFolder->bindParam(':target', $thisTargetFolder);
          $thisTargetFolder = str_replace($thisFolderTarget, $target, $thisTargetFolder);
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
        $thisTargetFile = str_replace($thisFolderTarget, $target, $thisTargetFile);
        $updateTargetFile->execute();
      }

      // Mis à jour du chemin d'accès du dossier
      $query = $db->prepare("UPDATE FOLDER SET id_folder=:id_folder, target=:target WHERE id=:id");
      $query->bindParam(':id_folder', $newFolder);
      $query->bindParam(':target', $target);
      $query->bindParam(':id', $folderId);
      $query->execute();

      rename("../" . $thisFolderTarget, "../" . $newFolderTarget . "/" . fixeAccent($thisFolderName));

      $_SESSION["folder"]["moved"] = 1;
    }
  } else {
    $_SESSION["folder"]["notMoved"] = 1;
  }
} else {
  $_SESSION["folder"]["empty"] = 1;
}


if (isset($_SESSION["folder"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["folder"]["moved"])) {
      echo "Dossier déplacé avec succès.";
    }
    if (isset($_SESSION["folder"]["notMoved"])) {
      echo "Dossier non déplacé, vous n'avez pas de dossier parent.";
    }
    if (isset($_SESSION["folder"]["empty"])) {
      echo "Aucun dossier sélectionné.";
    }
    if (isset($_SESSION["folder"]["exist"])) {
      echo "Un dossier portant ce nom existe déjà dans ce dossier.";
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
