<?php
session_start();

function sidebar() {
  // ew-contents-main-form, sidebar
  $html = '<ul><label>tr{Libraries}</label>'
          . '<li><a rel="ajax" data-default="true" data-ew-nav="documents" href="' . EW_ROOT_URL . '~admin/content-management/documents.php">tr{Explorer}</a></li>'
          . '<li><a rel="ajax" data-ew-nav="media" href="' . EW_ROOT_URL . '~admin/content-management/media.php">tr{Media}</a></li>';
  $html.= '</ul><ul><label>tr{Apps}</label>';
  $content_labels = EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT);
  foreach ($content_labels as $comp_id => $label_object) {
    //$filter = EWCore::call($label_object['explorer']);
    $html.= '<li><a rel="ajax" data-ew-nav="' . $label_object["title"] . '" href="' . $label_object["explorerUrl"] . '">tr{' . $label_object["title"] . '}</a></li>';
  }
  $html.='</ul>';
  return $html;
}

echo admin\AppsManagement::create_section_main_form();
?>
<script>
  (function (System) {
    System.module("content-management", function () {
      this.type = "app";

      this.bind('init', function (nav) {
        this.data.sections = [
          {
            title: "tr{Documents}",
            id: "content-management/documents",
            url: "~admin/html/content-management/documents.php"
          },
          {
            title: "tr{Media}",
            id: "content-management/media",
            url: "~admin/html/content-management/media/index.php"
          }
        ];

        this.installModulesOnInit(this.data.sections);
      });

      this.bind('start', function () {
        //alert('inja');
        this.data.tab = null;
      });

      this.on("app", function (p, section) {
        if (!section) {                    
          System.UI.components.sectionsMenuList[0].value = '0';
          return;
        }               

        if (this.data.tab === section)
          return;

        this.data.tab = section;
        System.services.app_service.load_section(section);
      });

      return this;
    });
  })(System);
</script>
