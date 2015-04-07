<?php
session_start();

function sidebar()
{
   // ew-contents-main-form, sidebar
   $html = '<label>tr{Libraries}</label><ul>'
           . '<li><a rel="ajax" data-default="true" data-ew-nav="documents" href="' . EW_ROOT_URL . 'app-admin/ContentManagement/Documents.php" />tr{Documents}</a></li>'
           . '<li><a rel="ajax" data-ew-nav="media" href="' . EW_ROOT_URL . 'app-admin/ContentManagement/Media.php">tr{Media}</a></li></ul>';

   return $html;
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form();
?>
<script>
   moduleAdmin.controller('Sidebar', function ($scope)
   {

   });
   moduleAdmin.controller('MainContent', function ($scope)
   {

   });

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
</script>
