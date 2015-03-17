<?php
namespace culturenight;

use Section;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class Venues extends Section
{
   public function init_plugin()
   {
      $this->register_activity("venue-form", array("title" => "New Venue", "form" => "venue-form.php"));
   }
   public function get_venues_list()
   {
      $db = \EWCore::get_db_connection();
      //$parentId = $db->real_escape_string($this->get_param("parentId"));
      $token = $db->real_escape_string($_REQUEST["token"]);
      $size = $db->real_escape_string($_REQUEST["size"]);
      $name_filter = $db->real_escape_string($_REQUEST["nameFilter"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $db->query("SELECT COUNT(*)  FROM venues WHERE  name LIKE '$name_filter%' ") or die($db->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $db->query("SELECT id,name,slug,address,description  FROM venues WHERE name LIKE '%$name_filter%' ORDER BY slug  LIMIT $token,$size") or die($db->error);

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

   public function get_venue($venueId)
   {
      $db = \EWCore::get_db_connection();
      //echo "ssssssssssssssssssss" . venueId;
      $result = $db->query("SELECT countries.id as country_id,venues.*,cities.name AS city_name FROM venues,cities,countries WHERE venues.city_id = cities.id AND cities.country_id = countries.id AND venues.id = '$venueId'") or die($db->error);

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         return json_encode($rows);
      }
   }

   public function add_venue($city_id = null, $name = null, $address = null, $description = null, $logo = null, $lat = null, $lng = null)
   {
      $db = \EWCore::get_db_connection();
      if (!$name)
         $name = $db->real_escape_string($_REQUEST["name"]);
      if (!$city_id)
         $city_id = $db->real_escape_string($_REQUEST["city_id"]);
      $country_id = $db->real_escape_string($_REQUEST["country_id"]);
      $city_name = $db->real_escape_string($_REQUEST["city_name"]);
      if (!$address)
         $address = $db->real_escape_string($_REQUEST["address"]);
      if (!$description)
         $description = $db->real_escape_string($_REQUEST["description"]);
      if (!$logo)
         $logo = $db->real_escape_string($_REQUEST["logo"]);
      if (!$lat)
         $lat = $db->real_escape_string($_REQUEST["lat"]);
      if (!$lng)
         $lng = $db->real_escape_string($_REQUEST["lng"]);
      if (!$name)
      {
         $res = array("status" => "error", "error_message" => "The field name is mandatory");
         return json_encode($res);
      }
      if (!$city_id && $city_name)
      {
         $city_info = json_decode(Cities::add_city($city_name, $country_id), TRUE);
         if ($city_info["status"] == "success")
         {
            $city_id = $city_info["id"];
         }
         else
         {
            return json_encode(array("status" => "error", "error_message" => "Can not add the city"));
         }
      }
      $slug = EWCore::to_slug($name, "venues");
      //if (!$order)
      //  $order = 0;

      $stm = $db->prepare("INSERT INTO venues (city_id,name, slug, address, description, logo, lat, lng , created, modified) 
            VALUES (?, ?, ?, ? , ?, ?, ? ,?, ?, ?)") or die($db->error);
      $address = "$city_name, $address";
      $stm->bind_param("ssssssssss", $city_id, $name, $slug, $address, $description, $logo, $lat, $lng, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $stm->insert_id, "name" => $name, "message" => "Venue {$name} has been added successfully");
         $stm->close();
         $db->close();
      }
      return json_encode($res);
   }

   public function update_venue($id, $city_id = null, $name = null, $address = null, $description = null, $logo = null, $lat = null, $lng = null)
   {
      $db = \EWCore::get_db_connection();
      if (!$id)
         $id = $db->real_escape_string($_REQUEST["id"]);
      if (!$name)
         $name = $db->real_escape_string($_REQUEST["name"]);
      if (!$city_id)
         $city_id = $db->real_escape_string($_REQUEST["city_id"]);
      $country_id = $db->real_escape_string($_REQUEST["country_id"]);
      $city_name = $db->real_escape_string($_REQUEST["city_name"]);
      if (!$address)
         $address = $db->real_escape_string($_REQUEST["address"]);
      if (!$description)
         $description = $db->real_escape_string($_REQUEST["description"]);
      if (!$logo)
         $logo = $db->real_escape_string($_REQUEST["logo"]);
      if (!$lat)
         $lat = $db->real_escape_string($_REQUEST["lat"]);
      if (!$lng)
         $lng = $db->real_escape_string($_REQUEST["lng"]);
      if (!$name)
      {
         $res = array("status" => "error", "error_message" => "The field name is mandatory");
         return json_encode($res);
      }
      if (!$city_id && $city_name)
      {
         $city_info = json_decode(Cities::add_city($city_name, $country_id), TRUE);
         if ($city_info["status"] == "success")
         {
            $city_id = $city_info["id"];
         }
         else
         {
            return json_encode(array("status" => "error", "error_message" => "Can not add the city"));
         }
      }
      $slug = EWCore::to_slug($name, "venues");
      //if (!$order)
      //  $order = 0;

      $stm = $db->prepare("UPDATE venues SET city_id = ?, name = ?, slug = ?, address = ?, description = ?, logo = ?, lat = ?, lng = ?,  modified = ? WHERE id = ?") or die($db->error);
      $address = "$city_name, $address";
      $stm->bind_param("ssssssssss", $city_id, $name, $slug, $address, $description, $logo, $lat, $lng, date('Y-m-d H:i:s'), $id) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $id, "message" => "Venue {$name} has been updated successfully");
         $stm->close();
         $db->close();
      }
      return json_encode($res);
   }

   public function delete_venue($id)
   {
      $db = \EWCore::get_db_connection();
      if (!$id)
         $id = $db->real_escape_string($_REQUEST["id"]);

      $stm = $db->prepare("DELETE FROM venues WHERE id = ?") or die($db->error);
      $stm->bind_param("s", $id) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $id, "message" => "Venue with id: {$id} has been deleted successfully");
         $stm->close();
         $db->close();
      }
      return json_encode($res);
   }

   public function get_title()
   {
      return "Venues";
   }

   public function get_description()
   {
      return "Manage venues";
   }

//put your code here
}

?>
