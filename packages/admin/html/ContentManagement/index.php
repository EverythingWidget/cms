<?php
session_start();

function sidebar()
{
   // ew-contents-main-form, sidebar
   $html = '<ul><label>tr{Libraries}</label>'
           . '<li><a rel="ajax" data-default="true" data-ew-nav="documents" href="' . EW_ROOT_URL . 'admin/ContentManagement/Documents.php">tr{Explorer}</a></li>'
           . '<li><a rel="ajax" data-ew-nav="media" href="' . EW_ROOT_URL . 'admin/ContentManagement/Media.php">tr{Media}</a></li>';
   $html.= '</ul><ul><label>tr{Apps}</label>';
   $content_labels = EWCore::read_registry("ew-content-labels");

   foreach ($content_labels as $comp_id => $label_object)
   {
      $filter = $label_object->get_explorer_nav($comp_id, []);
      $html.= '<li><a rel="ajax" data-ew-nav="' . $filter["title"] . '" href="' . $filter["url"] . '">tr{' . $filter["title"] . '}</a></li>';
   }
   $html.='</ul>';
   return $html;
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form();
?>
<script>
   /*moduleAdmin.controller('Sidebar', function ($scope)
    {
    
    });
    moduleAdmin.controller('MainContent', function ($scope)
    {
    
    });*/

   function ContentManagement()
   {
      this.parentId = null;
      this.categoryId = 0;
      this.articleId = 0;
      this.preCategoryId = -1;
      this.oldItem;
      $(window).resize(function () {
         var cn = Math.floor(($("#main-content").width()) / 164);
         var mw = Math.floor(($("#main-content").width() - (cn * 164)) / cn);
         $(".content-item").css("margin-right", mw);
      });
   }
   ContentManagement.prototype.dispose = function ()
   {
   };
   var oldLib = "";

   (function ()
   {
      System.module("ContentManagement",
              {
                 init: function ()
                 {
                 },
                 start: function ()
                 {

                 },
                 hashHandler: function (parameters)
                 {
                    //alert();
                 }
              });
   }());
</script>
