<?php
session_start();
//require_once 'database_config.php';
//include_once $_SERVER['DOCUMENT_ROOT'] . '/core/EWCore.class.php';
//include 'admin/WidgetsManagement/WidgetsManagementCore.php';

global $rootAddress, $pageAddress;

$app = "webroot";
//$EW = new EWCore("webroot/", $_REQUEST);
$currentAppConf = json_decode(admin\Settings::read_settings(), true);
//EWCore::set_default_locale("webroot");
//print_r($_REQUEST);

$website_title = $currentAppConf["web-title"];
$pageDescription = $currentAppConf["web-description"];
$defaultKeywords = $currentAppConf["web-keywords"];

//$EW = new EWCore("admin/", $_REQUEST);

$_SESSION['ROOT_DIR'] = EW_ROOT_DIR;
$_REQUEST['cmdResult'] = '';

$WM = new admin\WidgetsManagement("WidgetsManagement", $_REQUEST);
$HTML_BODY = admin\WidgetsManagement::generate_view($_REQUEST["_uis"]);
$HTML_TITLE = (admin\WidgetsManagement::get_html_title()) ? admin\WidgetsManagement::get_html_title() . " - " . $website_title : $website_title;
$HTML_KEYWORDS = admin\WidgetsManagement::get_html_keywords();
$HTML_SCRIPTS = admin\WidgetsManagement::get_html_scripts();
$HTML_STYLES = admin\WidgetsManagement::get_html_styles();

//$apps = json_decode(EWCore::get_apps(), true);
?>
<!DOCTYPE html> 
<html>
   <head>
      <title><?php echo $HTML_TITLE ?></title> 
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="keywords" content="<?php echo (($defaultKeywords) ? $defaultKeywords . "," : "") . $HTML_KEYWORDS . $website_title ?>"/>
      <?php
      if ($pageDescription)
      {
         ?>
         <meta name="description" content="<?php echo $pageDescription ?>"/>               
         <?php
      }
      ?>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <base href="<?php echo EW_ROOT_URL ?>">
      <link rel="shortcut icon" href="<?php echo ($_REQUEST["_uis_template"] . '/favicon.ico') ?>" />                    
      <link href="<?php echo (EW_ROOT_URL . "core/css/custom-theme/jquery-ui-1.8.21.custom.css") ?>" rel="Stylesheet" type="text/css"/>	
      <link href="<?php echo (EW_ROOT_URL . "core/css/bootstrap.css") ?>" rel="stylesheet" type="text/css"/>  
      <link href="<?php echo ($_REQUEST["_uis_template"] . '/template.css') ?>" rel="stylesheet" type="text/css"/>
      <script src="<?php echo (EW_ROOT_URL . "core/js/jquery/jquery-2.1.1.min.js") ?>"  type="text/javascript">
      </script>        
      <script type="text/javascript" src="<?php echo (EW_ROOT_URL . "core/js/jquery/jquery-ui-1.10.3.custom.min.js") ?>"></script>
      <script src="<?php echo (EW_ROOT_URL . "core/js/ewscript.js") ?>"  type="text/javascript">
      </script> 
      <script src="<?php echo EW_ROOT_URL ?>core/js/floatlabels.min.js"  type="text/javascript"></script>
      <script src = "<?php echo EW_ROOT_URL ?>core/js/gsap/plugins/CSSPlugin.min.js"  type = "text/javascript" ></script>
      <script src = "<?php echo EW_ROOT_URL ?>core/js/gsap/TweenLite.min.js"  type = "text/javascript" ></script>
      <script src = "<?php echo EW_ROOT_URL ?>core/js/gsap/jquery.gsap.min.js"  type = "text/javascript" ></script>

      <script>
         document.addEventListener("DOMNodeInserted", function (event)
         {
            var $elementJustAdded = $(event.target);
            if ($elementJustAdded)
            {
               $elementJustAdded.find('input[data-label], textarea[data-label], select[data-label]').floatlabel();
               //          $elementJustAdded.find('input[data-ew-plugin="link-chooser"], textarea[data-ew-plugin="link-chooser"]').EW().linkChooser();
               //$elementJustAdded.find('[data-ew-plugin="image-chooser"]').EW().imageChooser();
               //$elementJustAdded.find('[data-slider]').simpleSlider();
               /*$elementJustAdded.find("select").selectpicker({
                container: "body"
                });*/
            }
         });
         $(document).ready(function () {
            $(document).find('input[data-label], textarea[data-label], select[data-label]').floatlabel();
         });
      </script>
      <?php echo $HTML_SCRIPTS ?>
   </head>

   <body>
      <div id="base-content-pane" class="container">
         <?php
         echo $HTML_BODY;
         ?>  
      </div>   
      <script src="<?php echo EW_ROOT_URL ?>core/js/bootstrap.min.js"  type="text/javascript">
      </script>
   </body>  
</html>