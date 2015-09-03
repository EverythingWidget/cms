<?php

session_start();
// 05 November, 2013
// Do NOT touch this file unless you are expert in Everything Widget CMS
// All contents inside the apps directory is reached throuth this file
// parse app_name, section_name, function_name from url

ob_start();
require '../core/config/config.php';
require '../core/config/database_config.php';
require '../core/EWCore.class.php';
require '../core/modules/Valitron/Validator.php';
ob_end_clean();
if (ob_get_level())
   ob_end_clean();

error_reporting(E_ERROR);
ini_set('display_errors', '1');

EWCore::set_default_locale("admin");
//EWCore::set_db_connection(get_db_connection());

$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
// Decode url to a normal string
$path = urldecode($path);
if (strpos($path, '?') !== false)
   $path = substr($path, 0, strpos($path, '?'));
$elements = explode('/', $path);
$parameter_index = 0;
if (strpos(EW_DIR, $elements[0]))
{
   $root_dir = $elements[0];
   $parameter_index = 1;
}

// Check the language parameter
$language = "en";
$default_language = EWCore::read_setting("ew/language");

if ($default_language)
{
   $language = $default_language;
}
if (preg_match("/^(.{2,3})$/", $elements[$parameter_index], $match))
{
   $language = $match[0];
   $_REQUEST["_url_language"] = $language;
   $parameter_index++;
}

$_REQUEST["_language"] = $language;

$url_language = ($language == "en") ? '' : $language . '/';
// Set the language for the root url
$u = 'http://' . $_SERVER['SERVER_NAME'] . EW_DIR_URL . $url_language;
define('EW_ROOT_URL', $u);

// Check the app parameter
$app_name = "webroot";
$default_recourse = "html";

if ($elements[$parameter_index])
{
   $app_resource_path = explode('-', $elements[$parameter_index]);
   if (count($app_resource_path) === 1)
   {
      $app_resource_path[] = $default_recourse;
   }
   $app_name = implode('/', $app_resource_path);
   $parameter_index++;
}

//$resource_path = "$app_name-$default_recourse";
$_REQUEST["_app_name"] = $app_name;

// Check the asset parameter
/* if ($elements[$parameter_index] == 'asset')
  {
  $app_name = 'asset';
  $_REQUEST["_app_name"] = $app_name;
  $parameter_index++;
  } */

// Read the section name parameter
$section_name = null;
if (isset($elements[$parameter_index]) && preg_match("/^([^\.]*)$/", $elements[$parameter_index], $match))
{
   $section_name = $elements[$parameter_index];
   $_REQUEST["_section_name"] = $section_name;

   $parameter_index++;
}

// Read the function name parameter
$function_name = null;
if (isset($elements[$parameter_index]))
{
   $function_name = $elements[$parameter_index];
   $function_name = ($function_name == 'index.php') ? 'index' : $function_name;
   $_REQUEST["_function_name"] = $function_name;

   $rest_of_elements = array_slice($elements, $parameter_index);
   $file_uri = implode('/', $rest_of_elements);
   //if (strpos($file_uri, '?'))
   //$file_uri = substr($file_uri, 0, strpos($file_uri, '?'));
   $_file = $file_uri;
   $_REQUEST["_file"] = $_file;

   $parameter_index++;
}
// Create instance of EWCore class 
global $EW;
$EW = new \EWCore();

// set default user group if no user group has been spacified
if (!isset($_SESSION["EW.USERS_GROUP"]))
{
   $_SESSION["EW.USERS_GROUP"] = EWCore::get_default_users_group();
}

// If app name is asset then call get_resource
if ($app_name == "asset")
{
   EWCore::get_resource($section_name, array(
       $_file));
   return;
}

$r_uri = strtok($_SERVER["REQUEST_URI"], "?");

// If root dir is same with the uri then refer to the base url
if ($root_dir == str_replace("/", "", $r_uri))
   $r_uri = "/";
// Check if UI structure is specified
if (!isset($_REQUEST["_uis"]))
{

   if ($section_name)
      $r_uri = "/" . $section_name;
   if ($_file)
      $r_uri.='/' . $_file;
   $r_uri = str_replace('/' . $root_dir, "", $r_uri);
   //echo $r_uri.'<br/>';
   //echo $r_uri;
   // Remove last /
   if (strlen($r_uri) > 1 && substr($r_uri, -1) == "/")
      $r_uri = substr($r_uri, 0, strlen($r_uri) - 1);
   $uis_data = EWCore::get_url_uis($r_uri);
   $_REQUEST["_uis"] = $uis_data["uis_id"];
   $_REQUEST["_uis_template"] = $uis_data["uis_template"];
   if (!$_REQUEST["_uis_template_settings"])
      $_REQUEST["_uis_template_settings"] = $uis_data["uis_template_settings"];
   //print_r($_REQUEST);
}
else
{

   $uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);
   $_REQUEST["_uis_template"] = $uis_data["template"];
   if (!$_REQUEST["_uis_template_settings"])
      $_REQUEST["_uis_template_settings"] = $uis_data["template_settings"];
}

$GLOBALS["page_parameters"] = explode("/", $_REQUEST["_parameters"]);
$RESULT_CONTENT = "RESULT_CONTENT: EMPTY";

$real_class_name = $app_name . '\\' . $section_name;
print_r($_REQUEST);

//$RESULT_CONTENT = EWCore::process_command($app_name, $section_name, $function_name, $_REQUEST);

function translate($match)
{
   global $language;
   return EWCore::translate_to_locale($match, $language);
}

// show the result
if ($RESULT_CONTENT)
{
   //$RESULT_CONTENT = preg_replace_callback("/\{\{([^\|]*)\|?([^\|]*)\}\}/", $callback, $RESULT_CONTENT);
   // Show translated result  

   echo preg_replace_callback("/tr(\:\w*)?\{(.*?)\}/", "translate", $RESULT_CONTENT);
}
