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
    ob_start();
    ?>
    <script>
      /*(function ($) {
        $.fn.detectFont = function () {
          var fonts = $(this).css('font-family').split(",");
          if (fonts.length == 1)
            return fonts[0];

          var element = $(this);
          var detectedFont = null;
          fonts.forEach(function (font) {
            var clone = element.clone().css({'visibility': 'hidden', 'font-family': font}).appendTo('body');
            if (element.width() == clone.width())
              detectedFont = font;
            clone.remove();
          });

          return detectedFont.trim();
        }


        $(document).ready(function () {
          setTimeout(function () {
            var f = $("body").detectFont();
            if (f === "Amiri")
            {
              $("body").css("font-size", "18px");
            }
          }, 1000);

        });
      }(jQuery));*/
    </script>
    <?php

    return ob_get_clean();
  }

  public function get_template_settings_form() {
    
  }

  //put your code here
}
