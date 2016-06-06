<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin;

/**
 * Description of UsersGroupsRepository
 *
 * @author Eeliya
 */
class UsersGroupsRepository implements \ew\CRUDRepository {

  public function create($input) {
    
  }

  public function delete($input) {
    
  }

  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all($input->page, $input->page_size);
  }

  public function update($input) {
    
  }

  public function all($page = 0, $page_size = 100) {
    $db = \EWCore::get_db_connection();

    if (is_null($page)) {
      $page = 0;
    }
    if (is_null($page_size)) {
      $page_size = 100;
    }

    $totalRows = $db->query("SELECT COUNT(*) FROM ew_users_groups") or die(error_reporting());
    $totalRows = $totalRows->fetch_assoc();

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups ORDER BY id LIMIT $page, $page_size") or die($db->error);

    $rows = [];
    while ($r = $result->fetch_assoc()) {
      $rows[] = $r;
    }
    $db->close();

    $result = new \stdClass;

    $result->total = intval($totalRows['COUNT(*)']);
    $result->page_size = $page_size;
    $result->data = $rows;

    return $result;
  }

  public function find_by_id($groupId) {
    $db = \EWCore::get_db_PDO();

    $statement = $db->prepare("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups WHERE id = ?");
    $statement->execute([$groupId]);


    if ($user_group_info = $statement->fetch(\PDO::FETCH_ASSOC)) {
      if ($user_group_info["type"] === "superuser") {
        $user_group_info["permission"] = implode(",", EWCore::read_permissions_ids());
      }

      return $user_group_info;
    }

    return null;
  }

//put your code here
}
