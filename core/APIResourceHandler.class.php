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

   private function hyphenToCamel($val)
   {
      $val = str_replace(' ', '', ucwords(str_replace('-', ' ', $val)));
      $val = substr($val, 0);
      return $val;
   }

   protected function handle($app, $app_resource_path, $module_name, $method_name, $parameters = null)
   {
      $verb = $this->verbs[$_SERVER['REQUEST_METHOD']];

      $method_name = $method_name ? $method_name : $verb;
      // parse method name to a api method name
      $method_name = str_replace('-', '_', $method_name);

      // Parse module name to a api module name
      $module_name = $this->hyphenToCamel($module_name);

      $app_name = $app->get_root();
      $real_class_name = $app_name . '\\' . $module_name;
      if (!$module_name)
      {
         return \EWCore::log_error(400, "<h4>$app_name-api </h4><p>Please specify the api command</p>");
      }
      if (class_exists($real_class_name))
      {
         
         $permission_id = \EWCore::does_need_permission($app_name, $module_name, $method_name);
         if ($permission_id && $permission_id !== FALSE)
         {
            
            if (\admin\UsersManagement::user_has_permission($app_name, $module_name, $permission_id, $_SESSION['EW.USER_ID']))
            {
               $app_section_object = new $real_class_name($app);
               return $app_section_object->process_request($verb, $method_name, $parameters);
            }
            else
            {
               return \EWCore::log_error(403, "tr{You do not have permission for this command}", array(
                           "Access Denied" => "$app_name/$module_name/$method_name"));
            }
         }
         $app_section_object = new $real_class_name($app);
         return $app_section_object->process_request($verb, $method_name, $parameters);
      }
      else
      {
         return \EWCore::log_error(404, "<h4>$app_name-api </h4><p>Section `$module_name` not found</p>");
      }
   }

//put your code here
}
