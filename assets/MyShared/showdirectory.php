<?php
session_start();
include "../include/functions.php";

$page = "shared.php";
$direction = "ASC";
$idFolder = $_POST["directory"];

$db = connectDb();
if ($idFolder == 0) {
  $folderShared['name'] = "";

  // Obtenir tous les fichiers partagés par un utilisateur dans le dossier courant
  $query = $db->prepare("SELECT * FROM FILE
    INNER JOIN FILE_SHARE ON FILE_SHARE.id_user_host=FILE.id_user AND FILE_SHARE.id_file=FILE.id
    INNER JOIN USER ON FILE.id_user=USER.id
    WHERE FILE_SHARE.id_user_guest=:id_user_guest
    ORDER BY FILE.name ASC");
  $query->bindParam(':id_user_guest', $_SESSION["account"]["id"]);
  $query->execute();
  $fileResult = $query->fetchAll();

  // Obtenir tous les dossiers partagés par un utilisateur dans le dossier courant
  $query = $db->prepare("SELECT * FROM FOLDER
    INNER JOIN FOLDER_SHARE ON FOLDER_SHARE.id_user_host=FOLDER.id_user AND FOLDER_SHARE.id_folder=FOLDER.id
    INNER JOIN USER ON FOLDER.id_user=USER.id
    WHERE FOLDER_SHARE.id_user_guest=:id_user_guest
    ORDER BY FOLDER.name ASC");
  $query->bindParam(':id_user_guest', $_SESSION["account"]["id"]);
  $query->execute();
  $folderResult = $query->fetchAll();
} else {
  $myHostName = $_POST["myHostName"];
  $folderShared['name'] = $myHostName;

  // Obtenir tous les fichiers partagés par un utilisateur dans le dossier courant
  $query = $db->prepare("SELECT * FROM FILE WHERE id_folder=:id_folder ORDER BY name ASC");
  $query->bindParam(':id_folder', $idFolder);
  $query->execute();
  $fileResult = $query->fetchAll();

  // Obtenir tous les dossiers partagés par un utilisateur dans le dossier courant
  $query = $db->prepare("SELECT * FROM FOLDER WHERE id_folder=:id_folder ORDER BY name ASC");
  $query->bindParam(':id_folder', $idFolder);
  $query->execute();
  $folderResult = $query->fetchAll();
}

// Obtenir le dossier parent du dossier courant
$query = $db->prepare("SELECT id_folder FROM FOLDER WHERE id=:id");
$query->bindParam(':id', $idFolder);
$query->execute();
$myFolderParent = $query->fetch();
?>
<div class="row">
  <div class="col-md-2">
    <p>
      <a class="btn btn-teal btn-block" href="storage.php" style="color:white;"><i class="fas fa-file"></i> Mon stockage</a>
    </p>
  </div>
  <div class="col-md-2">
    <p>
      <input type="text" id="fileSearch" onkeyup="fileSearch()" class="form-control mr-sm-2" placeholder="Rechercher" aria-label="Rechercher">
    </p>
  </div>
</div>
<?php
if ($idFolder != 0) { ?>
  <div class="row">
    <div class="col-md-2">
      <p>
        <button type="button" class="btn btn-teal btn-block" onclick="showSharedDirectory(<?php echo $myFolderParent['id_folder']; ?>, '<?php echo $folderShared['name']; ?>')"><i class="fas fa-long-arrow-alt-left"></i> Retour au dossier parent</button>
      </p>
    </div>
  </div>
<?php } ?>
<div class="row">
  <div class="col-md-12">
    <div id="sort-file-size-storage">
      <?php
      if (empty($fileResult) AND empty($folderResult)) { ?>
        <p class="text-center mt-5">
          Vous n'avez aucun fichier partagé.
        </p>
      <?php } else { ?>
        <div class="table-responsive">
          <table class="table table-borderless table-hover" id="storageTable">
            <thead class="border-bottom">
              <tr>
                <th style="cursor: pointer;" onclick="sortTable(0)">Nom <i class="fas fa-sort"></i></th>
                <th style="cursor: pointer;" onclick="sortTable(1)">Propriétaire <i class="fas fa-sort"></i></th>
                <th style="cursor: pointer;" onclick="sortFileSize('<?php echo $direction; ?>', '<?php echo $page; ?>', <?php echo $idFolder; ?>, '<?php echo $folderShared['name']; ?>')">Taille du fichier <i class="fas fa-sort"></i></th>
                <th style="cursor: pointer;" onclick="sortTable(3)">Type de fichier <i class="fas fa-sort"></i></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($folderResult as $key => $folderShared) { ?>
                <tr>
                  <td><i class="fas fa-folder fa-lg"></i> <?php echo $folderShared['3']; ?></td>
                  <td>
                    <?php
                    if ($idFolder != 0) {
                      $folderShared['name'] = $myHostName;
                      echo $folderShared['name'];
                    } else {
                      echo $folderShared['name'];
                    }
                    ?>
                  </td>
                  <td></td>
                  <td></td>
                  <td>
                    <button type="button" class="btn btn-teal btn-block" onclick="showSharedDirectory(<?php echo $folderShared['0']; ?>, '<?php echo $folderShared['name']; ?>')"><i class="fas fa-sign-in-alt"></i> Ouvrir</button>
                  </td>
                  <td>
                    <button type="button" class="btn btn-cyan btn-block" style="color:white;" onclick="downloadFolder('<?php echo $folderShared['target']; ?>', '<?php echo fixeAccent($folderShared["3"]); ?>')"><i class="fas fa-file-download"></i> Télécharger</button>
                  </td>
                  <?php
                  if ($idFolder == 0) { ?>
                    <td>
                      <button type="button" class="btn btn-danger btn-block" name="delete-file" onclick="deleteSharedFolder(<?php echo $folderShared['0']; ?>, <?php echo $idFolder; ?>)"><i class="fas fa-trash"></i> Supprimer</button>
                    </td>
                  <?php } ?>
                </tr>
              <?php }
              foreach ($fileResult as $row => $fileShared) {
                $fileShared['size'] = human_filesize($fileShared['size']);
                ?>
                <tr>
                  <td><?php echo $fileShared['3']; ?></td>
                  <td>
                    <?php
                    if ($idFolder != 0) {
                      $fileShared['name'] = $myHostName;
                      echo $fileShared['name'];
                    } else {
                      echo $fileShared['name'];
                    }
                    ?>
                  </td>
                  <td><?php echo $fileShared['size']; ?></td>
                  <td><?php echo $fileShared['type']; ?></td>
                  <td>
                    <button type="button" class="btn btn-teal btn-block" data-toggle="modal" data-target="#infoUpload<?php echo $fileShared['0']; ?>">
                      <i class="far fa-eye"></i> Consulter
                    </button>
                    <div class="modal fade" id="infoUpload<?php echo $fileShared['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="infoUploadTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="infoUploadTitle">Information du fichier</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <?php
                            $imageFileType = strtolower(pathinfo($fileShared['target'],PATHINFO_EXTENSION));
                            if($imageFileType == "jpg" OR $imageFileType == "png" OR $imageFileType == "jpeg" OR $imageFileType == "gif" ) { ?>
                              <div class="text-center"><img style="max-width:50%; max-height:50%;" src="<?php echo $fileShared['target']; ?>" alt="<?php echo $fileShared['3']; ?>"></div>
                            <?php } ?>
                            <p>
                              Description du fichier <br>
                              <?php
                              if ($fileShared['description'] == "" OR $fileShared['description'] == null) {
                                $fileShared['description'] = "Aucune description";
                              }
                              echo $fileShared['description']; ?>
                            </p>
                          </div>
                          <div class="modal-footer">
                            <div class="col-md-12">
                              <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <a class="btn btn-cyan btn-block" href="<?php echo $fileShared['target']; ?>" style="color:white;" download><i class="fas fa-file-download"></i> Télécharger</a>
                  </td>
                  <?php
                  if ($idFolder == 0) { ?>
                    <td>
                      <button type="button" class="btn btn-danger btn-block" name="delete-shared-file" onclick="deleteSharedFile(<?php echo $fileShared['0']; ?>, <?php echo $idFolder; ?>)"><i class="fas fa-trash"></i> Supprimer</button>
                    </td>
                  <?php } ?>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
