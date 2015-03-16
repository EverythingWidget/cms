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
class Categories extends Section
{

  public function get_categories_list()
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

    $totalRows = $db->query("SELECT COUNT(*)  FROM categories") or die($db->error);
    $totalRows = $totalRows->fetch_assoc();
    $result = $db->query("SELECT *  FROM categories  LIMIT $token,$size") or die($db->error);

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

  public function get_title()
  {
    return "Categories";
  }

  public function get_description()
  {
    return "Manage categories";
  }

//put your code here
}


