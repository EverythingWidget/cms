<?php
if (!is_array($widget_parameters["feeder"])) {
  $feeder = json_decode($widget_parameters["feeder"], TRUE);
}
else {
  $feeder = $widget_parameters["feeder"];
}

$feeder_id = $feeder["feederId"];
$id = $feeder["id"];

if ($feeder["type"] === "widget-feeder") {
  $feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder_id);
  if ($feeder_obj->feeder_type !== "list") {
    return;
  }

  $items_list = EWCore::call_api($feeder_obj->api_url, [
              'id'        => $id,
              'token'     => $token * $num_of_items_per_page,
              'page_size' => $num_of_items_per_page,
              'order_by'  => $order,
              '_language' => $_REQUEST['_language']
  ]);
}
else {
  $id = $feeder["id"];
  if (!$feeder_id)
    return;
  $items_list = EWCore::call_api($feeder_id, [
              'id'        => $id,
              'token'     => $token * $num_of_items_per_page,
              'page_size' => $num_of_items_per_page,
              'order_by'  => $order,
              '_language' => $_REQUEST['_language']
  ]);
}
$item_per_slide_lg = 1;
$item_per_slide_md = 1;
$item_per_slide_sm = 1;
$item_per_slide_xs = 1;
if ($widget_parameters["items-per-slide-lg"] && is_numeric($widget_parameters["items-per-slide-lg"]))
  $item_per_slide_lg = $widget_parameters["items-per-slide-lg"];
if ($widget_parameters["items-per-slide-md"] && is_numeric($widget_parameters["items-per-slide-md"]))
  $item_per_slide_md = $widget_parameters["items-per-slide-md"];

if ($widget_parameters["items-per-slide-sm"] && is_numeric($widget_parameters["items-per-slide-sm"]))
  $item_per_slide_sm = $widget_parameters["items-per-slide-sm"];

if ($widget_parameters["items-per-slide-xs"] && is_numeric($widget_parameters["items-per-slide-xs"]))
  $item_per_slide_xs = $widget_parameters["items-per-slide-xs"];
$auto_height = 'false';
if ($widget_parameters["auto-height"])
  $auto_height = $widget_parameters["auto-height"];

$loop = 'false';
if ($widget_parameters["loop"])
  $loop = $widget_parameters["loop"];

$center = 'false';
if ($widget_parameters["center"])
  $center = $widget_parameters["center"];

$slide_indicator = 'false';
if ($widget_parameters["slide-indicator"])
  $slide_indicator = $widget_parameters["slide-indicator"];

$nav = 'false';
if ($widget_parameters["nav"])
  $nav = $widget_parameters["nav"];

$auto_play = 'false';
if ($widget_parameters["auto-play"])
  $auto_play = $widget_parameters["auto-play"];

$autoPlayPause = 'false';
if ($widget_parameters["auto-play-pause"])
  $autoPlayPause = $widget_parameters["auto-play-pause"];

$slide_timeout = '1000';
if ($widget_parameters["slide-timeout"])
  $slide_timeout = $widget_parameters["slide-timeout"];

if ($widget_parameters["default-content"] == "yes") {
  if ($_REQUEST["_page"]) {
    $feeder_id = $_REQUEST["_page"];
  }
}

$smart_speed = '200';
if ($widget_parameters["smart-speed"])
  $smart_speed = $widget_parameters["smart-speed"];


$token = $_REQUEST["$feeder_id-token"];
$num_of_items_per_page = 10;
if (!$token)
  $token = 0;
$row_num = ($token * $num_of_items_per_page) + $num_of_items_per_page;

//$items_list = EWCore::get_widget_feeder("list", $feeder_app, $feeder_id, [$id, $token * $num_of_items_per_page, $num_of_items_per_page]);
//


$items_count = $items_list["total"];
$items = $items_list["data"];
$page = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

webroot\WidgetsManagement::add_html_script([
    'include' => "rm/public/js/owl-carousel/owl.carousel.js"
]);

webroot\WidgetsManagement::include_html_link([
    'rm/public/js/owl-carousel/animate.css',
    'rm/public/js/owl-carousel/owl.carousel.css',
    'rm/public/js/owl-carousel/owl.theme.default.css'
]);

if ($widget_parameters['content_fields'] && isset($items)) {
  $fields = $widget_parameters["content_fields"];

  if (is_string($fields)) {
    $fields = [$fields];
  }

  foreach ($items as $i => $item) {
    $items[$i]['html'] = '';

    foreach ($fields as $field) {
      $field_data = $item['content_fields'][$field];
      $tag = $field_data['tag'];

      if (isset($tag)) {
        if ($tag === 'img') {
          $items[$i]['html'] .= "<img class='$field' src='{$field_data['src']}'/>";
        }
        else {
          $items[$i]['html'] .= "<$tag class='$field'>{$field_data['content']}</$tag>";
        }
      }
    }
  }
}
?>

<div class="owl-carousel" style="width: 1px;min-width: 100%;">
  <?php
  $index = 1;
  if (isset($items)) {
    foreach ($items as $item) {
      echo "<div class='item' >{$item["html"]}</div>";
    }
  }
  ?>
</div>

<script>
  window.addEventListener('load', function () {
    $("div[data-widget-id='{$widget_id}'] > .owl-carousel").owlCarousel({
      responsiveClass: true,
      autoHeight:<?php echo $auto_height ?>,
      loop:<?php echo $loop ?>,
      center:<?php echo $center ?>,
      dots:<?php echo $slide_indicator ?>,
      nav:<?php echo $nav ?>,
      autoplay:<?php echo $auto_play ?>,
      autoplayTimeout: <?php echo $slide_timeout ?>,
      autoplayHoverPause:<?php echo $autoPlayPause ?>,
      smartSpeed: <?= $smart_speed ?>,
      navText: [
        '',
        ''
      ],
      responsive: {
        0: {
          items: <?php echo $item_per_slide_xs ?>
        },
        768: {
          items:<?php echo $item_per_slide_sm ?>
        },
        991: {
          items:<?php echo $item_per_slide_md ?>
        },
        1359: {
          items:<?php echo $item_per_slide_lg ?>
        }
      }
    });
  });
  /*$(document).ready(function () {
   
   });*/
</script>