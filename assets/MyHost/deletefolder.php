<?php
session_start();
include "../include/functions.php";

$folderId = $_POST["folder"];
$idUserGuest = $_POST["userGuest"];

$db = connectDb();
// Supprimer le dossier partagé à son utilisateur
$query = $db->prepare("DELETE FROM FOLDER_SHARE WHERE id_folder=:id_folder AND id_user_guest=:id_user_guest");
$query->bindParam(':id_folder', $folderId);
$query->bindParam(':id_user_guest', $idUserGuest);
$query->execute();

$_SESSION["deleteFolder"]["success"] = 1;


if (isset($_SESSION["deleteFolder"])) {
  ?>
  <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    <?php
    if (isset($_SESSION["deleteFolder"]["success"])) {
      echo "Dossier supprimé avec succès.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <?php
}

if (isset($_SESSION["deleteFolder"])) {
  unset($_SESSION["deleteFolder"]);
}
?>
