<?php

use TemplateControl;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template
 *
 * @author Eeliya
 */
class template extends TemplateControl
{

   public function get_template_body($html_body, $template_settings)
   {
      //echo $template_settings;
      $settings = json_decode($template_settings, TRUE);

      $pages = json_decode($settings["pages"], true);
      /* print_r($settings);
        print_r($pages);
        echo $settings["spw"]; */
      $new_body = "<div class='page-slide'>" . $html_body . "</div>";
      if ($settings["spw"] == "true")
      {
         if (is_array($pages["link"]))
         {
            $pages = $pages["link"];
         }
         //print_r($pages);
         foreach ($pages as $page)
         {
            //echo $i++;
            $page = json_decode($page, TRUE);
            if ($page["type"] == "uis")
            {
               $html = admin\WidgetsManagement::generate_view($page["id"]);
               $new_body.="$i<div class='page-slide' data-not-editable=true>" . $html . "</div>";
            }
            else
               $new_body.="<div class='page-slide' data-not-editable=true>$i Not supported</div>";
         }
      }
      else
      {
         $new_body = $html_body;
      }

      return $new_body;
   }

   public function get_template_script($template_settings)
   {
      $settings = json_decode($template_settings, TRUE);
      //$settings = json_decode($settings, TRUE);
      //echo json_last_error_msg();
      //print_r($settings);
      if ($settings["spw"] == "true")
      {
         ob_start();
         ?>
         <script>
            $(document).ready(function ()
            {
               //alert($("body").html());
               $("body").onepage_scroll({
                  sections: "div.page-slide", // sectionContainer accepts any kind of selector in case you don't want to use section
                  easing: "Power2.easeOut", // Easing options accepts the CSS3 easing animation such "ease", "linear", "ease-in",
                  // "ease-out", "ease-in-out", or even cubic bezier value such as "cubic-bezier(0.175, 0.885, 0.420, 1.310)"
                  animationTime: 1000, // AnimationTime let you define how long each section takes to animate
                  pagination: false, // You can either show or hide the pagination. Toggle true for show, false for hide.
                  updateURL: false, // Toggle this true if you want the URL to be updated automatically when the user scroll to each page.
                  beforeMove: function (index) {
                  }, // This option accepts a callback function. The function will be called before the page moves.
                  afterMove: function (index) {
                  }, // This option accepts a callback function. The function will be called after the page moves.
                  loop: false, // You can have the page loop back to the top/bottom when the user navigates at up/down on the first/last page.
                  keyboard: true, // You can activate the keyboard controls
                  responsiveFallback: false, // You can fallback to normal page scroll by defining the width of the browser in which
                  // you want the responsive fallback to be triggered. For example, set this to 600 and whenever
                  // the browser's width is less than 600, the fallback will kick in.
                  direction: "horizontal"            // You can now define the direction of the One Page Scroll animation. Options available are "vertical" and "horizontal". The default value is "vertical".  
               });
            });
         </script>
         <?php
         return ob_get_clean();
      }
      return;
   }

   public function get_template_cp()
   {
      ob_start();
      ?>
      <div class="row">
         <div class="col-xs-12 mar-top" data-toggle="buttons">
            <label id="spw" type="button" class="btn btn-primary col-xs-12" ><input type="checkbox" name="spw" value="true">Single Page Website</label>
         </div>
         <div id="spw-cp">
            <div  class="col-xs-12 mar-top">
               <label>tr{Specify your pages}</label>
            </div>
            <div class="col-xs-12">
               <ul id="website_pages" class="list arrangeable">
                  <li class="" style="">
                     <div class="wrapper">
                        <div class="handle"></div>                         
                        <input class="text-field test" data-label='Page' data-ew-plugin="link-chooser" name="link"/>
                     </div>
                  </li>
               </ul>
            </div>
         </div>
      </div>
      <script>
         var onNewItem = function (ni)
         {
            ni.find(":input").attr('disabled', false);
         };
         $("#template_settings_form").on("refresh", function (e, data)
         {
            if (!$("#spw input").is(":checked"))
            {
               $("#spw-cp :input").attr('disabled', true);
               $("#spw-cp").hide();
            }
            if (data.pages)
               $("#website_pages").EW().dynamicList({value: $.parseJSON(data.pages), onNewItem: onNewItem});
            else
               $("#website_pages").EW().dynamicList({onNewItem: onNewItem});

            $("#spw").off("change");
            $("#spw").on("change", function ()
            {
               if ($("#spw input").is(":checked"))
               {
                  $("#spw-cp :input").attr('disabled', false);
                  $("#spw-cp").stop().animate({height: "toggle"}, 400, "Power2.easeOut");
               }
               else
               {

                  $("#spw-cp").stop().fadeOut(200, function () {
                     $("#spw-cp :input").attr('disabled', true);
                  });
               }
               uisForm.updateTemplateBody();
            });
         });
         $("#template_settings_form").on("getData", function (e)
         {
            var data = $.parseJSON($("#template_settings_form").serializeJSON());
            if (data)
               data.pages = $("#website_pages").EW().dynamicList("getJSON");
            uisForm.setTemplateSettings(data);
         });
      </script>
      <?php
      return ob_get_clean();
   }

   //put your code here
}
