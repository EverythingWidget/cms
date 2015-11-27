<?php

namespace ew;

/** PreProcessor is called whenever before a Collection method is called.
 * PreProcessor then search for the method with the same name and invoke it if it exist.
 * 
 * If a method of PreProcessor wants to stop the system process, then it should return false
 * The method can also add a error to $input to explain the reason of stop.
 * 
 * Remeber the main process stops if and only if the PreProcessor method return false
 *
 * @author Eeliya
 */
class PreProcessor
{

   public function __construct()
   {
      
   }

   /** This method will be called every time an Collection method/resource is called.
    * Then this method search for a method with same name as requested collection method and if it exist it will call it
    * 
    * @param type $module
    * @param type $verb
    * @param type $method
    * @param type $input
    * @return boolean
    */
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
