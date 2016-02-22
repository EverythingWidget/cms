<?php

require_once('update.php');
$user = 'Eeliya';
$repository = 'EverythingWidget';
$localVersion = 'v0.8';

$updater = new PhpGithubUpdater($user, $repository);
try {

  $isUpToDate = $updater->isUpToDate($localVersion);
}
catch (PguRemoteException $e) {
  die($e);
  //couldn't access Github API
}
$root = '/root';
$tempDir = '/temp';

function rrmdir($dir) {
  foreach (glob($dir . '/*') as $file) {
    if (is_dir($file))
      rrmdir($file);
    else
      unlink($file);
  } rmdir($dir);
}

//if( !$isUpToDate ) {
//$nextVersion = $updater->getNextVersion($localVersion);
//download zip file onto your server in a temporary directory
try {
  $archive = $updater->downloadVersion('v0.9.2', __DIR__ . $tempDir);
}
catch (PguRemoteException $e) {
  die($e);
  //couldn't download latest version
}

//extract zip file to the same temporary directory
try {
  if (file_exists(__DIR__ . $root . '/')) {
    //echo "how " . __DIR__ . $root;
    rrmdir(__DIR__ . $root);
  }
  $extractDir = $updater->extractArchive($archive);
  unlink($archive);
  var_dump($extractDir);
}
catch (PguRemoteException $e) {
  die($e);
  //the zip is corrupted or you don't have persmission to write to the extract location
}

//BACKUP: you could do a backup here
//get a description of the update to show to your user
//$updateTitle = $updater->getTitle($nextVersion);
//$updateDescription = $updater->getDescription($nextVersion);

try {
  $update_folder_name = "";
  $scanned_directory = array_values(array_diff(scandir(__DIR__ . $tempDir), array('..', '.')));
  //var_dump($scanned_directory);
  $update_folder_name = $scanned_directory[0];
  //note that $tempDir, $extractDir and $root were defined in the previous script
  $result = $updater->moveFilesRecursive(
          __DIR__ . $tempDir . DIRECTORY_SEPARATOR . $update_folder_name, __DIR__ . $root
  );
  rmdir(__DIR__ . $tempDir);
}
catch (PguOverwriteException $e) {
  die($e);
  //couldn't overwrite existing installation
  // /!\ WARNING /!\ You should restore your backup here!
}
