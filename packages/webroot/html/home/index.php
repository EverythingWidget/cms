<?php
session_start();
global $rootAddress, $pageAddress;

$app = "webroot";

$currentAppConf = json_decode(admin\Settings::read_settings(), true);

$website_title = $currentAppConf["webroot/web-title"];
$pageDescription = $currentAppConf["webroot/web-description"];
$defaultKeywords = $currentAppConf["webroot/web-keywords"];

$_SESSION['ROOT_DIR'] = EW_ROOT_DIR;
$_REQUEST['cmdResult'] = '';

//$WM = new admin\WidgetsManagement("WidgetsManagement", $_REQUEST);
//$HTML_BODY = admin\WidgetsManagement::generate_view($_REQUEST["_uis"]);
$VIEW = webroot\WidgetsManagement::generate_view($_REQUEST["_uis"]);
$HTML_BODY = $VIEW["body_html"];
$WIDGET_DATA = $VIEW["widget_data"];

// If template has a 'template.php' then include it
$template_php = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.php';
if (file_exists($template_php))
{
   require_once $template_php;
   $template = new \template();
   //$uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);
   $template_settings = json_decode($_REQUEST["_uis_template_settings"], true);
   if (json_last_error() != JSON_ERROR_NONE)
   {
      $template_settings = json_decode(stripslashes($_REQUEST["_uis_template_settings"]), true);
   }

   $HTML_BODY = $template->get_template_body($HTML_BODY, $template_settings);
   //$template.=new DOMElement$template->get_template_script(stripslashes($_REQUEST["_uis_template_settings"]));
   $DOM = new DOMDocument;
   $DOM->loadHTML($template->get_template_script($template_settings));
   $script_tasg = $DOM->getElementsByTagName("script");
   // Retrive template main js script
   $template_script = $script_tasg->item(0)->nodeValue;
}
// if template.js exist, then include it in HTML_SCRIPTS
$template_js = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.js';
if (file_exists($template_js))
{
   \webroot\WidgetsManagement::add_html_script('~rm-public/' . $_REQUEST["_uis_template"] . '/template.js', $script);
}

$HTML_TITLE = (webroot\WidgetsManagement::get_html_title()) ? webroot\WidgetsManagement::get_html_title() . " - " . $website_title : $website_title;
$HTML_KEYWORDS = webroot\WidgetsManagement::get_html_keywords();
$HTML_SCRIPTS = webroot\WidgetsManagement::get_html_scripts();
$HTML_STYLES = webroot\WidgetsManagement::get_html_styles();
?>
<!DOCTYPE html> 
<html>
   <head>
      <?php
      echo '<title>' . $HTML_TITLE . '</title>';
      echo "<meta name='description' content='$pageDescription'/>";
      echo "<meta name='keywords' content='$defaultKeywords, $HTML_KEYWORDS, $website_title'/>";
      ?>      
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1" />

      <base href="<?= EW_ROOT_URL ?>">

      <link rel="stylesheet" href="~rm-public/css/bootstrap.css" >  
      <link id="template-css" href="~rm-public/<?= $_REQUEST["_uis_template"] ?>/template.css" rel="stylesheet" type="text/css"/>

      <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>           
      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenLite.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/jquery.gsap.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/plugins/CSSPlugin.min.js"></script>

      <script src="~admin-public/js/lib/ewscript.js"></script>            
      <script src="~admin-public/js/lib/floatlabels.min.js" ></script>

      <?php
      // Add registered scripts
      echo $HTML_SCRIPTS;
      // Add template main script if existed
      if ($template_script)
      {
         echo '<script id="template-script">' . $template_script . '</script>';
      }
      ?>

      <script id="widget-data">
         $(document).ready(function () {
<?= $WIDGET_DATA; ?>
         });
      </script>

      <script>
         $(document).ready(function () {
            document.addEventListener("DOMNodeInserted", function (event)
            {
               var $elementJustAdded = $(event.target);
               if ($elementJustAdded)
               {
                  $elementJustAdded.find('input[data-label], textarea[data-label], select[data-label]').floatlabel();
               }
            });
         });
      </script>

   </head>
   <body class="">
      <div id="base-content-pane" class="container">
         <?= $HTML_BODY; ?>  
      </div>   
      <script src="~admin-public/js/lib/bootstrap.js"></script>
   </body>  
</html>