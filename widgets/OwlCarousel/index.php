<?php
$feeder = json_decode($widget_parameters["feeder"], TRUE);
$feeder_id = $feeder["feederId"];
//admin\WidgetsManagement::add_html_script("widgets/OwlCarousel/owl.carousel.js");
$id = $feeder["id"];
if (!$feeder_id)
   return;
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
//$list = json_decode($widget_parameters["list"], TRUE);

if ($widget_parameters["default-content"] == "yes")
{
   if ($_REQUEST["_page"])
   {
      $feeder_id = $_REQUEST["_page"];
   }
}
$token = $_REQUEST["$feeder_id-token"];
$num_of_items_per_page = 10;
if (!$token)
   $token = 0;
$row_num = ($token * $num_of_items_per_page) + $num_of_items_per_page;

//$items_list = EWCore::get_widget_feeder("list", $feeder_app, $feeder_id, [$id, $token * $num_of_items_per_page, $num_of_items_per_page]);

$items_list = json_decode(EWCore::call($feeder_id, ["id" => $id]), TRUE);
$items_count = $items_list["totalRows"];
$items = $items_list["data"];
$page = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

webroot\WidgetsManagement::add_html_script("~rm/public/js/owl-carousel/owl.carousel.js");

webroot\WidgetsManagement::add_html_link("~rm/public/js/owl-carousel/animate.css");
webroot\WidgetsManagement::add_html_link("~rm/public/js/owl-carousel/owl.carousel.css");
webroot\WidgetsManagement::add_html_link("~rm/public/js/owl-carousel/owl.theme.default.css");
?>

<div class="owl-carousel">
   <?php
   $index = 1;
   if (isset($items))
   {
      foreach ($items as $item)
      {
         //$row_seprator = false;
         echo "<div class='item {$item["class"]}' >{$item["html"]}</div>";
      }
   }
   ?>
</div>

<script>
   $(document).ready(function () {
      $("div[data-widget-id='{$widget_id}'] > .owl-carousel").owlCarousel({responsiveClass: true,
         autoHeight:<?php echo $auto_height ?>,
         loop:<?php echo $loop ?>,
         center:<?php echo $center ?>,
         dots:<?php echo $slide_indicator ?>,
         nav:<?php echo $nav ?>,
         autoplay:<?php echo $auto_play ?>,
         autoplayTimeout: <?php echo $slide_timeout ?>,
         autoplayHoverPause:<?php echo $autoPlayPause ?>,
         smartSpeed: 1000,
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
         }});
   });
</script>