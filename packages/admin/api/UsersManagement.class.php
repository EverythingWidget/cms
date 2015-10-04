<?php

namespace admin;

use Section;
use EWCore;

session_start();

class UsersManagement extends Section
{

   public function init_plugin()
   {
      //echo "asdasd";
      $this->register_permission("see-users", "User can see users list", array(
          "get_users_list",
          "get_user_by_id",
          "get_user_by_email",
          "user-form.php_see",
          $this->get_index()));
      $this->register_permission("manipulate-users", "User can add, edit delete users", array(
          "add_user",
          "update_user",
          "delete_user",
          "user-form.php:tr{New User}",
          $this->get_index()));
      $this->register_permission("see-groups", "User can see user groups list", array(
          "get_users_groups_list",
          "get_user_group_by_id",
          "get_users_group_by_type",
          "users-group-form.php_see",
          $this->get_index()));
      $this->register_permission("manipulate-groups", "User can add, edit delete user group", array(
          "add_group",
          "update_group",
          "delete_group",
          "users-group-form.php:tr{New Group}",
          $this->get_index()));
      $this->add_listener("admin-api/UsersManagement/get_user_by_id", "test_plugin");
   }

   public function get_title()
   {
      return "Users";
   }

   public function get_description()
   {
      return "Manage users, create and edit roles, manage users premissions";
   }

   public function test_plugin($_data)
   {
      //$_data["first_name"] = "Jawady";
      return json_encode($_data);
   }

   public static function login($username, $password)
   {
      $db = \EWCore::get_db_connection();
      if (!$username)
         $username = $db->real_escape_string($_POST["username"]);
      if (!$password)
         $password = $db->real_escape_string($_POST["password"]);
//echo $username." ".$password;
      $user = $db->query("SELECT * FROM ew_users WHERE email = '$username' AND password = '$password' LIMIT 1") or die($db->error);
      /* $stm->execute();

        $user = $db->query("SELECT COUNT(*)  FROM events ") or die($db->error); */
      if ($user_info = $user->fetch_assoc())
      {
         $_SESSION['login'] = '1';
         $_SESSION['sesUserName'] = $username;
         $_SESSION['EW.USER_ID'] = $user_info["id"];
         $_SESSION['EW.USERNAME'] = $user_info["username"];
         //$_SESSION['EW.USER_PERMISSIONS'] = explode(",", $user_info["permission"]);
         return TRUE;
      }

      return FALSE;
   }

   public function logout($url)
   {
      unset($_SESSION['login']);
      session_destroy();
      if (!$url)
         $url = '/';
      header("Location: $url");
   }

   public static function sign_up()
   {
      $resullt = json_decode(self::add_user(null, null, null, null, null), TRUE);
      $user_id = $resullt["id"];
      if ($user_id)
      {
         $modules = EWCore::read_actions_registry("ew-user-action-sign-up");
         try
         {
            foreach ($modules as $id => $data)
            {
               if (method_exists($data["class"], $data["function"]))
               {
                  $function_result = call_user_func(array(
                      $data["class"],
                      $data["function"]), $user_id);
                  if ($function_result != true)
                  {
                     $message.=$function_result . "<br/>";
                  }
               }
            }
            $resullt = array(
                "status" => "success",
                "error_message" => $message);
         }
         catch (Exception $e)
         {
            
         }
      }
      return json_encode($resullt);
   }

   public static function user_has_permission($app_name, $class_name, $permission_id, $user_id)
   {
      $db_con = \EWCore::get_db_connection();
      if (!$user_id)
         $user_id = $_SESSION['EW.USER_ID'];
      if (!$user_id)
         $user_id = $db_con->real_escape_string($_REQUEST["userId"]);

      $user = $db_con->query("SELECT * FROM ew_users, ew_users_groups WHERE ew_users.group_id = ew_users_groups.id AND ew_users.id = '$user_id' LIMIT 1") or die($db_con->error);
      if ($user_info = $user->fetch_assoc())
      {
         //echo $user_info["permission"]."-----$app_name+$class_name+$permission_id-";
         $user_permissions = explode(",", $user_info["permission"]);
         //if (is_array($permission_id))
         //{
         foreach ($user_permissions as $permission)
         {
            foreach ($permission_id as $item)
            {
               if ($permission === "$app_name.$class_name.$item")
                  return TRUE;
            }
         }
      }
      return FALSE;
   }

   public function get_users_list($token = 0, $size = 999999)
   {
      $db = \EWCore::get_db_connection();

      if (!isset($token))
      {
         $token = 0;
      }
      if (!isset($size))
      {
         $size = '18446744073709551610';
      }
      $size = ", $size";

      $totalRows = $db->query("SELECT COUNT(*) FROM ew_users, ew_users_groups WHERE ew_users.group_id = ew_users_groups.id") or die(error_reporting());
      $totalRows = $totalRows->fetch_assoc();
      //echo $size;
      $result = $db->query("SELECT ew_users.id,email, first_name, last_name,ew_users_groups.title, DATE_FORMAT(ew_users.date_created,'%Y-%m-%d') AS round_date_created FROM ew_users, ew_users_groups WHERE ew_users.group_id = ew_users_groups.id ORDER BY ew_users.id LIMIT $token $size") or die($db->error);

      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $db->close();
      $out = array(
          "totalRows" => $totalRows['COUNT(*)'],
          "result" => $rows);
      return json_encode($out);
   }

