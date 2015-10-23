<?php

namespace ew;

/**
 *
 * @author Eeliya
 */
class PreProcessor
{

   public function __construct()
   {
      
   }

   //put your code here
   public function process($module, $verb, $method, $input)
   {
      if (method_exists($object, $method_name))
      {
         $method_object = new \ReflectionMethod($this, $method);
         return $method_object->invoke($this, $module, $verb, $method, $input);
      }
      return true;
   }

}
