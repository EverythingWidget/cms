<?php

function sidebar()
{
   ob_start();
   ?>
   <ul>
      <li>
         <a rel="ajax" href="app=dashboard"   data-default="true">
            Dashboard
         </a>
      </li>
   </ul>

   <label>Apps</label>
   <ul id="apps">
      <?php
      $apps = json_decode(EWCore::get_apps(), true);
      foreach ($apps as $app)
      {
         //if()
         ?>
         <li><a rel="ajax" href="app=<?php echo $app["root"]; ?>"   ><?php echo $app["name"]; ?></a></li>
         <?php
      }
      ?>
   </ul>

   <?php
    ob_get_clean();
}

function content()
{
   ob_start();
   ?>
   <div class="header-pane tabs-bar row">
      <ul id="app-sections" class="nav nav-pills xs-nav-tabs col-xs-12">    
      </ul>
   </div>
   <div id="app-section-content" class="form-content tab-content tabs-bar no-footer row" >
   </div>
   <?php
   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
   <script >
      /*moduleAdmin.controller('Sidebar', function ($scope)
       {
       
       });
       moduleAdmin.controller('MainContent', function ($scope)
       {
       
       });*/
      var sectionManagement = (function () {
         var appContent = $("#main-content").html();
         //$("#main-content").empty();
         function SectionManagement()
         {
            //EW.setHashParameter("app", "webroot");
            //$("#action-bar-items").append($("#apps-list"));
         }

         SectionManagement.prototype.getAppConf = function (app)
         {
            $.post('<?php echo EW_ROOT_URL; ?>app-admin/AppsManagement/AppInfo.php', {
               appDir: app
            },
            function (data)
            {

               $("#app-conf").html(data);
               $('#app-sections a[href="#app-conf"]').tab("show");
            });

         };

         SectionManagement.prototype.getAppSections = function (app)
         {
            var self = this;
            EW.lock($("#main-content"));
            $("#main-content").empty();
            $("#main-content").html(appContent);
            $.post('<?php echo EW_ROOT_URL; ?>app-admin/AppsManagement/get_app_sections', {
               appDir: app
            },
            function (data)
            {
               $.each(data, function (k, v) {
                  var li = $("<li></li>");
                  var a = $("<a></a>");
                  a.attr("data-toggle", "tab");
                  a.text(v.title);
                  a.attr("href", "#" + v.className);
                  a.click(function () {
                     //if ($("#" + v.className).is(":empty"))
                     //{
                     //self.loadAppSection(app, v.className);
                     //}
                     //EW.setHashParameter("section", v.className, app);
                     EW.setHashParameter("section", v.className);
                  });
                  li.append(a);
                  $("#app-sections").append(li);
                  $("#app-section-content").append("<div class='tab-pane' id='" + v.className + "'></div>");
               });
               var li = $("<li ></li>");
               var a = $("<a></a>");
               a.attr("data-toggle", "tab");
               a.text("Configuration");
               a.attr("href", "#app-conf");
               a.click(function () {
                  //EW.setHashParameter("section", "app-conf", app);
                  EW.setHashParameter("section", "app-conf");
                  //self.loadAppSection(app, "app-conf");
               });
               li.append(a);
               $("#app-sections").append(li);
               $("#app-section-content").append("<div class='tab-pane' id='app-conf'></div>");

               if (EW.getHashParameter("section", app))
               {
                  // Get the selected section of the 'app'
                  var sec = EW.getHashParameter("section", app);
                  EW.setHashParameter("section", sec);

                  // Load perviuos app section if the page has been refreshed or last selected section and current selected section are the same.
                  // When the app is changed, the last selected section and the currently selected section could be the same
                  if (!self.oldSec || self.oldSec == sec)
                  {
                     self.loadAppSection(app, sec);
                     //self.oldSec = sec;
                  }
               }
               EW.unlock($("#main-content"));
            }, "json");

         };

         SectionManagement.prototype.loadAppSection = function (app, section)
         {
            //console.log(app + " " + section);
            //if (this.oldSec == section)
            //return;
            var self = this;
            if (app == "dashboard")
               return;
            EW.setHashParameter("section", section, app);
            $("#action-bar-items").find("button").remove();
            if (section == "app-conf")
               self.getAppConf(app);
            else
               $.post("<?php echo EW_ROOT_URL; ?>app-" + app + "/" + section + "/index.php", {}, function (data) {
                  $("#action-bar-items").find("button").remove();
                  $("#" + section).html(data);
                  $('#app-sections a[href="#' + section + '"]').tab("show");
               });
         };

         SectionManagement.prototype.loadDashboard = function (sec)
         {
            EW.lock($("#main-content"));
            //$("#app-sections").html("");
            $("#action-bar-items").find("button").remove();
            $.post("<?php echo EW_ROOT_URL; ?>app-admin/AppsManagement/dashboard.php", {}, function (data) {
               $("#action-bar-items").find("button").remove();
               $("#main-content").html(data);
               EW.unlock($("#main-content"));
            });
         };
         return new SectionManagement();
      })();


      $(document).ready(function ()
      {
         // Set app to dashboard when there is no app specified. This happens when the page is loaded for the first time
         /*if (!EW.getHashParameter("app"))
          {
          EW.setHashParameter("app", "dashboard");
          }*/

         EW.addURLHandler(function ()
         {
            var app = EW.getHashParameter("app");
            var dashboard = EW.getHashParameter("dashboard");

            var sec = EW.getHashParameter("section");
            var appChanged = false;

            if (app && sectionManagement.oldApp != app)
            {
               //appChanged = true;
               // Set default selected section as 'app-conf' and break
               if (!sec)
               {
                  sec = "app-conf";
                  EW.setHashParameter("section", sec);
                  return;
               }
               // set the currently selected section for the current app in the case which page has been refreshed
               // for persistency
               if (!EW.getHashParameter("section", app) && !sectionManagement.oldApp)
               {
                  EW.setHashParameter("section", sec, app);
               }
               // set app conf as section when the app open for the first time
               else if (!EW.getHashParameter("section", app))
               {
                  EW.setHashParameter("section", "app-conf", app);
               }
               if (app == "dashboard")
               {
                  sectionManagement.loadDashboard();
                  sec = "app-conf";
               }
               else
               {
                  // If app has been changed, read app sections and then break
                  sectionManagement.getAppSections(app);
                  sectionManagement.oldApp = app;
                  return;
                  //if (EW.getHashParameter("section", app))
                  //sec = EW.getHashParameter("section", app);
                  //else
                  //sec = "app-conf";
               }
               sectionManagement.oldApp = app;
            }

            // Happens when different section inside the current app is selected
            if (sectionManagement.oldSec != sec)
            {
               if (sectionManagement.section)
               {
                  sectionManagement.section.dispose();
               }
               sectionManagement.loadAppSection(app, sec);
               sectionManagement.oldSec = sec;
            }

            return "SectionManagementHandler";
         });

      });
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);

