<?php 

require_once('update.php');
$user = 'Eeliya';
$repository = 'EverythingWidget';
$localVersion = 'v0.8';

$updater = new PhpGithubUpdater($user, $repository);
try {

    $isUpToDate = $updater->isUpToDate($localVersion);

} catch (PguRemoteException $e) {
	die($e);
    //couldn't access Github API
}
$root = '/root';
$tempDir = '/temp';

//if( !$isUpToDate ) {
    
    //$nextVersion = $updater->getNextVersion($localVersion);


    //download zip file onto your server in a temporary directory
    try {
        $archive = $updater->downloadVersion( 'v0.9.2', __DIR__.$tempDir );
        
    } catch (PguRemoteException $e) {
    	die($e);
        //couldn't download latest version
    }

    //extract zip file to the same temporary directory
    try {

        $extractDir = $updater->extractArchive($archive);
        var_dump($archive);
    } catch (PguRemoteException $e) {
    	die($e);
        //the zip is corrupted or you don't have persmission to write to the extract location
    }

    //BACKUP: you could do a backup here

    //get a description of the update to show to your user
    //$updateTitle = $updater->getTitle($nextVersion);
    //$updateDescription = $updater->getDescription($nextVersion);

    try {
    //note that $tempDir, $extractDir and $root were defined in the previous script
    $result = $updater->moveFilesRecursive(
        __DIR__.$tempDir.DIRECTORY_SEPARATOR.$extractDir,
        __DIR__.$root
    );
} catch (PguOverwriteException $e) {
    die($e);
    //couldn't overwrite existing installation
    // /!\ WARNING /!\ You should restore your backup here!
}

// }
// else {
// 	echo "it is up to date";
// }


