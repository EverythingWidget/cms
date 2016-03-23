<?php
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
class template extends TemplateControl {

  public function get_template_body($html_body, $template_settings) {
    return $html_body;
  }

  public function get_template_script($template_settings) {
    if ($template_settings["spw"] == "true") {
      ob_start();
      ?>
      <script>
        (function ($) {
          var mainManuId = <?= isset($template_settings["menu-id"]) ? '"#' . $template_settings["menu-id"] . '"' : null ?>;
          var onePageScrollId = <?= isset($template_settings["page-slider"]) ? '"#' . $template_settings["page-slider"] . '"' : null ?>;
          var transitionSpeed =<?= isset($template_settings["transitionSpeed"]) ? $template_settings["transitionSpeed"] : 1000 ?>;
          $(document).ready(function () {
            if (onePageScrollId) {
              $(onePageScrollId).onepage_scroll({
                sections: "div.page-slide", // sectionContainer accepts any kind of selector in case you don't want to use section
                //easing: "Power2.easeOut", // Easing options accepts the CSS3 easing animation such "ease", "linear", "ease-in",
                // "ease-out", "ease-in-out", or even cubic bezier value such as "cubic-bezier(0.175, 0.885, 0.420, 1.310)"
                animationTime: transitionSpeed, // AnimationTime let you define how long each section takes to animate
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
                direction: "horizontal", // You can now define the direction of the One Page Scroll animation. Options available are "vertical" and "horizontal". The default value is "vertical".  
                mainMenu: mainManuId
              });
            }
            //if (mainManuId)
            //$('#base-content-pane').prepend($(mainManuId).detach())
            $(window).resize(function () {
              $(".section.background").css("min-height", $(window).height());
            });
            $(".section.background").css("min-height", $(window).height());
          });
        }(jQuery));
      </script>
      <?php
      return ob_get_clean();
    }
    return;
  }

  public function get_template_settings_form() {
    ob_start();
    ?>
    <div class="row">
      <div class="col-xs-12 mt" data-toggle="buttons">
        <label id="spw" type="button" class="btn btn-primary col-xs-12" ><input type="checkbox" name="spw" value="true">Single Page Website</label>
      </div>
    </div>
    <div id="spw-cp">         
      <div class="row mt">
        <div class="col-xs-12">
          <select class="text-field" id="menu-id" name="menu-id" data-label="Main Menu ID">
            <option value=''></option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <select class="text-field" id="page-slider" name="page-slider" data-label="Page Slider ID">
            <option value=''></option>
          </select>      
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <select class="text-field" id="transitionSpeed" name="transitionSpeed" data-label="Slider Transtion Speed">
            <option value='1000'>1000</option>
            <option value='1500'>1500</option>
            <option value='2000'>2000</option>
            <option value='3000'>3000</option>
          </select>      
        </div>
      </div>         
    </div>
    <script>
      var onNewItem = function (ni)
      {
        ni.find(":input").attr('disabled', false);
        var ml = ni.find("#menu-link");
        var editor = uisForm.getEditor();
        //alert($("#"+$("#menu-id").val()).html());
        $.each(editor.find("#" + $("#menu-id").val() + " ul li a"), function (i, a)
        {
          ml.append("<option value='" + $(a).attr("href") + "'>" + $(a).attr("href") + "</option>");
        });
      };

      function initSelect(select, value)
      {
        $(select).empty();
        $(select).append("<option value=''></option>");
        //console.log(uisForm.getLayoutWidgets());
        $.each(uisForm.getLayoutWidgets(), function (i, e)
        {
          var e = $(e);
          if (e.attr("id"))
          {
            var selected = (value == e.attr("id")) ? "selected=true" : "";
            $(select).append("<option value='" + e.attr("id") + "' " + selected + ">" + e.attr("id") + "</option>");
          }
        });
      }

      $("#inspector-editor").off("refresh.template");
      $("#inspector-editor").on("refresh.template", function (e, data)
      {
        //var ts = uisForm.templateSettings;
        var currentValue = $("#menu-id").val();
        var currentValueSlider = $("#page-slider").val();
        initSelect("#menu-id", currentValue);
        initSelect("#page-slider", currentValueSlider);
      });
      $("#template_settings_form").on("refresh", function (e, data)
      {
        if (!$("#spw input").is(":checked"))
        {
          $("#spw-cp :input").attr('disabled', true);
          $("#spw-cp").hide();
        }

        // Init menu id list
        initSelect("#menu-id", data["menu-id"]);
        initSelect("#page-slider", data["page-slider"]);

        // Init pages
        // if (data.pages)
        //    $("#website_pages").EW().dynamicList({value: $.parseJSON(data.pages), onNewItem: onNewItem});
        //else
        //               $("#website_pages").EW().dynamicList({onNewItem: onNewItem});

        $("#spw").off("change");
        $("#spw").on("change", function ()
        {
          if ($("#spw input").is(":checked"))
          {
            $("#spw-cp :input").attr('disabled', false);
            $("#spw-cp").stop().animate({
              height: "toggle"
            },
            400, "Power2.easeOut");
          } else
          {

            $("#spw-cp").stop().fadeOut(200, function () {
              $("#spw-cp :input").attr('disabled', true);
            });
          }
          uisForm.updateTemplateBody();
        });
      });

      $("#template_settings_form").on("getData", function (e) {
        var data = $.parseJSON($("#template_settings_form").serializeJSON());
        //if (data)
        //  data.pages = $("#website_pages").EW().dynamicList("getJSON");
        uisForm.setTemplateSettings(data);
      });
    </script>
    <?php
    return ob_get_clean();
  }

  //put your code here
}
