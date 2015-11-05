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
class APIResourceHandler extends ResourceHandler
{

   private $verbs = [
       'GET' => 'get',
       'POST' => 'create',
       'PUT' => 'update',
       'DELETE' => 'delete'
   ];

   protected function handle($app, $app_resource_path, $module_name, $command_name, $parameters = null)
   {
      $verb = $this->verbs[$_SERVER['REQUEST_METHOD']];

      $command_name = $command_name ? $command_name : $verb;
      // parse method name to a api method name
      $method_name = str_replace('-', '_', $command_name);

      // Parse module name to a api module name
      $module_class_name = \EWCore::hyphenToCamel($module_name);

      $app_name = $app->get_root();
      $resource_name = $app_resource_path[1];
      $real_class_name = $app_name . '\\' . $module_class_name;
      if (!$module_class_name)
      {
         return \EWCore::log_error(400, "<h4>$app_name-api </h4><p>Please specify the api command</p>");
      }
      if (class_exists($real_class_name))
      {
         $permission_id = \EWCore::does_need_permission($app_name, $module_name, 'api/' . $command_name);
         if ($permission_id && $permission_id !== FALSE)
         {
            if (\admin\UsersManagement::user_has_permission($app_name, $module_name, $permission_id, $_SESSION['EW.USER_GROUP_ID']))
            {
               $app_section_object = new $real_class_name($app);
               return $app_section_object->process_request($verb, $method_name, $parameters);
            }
            else
            {
               return \EWCore::log_error(403, "tr{You do not have permission for this command}", array(
                           "Access Denied" => "$app_name/$module_class_name/$method_name"));
            }
         }
         $app_section_object = new $real_class_name($app);
         $result = $app_section_object->process_request($verb, $method_name, $parameters);

         $listeners = \EWCore::read_registry("$app_name-$resource_name/$module_class_name/$method_name" . '_listener');

         if (isset($listeners) && !is_array($result))
         {

            $converted_result = json_decode($result, true);
            if (json_last_error() === JSON_ERROR_NONE)
            {
               $result = $converted_result;
            }
         }

         try
         {
            // Call the listeners with the same data as the command data
            if (isset($listeners))
            {
               if (!is_array($result))
               {
                  $converted_result = json_decode($result, true);
                  if (json_last_error() === JSON_ERROR_NONE)
                  {
                     $result = $converted_result;
                  }
               }

               foreach ($listeners as $id => $listener)
               {
                  if (method_exists($listener["object"], $listener["function"]))
                  {
                     $listener_method_object = new \ReflectionMethod($listener["object"], $listener["function"]);
                     $arguments = \EWCore::create_arguments($listener_method_object, $parameters);

                     $listener_result = $listener_method_object->invokeArgs($listener["object"], $arguments);

                     if (isset($listener_result))
                     {
                        $result = array_merge($result, $listener_result);
                     }
                  }
               }
            }
         }
         catch (Exception $e)
         {
            echo $e->getTraceAsString();
         }

         if (is_array($result))
         {
            return json_encode($result);
         }
         return $result;
      }
      else
      {
         return \EWCore::log_error(404, "<h4>$app_name-api </h4><p>Section `$module_class_name` not found</p>");
      }
   }

//put your code here
}
