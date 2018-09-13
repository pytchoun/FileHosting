<?php
$folderPath = $_POST["folder"];
$folderName = pathinfo("../" . $folderPath,PATHINFO_BASENAME);
$zipName = "../../uploads/". $folderName . ".zip";
$rootPath = realpath("../" . $folderPath);
$emptyFolder = true;

if (file_exists($zipName)) {
  unlink($zipName);
}

$zip = new ZipArchive;
$res = $zip->open($zipName, ZipArchive::CREATE);
if ($res == TRUE) {
  // Itérer récursivement sur le dossier afin d'obtenir tous les fichiers
  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
  foreach ($files as $name => $file) {
    if (!$file->isDir()) {
      // Retourner pour chaque fichier son chemin absolu
      $filePath = $file->getRealPath();
      // Supprimer le chemin racine du chemin complet
      $relativePath = substr($filePath, strlen($rootPath) + 1);
      $zip->addFile($filePath, $relativePath);
      $emptyFolder = false;
    }
  }
  if ($emptyFolder == true) {
    $zip->addFromString('Dossier vide.txt', 'Votre dossier est vide.');
  }
  $zip->close();
}
?>
