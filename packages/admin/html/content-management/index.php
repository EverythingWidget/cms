<?php
session_start();

function sidebar()
{
   // ew-contents-main-form, sidebar
   $html = '<ul><label>tr{Libraries}</label>'
           . '<li><a rel="ajax" data-default="true" data-ew-nav="documents" href="' . EW_ROOT_URL . '~admin/content-management/Documents.php">tr{Explorer}</a></li>'
           . '<li><a rel="ajax" data-ew-nav="media" href="' . EW_ROOT_URL . '~admin/content-management/Media.php">tr{Media}</a></li>';
   $html.= '</ul><ul><label>tr{Apps}</label>';
   $content_labels = EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT);
   foreach ($content_labels as $comp_id => $label_object)
   {
      //$filter = EWCore::call($label_object['explorer']);
      $html.= '<li><a rel="ajax" data-ew-nav="' . $label_object["title"] . '" href="' . $label_object["explorerUrl"] . '">tr{' . $label_object["title"] . '}</a></li>';
   }
   $html.='</ul>';
   return $html;
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form();

//$content_labels = EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT);
?>

<script>
   (function ()
   {
      var ContentManagement = System.module("content-management");

      ContentManagement.onInit = function (nav) {
         console.log("ContentManagement is here :)");
      };

      ContentManagement.onStart = function () {

      };

      ContentManagement.on("app", function (p, section) {
         if (!section /*|| section === this.data.tab*/)
            return;
         this.data.tab = section;

         EW.appNav.setCurrentTab($("a[data-ew-nav='" + section + "']"));
      });

      ContentManagement.on("ew_activity", function (p, path) {
      });

   }());
</script>
