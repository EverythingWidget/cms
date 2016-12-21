<?php
session_start();
global $rootAddress, $pageAddress;

$current_path = str_replace(EW_DIR, '', $_SERVER['REQUEST_URI']);
$app = "webroot";

$_SESSION['ROOT_DIR'] = EW_ROOT_DIR;
$_REQUEST['cmdResult'] = '';

webroot\WidgetsManagement::include_html_link(['rm/public/css/grid.css']);

webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/jquery/build.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/webcomponents/webcomponents-lite.min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/x-tag/x-tag.min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/galaxyjs/galaxy-tags-min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/vue/vue.min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/gsap/TweenLite.min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/gsap/easing/EasePack.min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/gsap/jquery.gsap.min.js']);
webroot\WidgetsManagement::add_html_script(['include' => 'rm/public/js/gsap/plugins/CSSPlugin.min.js']);

$VIEW = webroot\WidgetsManagement::generate_view($_REQUEST["_uis"]);
$HTML_BODY = $VIEW['body_html'];
$WIDGET_DATA = $VIEW['widget_data'];

// If template has a 'template.php' then include it
$template_php = EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST['_uis_template'] . '/template.php';
if (file_exists($template_php)) {
  require_once $template_php;
  $template = new \template();
  //$uis_data = json_decode(admin\WidgetsManagement::get_uis($_REQUEST["_uis"]), true);
  $template_settings = $_REQUEST['_uis_template_settings'];

  if (is_array($template_settings)) {
    $template_settings = json_encode($template_settings);
  }

  if (empty($template_settings) || $template_settings === 'null') {
    $template_settings = '{}';
  }

  $TEMPLATE_SCRIPT = "";
  $template_script_dom = $template->get_template_script(json_decode($_REQUEST["_uis_template_settings"], true));
  if ($template_script_dom) {
    $template_script_dom = preg_replace('/\$php\.\$template_settings/', $template_settings, $template_script_dom);

    $TEMPLATE_SCRIPT = '<script id="template-script" async>' . $template_script_dom . '</script>';
  }
}

if ($_REQUEST['_uis_template']) {
  webroot\WidgetsManagement::include_html_link(['rm/public/' . $_REQUEST['_uis_template'] . '/template.css']);
}

$currentAppConf = webroot\WidgetsManagement::get_page_info();

$website_title = $currentAppConf['webroot/title'];
$page_description = $currentAppConf['webroot/description'];
$website_keywords = $currentAppConf['webroot/keywords'];
$favicon = $currentAppConf['webroot/favicon'];
$google_analytics_id = $currentAppConf['webroot/google-analytics-id'];
$accelerated_mobile_pages = $currentAppConf['webroot/accelerated-mobile-pages'];
$page_language = $currentAppConf['webroot/language'] ? $currentAppConf['webroot/language'] : 'en';

if ($page_description) {
  \webroot\WidgetsManagement::set_meta_tag([
      'name'    => 'description',
      'content' => $page_description
  ]);
}

$HTML_KEYWORDS = webroot\WidgetsManagement::get_html_keywords();
\webroot\WidgetsManagement::set_meta_tag([
    'name'    => 'keywords',
    'content' => $HTML_KEYWORDS
]);

$HTML_SCRIPTS = webroot\WidgetsManagement::get_html_scripts();
$HTML_LINKS = webroot\WidgetsManagement::get_html_links();
$HTML_CSS = webroot\WidgetsManagement::get_html_links_concatinated();
$HTML_META_TAGS = webroot\WidgetsManagement::get_meta_tags();
?>
<!doctype html> 
<html amp lang="<?= $page_language ?>">
  <head>
    <base href="<?= EW_ROOT_URL ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />  

    <?php
    echo "<title>$website_title</title>";
    echo $HTML_META_TAGS;
    echo "<link rel='alternate' href='{$_SERVER['REQUEST_URI']}' hreflang='$page_language' />";
    echo "<link rel='shortcut icon' href='$favicon' />";
    echo "<link rel='apple-touch-icon-precomposed' href='$favicon' />";
    echo $HTML_LINKS['head'];
    echo "<meta name='msapplication-TileColor' content='#FFFFFF' />";
    echo "<meta name='msapplication-TileImage' content='$favicon' />";

//    echo '<style>' . file_get_contents(EW_PACKAGES_DIR . '/rm/public/css/grid.css') . '</style>';

    if (isset($google_analytics_id)) {
      ?>

      <script id="google-analytics">
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
        ga('require', 'linkid');
        ga('send', 'pageview');
      </script>
      <?php
    }

    if ($accelerated_mobile_pages) {
      echo "<link rel='canonical' href='{$_SERVER['REQUEST_URI']}' />";
      ?> 

      <script async src="https://cdn.ampproject.org/v0.js"></script>
      <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <?php } ?>

    <script id="widget-data">
        (function () {
          var ew_widget_data = {};
          var ew_widget_actions = {};

<?= $WIDGET_DATA; ?>

          window.ew_widget_data = ew_widget_data;
          window.ew_widget_actions = ew_widget_actions;
        })();
    </script>      

    <?= $HTML_SCRIPTS; ?>
    <?= $TEMPLATE_SCRIPT; ?>          
  </head>
  <body class="<?= EWCore::get_language_dir($_REQUEST["_language"]) ?>">
    <div id="base-content-pane" class="container">
      <?= $HTML_BODY; ?>
    </div>

    <?= $HTML_CSS['tag'] ?>
    <?= $HTML_LINKS['body'] ?>
  </body>  
</html>