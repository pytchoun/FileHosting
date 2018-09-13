<?php
session_start();
include "../include/functions.php";

$fileId = $_POST["file"];
$fileName = $_POST["name"];
$description = $_POST["description"];
$fileName = verifyInput($fileName);

$db = connectDb();
// Récupérons l'emplacement du fichier et son nom
$query = $db->prepare("SELECT target, name FROM FILE WHERE id=:id");
$query->bindParam(':id', $fileId);
$query->execute();
$dataTarget = $query->fetch();

$directory = pathinfo($dataTarget['target'],PATHINFO_DIRNAME);
$oldTarget = $dataTarget['target'];
$target = $directory . "/" . fixeAccent($fileName);

if ($fileName != null) {
  if (file_exists("../" . $target) AND "../" . $target != "../" . $oldTarget) {
    $_SESSION["file"]["exist"] = 1;
  } else {
    rename("../" . $oldTarget, "../" . $target);
    // Mis à jour du fichier dans la base de donnée
    $query = $db->prepare("UPDATE FILE SET description=:description, name=:name, target=:target WHERE id=:id");
    $query->bindParam(':description', $description);
    $query->bindParam(':name', $fileName);
    $query->bindParam(':target', $target);
    $query->bindParam(':id', $fileId);
    $query->execute();
    $_SESSION["file"]["description"] = 1;
  }
} else {
  $_SESSION["file"]["error"] = 1;
}


if (isset($_SESSION["file"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["file"]["description"])) {
      echo "Mise à jour de la description.";
    }
    if (isset($_SESSION["file"]["exist"])) {
      echo "Un fichier portant ce nom existe déjà dans ce dossier.";
    }
    if (isset($_SESSION["file"]["error"])) {
      echo "Le fichier a besoin d'un nom.";
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
