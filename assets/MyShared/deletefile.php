<?php
session_start();
include "../include/functions.php";

$fileId = $_POST["file"];

$db = connectDb();
// Supprimer le fichier partagé par un utilisateur
$query = $db->prepare("DELETE FROM FILE_SHARE WHERE id_file=:id_file AND id_user_guest=:id_user_guest");
$query->bindParam(':id_file', $fileId);
$query->bindParam(':id_user_guest', $_SESSION["account"]["id"]);
$query->execute();

$_SESSION["deleteFile"]["success"] = 1;


if (isset($_SESSION["deleteFile"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["deleteFile"]["success"])) {
      echo "Fichier supprimé avec succès.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["deleteFile"])) {
  unset($_SESSION["deleteFile"]);
}
?>
