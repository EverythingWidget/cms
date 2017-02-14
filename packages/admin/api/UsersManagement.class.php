<?php

namespace admin;

use ew\DBUtility;
use EWCore;

class UsersManagement extends \ew\Module {

  protected $resource = "api";

  //protected $unauthorized_method_invoke = true;

  protected function get_pre_processors() {
    return [new UserValidator];
  }

  protected function install_assets() {
    EWCore::register_app_ui_element('users-management', $this);

    if (!in_array('ew_users_profiles', \EWCore::$DEFINED_TABLES)) {
      $table_install = DBUtility::create_table('ew_users_profiles', [
          'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
          'user_id' => 'BIGINT NOT NULL',
          'key' => 'VARCHAR(300) NOT NULL',
          'value' => 'TEXT NULL',
          'date_created' => 'DATETIME NULL'
      ]);

      $pdo = \EWCore::get_db_PDO();
      $stm = $pdo->prepare($table_install);
      if (!$stm->execute()) {
        echo \EWCore::log_error(500, '', $stm->errorInfo());
      }
    }

    EWCore::register_form('ew/ui/apps/users/navs', 'users', [
        'id' => 'users-management/users',
        'title' => 'Users',
        'url' => 'html/admin/users-management/users/component.php'
    ]);

    EWCore::register_form("ew/ui/apps/users/navs", "groups", [
        'id' => 'users-management/users-groups',
        'title' => 'Groups',
        'url' => 'html/admin/users-management/groups/component.php'
    ]);

    EWCore::register_ui_element('forms/user/tabs', 'user-info', [
        'title' => 'User Info',
        'template_url' => 'admin/html/users-management/user-form/info.php'
    ]);

    EWCore::register_ui_element('forms/user-group/tabs', 'group-info', [
        'title' => 'Group Info',
        'template_url' => 'admin/html/users-management/group-form/info.php'
    ]);

    EWCore::register_ui_element('forms/user-group/tabs', 'group-permissions', [
        'title' => 'Permissions',
        'template_url' => 'admin/html/users-management/group-form/permissions.php'
    ]);
  }

  protected function install_permissions() {
    $this->register_permission("see-users", "User can see users list", [
        'api/get',
        'api/users-read',
        'api/groups-read',
        'api/get_user_by_id',
        'api/get_user_by_email',
        'api/logout',
        'html/user-form/component.php',
        'html/' . $this->get_index()
    ]);

    $this->register_permission("manipulate-users", "User can add, edit delete users", [
        'api/users-create',
        "api/users-update",
        "api/users-delete",
        'html/user-form/component.php',
        "api/logout",
        'html/' . $this->get_index()
    ]);

    $this->register_permission("see-groups", "User can see user groups list", [
        "api/get_users_groups_list",
        "api/get_user_group_by_id",
        "api/get_users_group_by_type",
        'html/group-form/component.php',
        'html/' . $this->get_index()
    ]);

    $this->register_permission("manipulate-groups", "User can add, edit delete user group", [
        'api/groups-create',
        'api/groups-update',
        'api/groups-delete',
        'html/groups/group-form/component.php',
        'html/' . $this->get_index()
    ]);

    $this->register_public_access([
        'api/read',
        "api/am-i-in"
    ]);

    //$this->add_listener("admin-api/UsersManagement/get_user_by_id", "test_plugin");
  }

  public function get_title() {
    return "Users";
  }

  public function get_description() {
    return "Manage users, create and edit roles, manage users premissions";
  }

  public function test_plugin($_data) {
    return $_data;
  }

  public static function generate_hash($password) {
    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2a$%02d$", 10) . $salt;
    return crypt($password, $salt);
  }

  public static function verify_hash($password, $hash) {
    return $hash == crypt($password, $hash);
  }

  public static function login($username, $password) {
    $user_info = null;
    $db = \EWCore::get_db_PDO();

    $stm = $db->prepare("SELECT ew_users.id, password, group_id, email FROM ew_users, ew_users_groups WHERE ew_users.group_id = ew_users_groups.id AND ew_users.email = ? LIMIT 1") or die($db->error);

    $stm->execute([$username]);
    $user_info = $stm->fetch(\PDO::FETCH_ASSOC);

    if (!isset($user_info)) {
      return false;
    }

    if (!static::verify_hash($password, $user_info["password"])) {
      return false;
    }

    $_SESSION['login'] = '1';
    $_SESSION['sesUserName'] = $username;
    $_SESSION['EW.USER_ID'] = $user_info["id"];
    $_SESSION['EW.USER_GROUP_ID'] = $user_info["group_id"];
    $_SESSION['EW.USERNAME'] = $user_info["email"];

    return TRUE;
  }

  public function logout($url) {
    unset($_SESSION['login']);
    session_destroy();
    if (!$url)
      $url = '/';

    header("Location: $url");
  }

  public function am_i_in() {

  }

