<?php
session_start();
global $rootAddress, $pageAddress;

$current_path = str_replace(EW_DIR, '', $_SERVER['REQUEST_URI']);
$app = "webroot";

$currentAppConf = EWCore::call_api("admin/api/settings/read-settings", [
            "app_name" => "webroot"
        ]);

$website_title = $currentAppConf["webroot/title"];
$page_description = ($current_path === '/' || !$current_path) ? $currentAppConf["webroot/description"] : null;
$website_keywords = $currentAppConf["webroot/keywords"];
$favicon = $currentAppConf["webroot/favicon"];
$google_analytics_id = $currentAppConf["webroot/google-analytics-id"];

$_SESSION['ROOT_DIR'] = EW_ROOT_DIR;
$_REQUEST['cmdResult'] = '';

\webroot\WidgetsManagement::set_html_keywords($website_title . ',' . $website_keywords);

webroot\WidgetsManagement::include_html_link(["rm/public/css/bootstrap.css"]);

webroot\WidgetsManagement::add_html_script(["include" => "admin/public/js/lib/bootstrap.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/TweenLite.min.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/easing/EasePack.min.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/jquery.gsap.min.js"]);
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/gsap/plugins/CSSPlugin.min.js"]);

$VIEW = webroot\WidgetsManagement::generate_view($_REQUEST["_uis"]);
$HTML_BODY = $VIEW["body_html"];
$WIDGET_DATA = $VIEW["widget_data"];

$TEMPLATE_LINK = ($_REQUEST["_uis_template"]) ?
        '<link rel="stylesheet" property="stylesheet" type="text/css" id="template-css" href="public/rm/' . $_REQUEST["_uis_template"] . '/template.css" />' : "";

// If template has a 'template.php' then include it
$template_php = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.php';
if (file_exists($template_php)) {
  require_once $template_php;
  $template = new \template();
  //$uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);
  $template_settings = $_REQUEST['_uis_template_settings'];

  if (!isset($template_settings) || $template_settings === 'null') {
    $template_settings = '{}';
  }

  $TEMPLATE_SCRIPT = "";
  $template_script_dom = $template->get_template_script(json_decode($_REQUEST["_uis_template_settings"], true));
  if ($template_script_dom) {
    $template_script_dom = preg_replace('/\'json\|\$template_settings\'/', $template_settings, $template_script_dom);
    $TEMPLATE_SCRIPT = '<script id="template-script">' . $template_script_dom . '</script>';
  }
}

// if template.js exist, then include it in HTML_SCRIPTS
/* $template_js = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["_uis_template"] . '/template.js';
  if (file_exists($template_js)) {
  \webroot\WidgetsManagement::add_html_script([
  'src' => 'public/rm/' . $_REQUEST["_uis_template"] . '/template.js'
  ]);
  } */

$html_keywords_string = webroot\WidgetsManagement::get_html_title();
$HTML_TITLE = ($current_path === '/' || !$current_path) ? $website_title : $html_keywords_string;
$HTML_KEYWORDS = webroot\WidgetsManagement::get_html_keywords();
$HTML_SCRIPTS = webroot\WidgetsManagement::get_html_scripts();
$HTML_LINKS = webroot\WidgetsManagement::get_html_links();
$HTML_CSS = webroot\WidgetsManagement::get_html_links_concatinated();
?>
<!DOCTYPE html> 
<html>
  <head>
    <base href="<?= EW_ROOT_URL ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />  

    <?php
    echo "<title>$HTML_TITLE</title>";
    if ($page_description) {
      echo "<meta name='description' content='$pageDescription'/>";
    }
    echo "<meta name='keywords' content='$HTML_KEYWORDS'/>";
    echo "<link rel='shortcut icon' href='$favicon'>";
    echo "<link rel='apple-touch-icon-precomposed' href='$favicon'>";
    echo '<meta name="msapplication-TileColor" content="#FFFFFF">';
    echo "<meta name='msapplication-TileImage' content='$favicon'>";

    if (isset($google_analytics_id)) {
      ?>
      <script>
        (function (i, s, o, g, r, a, m) {
          i['GoogleAnalyticsObject'] = r;
          i[r] = i[r] || function () {
            (i[r].q = i[r].q || [
            ]).push(arguments)
          }, i[r].l = 1 * new Date();
          a = s.createElement(o),
                  m = s.getElementsByTagName(o)[0];
          a.async = 1;
          a.src = g;
          m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', '<?= $google_analytics_id ?>', 'auto');
        ga('send', 'pageview');
      </script>
      <?php
    }
    ?>
    <script id="widget-data">
      (function () {
        window.ew_widget_data = {};
        window.ew_widget_actions = {};
<?= $WIDGET_DATA; ?>
      })();
    </script>

    <script src="public/rm/js/jquery/build.js"></script>
    <script src="public/rm/js/vue/vue.min.js"></script>       


    <?= $HTML_SCRIPTS; ?>
    <?= $TEMPLATE_SCRIPT; ?>      

  </head>
  <body class="<?= EWCore::get_language_dir($_REQUEST["_language"]) ?>">
    <div id="base-content-pane" class="container">
      <?= $HTML_BODY; ?>
    </div>

    <?= $HTML_CSS ?>
    <?= $HTML_LINKS; ?>
    <?= $TEMPLATE_LINK; ?>    
  </body>  
</html>