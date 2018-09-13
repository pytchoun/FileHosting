<?php
session_start();
include "../include/functions.php";

$page = "storage.php";
$direction = $_POST["direction"];
$idFolder = $_POST["directory"];

$db = connectDb();
// Obtenir tous les fichiers de l'utilisateur dans le dossier courant
$query = $db->prepare("SELECT * FROM FILE INNER JOIN USER ON FILE.id_user=USER.id WHERE id_user=:id_user AND id_folder=:id_folder ORDER BY FILE.size $direction");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->bindParam(':id_folder', $idFolder);
$query->execute();
$fileResult = $query->fetchAll();

// Obtenir tous les dossiers de l'utilisateur dans le dossier courant
$query = $db->prepare("SELECT * FROM FOLDER INNER JOIN USER ON FOLDER.id_user=USER.id WHERE id_user=:id_user AND id_folder=:id_folder ORDER BY FOLDER.name ASC");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->bindParam(':id_folder', $idFolder);
$query->execute();
$folderResult = $query->fetchAll();

// Obtenir tous les dossiers de l'utilisateur
$query = $db->prepare("SELECT * FROM FOLDER WHERE id_user=:id_user ORDER BY name ASC");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->execute();
$folderList = $query->fetchAll();

// Obtenir le dossier parent du dossier courant
$query = $db->prepare("SELECT id_folder FROM FOLDER WHERE id=:id");
$query->bindParam(':id', $idFolder);
$query->execute();
$myFolderParent = $query->fetch();

if ($direction == "ASC") {
  $direction = "DESC";
} elseif ($direction == "DESC") {
  $direction = "ASC";
}

