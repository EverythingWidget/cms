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
      $db = \EWCore::get_db_connection();
      //$parentId = $db->real_escape_string($this->get_param("parentId"));
      $token = $db->real_escape_string($_REQUEST["token"]);
      $size = $db->real_escape_string($_REQUEST["size"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $db->query("SELECT COUNT(*)  FROM countries ") or die($db->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $db->query("SELECT *  FROM countries  LIMIT $token,$size") or die($db->error);

      //$out = array();
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $db->close();
      $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($out);
   }

   public static function get_country($country_id)
   {
      $db = \EWCore::get_db_connection();
      if (!$country_id)
         $country_id = $db->real_escape_string($_REQUEST["country_id"]);

      $result = $db->query("SELECT * FROM countries WHERE id = '$country_id'") or $db->error;

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         return json_encode($rows);
      }
   }

   public function add_country($name, $iso)
   {
      $db = \EWCore::get_db_connection();
      if (!$name)
         $name = $db->real_escape_string($_REQUEST["name"]);
      if (!$iso)
         $iso = $db->real_escape_string($_REQUEST["iso"]);
      if (!$name)
      {
         $res = array("status" => "error", "error_message" => "The field name is mandatory");
         return json_encode($res);
      }
      $slug = EWCore::to_slug($name, "countries");
      //if (!$order)
      //  $order = 0;

      $stm = $db->prepare("INSERT INTO countries (name, iso, slug) 
            VALUES (?, ?, ?)") or die($db->error);
      $stm->bind_param("sss", $name, $iso, $slug) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $stm->insert_id, "message" => "Country {$name} has been added successfully");
         $stm->close();
         $db->close();
      }
      return json_encode($res);
   }

   public function update_country($country_id, $name, $iso)
   {
      $db = \EWCore::get_db_connection();
      if (!$country_id)
         $country_id = $db->real_escape_string($_REQUEST["id"]);
      if (!$name)
         $name = $db->real_escape_string($_REQUEST["name"]);
      if (!$iso)
         $iso = $db->real_escape_string($_REQUEST["iso"]);
      if (!$name)
      {
         $res = array("status" => "error", "error_message" => "The field name is mandatory");
         return json_encode($res);
      }
      $slug = EWCore::to_slug($name, "countries");
      //if (!$order)
      //  $order = 0;

      $stm = $db->prepare("UPDATE countries SET name = ?, iso = ?, slug = ? WHERE id = ?") or die($db->error);
      $stm->bind_param("ssss", $name, $iso, $slug, $country_id) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $country_id, "message" => "Country {$name} has been updated successfully");
         $stm->close();
         $db->close();
      }
      return json_encode($res);
   }

   public function delete_country($country_id)
   {
      $db = \EWCore::get_db_connection();
      if (!$country_id)
         $country_id = $db->real_escape_string($_REQUEST["country_id"]);

      $stm = $db->prepare("DELETE FROM countries WHERE id = ?") or die($db->error);
      $stm->bind_param("s", $country_id) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $country_id, "message" => "Country with id: {$country_id} has been deleted successfully");
         $stm->close();
         $db->close();
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
