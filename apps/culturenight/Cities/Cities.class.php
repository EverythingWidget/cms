<?php
namespace culturenight;

use Section;
use EWCore;

/**
 * Cities
 *
 * @author Eeliya
 */
class Cities extends Section
{

  public function getcities_list()
  {
    $db = \EWCore::get_db_connection();
    $token = $db->real_escape_string($_REQUEST["token"]);
    $size = $db->real_escape_string($_REQUEST["size"]);
    if (!$token)
    {
      $token = 0;
    }
    if (!$size)
    {
      $size = 99999999999999;
    }

    $totalRows = $db->query("SELECT COUNT(*)  FROM cities, countries WHERE countries.id = cities.country_id") or die($db->error);
    $totalRows = $totalRows->fetch_assoc();
    $result = $db->query("SELECT cities.id, countries.name as country_name,cities.name, cities.slug  FROM cities, countries WHERE countries.id = cities.country_id  LIMIT $token,$size") or die($db->error);

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

  public function get_cities_by_country_id($country_id = null, $name_filter = null)
  {
    $db = \EWCore::get_db_connection();
    if (!$country_id)
      $country_id = $db->real_escape_string($_REQUEST["countryId"]);
    $token = $db->real_escape_string($_REQUEST["token"]);
    $size = $db->real_escape_string($_REQUEST["size"]);
    if (!$name_filter)
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

    $totalRows = $db->query("SELECT COUNT(*)  FROM cities, countries WHERE countries.id = cities.country_id AND countries.id = '$country_id' AND cities.name LIKE '$name_filter%'") or die($db->error);
    $totalRows = $totalRows->fetch_assoc();
    $result = $db->query("SELECT cities.id, countries.name as country_name, cities.name, cities.slug  FROM cities, countries WHERE countries.id = cities.country_id AND countries.id = '$country_id' AND  cities.name LIKE '$name_filter%'  LIMIT $token, $size") or die($db->error);

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

  public function get_cities_by_country_slug($country_slug = null)
  {
    $db = \EWCore::get_db_connection();
    if (!$country_slug)
      $country_slug = $db->real_escape_string($_REQUEST["countrySlug"]);
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

    $totalRows = $db->query("SELECT COUNT(*)  FROM cities, countries WHERE countries.id = cities.country_id AND countries.slug = '$country_slug'") or die($db->error);
    $totalRows = $totalRows->fetch_assoc();
    $result = $db->query("SELECT cities.id, countries.name as country_name, cities.name, cities.slug  FROM cities, countries WHERE countries.id = cities.country_id AND countries.slug = '$country_slug'  LIMIT $token, $size") or die($db->error);

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

  public static function get_city($city_id)
  {
    $db = \EWCore::get_db_connection();
    if ($_REQUEST["cityId"])
    {
      $city_id = $_REQUEST["cityId"];
    }

    $result = $db->query("SELECT * FROM cities WHERE id = '$city_id'") or die($db->error);

    if ($rows = $result->fetch_assoc())
    {
      $db->close();
      return json_encode($rows);
    }
  }

  public static function add_city($name, $country_id)
  {
    $db = \EWCore::get_db_connection();
    if (!$name)
      $name = $db->real_escape_string($_REQUEST["name"]);
    if (!$country_id)
      $country_id = $db->real_escape_string($_REQUEST["country_id"]);
    if (!$name)
    {
      $res = array("status" => "error", "error_message" => "The field name is mandatory");
      return json_encode($res);
    }
    $slug = EWCore::to_slug($name, "cities");
    //if (!$order)
    //  $order = 0;

    $stm = $db->prepare("INSERT INTO cities (name, slug, country_id) 
            VALUES (?, ?, ?)") or die($db->error);
    $stm->bind_param("sss", $name, $slug, $country_id) or die($db->error);
    if ($stm->execute())
    {
      $res = array("status" => "success", "id" => $stm->insert_id, "message" => "City {$name} has been added successfully");
      $stm->close();
      $db->close();
    }
    return json_encode($res);
  }

  public function update_city($id, $name, $country_id)
  {
    $db = \EWCore::get_db_connection();
    if (!$id)
      $id = $db->real_escape_string($_REQUEST["id"]);
    if (!$name)
      $name = $db->real_escape_string($_REQUEST["name"]);
    if (!$country_id)
      $country_id = $db->real_escape_string($_REQUEST["country_id"]);
    if (!$name)
    {
      $res = array("status" => "error", "error_message" => "The field name is mandatory");
      return json_encode($res);
    }
    $slug = EWCore::to_slug($name, "cities");
    //if (!$order)
    //  $order = 0;

    $stm = $db->prepare("UPDATE cities SET name = ?, country_id = ?, slug = ? WHERE id = ?") or die($db->error);
    $stm->bind_param("ssss", $name, $country_id, $slug, $id) or die($db->error);
    if ($stm->execute())
    {
      $res = array("status" => "success", "id" => $id, "message" => "City {$name} with id {$id} has been updated successfully");
      $stm->close();
      $db->close();
    }
    return json_encode($res);
  }

  public function get_title()
  {
    return "Cities";
  }

  public function get_description()
  {
    return "Manage cities";
  }

//put your code here
}

?>