  public static function sign_up() {
    $resullt = json_decode(self::add_user(null, null, null, null, null), TRUE);
    $user_id = $resullt["id"];
    if ($user_id) {
      $modules = EWCore::read_actions_registry("ew-user-action-sign-up");
      try {
        foreach ($modules as $id => $data) {
          if (method_exists($data["class"], $data["function"])) {
            $function_result = call_user_func([
                $data["class"],
                $data["function"]
            ], $user_id);
            if ($function_result != true) {
              $message .= $function_result . "<br/>";
            }
          }
        }
        $resullt = [
            "status" => "success",
            "error_message" => $message
        ];
      } catch (Exception $e) {

      }
    }
    return json_encode($resullt);
  }

  /**
   *
   * @param string $app_name
   * @param string $class_name
   * @param string $permission_id
   * @param string $user_id
   * @return boolean
   */
  public static function group_has_permission($app_name, $class_name, $permission_id, $user_group_id) {
    $db_con = \EWCore::get_db_connection();

    $permissions = $db_con->query("SELECT ew_users_groups.permission, ew_users_groups.type FROM ew_users_groups WHERE id = '$user_group_id' LIMIT 1") or die($db_con->error);
    $user_info = $permissions->fetch_assoc();

    if ($user_info['type'] === 'superuser') {
      return true;
    }

    if (isset($user_info)) {
      $user_permissions = explode(",", $user_info['permission']);
      foreach ($user_permissions as $permission) {
        foreach ($permission_id as $item) {
          //echo "$app_name.$class_name.$item === $permission<br>";
          if ($permission === "$app_name.$class_name.$item")
            return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   *
   * @param string $app_name
   * @param string $class_name
   * @param string $permission_id
   * @param string $user_group_id
   * @return boolean
   */
  public static function user_has_permission_for_resource($app_name, $resource_name, $user_group_id) {
    $db_con = \EWCore::get_db_connection();
    //echo $user_id."asfdasd";

    if (!$user_group_id) {
      $permissions = $db_con->query("SELECT permission FROM ew_users_groups WHERE type = 'default' LIMIT 1") or die($db_con->error);
    } else {
      $permissions = $db_con->query("SELECT ew_users_groups.permission FROM ew_users_groups WHERE id = '$user_group_id' LIMIT 1") or die($db_con->error);
    }

    if ($user_info = $permissions->fetch_assoc()) {
      $user_permissions = explode(",", $user_info["permission"]);

      foreach ($user_permissions as $permission) {
        if (strpos($permission, $app_name) !== false) {
          return true;
        }
      }
    }
    return FALSE;
  }

  public static function user_has_permission($app_name, $resource, $module_name, $command_name) {
    $permission_id = \EWCore::does_need_permission($app_name, $module_name, $resource . '/' . $command_name);

    if ($permission_id && $permission_id !== FALSE) {
      if (!static::group_has_permission($app_name, $module_name, $permission_id, $_SESSION['EW.USER_GROUP_ID'])) {
        return false;
      }
    }

    return true;
  }

  public function users_create($_input, \ew\APIResponse $_response) {
    $result = (new UsersRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function users_read($_response, $_input, $_parts__id) {
    $_input->id = $_parts__id;

    $result = (new UsersRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function users_update(\ew\APIResponse $_response, $_input, $_parts__id) {
    $_input->id = $_parts__id;

    $result = (new UsersRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function users_delete(\ew\APIResponse $_response, $_input, $_parts__id) {
    $_input->id = $_parts__id;

    $result = (new UsersRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public static function get_users_groups_list($_response, $page = 0, $page_size = 100) {
    $db = \EWCore::get_db_connection();

    if (!$page) {
      $page = 0;
    }
    if (!$page_size) {
      $page_size = 100;
    }
    $total_rows = $db->query("SELECT COUNT(*) FROM ew_users_groups") or die(error_reporting());
    $total_rows_data = $total_rows->fetch_assoc();

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups ORDER BY id LIMIT $page, $page_size") or die($db->error);

    $rows = [];
    while ($r = $result->fetch_assoc()) {
      $rows[] = $r;
    }
    $db->close();

    $_response->properties['size'] = intval($total_rows_data['COUNT(*)']);
    $_response->properties['page_size'] = $page_size;

    return $rows;
  }

  public function get_user_group_by_id($_response, $groupId) {
    $db = \EWCore::get_db_PDO();

    $statement = $db->prepare("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups WHERE id = ?");
    $statement->execute([$groupId]);


    if ($user_group_info = $statement->fetch(\PDO::FETCH_ASSOC)) {
      if ($user_group_info["type"] === "superuser") {
        $user_group_info["permission"] = implode(",", EWCore::read_permissions_ids());
      }

      return \ew\APIResourceHandler::to_api_response($user_group_info);
    }
    return \ew\APIResourceHandler::to_api_response(null);
  }

  public static function get_users_group_by_type($type) {
    $db = \EWCore::get_db_connection();
    if (!$type)
      $type = $db->real_escape_string($_REQUEST['type']);

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups WHERE type = '$type'") or die($db->error);

    if ($rows = $result->fetch_assoc()) {
      $db->close();
      return json_encode($rows);
    }
  }

  public static function get_user_by_id($userId) {
    $db = \EWCore::get_db_PDO();

    $statement = $db->prepare("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE id = ?");
    $statement->execute([$userId]);
    //$result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE id = '$userId'") or $db->error;

    if ($user_info = $statement->fetch(\PDO::FETCH_ASSOC)) {
      //$db->close();
      return \ew\APIResourceHandler::to_api_response($user_info);
    }
    return \EWCore::log_error(404, "User not found");
  }

  public static function get_user($userId = null) {
    $db = \EWCore::get_db_connection();
    if (!$userId) {
      $userId = $db->real_escape_string($_REQUEST["userId"]);
    }

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE id = '$userId'") or $db->error;

    if ($rows = $result->fetch_assoc()) {
      $db->close();
      return json_encode($rows);
    }
  }

  public static function get_user_by_email($email = null) {
    $db = \EWCore::get_db_connection();
    if (!$email) {
      $email = $db->real_escape_string($_REQUEST['email']);
    }

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE email = '$email'") or $db->error;

    if ($rows = $result->fetch_assoc()) {
      $db->close();

      return json_encode($rows);
    }
    return NULL;
  }

  public static function random_password() {
    $alphabet = "!@#$%^&*() abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = []; //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 20; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }

  public static function add_user($email, $first_name, $last_name, $password, $group_id = 0) {
    $db = \EWCore::get_db_connection();
    /* if (!$email)
      $email = $db->real_escape_string($_REQUEST["email"]);
      if (!$first_name)
      $first_name = $db->real_escape_string($_REQUEST["first_name"]);
      if (!$last_name)
      $last_name = $db->real_escape_string($_REQUEST["last_name"]);
      if (!$password)
      $password = $db->real_escape_string($_REQUEST["password"]);
      if (!$group_id)
      $group_id = $db->real_escape_string($_REQUEST["group_id"]);
      if (!$group_id)
      $group_id = 0; */

    if (self::get_user_by_email($email) != NULL) {
      return json_encode([
          status => "duplicate",
          error_message => "An  account with this email address is already exist"
      ]);
    }
    $stm = $db->prepare("INSERT INTO ew_users (email, first_name, last_name, password, group_id, date_created)
            VALUES (?, ?, ?, ?, ? ,?)") or die($db->error);
    $stm->bind_param("ssssss", $email, $first_name, $last_name, static::generate_hash($password), $group_id, date('Y-m-d H:i:s'));

    if ($stm->execute()) {

      return [
          status => "success",
          email => $email,
          message => "New user '$email' has been added successfully",
          "id" => $stm->insert_id
      ];
      $db->close();
    }
    return json_encode([
        status => "unsuccess",
        message => "New User has been NOT added"
    ]);
  }

  public static function add_user_skip($email, $first_name, $last_name, $password) {

    $db = \EWCore::get_db_connection();
    if (!$email)
      $email = $db->real_escape_string($_REQUEST["email"]);
    if (!$first_name)
      $first_name = $db->real_escape_string($_REQUEST["first_name"]);
    if (!$last_name)
      $last_name = $db->real_escape_string($_REQUEST["last_name"]);
    $password = self::random_password();
    $group_id = 0;
    if (!$group_id)
      $group_id = 0;

    $user_info = json_decode(self::get_user_by_email($email), TRUE);

    if (!$user_info) {
      $stm = $db->prepare("INSERT INTO ew_users (email, first_name, last_name, password, group_id, date_created)
            VALUES (?, ?, ?, ?, ? ,?)") or die($db->error);
      $stm->bind_param("ssssss", $email, $first_name, $last_name, $password, $group_id, date('Y-m-d H:i:s'));

      if ($stm->execute()) {
        $user_info = [
            "id" => $stm->insert_id,
            "email" => $email,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "password" => $password
        ];
        $db->close();
      }
    }
    return json_encode($user_info);
  }

  public function groups_create(\ew\APIResponse $_response, $_input) {
    $result = (new UsersGroupsRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function groups_read(\ew\APIResponse $_response, $_input, $_parts__id) {
    $_input->id = $_parts__id;

    $result = (new UsersGroupsRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function groups_update(\ew\APIResponse $_response, $_input, $_parts__id) {
    $_input->id = $_parts__id;

    $result = (new UsersGroupsRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function groups_delete(\ew\APIResponse $_response, $_input, $_parts__id) {
    $_input->id = $_parts__id;

    $result = (new UsersGroupsRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function read() {
    return [
        'title' => $this->get_title(),
        'description' => $this->get_description(),
        'resources' => \EWCore::read_activities_as_array($this)
    ];
  }

}
