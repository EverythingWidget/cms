<?php

namespace ew;

use admin\UsersManagement;

/**
 * Description of APIResourceHandler
 *
 * @author Eeliya
 */
class APIResourceHandler extends ResourceHandler {

  private $verbs = [
      'GET' => 'read',
      'POST' => 'create',
      'PUT' => 'update',
      'DELETE' => 'delete',
      'OPTIONS' => 'options'
  ];
  public static $VERBS = [
      'read' => 'GET',
      'create' => 'POST',
      'update' => 'PUT',
      'delete' => 'DELETE',
      'options' => 'OPTIONS'
  ];
  private $cached_modules = [];

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
      return \EWCore::log_error(400, 'Request verb is unknown: server: ' . $_SERVER['REQUEST_METHOD'] . ', internal: ' . $api_verb);
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

    if ($this->cached_modules[$real_class_name]) {
      $app_section_object = $this->cached_modules[$real_class_name];
    } else if (class_exists($real_class_name)) {
      $app_section_object = new $real_class_name($app);
      $this->cached_modules[$real_class_name] = $app_section_object;
    } else {
      if (isset($output_as_array)) {
        return \EWCore::log_api_error(404, "Section not found: `$module_class_name`", [
            "$app_name/$module_class_name/$method_name"
        ]);
      }

      return \EWCore::log_error(404, "Section not found: `$module_class_name`");
    }

    // if command is null then fallback to the $verb
    if (!$command || is_numeric($method_name)) {
      $api_command_name = $api_method_name = $verb;
    } else {
      $api_method_name = $method_name . '_' . $verb;
      $api_command_name = $command_name . '-' . $verb;
    }

    if (!method_exists($app_section_object, $api_method_name)) {
      $api_method_name = $method_name;
      $api_command_name = $command_name;
    }

    // if method() does not exist
    if (!method_exists($app_section_object, $api_method_name)) {
      if (isset($output_as_array)) {
        return \EWCore::log_api_error(404, "api command not found: `$api_command_name`", [
            "$app_name/$module_name/$api_command_name"
        ]);
      }

      return \EWCore::log_error(404, "api command not found: `$api_command_name``", [
          'api call' => "$app_name/$module_name/$api_command_name"
      ]);
    }

    // The value after method name will be parsed into an array and passed ass _parts
    $parameters['_parts'] = array_slice(explode('/', $parameters['_file']), 1);

    // If id exist in header data, then take it as the id
    if (is_null($parameters['id'])) {
      // First part would be considered as an id if it's an integer value
      $parameters['id'] = is_numeric($parameters['_parts'][0]) ? intval($parameters['_parts'][0]) : null;
      // In the case that method name itself is an integer, then it will be considered as an id
      $parameters['id'] = is_numeric($method_name) ? intval($method_name) : $parameters['id'];
    }

    $parameters['_resource_id'] = $parameters['_parts'][0] ?
        rtrim($parameters['_parts'][0], '/') :
        rtrim($parameters['id'], '/');

    $permission_id = \EWCore::does_need_permission($app_name, $module_name, $resource_name . '/' . $api_command_name);
    if (!method_exists($app_section_object, $api_method_name)) {
      return \EWCore::log_error(404, "$app_name-$resource_name: Method not found: `$api_method_name`");
    }

    $response = new APIResponse("$resource_name/$app_name/$module_name/$api_command_name");
    $parameters['_response'] = $response;
    if ($permission_id === 'public-access') {
      $response_data = $app_section_object->process_request($verb, $api_method_name, $parameters);
      $call = true;
    } else if ($permission_id && $permission_id !== false) {
      if (UsersManagement::group_has_permission($app_name, $api_method_name, $permission_id, $_SESSION['EW.USER_GROUP_ID'])) {
        $response_data = $app_section_object->process_request($verb, $api_method_name, $parameters);
        $call = true;
      }
    } else if ($app_section_object->is_unathorized_method_invoke()) {
      $response_data = $app_section_object->process_request($verb, $api_method_name, $parameters);
      $call = true;
    }


    if (!isset($response_data)) {
      if ($call === false) {
        return \EWCore::log_error(403, 'You do not have corresponding permission to invoke this api request', [
            'Access Denied' => "$app_name/$module_class_name/$api_command_name"
        ]);
      }
    }

    $api_listeners = \EWCore::read_registry("$app_name/$resource_name/$module_name/$api_command_name");
    try {
      // Call the listeners with the same data as the command data
      $this->add_response_data($response, $response_data, $parameters, $real_class_name, $api_method_name, $output_as_array);
      if (isset($api_listeners)) {
        $this->execute_api_listeners($api_listeners, $parameters, $response);
      }
    } catch (Exception $e) {
      echo $e->getTraceAsString();
    }

//    if($response->properties['error_code']) {
//
//    }


    if ($response->downloadable) {
      return $response->to_file();
    }

    if (isset($output_as_array)) {
      return $response->to_array();
    }

    return $response->to_json();
  }

  private function add_response_data($response, $response_data, $parameters, $real_class_name, $api_method_name, $output_as_array) {
    if ($response_data instanceof APIResponse && $response_data !== $response) {
      $response = $response_data;
      $parameters['_response'] = $response;
    } else if (!($response_data instanceof APIResponse)) {
      if ($response_data instanceof \stdClass) {
        $response_data = (array)$response_data;
      } else if ($response_data !== null && !is_a($response_data, 'stdClass') && !is_array($response_data)) {
        $type = is_object($response_data) ? get_class($response_data) : gettype($response_data);

        if ($output_as_array) {
          return \EWCore::log_api_error(500, "Module can not return `$type`. Only array or stdClass is allowed", [
              $real_class_name . '->' . $api_method_name . " returns `$type`."
          ]);
        }

        die(\EWCore::log_error(500, "Module can not return `$type`. Only array or stdClass is allowed", [
            $real_class_name . '->' . $api_method_name . " returns `$type`."
        ]));
      }

      $response->set_data($response_data);
    }
  }

  private function execute_api_listeners($api_listeners, $parameters, $response) {
    foreach ($api_listeners as $id => $listener) {
      $object = $listener['object'];
      $listener = $listener['method'];
      if (method_exists($object, $listener)) {
        //$response_data = $response->to_array();
        $listener_method_object = new \ReflectionMethod($object, $listener);
        $arguments = \EWCore::create_arguments($listener_method_object, $parameters, $response);

        $listener_result = $listener_method_object->invokeArgs($object, $arguments);

        if (isset($listener_result)) {
          $response_data = $response->data;

          if ($listener_result instanceof \stdClass) {
            $listener_result = (array)$listener_result;
          } else if ($listener_result !== null && !is_a($listener_result, 'stdClass') && !is_array($listener_result)) {
            $type = is_object($object) ? get_class($object) : gettype($object);
            die(\EWCore::log_error(500, 'Module can not return object. Only array or stdClass is allowed', [
                $type . '->' . $listener . ' returns object.'
            ]));
          }

          $response->set_data(array_merge_recursive(is_array($response_data) ? $response_data : [], $listener_result));
        }
      }
    }
  }

  public static function to_api_response($data, $meta = []) {
    $response = new APIResponse($_SERVER['REQUEST_URI']);
    $response->set_status_code(200);
    if (is_null($data)) {
      $response->set_type(null);
    } else {
      $response->set_type(array_keys($data) === range(0, count($data) - 1) ? 'list' : 'item');
    }

    $response->set_meta($meta);
    $response->set_data($data);

    return $response;
  }

  //put your code here
}
