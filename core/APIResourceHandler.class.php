<?php

namespace ew;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of APIResourceHandler
 *
 * @author Eeliya
 */
class APIResourceHandler extends ResourceHandler {

  private $verbs = [
      'GET'    => 'get',
      'POST'   => 'create',
      'PUT'    => 'update',
      'DELETE' => 'delete'
  ];

  protected function handle($app, $package, $resource_type, $module_name, $command, $parameters = null) {
    $output_array = $this->get_parameter("output_array");
    if ($output_array !== true) {
      header("Content-Type: application/json");
    }
    // check module name string
    if (preg_match('/[A-Z]/', $module_name)) {
      return \EWCore::log_error(400, "Incorrect module name: $module_name");
    }
    // check command string
    if (preg_match('/_/', $command)) {
      return \EWCore::log_error(400, "Incorrect function name: $command");
    }
    $verb = $this->verbs[$_SERVER['REQUEST_METHOD']];

    // Set the verb as command name if command is empty
    $command_name = $command ? $command : $verb;
    // parse method name to a api method name
    $method_name = str_replace('-', '_', $command_name);
    // Parse module name to a api module class name
    $module_class_name = \EWCore::hyphenToCamel($module_name);

    $app_name = $app->get_root();
    $resource_name = $resource_type;
    $real_class_name = $app_name . '\\' . $module_class_name;
    
    $call = false;

    if (!$module_class_name) {
      return \EWCore::log_error(400, "<h4>$app_name-api </h4><p>Please specify the api command</p>");
    }
    
    if (class_exists($real_class_name)) {
      $permission_id = \EWCore::does_need_permission($app_name, $module_name, $resource_name . '/' . $command_name);
      
      $parameters["_parts"] = array_slice(explode('/', $parameters["_file"]), 1);
      $app_section_object = new $real_class_name($app);

      if ($permission_id === "public-access") {
        $result = $app_section_object->process_request($verb, $method_name, $parameters);
        $call = true;
      }
      else {
        //var_dump($permission_id);

        if (!method_exists($app_section_object, $method_name)) {
          return \EWCore::log_error(404, "$app_name-$resource_name: Method not found: `$method_name`");
        }

        if ($permission_id && $permission_id !== false) {
          if (\admin\UsersManagement::group_has_permission($app_name, $module_name, $permission_id, $_SESSION['EW.USER_GROUP_ID'])) {
            $result = $app_section_object->process_request($verb, $method_name, $parameters);
            $call = true;
          }
        }
        else if ($app_section_object->is_unathorized_method_invoke()) {
          $result = $app_section_object->process_request($verb, $method_name, $parameters);
          $call = true;
        }
      }

      if (!isset($result) && $call === false) {
        return \EWCore::log_error(403, "You do not have corresponding permission to invoke this api request", array(
                    "Access Denied" => "$app_name/$module_class_name/$method_name"));
      }

      $api_listeners = \EWCore::read_registry("$app_name/$resource_name/$module_name/$command_name");

      if (isset($api_listeners) && !is_array($result)) {

        $converted_result = json_decode($result, true);
        if (json_last_error() === JSON_ERROR_NONE) {
          $result = $converted_result;
        }
      }

      try {
        // Call the listeners with the same data as the command data
        if (isset($api_listeners)) {
          if (!is_array($result)) {
            $converted_result = json_decode($result, true);
            if (json_last_error() === JSON_ERROR_NONE) {
              $result = $converted_result;
            }
          }

          foreach ($api_listeners as $id => $listener) {
            if (method_exists($listener["object"], $listener["method"])) {
              $listener_method_object = new \ReflectionMethod($listener["object"], $listener["method"]);
              $arguments = \EWCore::create_arguments($listener_method_object, $parameters, $result);

              $listener_result = $listener_method_object->invokeArgs($listener["object"], $arguments);

              if (isset($listener_result)) {
                $result = array_merge_recursive($result, $listener_result);
              }
            }
          }
        }
      }
      catch (Exception $e) {
        echo $e->getTraceAsString();
      }

      if (!isset($output_array)) {
        if (is_array($result)) {
          return json_encode($result);
        }
      }
      return $result;
    }
    else {
      return \EWCore::log_error(404, "Section not found: `$module_class_name`");
    }
  }

  public static function to_api_response($data, $meta = []) {
    $response = ["statusCode" => 200];
    $response = array_merge($response, $meta);
    $response["data"] = $data;
    return $response;
  }

//put your code here
}
