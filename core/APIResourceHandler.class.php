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
      'GET'    => 'read',
      'POST'   => 'create',
      'PUT'    => 'update',
      'DELETE' => 'delete'
  ];
  public static $VERBS = [
      'read'   => 'GET',
      'create' => 'POST',
      'update' => 'PUT',
      'delete' => 'DELETE'
  ];

  protected function handle($app, $package, $resource_type, $module_name, $command, $parameters = null) {
    $output_as_array = $this->get_parameter('output_array');
    $api_verb = $this->get_parameter('verb');

    if ($output_as_array !== true) {
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

    $verb = $api_verb ? $this->verbs[strtoupper($api_verb)] : $this->verbs[strtoupper($_SERVER['REQUEST_METHOD'])];

    if (!$verb) {
      return \EWCore::log_error(400, 'Request verb is unknown: $verb');
    }

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
      //echo "$app_name, $module_name, $resource_name / $command_name";
      $app_section_object = new $real_class_name($app);
      $api_method_name = $method_name . '_' . $verb;
      $api_command_name = $method_name . '-' . $verb;
      if (method_exists($app_section_object, $method_name)) {
        $api_method_name = $method_name;
        $api_command_name = $command_name;
      }

      $permission_id = \EWCore::does_need_permission($app_name, $module_name, $resource_name . '/' . $api_command_name);

      if (!method_exists($app_section_object, $api_method_name)) {
        return \EWCore::log_error(404, "$app_name-$resource_name: Method not found: `$api_method_name`");
      }

      $parameters["_parts"] = array_slice(explode('/', $parameters["_file"]), 1);
      $response = new APIResponse();
      $parameters['_response'] = $response;

      if ($permission_id === "public-access") {
        $response_data = $app_section_object->process_request($verb, $api_method_name, $parameters);
        $call = true;
      }
      else {
        if ($permission_id && $permission_id !== false) {
          if (\admin\UsersManagement::group_has_permission($app_name, $api_method_name, $permission_id, $_SESSION['EW.USER_GROUP_ID'])) {
            $response_data = $app_section_object->process_request($verb, $api_method_name, $parameters);
            $call = true;
          }
        }
        else if ($app_section_object->is_unathorized_method_invoke()) {
          $response_data = $app_section_object->process_request($verb, $api_method_name, $parameters);
          $call = true;
        }
      }

      if (!isset($response_data)) {
        if ($call === false) {
          return \EWCore::log_error(403, "You do not have corresponding permission to invoke this api request", [
                      "Access Denied" => "$app_name/$module_class_name/$api_command_name"
          ]);
        }
      }

      $api_listeners = \EWCore::read_registry("$app_name/$resource_name/$module_name/$api_command_name");

//      if (isset($api_listeners) && is_string($result)) {
//        $converted_result = json_decode($result, true);
//        if (json_last_error() === JSON_ERROR_NONE) {
//          $result = $converted_result;
//        }
//      }


      try {
        // Call the listeners with the same data as the command data
        if (isset($api_listeners)) {
          if ($response_data instanceof APIResponse && $response_data !== $response) {
            $response = $response_data;
            $parameters['_response'] = $response;
          }
          else if (!($response_data instanceof APIResponse)) {
            if ($response_data instanceof \stdClass) {
              $response_data = (array) $response_data;
            }
            else if ($response_data !== null && !is_a($response_data, 'stdClass') && !is_array($response_data)) {
              die(\EWCore::log_error(500, 'Module can not return object. Only array or stdClass is allowed ' . get_class($response_data), [
                          $real_class_name . '->' . $api_method_name . ' returns object.'
              ]));
            }

            $response->set_data($response_data);
          }

          $this->execute_api_listeners($api_listeners, $parameters, $response);
        }
      }
      catch (Exception $e) {
        echo $e->getTraceAsString();
      }

//      if (is_null($result)) {
//        $result = $this->to_api_response(null);
//      }

      if (isset($output_as_array)) {
        return $response->to_array();
      }

      return $response->to_json();
    }
    else {
      return \EWCore::log_error(404, "Section not found: `$module_class_name`");
    }
  }

  private function execute_api_listeners($api_listeners, $parameters, $response) {
    foreach ($api_listeners as $id => $listener) {
      if (method_exists($listener["object"], $listener["method"])) {
        //$response_data = $response->to_array();
        $listener_method_object = new \ReflectionMethod($listener["object"], $listener["method"]);
        $arguments = \EWCore::create_arguments($listener_method_object, $parameters, $response);

        $listener_result = $listener_method_object->invokeArgs($listener["object"], $arguments);

        if (isset($listener_result)) {
          $response_data = $response->data;

          if ($listener_result instanceof \stdClass) {
            $listener_result = (array) $listener_result;
          }
          else if ($listener_result !== null && !is_a($listener_result, 'stdClass') && !is_array($listener_result)) {
            die(\EWCore::log_error(500, 'Module can not return object. Only array or stdClass is allowed', [
                        get_class($listener['object']) . '->' . $listener['method'] . ' returns object.'
            ]));
          }

          $response->set_data(array_merge_recursive(is_array($response_data) ? $response_data : [], $listener_result));
        }
      }
    }
  }

  public static function to_api_response($data, $meta = []) {
    $response = new APIResponse();
    //$response['status_code'] = 200;
    $response->set_status_code(200);

    if (is_null($data)) {
      $response->set_type(null);
    }
    else {
      $response->set_type(array_keys($data) === range(0, count($data) - 1) ? 'list' : 'item');
    }

    $response->set_meta($meta);
    $response->set_data($data);

    return $response;
  }

//put your code here
}
