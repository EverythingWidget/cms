<?php
session_start();

function sidebar() {
  ob_start();
  ?>
  <ul>  
    <li>   
      <a rel="ajax" data-default="true" data-ew-nav="users" href="<?php echo EW_ROOT_URL; ?>~admin/users-management/users.php">tr{Users}</a> 
    </li>     
    <li>      
      <a rel="ajax" data-ew-nav="users_groups" href="<?php echo EW_ROOT_URL; ?>~admin/users-management/users-groups.php">tr{Users Groups}</a>       
    </li>    
  </ul>   
  <?php
  return ob_get_clean();
}

function script() {
  ob_start();
  ?>
  <script >
    (function (System) {

      /**
       * Users management component
       * 
       * @param {System.MODULE_ABSTRACT} module a instance of system module
       */
      function UsersManagementComponent(module) {
        var component = this;
        this.module = module;
        this.module.type = "app";
        this.data = {};

        this.module.bind('init', function () {
          component.init();
        });

        this.module.bind('start', function () {
          component.start();
        });
      }

      UsersManagementComponent.prototype.init = function () {
        var component = this;
        this.module.data.sections =<?= json_encode(EWCore::read_registry_as_array('ew/ui/apps/users/navs')) ?>;

        this.module.installModules = this.module.data.sections;

        this.module.on("app", function (path, section) {
          if (!section /*|| section === component.data.tab*/) {
            System.UI.components.sectionsMenuList[0].value = '0';
            return;
          }

          component.data.tab = section;
          System.services.app_service.load_section(section);
        });
      };

      UsersManagementComponent.prototype.start = function () {
        this.data.tab = null;
      };

      System.module("users-management", function () {
        new UsersManagementComponent(this);
      });

    }(System));
  </script>
  <?php
  return ob_get_clean();
}

//\EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
