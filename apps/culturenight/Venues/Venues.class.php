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
      $MYSQLI = get_db_connection();
      //$parentId = $MYSQLI->real_escape_string($this->get_param("parentId"));
      $token = $MYSQLI->real_escape_string($_REQUEST["token"]);
      $size = $MYSQLI->real_escape_string($_REQUEST["size"]);
      $name_filter = $MYSQLI->real_escape_string($_REQUEST["nameFilter"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM venues WHERE  name LIKE '$name_filter%' ") or die($MYSQLI->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $MYSQLI->query("SELECT id,name,slug,address,description  FROM venues WHERE name LIKE '%$name_filter%' ORDER BY slug  LIMIT $token,$size") or die($MYSQLI->error);

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

   public function get_venue($venueId)
   {
      $MYSQLI = get_db_connection();
      //echo "ssssssssssssssssssss" . venueId;
      $result = $MYSQLI->query("SELECT countries.id as country_id,venues.*,cities.name AS city_name FROM venues,cities,countries WHERE venues.city_id = cities.id AND cities.country_id = countries.id AND venues.id = '$venueId'") or die($MYSQLI->error);

      if ($rows = $result->fetch_assoc())
      {
         $MYSQLI->close();
         return json_encode($rows);
      }
   }

   public function add_venue($city_id = null, $name = null, $address = null, $description = null, $logo = null, $lat = null, $lng = null)
   {
      $MYSQLI = get_db_connection();
      if (!$name)
         $name = $MYSQLI->real_escape_string($_REQUEST["name"]);
      if (!$city_id)
         $city_id = $MYSQLI->real_escape_string($_REQUEST["city_id"]);
      $country_id = $MYSQLI->real_escape_string($_REQUEST["country_id"]);
      $city_name = $MYSQLI->real_escape_string($_REQUEST["city_name"]);
      if (!$address)
         $address = $MYSQLI->real_escape_string($_REQUEST["address"]);
      if (!$description)
         $description = $MYSQLI->real_escape_string($_REQUEST["description"]);
      if (!$logo)
         $logo = $MYSQLI->real_escape_string($_REQUEST["logo"]);
      if (!$lat)
         $lat = $MYSQLI->real_escape_string($_REQUEST["lat"]);
      if (!$lng)
         $lng = $MYSQLI->real_escape_string($_REQUEST["lng"]);
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

      $stm = $MYSQLI->prepare("INSERT INTO venues (city_id,name, slug, address, description, logo, lat, lng , created, modified) 
            VALUES (?, ?, ?, ? , ?, ?, ? ,?, ?, ?)") or die($MYSQLI->error);
      $address = "$city_name, $address";
      $stm->bind_param("ssssssssss", $city_id, $name, $slug, $address, $description, $logo, $lat, $lng, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $stm->insert_id, "name" => $name, "message" => "Venue {$name} has been added successfully");
         $stm->close();
         $MYSQLI->close();
      }
      return json_encode($res);
   }

   public function update_venue($id, $city_id = null, $name = null, $address = null, $description = null, $logo = null, $lat = null, $lng = null)
   {
      $MYSQLI = get_db_connection();
      if (!$id)
         $id = $MYSQLI->real_escape_string($_REQUEST["id"]);
      if (!$name)
         $name = $MYSQLI->real_escape_string($_REQUEST["name"]);
      if (!$city_id)
         $city_id = $MYSQLI->real_escape_string($_REQUEST["city_id"]);
      $country_id = $MYSQLI->real_escape_string($_REQUEST["country_id"]);
      $city_name = $MYSQLI->real_escape_string($_REQUEST["city_name"]);
      if (!$address)
         $address = $MYSQLI->real_escape_string($_REQUEST["address"]);
      if (!$description)
         $description = $MYSQLI->real_escape_string($_REQUEST["description"]);
      if (!$logo)
         $logo = $MYSQLI->real_escape_string($_REQUEST["logo"]);
      if (!$lat)
         $lat = $MYSQLI->real_escape_string($_REQUEST["lat"]);
      if (!$lng)
         $lng = $MYSQLI->real_escape_string($_REQUEST["lng"]);
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

      $stm = $MYSQLI->prepare("UPDATE venues SET city_id = ?, name = ?, slug = ?, address = ?, description = ?, logo = ?, lat = ?, lng = ?,  modified = ? WHERE id = ?") or die($MYSQLI->error);
      $address = "$city_name, $address";
      $stm->bind_param("ssssssssss", $city_id, $name, $slug, $address, $description, $logo, $lat, $lng, date('Y-m-d H:i:s'), $id) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $id, "message" => "Venue {$name} has been updated successfully");
         $stm->close();
         $MYSQLI->close();
      }
      return json_encode($res);
   }

   public function delete_venue($id)
   {
      $MYSQLI = get_db_connection();
      if (!$id)
         $id = $MYSQLI->real_escape_string($_REQUEST["id"]);

      $stm = $MYSQLI->prepare("DELETE FROM venues WHERE id = ?") or die($MYSQLI->error);
      $stm->bind_param("s", $id) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $id, "message" => "Venue with id: {$id} has been deleted successfully");
         $stm->close();
         $MYSQLI->close();
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
