<?php
session_start();

function sidebar()
{
   // ew-contents-main-form, sidebar
   $html = '<ul><label>tr{Libraries}</label>'
           . '<li><a rel="ajax" data-default="true" data-ew-nav="documents" href="' . EW_ROOT_URL . '~admin/content-management/Documents.php">tr{Explorer}</a></li>'
           . '<li><a rel="ajax" data-ew-nav="media" href="' . EW_ROOT_URL . '~admin/content-management/Media.php">tr{Media}</a></li>';
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

$content_labels = EWCore::read_registry("ew-content-labels");
?>
<script>
   (function ()
   {
      var ContentManagement = System.module("content-management");

      ContentManagement.onInit = function (nav)
      {
         //alert('<?php echo json_encode($content_labels) ?>');
         //alert("a");
         console.log("ContentManagement is here :)");
      };

      ContentManagement.onStart = function ()
      {

      };

      ContentManagement.on("app", function (p, section)
      {
         if (!section /*|| section === this.data.tab*/)
            return;
         this.data.tab = section;

         EW.appNav.setCurrentTab($("a[data-ew-nav='" + section + "']"));
      });

      ContentManagement.on("ew_activity", function (p, path) {
      });
   }());
</script>
