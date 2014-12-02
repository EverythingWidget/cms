<?php

namespace culturenight;

use Section;
use EWCore;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class Countries extends Section
{

   private $app_name = "culturenight";

   public function init_plugin()
   {
      $this->register_permission("see-countries", "User can see the countries", array("getcountries_list", "get_country"));
      $this->register_permission("manipulate-countries", "User can add new, edit, delete countries", array("add_country", "update_country", "edit_country"));
   }

   public function getcountries_list()
   {
      $MYSQLI = get_db_connection();
      //$parentId = $MYSQLI->real_escape_string($this->get_param("parentId"));
      $token = $MYSQLI->real_escape_string($_REQUEST["token"]);
      $size = $MYSQLI->real_escape_string($_REQUEST["size"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM countries ") or die($MYSQLI->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $MYSQLI->query("SELECT *  FROM countries  LIMIT $token,$size") or die($MYSQLI->error);

      //$out = array();
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $MYSQLI->close();
      $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($out);
   }

   public static function get_country($country_id)
   {
      $MYSQLI = get_db_connection();
      if (!$country_id)
         $country_id = $MYSQLI->real_escape_string($_REQUEST["country_id"]);

      $result = $MYSQLI->query("SELECT * FROM countries WHERE id = '$country_id'") or $MYSQLI->error;

      if ($rows = $result->fetch_assoc())
      {
         $MYSQLI->close();
         return json_encode($rows);
      }
   }

   public function add_country($name, $iso)
   {
      $MYSQLI = get_db_connection();
      if (!$name)
         $name = $MYSQLI->real_escape_string($_REQUEST["name"]);
      if (!$iso)
         $iso = $MYSQLI->real_escape_string($_REQUEST["iso"]);
      if (!$name)
      {
         $res = array("status" => "error", "error_message" => "The field name is mandatory");
         return json_encode($res);
      }
      $slug = EWCore::to_slug($name, "countries");
      //if (!$order)
      //  $order = 0;

      $stm = $MYSQLI->prepare("INSERT INTO countries (name, iso, slug) 
            VALUES (?, ?, ?)") or die($MYSQLI->error);
      $stm->bind_param("sss", $name, $iso, $slug) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $stm->insert_id, "message" => "Country {$name} has been added successfully");
         $stm->close();
         $MYSQLI->close();
      }
      return json_encode($res);
   }

   public function update_country($country_id, $name, $iso)
   {
      $MYSQLI = get_db_connection();
      if (!$country_id)
         $country_id = $MYSQLI->real_escape_string($_REQUEST["id"]);
      if (!$name)
         $name = $MYSQLI->real_escape_string($_REQUEST["name"]);
      if (!$iso)
         $iso = $MYSQLI->real_escape_string($_REQUEST["iso"]);
      if (!$name)
      {
         $res = array("status" => "error", "error_message" => "The field name is mandatory");
         return json_encode($res);
      }
      $slug = EWCore::to_slug($name, "countries");
      //if (!$order)
      //  $order = 0;

      $stm = $MYSQLI->prepare("UPDATE countries SET name = ?, iso = ?, slug = ? WHERE id = ?") or die($MYSQLI->error);
      $stm->bind_param("ssss", $name, $iso, $slug, $country_id) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $country_id, "message" => "Country {$name} has been updated successfully");
         $stm->close();
         $MYSQLI->close();
      }
      return json_encode($res);
   }

   public function delete_country($country_id)
   {
      $MYSQLI = get_db_connection();
      if (!$country_id)
         $country_id = $MYSQLI->real_escape_string($_REQUEST["country_id"]);

      $stm = $MYSQLI->prepare("DELETE FROM countries WHERE id = ?") or die($MYSQLI->error);
      $stm->bind_param("s", $country_id) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $country_id, "message" => "Country with id: {$country_id} has been deleted successfully");
         $stm->close();
         $MYSQLI->close();
      }
      return json_encode($res);
   }

   public function get_title()
   {
      return "Countries";
   }

   public function get_description()
   {
      return "Manage events";
   }

//put your code here
}

?>
