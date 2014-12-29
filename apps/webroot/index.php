<?php
session_start();
global $rootAddress, $pageAddress;

$app = "webroot";
$currentAppConf = json_decode(admin\Settings::read_settings(), true);

$website_title = $currentAppConf["web-title"];
$pageDescription = $currentAppConf["web-description"];
$defaultKeywords = $currentAppConf["web-keywords"];

$_SESSION['ROOT_DIR'] = EW_ROOT_DIR;
$_REQUEST['cmdResult'] = '';

// if template.js exist, then include it in HTML_SCRIPTS
if (file_exists(EW_ROOT_DIR . $_REQUEST["_uis_template"] . '/template.js'))
{
   \admin\WidgetsManagement::add_html_script($_REQUEST["_uis_template"] . '/template.js', $script);
}

$WM = new admin\WidgetsManagement("WidgetsManagement", $_REQUEST);
$HTML_BODY = admin\WidgetsManagement::generate_view($_REQUEST["_uis"]);
// If template has a 'template.php' then include it
if (file_exists(EW_ROOT_DIR . $_REQUEST["_uis_template"] . '/template.php'))
{
   require_once EW_ROOT_DIR . $_REQUEST["_uis_template"] . '/template.php';
   $template = new \template();
   $uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);

   $HTML_BODY = $template->get_html_body($HTML_BODY, stripslashes($uis_data["template_settings"]));
}

$HTML_TITLE = (admin\WidgetsManagement::get_html_title()) ? admin\WidgetsManagement::get_html_title() . " - " . $website_title : $website_title;
$HTML_KEYWORDS = admin\WidgetsManagement::get_html_keywords();
$HTML_SCRIPTS = admin\WidgetsManagement::get_html_scripts();
$HTML_STYLES = admin\WidgetsManagement::get_html_styles();
//$apps = json_decode(EWCore::get_apps(), true);
?>
<!DOCTYPE html> 
<html>
   <head>
      <?php
      echo '<title>' . $HTML_TITLE . '</title>';
      echo ($pageDescription) ? "<meta name='description' content='$pageDescription'/>" : '';
      ?>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="keywords" content="<?php echo (($defaultKeywords) ? $defaultKeywords . "," : "") . $HTML_KEYWORDS . $website_title ?>"/>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

      <base href="<?php echo EW_ROOT_URL ?>">
      <link rel="shortcut icon" href="<?php echo ($_REQUEST["_uis_template"] . '/favicon.ico') ?>" />                    
      <link href="<?php echo (EW_ROOT_URL . "core/css/custom-theme/jquery-ui-1.8.21.custom.css") ?>" rel="Stylesheet" type="text/css"/>	
      <link href="<?php echo (EW_ROOT_URL . "core/css/bootstrap.css") ?>" rel="stylesheet" type="text/css"/>  
      <link href="<?php echo ($_REQUEST["_uis_template"] . '/template.css') ?>" rel="stylesheet" type="text/css"/>

      <script src="<?php echo EW_ROOT_URL ?>core/js/jquery/jquery-2.1.1.min.js"></script>        
      <script src="<?php echo EW_ROOT_URL ?>core/js/jquery/jquery-ui-1.10.3.custom.min.js" ></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/ewscript.js"></script> 
      <script src="<?php echo EW_ROOT_URL ?>core/js/floatlabels.min.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/gsap/plugins/CSSPlugin.min.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/gsap/TweenLite.min.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/gsap/jquery.gsap.min.js"></script>
      <script>
         document.addEventListener("DOMNodeInserted", function (event)
         {
            var $elementJustAdded = $(event.target);
            if ($elementJustAdded)
            {
               $elementJustAdded.find('input[data-label], textarea[data-label], select[data-label]').floatlabel();
            }
         });
         $(document).ready(function () {
            $(document).find('input[data-label], textarea[data-label], select[data-label]').floatlabel();
         });
      </script>
      <?php
      // Add registered scripts
      echo $HTML_SCRIPTS;
      ?>
   </head>
   <body class="<?php echo EWCore::get_language_dir($_REQUEST["_language"]) ?>">
      <div id="base-content-pane" class="container">
         <?php echo $HTML_BODY; ?>  
      </div>   
      <script src="<?php echo EW_ROOT_URL ?>core/js/bootstrap.min.js"></script>
   </body>  
</html>