if (empty($fileResult) AND empty($folderResult)) { ?>
  <p class="text-center mt-5">
    Vous n'avez aucun fichier.
  </p>
<?php } else { ?>
  <div class="table-responsive">
    <table class="table table-borderless table-hover" id="storageTable">
      <thead class="border-bottom">
        <tr>
          <th style="cursor: pointer;" onclick="sortTable(0)">Nom <i class="fas fa-sort"></i></th>
          <th style="cursor: pointer;" onclick="sortTable(1)">Propriétaire <i class="fas fa-sort"></i></th>
          <th style="cursor: pointer;" onclick="sortFileSize('<?php echo $direction; ?>', '<?php echo $page; ?>', <?php echo $idFolder; ?>)">Taille du fichier <i class="fas fa-sort"></i></th>
          <th style="cursor: pointer;" onclick="sortTable(3)">Type de fichier <i class="fas fa-sort"></i></th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($folderResult as $key => $folder) {
          if ($folder['name'] == $_SESSION["account"]["name"]) {
            $folder['name'] = "Moi";
          } ?>
          <tr>
            <td>
              <i class="fas fa-folder fa-lg"></i> <?php echo $folder['3']; ?>
              <i class="fas fa-edit fa-xs" data-toggle="modal" data-target="#editFolder<?php echo $folder['0']; ?>"></i>
              <div class="modal fade" id="editFolder<?php echo $folder['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="editFolderTitle<?php echo $folder['0']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editFolderTitle<?php echo $folder['0']; ?>">Nom du dossier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group col">
                        <label>Nom du dossier</label>
                        <input type="text" class="form-control" id="folderName<?php echo $folder['0']; ?>" value="<?php echo $folder['3']; ?>" placeholder="<?php echo $folder['3']; ?>" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                      </div>
                      <div class="col-md-6">
                        <button type="button" class="btn btn-orange btn-block" onclick="editFolder(<?php echo $folder['0']; ?>, folderName<?php echo $folder['0']; ?>.value, <?php echo $idFolder; ?>)" data-dismiss="modal">Renommer le dossier</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td><?php echo $folder['name']; ?></td>
            <td></td>
            <td></td>
            <td>
              <button type="button" class="btn btn-teal btn-block" onclick="showDirectory(<?php echo $folder['0']; ?>)"><i class="fas fa-sign-in-alt"></i> Ouvrir</button>
            </td>
            <td>
              <button type="button" class="btn btn-orange btn-block" data-toggle="modal" data-target="#moveFolderUpload<?php echo $folder['0']; ?>">
                <i class="fas fa-retweet"></i> Déplacer
              </button>
              <div class="modal fade" id="moveFolderUpload<?php echo $folder['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="moveFolderUploadTitle<?php echo $folder['0']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="moveFolderUploadTitle<?php echo $folder['0']; ?>">Déplacer le dossier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group col">
                        <label>Dossier de destination</label>
                        <select id="newFolderTarget<?php echo $folder['0']; ?>" class="form-control" name="move-folder" required>
                          <option disabled selected value="">--Dossier de destination--</option>
                          <?php
                          $checkFolder = 0;
                          foreach ($folderList as $key => $list) {
                            if ($folder['id_folder'] != 0 AND $checkFolder == 0) { ?>
                              <option value='0'>Dossier racine</option>
                            <?php
                            $checkFolder = 1;
                            }
                            if ($folder['0'] != $list["id"] AND $folder['id_folder'] != $list["id"]) { ?>
                              <option value='<?php echo $list["id"]; ?>'><?php echo $list["name"]; ?></option>
                            <?php }
                          } ?>
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                      </div>
                      <div class="col-md-6">
                        <button type="button" class="btn btn-orange btn-block" name="move-file" onclick="moveFolder(<?php echo $folder['0']; ?>, newFolderTarget<?php echo $folder['0']; ?>.value, <?php echo $idFolder; ?>)" data-dismiss="modal">Déplacer le dossier</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <button type="button" class="btn btn-cyan btn-block" style="color:white;" onclick="downloadFolder('<?php echo $folder['target']; ?>', '<?php echo fixeAccent($folder["3"]); ?>')"><i class="fas fa-file-download"></i> Télécharger</button>
            </td>
            <td>
              <button type="button" class="btn btn-green btn-block" data-toggle="modal" data-target="#shareFolderUpload<?php echo $folder['0']; ?>">
                <i class="fas fa-share-alt"></i> Partager
              </button>
              <div class="modal fade" id="shareFolderUpload<?php echo $folder['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="shareFolderUploadTitle<?php echo $folder['0']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="shareFolderUploadTitle<?php echo $folder['0']; ?>">Partage du dossier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group col">
                        <label>Adresse email</label>
                        <input type="email" class="form-control" id="guestEmail<?php echo $folder['0']; ?>" name="email" placeholder="Entrer l'adresse email de votre destinataire" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                      </div>
                      <div class="col-md-6">
                        <button type="button" class="btn btn-orange btn-block" name="share-file" onclick="shareFolder(guestEmail<?php echo $folder['0']; ?>.value, <?php echo $folder['0']; ?>, <?php echo $idFolder; ?>)" data-dismiss="modal">Partager le dossier</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <button type="button" class="btn btn-danger btn-block" name="delete-file" onclick="deleteFolder(<?php echo $folder['0']; ?>, <?php echo $idFolder; ?>)"><i class="fas fa-trash"></i> Supprimer</button>
            </td>
          </tr>
        <?php }
        foreach ($fileResult as $row => $file) {
          if ($file['name'] == $_SESSION["account"]["name"]) {
            $file['name'] = "Moi";
          }
          $file['size'] = human_filesize($file['size']);
          ?>
          <tr>
            <td><?php echo $file['3']; ?></td>
            <td><?php echo $file['name']; ?></td>
            <td><?php echo $file['size']; ?></td>
            <td><?php echo $file['type']; ?></td>
            <td>
              <button type="button" class="btn btn-teal btn-block" data-toggle="modal" data-target="#infoUpload<?php echo $file['0']; ?>">
                <i class="far fa-eye"></i> Consulter
              </button>
              <div class="modal fade" id="infoUpload<?php echo $file['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="infoUploadTitle<?php echo $file['0']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="infoUploadTitle<?php echo $file['0']; ?>">Information du fichier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <?php
                      $imageFileType = strtolower(pathinfo($file['target'],PATHINFO_EXTENSION));
                      if($imageFileType == "jpg" OR $imageFileType == "png" OR $imageFileType == "jpeg" OR $imageFileType == "gif" ) { ?>
                        <div class="text-center"><img style="max-width:50%; max-height:50%;" src="<?php echo $file['target']; ?>" alt="<?php echo $file['3']; ?>"></div>
                      <?php } ?>
                      <div class="form-group col">
                        <label>Description du fichier</label>
                        <textarea name="file-description" class="form-control" id="fileDescription<?php echo $file['0']; ?>" rows="3" placeholder="Aucune description"><?php echo $file['description']; ?></textarea>
                      </div>
                      <div class="form-group col">
                        <label>Nom du fichier</label>
                        <input type="text" class="form-control" id="fileName<?php echo $file['0']; ?>" name="file-name" value="<?php echo $file['3']; ?>" placeholder="<?php echo $file['3']; ?>" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                      </div>
                      <div class="col-md-6">
                        <button type="button" class="btn btn-orange btn-block" name="edit-file-description" onclick="editFile(<?php echo $file['0']; ?>, fileName<?php echo $file['0']; ?>.value, fileDescription<?php echo $file['0']; ?>.value, <?php echo $idFolder; ?>)" data-dismiss="modal">Modifier la description</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <button type="button" class="btn btn-orange btn-block" data-toggle="modal" data-target="#moveFileUpload<?php echo $file['0']; ?>">
                <i class="fas fa-retweet"></i> Déplacer
              </button>
              <div class="modal fade" id="moveFileUpload<?php echo $file['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="moveFileUploadTitle<?php echo $file['0']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="moveFileUploadTitle<?php echo $file['0']; ?>">Déplacer le fichier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group col">
                        <label>Dossier de destination</label>
                        <select id="moveToThisFolder<?php echo $file['0']; ?>" class="form-control" name="move-folder" required>
                          <option disabled selected value="">--Dossier de destination--</option>
                          <?php
                          $checkFolder = 0;
                          foreach ($folderList as $key => $list) {
                            if ($file['id_folder'] != 0 AND $checkFolder == 0) { ?>
                              <option value='0'>Dossier racine</option>
                            <?php
                            $checkFolder = 1;
                            }
                            if ($file['id_folder'] != $list["id"]) { ?>
                              <option value='<?php echo $list["id"]; ?>'><?php echo $list["name"]; ?></option>
                            <?php }
                          } ?>
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                      </div>
                      <div class="col-md-6">
                        <button type="button" class="btn btn-orange btn-block" name="move-file" onclick="moveFile(<?php echo $file['0']; ?>, moveToThisFolder<?php echo $file['0']; ?>.value, <?php echo $idFolder; ?>)" data-dismiss="modal">Déplacer le fichier</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <a class="btn btn-cyan btn-block" href="<?php echo $file['target']; ?>" style="color:white;" download><i class="fas fa-file-download"></i> Télécharger</a>
            </td>
            <td>
              <button type="button" class="btn btn-green btn-block" data-toggle="modal" data-target="#shareFileUpload<?php echo $file['0']; ?>">
                <i class="fas fa-share-alt"></i> Partager
              </button>
              <div class="modal fade" id="shareFileUpload<?php echo $file['0']; ?>" tabindex="-1" role="dialog" aria-labelledby="shareFileUploadTitle<?php echo $file['0']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="shareFileUploadTitle<?php echo $file['0']; ?>">Partage du fichier</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group col">
                        <label>Adresse email</label>
                        <input type="email" class="form-control" id="guestFileEmail<?php echo $file['0']; ?>" name="email" placeholder="Entrer l'adresse email de votre destinataire" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <button type="button" class="btn btn-purple btn-block" data-dismiss="modal">Fermer</button>
                      </div>
                      <div class="col-md-6">
                        <button type="button" class="btn btn-orange btn-block" name="share-file" onclick="shareFile(guestFileEmail<?php echo $file['0']; ?>.value, <?php echo $file['0']; ?>, <?php echo $idFolder; ?>)" data-dismiss="modal">Partager le fichier</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <button type="button" class="btn btn-danger btn-block" name="delete-file" onclick="deleteFile(<?php echo $file['0']; ?>, <?php echo $idFolder; ?>)"><i class="fas fa-trash"></i> Supprimer</button>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>
