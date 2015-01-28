<?php

namespace admin;

use Section;

/**
 * Description of Settings
 *
 * @author Eeliya
 */
class Settings extends Section
{

   public function get_title()
   {
      return "Settings";
   }

   public function get_description()
   {
      return "Configure your Apps and Administration panel";
   }

   public function save_setting($key = null, $value = null)
   {
      $MYSQLI = get_db_connection();

      if (!$key)
         $key = $MYSQLI->real_escape_string($_REQUEST["key"]);
      if (!$value)
         $value = $MYSQLI->real_escape_string($_REQUEST["value"]);

      $setting = $MYSQLI->query("SELECT * FROM ew_settings WHERE `key` = '$key' ") or die($MYSQLI->error);
      if ($setting = $setting->fetch_assoc())
      {
         $MYSQLI->query("UPDATE ew_settings SET value = '$value' WHERE `key` = '$key' ") or die($MYSQLI->error);
         return TRUE;
      }
      else
      {
         $MYSQLI->query("INSERT INTO ew_settings(`key`, `value`) VALUES('$key','$value')") or die($MYSQLI->error);
         return TRUE;
      }
      return FALSE;
   }

   public function save_settings($params)
   {
      //$MYSQLI = get_db_connection();
      //print_r($params);
      if (!$params)
         $params = $_REQUEST["params"];
      $params = json_decode(stripslashes($params), TRUE);
      
      foreach ($params as $key => $value)
      {
         //echo $key . " " . $value;
         if (!self::save_setting($key, $value))
            return json_encode(array(status => "error", message => "App configurations has NOT been saved, Please try again"));
      }
//echo "asdasd";
      return json_encode(array(status => "success", message => "App configurations has been saved succesfully"));
   }

   public static function read_settings()
   {
      $MYSQLI = get_db_connection();

      $setting = $MYSQLI->query("SELECT * FROM ew_settings") or die($MYSQLI->error);
      //$MYSQLI = get_db_connection();
      $rows = array();
      while ($r = $setting->fetch_assoc())
      {
         $rows[$r["key"]] = $r["value"];
      }
      $MYSQLI->close();
      //$out = array("totalRows" => $setting->num_rows, "result" => $rows);
      return json_encode($rows);
   }

   public function read_setting($key)
   {
      $MYSQLI = get_db_connection();
      if (!$key)
         $key = $MYSQLI->real_escape_string($_REQUEST["key"]);
      $setting = $MYSQLI->query("SELECT * FROM ew_settings WHERE `key` = '$key'") or die($MYSQLI->error);
      //$MYSQLI = get_db_connection();
      //$rows = array();
      while ($r = $setting->fetch_assoc())
      {
         $MYSQLI->close();
         return $r;
      }

      //$out = array("totalRows" => $setting->num_rows, "result" => $rows);
      return FALSE;
   }

   public static function get_language_strings($app, $language)
   {
      $path = EW_APPS_DIR . "/$app/locale/$language";
//echo $path;
      if (!file_exists($path))
         return;
      $locale_dir = opendir($path);
      $languages = array();


      if (strpos($language, ".json"))
      {
         $lang_file = json_decode(file_get_contents(EW_APPS_DIR . '/' . $app . '/locale/' . $language), true);
      }

      return json_encode(array("id" => array_keys($lang_file["strings"]), "text" => array_values($lang_file["strings"])));
   }

   public function update_language($app, $language, $id, $text)
   {
      $path = EW_APPS_DIR . "/$app/locale/$language";
      if (file_exists($path))
      {
         $lang_file = json_decode(file_get_contents($path), true);

         $lang_file["strings"] = array_combine($id, $text);
         $fp = file_put_contents($path, json_encode($lang_file, JSON_UNESCAPED_UNICODE));

         return json_encode(array(status => "success", message => "tr{The language file has been updated successfully}"));
      }
      return \EWCore::log_error(400, "Can't find the language file");
   }

   //put your code here
}
