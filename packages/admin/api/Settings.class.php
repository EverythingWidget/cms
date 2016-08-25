<?php

namespace admin;

use Module;

/**
 * Description of Settings
 *
 * @author Eeliya
 */
class Settings extends \ew\Module {

  protected $resource = "api";

  protected function install_assets() {
    \EWCore::register_app_ui_element("settings", $this);
    include_once 'models/ew_settings.php';

    \EWCore::register_form('ew/ui/settings/general', 'ew-admin-settings', [
        'title'   => "EW Admin",
        'content' => '',
        'url'     => 'html/admin/settings/settings-index.php'
    ]);

    \EWCore::register_form('ew/ui/apps/settings/navs', 'general', [
        'id'    => 'settings/general',
        'title' => 'tr{General}',
        'url'   => 'html/admin/settings/general.php'
    ]);

    \EWCore::register_form('ew/ui/apps/settings/navs', 'media', [
        'id'    => 'settings/preference',
        'title' => 'tr{Preference}',
        'url'   => 'html/admin/settings/preference.php'
    ]);
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/read_settings',
        'api/check_for_updates'
    ]);

    $this->register_permission("settings", "User can view EW Admin general settings and configure them", [
        'api/save_settings',
        'api/do_update',
        'html/settings-index.php'
    ]);
  }

  public function get_title() {
    return "Settings";
  }

  public function get_description() {
    return "Configure your Apps and Administration panel";
  }

  public function save_setting($key = null, $value = null) {
    //$db = \EWCore::get_db_connection();
    $db_pdo = \EWCore::get_db_PDO();

    $setting = $db_pdo->prepare('SELECT * FROM `ew_settings` WHERE `key`= ?');
    $setting->execute([$key]);
    $row_count = $setting->rowCount();

    if ($row_count > 0) {
      $db_pdo = \EWCore::get_db_PDO();
      $stm = $db_pdo->prepare("UPDATE ew_settings SET value = ? WHERE `key`= ? ");
      return $stm->execute([$value, $key]);
    }
    else {
      $db_pdo = \EWCore::get_db_PDO();
      $stm = $db_pdo->prepare("INSERT INTO ew_settings(`key`, `value`) VALUES(?, ?)");
      return $stm->execute([$key, $value]);
    }
    return FALSE;
  }

  public function save_settings($params) {

    $params = json_decode($params, TRUE);
    if (isset($params)) {
      foreach ($params as $key => $value) {
        if (!self::save_setting($key, $value)) {
          return [
              status  => "error",
              message => "App configurations has NOT been saved, Please try again"
          ];
        }
      }
    }
    return \ew\APIResourceHandler::to_api_response($params, [
                "status"  => "success",
                "message" => "App configurations has been saved succesfully"
    ]);
  }

  private function read_settings($app_name = '') {

    $app_name .= "%";
    $settings = ew_settings::where('key', "LIKE", $app_name)->get();
    $rows = [];
    foreach ($settings as $set) {
      $rows[$set["key"]] = $set["value"];
    }

    return $rows;
  }

  public function read_setting($key) {
    $db = \EWCore::get_db_connection();
    if (!$key)
      $key = $db->real_escape_string($_REQUEST["key"]);
    $setting = $db->query("SELECT * FROM ew_settings WHERE `key` = '$key'") or die($db->error);
    //$db = \EWCore::get_db_connection();
    //$rows = array();
    while ($r = $setting->fetch_assoc()) {
      $db->close();
      return $r;
    }
    //$out = array("totalRows" => $setting->num_rows, "result" => $rows);
    return FALSE;
  }

  public static function get_language_strings($app, $language) {
    $path = EW_PACKAGES_DIR . "/$app/locale/$language";
//echo $path;
    if (!file_exists($path))
      return;
    $locale_dir = opendir($path);
    $languages = [];

    if (strpos($language, ".json")) {
      $lang_file = json_decode(file_get_contents(EW_PACKAGES_DIR . '/' . $app . '/locale/' . $language), true);
    }

    return json_encode([
        "id"   => array_keys($lang_file["strings"]),
        "text" => array_values($lang_file["strings"])]);
  }

  public function update_language($app, $language, $id, $text) {
    $path = EW_PACKAGES_DIR . "/$app/locale/$language";
    if (file_exists($path)) {
      $lang_file = json_decode(file_get_contents($path), true);

      $lang_file["strings"] = array_combine($id, $text);
      $fp = file_put_contents($path, json_encode($lang_file, JSON_UNESCAPED_UNICODE));

      return json_encode([
          status  => "success",
          message => "tr{The language file has been updated successfully}"]);
    }
    return \EWCore::log_error(400, "Can't find the language file");
  }

  public function check_for_updates() {
    $user = 'Eeliya';
    $repository = 'EverythingWidget';
    $localVersion = 'v0.9.0';

    $updater = new PhpGithubUpdater($user, $repository);
    try {

      $is_up_to_date = $updater->next_version_info($localVersion);
    }
    catch (PguRemoteException $e) {
      die($e);
      //couldn't access Github API
    }

    return \ew\APIResourceHandler::to_api_response($is_up_to_date);
  }

  public function do_update() {
    $user = 'Eeliya';
    $repository = 'EverythingWidget';
    $localVersion = 'v0.8';

    $updater = new PhpGithubUpdater($user, $repository);

    $root = EW_ROOT_DIR;
    $tempDir = EW_ROOT_DIR . 'temp-new-version-download';
    echo EW_ROOT_DIR . '\n';

    //download zip file onto your server in a temporary directory
    /* try {
      $archive = $updater->downloadVersion('v0.9.2', $tempDir);
      echo $archive . '\n';
      }
      catch (PguRemoteException $e) {
      die($e);
      //couldn't download latest version
      }

      //extract zip file to the same temporary directory
      try {

      $updater->extractArchive($archive);
      unlink($archive);
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
      $scanned_directory = array_values(array_diff(scandir($tempDir), array('..', '.')));
      var_dump($scanned_directory);
      $update_folder_name = $scanned_directory[0];
      //note that $tempDir, $extractDir and $root were defined in the previous script
      $result = $updater->moveFilesRecursive(
      $tempDir . DIRECTORY_SEPARATOR . $update_folder_name, $root
      , [
      EW_ROOT_DIR . 'config'
      ]);
      rmdir($tempDir);
      }
      catch (PguOverwriteException $e) {
      die($e);
      //couldn't overwrite existing installation
      // /!\ WARNING /!\ You should restore your backup here!
      } */
    /* $host = $_SERVER['HTTP_HOST'];
      $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
      $extra = '~admin/';
      header("Location: http://$host$uri/$extra"); */
  }

  //put your code here
}
