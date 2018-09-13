<?php
session_start();
include "../include/functions.php";

$fileId = $_POST["file"];

$db = connectDb();
// Récupérons l'emplacement du fichier et sa taille
$query = $db->prepare("SELECT target, size FROM FILE WHERE id=:id");
$query->bindParam(':id', $fileId);
$query->execute();
$result = $query->fetch();

// Supprimons le fichier du serveur
unlink("../" . $result["target"]);

// Supprimons le fichier de la base de donnée
$query = $db->prepare("DELETE FROM FILE WHERE id=:id; DELETE FROM FILE_SHARE WHERE id_file=:id_file");
$query->bindParam(':id', $fileId);
$query->bindParam(':id_file', $fileId);
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
$currentStorageSize = $dataSize["current_storage_size"] - $result["size"];
$query->execute();

$_SESSION["delete"]["success"] = 1;


if (isset($_SESSION["delete"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["delete"]["success"])) {
      echo "Fichier supprimé avec succès.";
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
