<div id="settings-cards">

</div>

<script  type="text/javascript">

   (function (Domain) {
      function SettingsComponent(module) {
         var component = this;
         this.module = module;
         this.module.type = "app-section";
         this.module.component = this;

         this.module.onInit = function () {
            component.init();
         };

         this.module.onStart = function () {
            component.start();
         };
      }

      SettingsComponent.prototype.init = function () {

      };

      SettingsComponent.prototype.start = function () {
         this.selectMediaAction = EW.addActionButton({
            text: "",
            handler: function () {
               //_this.selectMedia(_this.selectedImage);
            },
            class: "btn-float btn-success icon-ok",
            parent: System.UI.components.appMainActions
         });
         this.loadAppsGeneralSettings(<?= json_encode($ui_settings_general) ?>);
      };

      SettingsComponent.prototype.loadAppsGeneralSettings = function (apps) {
         var settingsCard = $("#settings-cards");
         settingsCard.empty();

         for (var app in apps) {
            console.log(apps[app]);
            if (!apps[app].url) {
               continue;
            }

            $.get(apps[app].url, function (response) {
               settingsCard.append(response);
            });
         }
      };

      Domain.module("settings/general", function () {
         new SettingsComponent(this);
      });
   })(System);
</script>
