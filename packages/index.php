<?php
//$before = microtime(true);
/*echo '<pre>' . var_export($_SERVER, true) . '</pre>';
echo '<pre>' . var_export($_REQUEST, true) . '</pre>';
die();*/
ini_set('session.cookie_httponly', 1);
session_start();
header_remove("X-Powered-By");
//$before = microtime(true);
// 05 November, 2013
// Do NOT touch this file unless you are expert in Everything Widget CMS
// All contents inside the apps directory is reached throuth this file
// parse app_name, section_name, function_name from url
//$time_start = microtime(true);
// ([^\/\s]{2,3}\/)?~?([^\/\s]*)\/?([^\/\s]*)?\/?([^\/\s]*)?\/?([^\/\s]*)?\/

ob_start();
require '../config/config.php';
/*require '../config/database_config.php';*/
require '../core/EWCore.class.php';
/* require '../core/modules/Valitron/Validator.php'; */
ob_end_clean();
if (ob_get_level())
  ob_end_clean();

$_file = null;
error_reporting(E_WARNING | E_ERROR);
/* $api_call = urldecode($_SERVER['REQUEST_URI']);

  $api_call =str_replace(EW_DIR,'', $api_call);
  echo $api_call;
  preg_match_all('/([^\/\s]{2,3}\/)?~?([^\/\s]*)\/?([^\/\s]*)?\/?([^\/\s]*)?\/?([^\/\s]*)?\//', $api_call, $matches);
  var_dump($matches);
  die(); */
//ini_set('display_errors', '1');

EWCore::set_default_locale("admin");
//EWCore::set_db_connection(get_db_connection());

$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
if (substr($path, -1) === '/') {
  $path = substr($path, 0, -1);
}

// Decode url to a normal string
$path = urldecode($path);
if (strpos($path, '?') !== false)
  $path = substr($path, 0, strpos($path, '?'));
$elements = explode('/', $path);
$parameter_index = 0;
if ($elements[0] && strpos(EW_DIR, $elements[0])) {
  $root_dir = array_shift($elements);
  //$parameter_index = 1;
}

// Check the language parameter
$language = "en";
$default_language = EWCore::read_setting("ew/language");

if ($default_language) {
  $language = $default_language;
}

if (preg_match("/^([^~]{2,3})$/", $elements[$parameter_index], $match)) {
  $language = $match[0];
  $_REQUEST["_url_language"] = $language;
  //echo array_shift($elements);   
  //$parameter_index++;
  array_shift($elements);
}

$_REQUEST["_language"] = $language;

$url_language = ($language == "en") ? '' : $language . '/';

// Set protocol to https if detected
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
        $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Set the language for the root url
if ($_SERVER['SERVER_PORT'] !== "80" && $_SERVER['SERVER_PORT'] !== "443") {
  $u = $protocol . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . EW_DIR_URL . $url_language;
}
else {
  $u = $protocol . $_SERVER['SERVER_NAME'] . EW_DIR_URL . $url_language;
}

define('EW_ROOT_URL', $u);

$EW = new \EWCore();

// Check the app parameter
$resource_type = "html";
$app_name = "webroot";

if (strpos($elements[$parameter_index], '~') === 0) {
  $app_name = str_replace('~', '', $elements[$parameter_index]);
  $parameter_index++;

  if (count($elements) > 3 && $elements[$parameter_index]) {
    $resource_type = $elements[$parameter_index];
    $parameter_index++;
  }
}

// Read the section name parameter
$section_name = null;
if (isset($elements[$parameter_index]) && preg_match("/^([^\.]*)$/", $elements[$parameter_index], $match)) {
  $section_name = $elements[$parameter_index];
  $_REQUEST["_module_name"] = $section_name;

  $parameter_index++;
}

// Read the function name parameter
$function_name = null;
if (isset($elements[$parameter_index])) {
  $function_name = $elements[$parameter_index];
  //$function_name = ($function_name == 'index.php') ? 'index' : $function_name;
  $_REQUEST["_method_name"] = $function_name;

  $rest_of_elements = array_slice($elements, $parameter_index);
  $file_uri = implode('/', $rest_of_elements);
  //if (strpos($file_uri, '?'))
  //$file_uri = substr($file_uri, 0, strpos($file_uri, '?'));
  $_file = preg_replace('{/$}', '', $file_uri);
  $_REQUEST["_file"] = $_file;

  $parameter_index++;
}
// Create instance of EWCore class 

if (!ob_start("ob_gzhandler"))
  ob_start();

// set default user group if no user group has been spacified
if (!isset($_SESSION["EW.USER_GROUP_ID"])) {
  $_SESSION['EW.USER_GROUP_ID'] = /* json_decode(EWCore::get_default_users_group(), true)["id"] */ 1;
}

$RESULT_CONTENT = "RESULT_CONTENT: EMPTY";

$real_class_name = $app_name . '\\' . $section_name;

$RESULT_CONTENT = EWCore::process_request_command($app_name, $resource_type, $section_name, $function_name, $_REQUEST);

function translate($match) {
  global $language;
  return EWCore::translate_to_locale($match, $language);
}


// show the result
if ($RESULT_CONTENT) {
  //$RESULT_CONTENT = preg_replace_callback("/\{\{([^\|]*)\|?([^\|]*)\}\}/", $callback, $RESULT_CONTENT);
  // Show translated result  

  echo preg_replace_callback("/tr(\:\w*)?\{(.*?)\}/", "translate", $RESULT_CONTENT);
  //$time_end = microtime(true);
  //$time = $time_end - $time_start;
  //echo  round($time,2) . " s";
}
//$after = microtime(true);
//echo ($after-$before) . " sec/serialize\n";

