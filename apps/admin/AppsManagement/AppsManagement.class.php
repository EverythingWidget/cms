<?php
namespace admin;

use Section;
use EWCore;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class AppsManagement extends Section
{

   public function get_title()
   {
      return "Apps";
   }

   public function get_app_sections($appDir)
   {
      $tempEW = new EWCore($appDir . '/');

      $path = EW_APPS_DIR . '/' . $appDir . '/';

      $section_dirs = opendir($path);
      $sections = array();

      // Search app's root's dir
      while ($section_dir = readdir($section_dirs))
      {
         if (strpos($section_dir, '.') === 0)
            continue;
         //$section_dir = opendir($path . $section_dir);
         //while ($file = readdir($section_dir))
         //{
         //if (strpos($file, '.') === 0)
         //continue;
         //$i = strpos($file, '.class.php');
         //if ($i != 0)
         //{
         //$section_class_name = substr($file, 0, $i);
         $section_class_name = $section_dir;
         $class_name = $section_dir;
         $namespace_class_name = $appDir . "\\" . $section_class_name;
         if (class_exists($namespace_class_name))
         {
            $section_class_name = $namespace_class_name;
         }

         if (class_exists($section_class_name) && get_parent_class($section_class_name) == 'Section')
         {
            $sc = new $section_class_name($section_class_name, $_REQUEST);
            //echo $appDir." ".$class_name;
            $permission_id = EWCore::does_need_permission($appDir, $class_name, $sc->get_index());

            if ($permission_id && $permission_id !== FALSE)
            {
               // Check for user permission
               if (!UsersManagement::user_has_permission($appDir, $class_name, $permission_id))
               {
                  continue;
               }
            }

            if ($sc->get_title() && !$sc->is_hidden())
               $sections[] = array("title" => "tr:$appDir" . "{" . $sc->get_title() . "}", "className" => $class_name, "description" => "tr:$appDir" . "{" . $sc->get_description() . "}");
         }
         // }
         //}
      }

      /* if (!empty($sections))
        return json_encode($sections);

        $path = EW_APPS_DIR . '/' . $appDir . '/sections/';

        $section_dirs = opendir($path);

        while ($section_dir = readdir($section_dirs))
        {
        if (strpos($section_dir, '.') === 0)
        continue;
        $section_dir = opendir($path . $section_dir);
        while ($file = readdir($section_dir))
        {
        if (strpos($file, '.') === 0)
        continue;
        $i = strpos($file, '.class.php');
        if ($i != 0)
        {
        $section_class_name = substr($file, 0, $i);
        $class_name = $section_class_name;
        $namespace_class_name = $appDir . "\\" . $section_class_name;
        if (class_exists($namespace_class_name))
        {
        $section_class_name = $namespace_class_name;
        }
        if (class_exists($section_class_name) && get_parent_class($section_class_name) == 'Section')
        {
        $sc = new $section_class_name($section_class_name, $_REQUEST);
        //echo $section_class_name;
        if ($sc->get_title() && !$sc->is_hidden())
        $sections[] = array("title" => "tr:$appDir" . "{" . $sc->get_title() . "}", "className" => $class_name, "description" => "tr:$appDir" . "{" . $sc->get_description() . "}");
        }
        }
        }
        } */
      //EWCore::add_app_locale_to_translator($_REQUEST["appDir"]);
      return json_encode($sections);
   }

   public function get_description()
   {
      return "Your app's Control Panel";
   }

//put your code here
}
