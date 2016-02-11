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

$TEMPLATE_LINK = ($_REQUEST["_uis_template"]) ?
        '<link id="template-css" href="~rm/public/' . $_REQUEST["_uis_template"] . '/template.css" rel="stylesheet" type="text/css"/>' : "";

// If template has a 'template.php' then include it
$template_php = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.php';
if (file_exists($template_php)) {
  require_once $template_php;
  $template = new \template();
  //$uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);
  $template_settings = json_decode($_REQUEST["_uis_template_settings"], true);
  if (json_last_error() != JSON_ERROR_NONE) {
    $template_settings = json_decode(stripslashes($_REQUEST["_uis_template_settings"]), true);
  }



  $TEMPLATE_SCRIPT = "";
  $template_script_dom = $template->get_template_script($template_settings);
  if ($template_script_dom) {
    $DOM = new DOMDocument;
    $DOM->loadHTML($template->get_template_script($template_settings));
    $script_tasg = $DOM->getElementsByTagName("script");
    // Retrive template main js script and create a script tag that are to be added to DOM
    $TEMPLATE_SCRIPT = '<script id="template-script">' . $script_tasg->item(0)->nodeValue . '</script>';
  }
}

// if template.js exist, then include it in HTML_SCRIPTS
$template_js = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.js';
if (file_exists($template_js)) {
  \webroot\WidgetsManagement::add_html_script('~rm/public/' . $_REQUEST["_uis_template"] . '/template.js', $script);
}

$HTML_TITLE = (webroot\WidgetsManagement::get_html_title()) ? webroot\WidgetsManagement::get_html_title() . " - " . $website_title : $website_title;
$HTML_KEYWORDS = webroot\WidgetsManagement::get_html_keywords();
$HTML_SCRIPTS = webroot\WidgetsManagement::get_html_scripts();
$HTML_LINKS = webroot\WidgetsManagement::get_html_links();
?>
<!DOCTYPE html> 
<html>
  <head>
    <base href="<?= EW_ROOT_URL ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />  

<?php
echo '<title>' . $HTML_TITLE . '</title>';
echo "<meta name='description' content='$pageDescription'/>";
echo "<meta name='keywords' content='$defaultKeywords, $HTML_KEYWORDS, $website_title'/>";
?>      

    <link rel="stylesheet" href="~rm/public/css/bootstrap.css" >  
    <?= $HTML_LINKS; ?>
    <?= $TEMPLATE_LINK; ?>      

    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="~admin/public/js/lib/bootstrap.js" defer></script> 
    <script src="~rm/public/js/gsap/TweenLite.min.js" defer></script>
    <script src="~rm/public/js/gsap/easing/EasePack.min.js" defer></script>
    <script src="~rm/public/js/gsap/jquery.gsap.min.js" defer></script>
    <script src="~rm/public/js/gsap/plugins/CSSPlugin.min.js" defer></script> 

    <script id="widget-data">
      (function () {
        window.ew_widget_data = {};
<?= $WIDGET_DATA; ?>
      })();
    </script>
<?= $HTML_SCRIPTS; ?>
<?= $TEMPLATE_SCRIPT; ?>      

  </head>
  <body class="<?= EWCore::get_language_dir($_REQUEST["_language"]) ?>">
    <div id="base-content-pane" class="container">
    <?= $HTML_BODY; ?>
    </div>
  </body>  
</html>