   public static function get_users_groups_list()
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
         $size = 1000000000;
      }
      $totalRows = $db->query("SELECT COUNT(*) FROM ew_users_groups") or die(error_reporting());
      $totalRows = $totalRows->fetch_assoc();

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups ORDER BY id LIMIT $token, $size") or die($db->error);

      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $db->close();
      $out = array(
          "totalRows" => $totalRows['COUNT(*)'],
          "result" => $rows);
      return json_encode($out);
   }

   public static function get_user_group_by_id($groupId)
   {
      $db = \EWCore::get_db_connection();
      if (!$groupId)
         $groupId = $db->real_escape_string($_REQUEST["groupId"]);

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups WHERE id = '$groupId'") or $db->error;

      if ($rows = $result->fetch_assoc())
      {
         $db->close();

         /* $actions = EWCore::read_actions_registry("ew-article-action-get");
           try
           {
           foreach ($actions as $userId => $data)
           {
           if (method_exists($data["class"], $data["function"]))
           {
           $func_result = call_user_func(array($data["class"], $data["function"]), $rows);
           if ($func_result)
           $rows = $func_result;
           }
           }
           } catch (Exception $e)
           {

           } */

         return json_encode($rows);
      }
   }

   public static function get_users_group_by_type($type)
   {
      $db = \EWCore::get_db_connection();
      if (!$type)
         $type = $db->real_escape_string($_REQUEST["type"]);

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users_groups WHERE type = '$type'") or die($db->error);

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         return json_encode($rows);
      }
   }

   public function add_group($title = null, $description = null, $permission = null)
   {
      $db = \EWCore::get_db_connection();
      if (!$title)
         $title = $db->real_escape_string($_REQUEST["title"]);
      if (!$description)
         $description = $db->real_escape_string($_REQUEST["description"]);
      if (!$permission)
         $permission = $db->real_escape_string($_REQUEST["permission"]);

      $stm = $db->prepare("INSERT INTO ew_users_groups (title, description, permission,date_created)
            VALUES (?, ?, ?, ?)");
      $stm->bind_param("ssss", $title, $description, $permission, date('Y-m-d H:i:s'));

      if ($stm->execute())
      {
         //$db->close();

         /* $actions = EWCore::read_actions_registry("ew-article-action-get");
           try
           {
           foreach ($actions as $userId => $data)
           {
           if (method_exists($data["class"], $data["function"]))
           {
           $func_result = call_user_func(array($data["class"], $data["function"]), $rows);
           if ($func_result)
           $rows = $func_result;
           }
           }
           } catch (Exception $e)
           {

           } */

         return json_encode(array(
             status => "success",
             title => $title,
             message => "Users group '$title' has been added successfully",
             "id" => $db->insert_id));
      }
      return json_encode(array(
          status => "unsuccess",
          title => "Update Group Unsuccessfull",
          message => "Users group has been NOT added"));
   }

   public function update_group($id = null, $title = null, $description = null, $permission = null)
   {
      $db = \EWCore::get_db_connection();

      $stm = $db->prepare("UPDATE ew_users_groups 
            SET title = ? 
            , description = ? 
            , permission = ? WHERE id = ?");
      $stm->bind_param("ssss", $title, $description, $permission, $id);

      if ($stm->execute())
      {
         $db->close();

         /* $actions = EWCore::read_actions_registry("ew-article-action-get");
           try
           {
           foreach ($actions as $userId => $data)
           {
           if (method_exists($data["class"], $data["function"]))
           {
           $func_result = call_user_func(array($data["class"], $data["function"]), $rows);
           if ($func_result)
           $rows = $func_result;
           }
           }
           } catch (Exception $e)
           {

           } */

         return json_encode(array(
             status => "success",
             title => $title,
             message => "tr{Users group} '$title' tr{has been updated successfully}"));
      }
      return EWCore::log_error("400", "Users group has been NOT updated", $db->error_list);
      //return json_encode(array(status => "unsuccess", title => "Update Group Unsuccessfull", message => "Users group has been NOT updated"));
   }

   public function delete_group($groupId = null)
   {

      $db = \EWCore::get_db_connection();
      if (!$groupId)
         $groupId = $db->real_escape_string($_REQUEST["id"]);
      $group_info = json_decode(self::get_user_group_by_id($groupId), true);
      $stm = $db->prepare("DELETE FROM ew_users_groups WHERE id = ?");
      $stm->bind_param("s", $groupId);

      if ($stm->execute())
      {
         $db->close();

         /* $actions = EWCore::read_actions_registry("ew-article-action-get");
           try
           {
           foreach ($actions as $userId => $data)
           {
           if (method_exists($data["class"], $data["function"]))
           {
           $func_result = call_user_func(array($data["class"], $data["function"]), $rows);
           if ($func_result)
           $rows = $func_result;
           }
           }
           } catch (Exception $e)
           {

           } */

         return json_encode(array(
             status => "success",
             title => $group_info["title"],
             message => "tr{Users group} '{$group_info["title"]}' tr{has been deleted successfully}"));
      }
      return EWCore::log_error("400", "tr{Users group has been NOT deleted}", $db->error_list);
   }

   public static function get_user_by_id($userId = null)
   {
      $db = \EWCore::get_db_connection();
      if (!$userId)
         $userId = $db->real_escape_string($_REQUEST["userId"]);

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE id = '$userId'") or $db->error;

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         return json_encode($rows);
      }
   }

   public static function get_user($userId = null)
   {
      $db = \EWCore::get_db_connection();
      if (!$userId)
         $userId = $db->real_escape_string($_REQUEST["userId"]);

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE id = '$userId'") or $db->error;

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         return json_encode($rows);
      }
   }

   public static function get_user_by_email($email = null)
   {
      $db = \EWCore::get_db_connection();
      if (!$email)
         $email = $db->real_escape_string($_REQUEST["email"]);

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_users WHERE email = '$email'") or $db->error;

      if ($rows = $result->fetch_assoc())
      {
         $db->close();

         return json_encode($rows);
      }
      return NULL;
   }

   public static function random_password()
   {
      $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 8; $i++)
      {
         $n = rand(0, $alphaLength);
         $pass[] = $alphabet[$n];
      }
      return implode($pass); //turn the array into a string
   }

   public static function add_user($email, $first_name, $last_name, $password, $group_id)
   {
      $db = \EWCore::get_db_connection();
      if (!$email)
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
         $group_id = 0;

      if (self::get_user_by_email($email) != NULL)
      {
         return json_encode(array(
             status => "duplicate",
             error_message => "An  account with this email address is already exist"));
      }
      $stm = $db->prepare("INSERT INTO ew_users (email, first_name, last_name, password, group_id, date_created)
            VALUES (?, ?, ?, ?, ? ,?)") or die($db->error);
      $stm->bind_param("ssssss", $email, $first_name, $last_name, $password, $group_id, date('Y-m-d H:i:s'));

      if ($stm->execute())
      {
         $db->close();
         return json_encode(array(
             status => "success",
             email => $email,
             message => "New user '$email' has been added successfully",
             "id" => $db->insert_id));
      }
      return json_encode(array(
          status => "unsuccess",
          message => "New User has been NOT added"));
   }

   public static function add_user_skip($email, $first_name, $last_name, $password)
   {

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

      if (!$user_info)
      {
         $stm = $db->prepare("INSERT INTO ew_users (email, first_name, last_name, password, group_id, date_created)
            VALUES (?, ?, ?, ?, ? ,?)") or die($db->error);
         $stm->bind_param("ssssss", $email, $first_name, $last_name, $password, $group_id, date('Y-m-d H:i:s'));

         if ($stm->execute())
         {
            $db->close();
            //return json_encode(array(status => "success", email => $email, message => "New user '$email' has been added successfully", "id" => $db->insert_id));
            $user_info = array(
                "id" => $db->insert_id,
                "email" => $email,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "password" => $password);
         }
      }
      return json_encode($user_info);
   }

   public function update_user($id, $email, $first_name, $last_name, $password, $group_id)
   {
      $db = \EWCore::get_db_connection();
      if (!$id)
         $id = $db->real_escape_string($_REQUEST["id"]);
      if (!$email)
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
         $group_id = 0;

      $stm = $db->prepare("UPDATE ew_users SET email = ?, first_name = ?, last_name = ?, password = ?, group_id = ? WHERE id = ?");
      $stm->bind_param("ssssss", $email, $first_name, $last_name, $password, $group_id, $id);

      if ($stm->execute())
      {
         $db->close();
         return json_encode(array(
             status => "success",
             email => $email,
             message => "New user '$email' has been added successfully",
             "id" => $db->insert_id));
      }
      return json_encode(array(
          status => "unsuccess",
          message => "New User has been NOT added"));
   }

   public static function delete_user($userId = null)
   {

      $db = \EWCore::get_db_connection();
      if (!$userId)
         $userId = $db->real_escape_string($_REQUEST["id"]);
      $user_info = json_decode(self::get_user_by_id($userId), true);
      $stm = $db->prepare("DELETE FROM ew_users WHERE id = ?");
      $stm->bind_param("s", $userId);

      if ($stm->execute())
      {
         $db->close();

         return json_encode(array(
             status => "success",
             title => $user_info["email"],
             message => "User  '{$user_info["email"]}' has been deleted successfully"));
      }
      return json_encode(array(
          status => "unsuccess",
          title => "Update user Unsuccessfull",
          message => "User has been NOT deleted"));
   }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
