<?php
session_start();

if (!$_SESSION['login'])
{
   header('Location: Login.php');
   return;
}
?>
<div id="sidebar" class="side-bar">
   <div class="row">
      <ul>
         <li>
            <a rel="ajax" href="app=dashboard"   >
               Dashboard
            </a>
         </li>
      </ul>
   </div>
   <div class="row">
      <label>Apps</label>
      <ul id="apps">
         <?php
         global $EW;
         $apps = json_decode($EW->get_apps(), true);
         foreach ($apps as $app)
         {
            //if()
            ?>
            <li><a rel="ajax" href="app=<?php echo $app["root"]; ?>"   ><?php echo $app["name"]; ?></a></li>
            <?php
         }
         ?>
      </ul>
   </div>
</div>

<div id="main-content" class="col-xs-12" role="main">

   <div class="header-pane tabs-bar row">
      <ul id="app-sections" class="nav nav-pills xs-nav-tabs col-xs-12">    
      </ul>
   </div>
   <div class="form-content  tabs-bar no-footer row" >
      <div id="app-section-content" class="tab-content col-xs-12" style="height:100%;">

      </div>
   </div>

</div>

<script  type="text/javascript">
   var sectionManagement = (function () {
      var appContent = $("#main-content").html();
      //$("#main-content").empty();
      function SectionManagement()
      {
         this.oldApp = null;
         //EW.setHashParameter("app", "webroot");
         //$("#action-bar-items").append($("#apps-list"));
      }

      SectionManagement.prototype.getAppConf = function (app)
      {
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/AppsManagement/AppInfo.php', {appDir: app}, function (data)
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
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/AppsManagement/get_app_sections', {appDir: app}, function (data)
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

               EW.setHashParameter("section", "app-conf");
               //self.loadAppSection(app, "app-conf");
            });
            li.append(a);
            $("#app-sections").append(li);
            $("#app-section-content").append("<div class='tab-pane' id='app-conf'></div>");

            if (EW.getHashParameter("section", app))
            {
               var sec = EW.getHashParameter("section", app);
               EW.setHashParameter("section", sec);
               //$('#app-sections a[href="#' + sec + '"]').tab("show");
               //alert(sec);
               //self.loadAppSection(app, sec);
            }
            EW.unlock($("#main-content"));
         }, "json");

      };

      SectionManagement.prototype.loadAppSection = function (app, section)
      {         
         EW.setHashParameter("section", section, app);
         var self = this;
         $("#action-bar-items").find("button").remove();
         if (section == "app-conf")
         {
            self.getAppConf(app);            
         }
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

   EW.addURLHandler(function ()
   {
      var app = EW.getHashParameter("app");
      var dashboard = EW.getHashParameter("dashboard");

      var sec = EW.getHashParameter("section");
      var appChanged = false;
      var init = false;
      if (app && sectionManagement.oldApp != app)
      {
         //appChanged = true;


         //if (!sec)
         //sec = "app-conf";
         // set current section for current app in the case which page has been refreshed
         // for persistency
         /*if (!EW.getHashParameter("section", app) && !sectionManagement.oldApp)
          {
          EW.setHashParameter("section", sec, app);
          }*/

         // set app conf as section when the app open for the first time

         if (app == "dashboard")
         {
            sectionManagement.loadDashboard();
         }
         else
         {
            sectionManagement.getAppSections(app);
            //return;
         }
         if (sectionManagement.oldApp && sectionManagement.oldApp != "dashboard")
         {
            appChanged = true;
            EW.setHashParameter("section", null);
            //alert("sec null");
            sectionManagement.oldApp = app;
            return;
         }
         else
            init = true;

         sectionManagement.oldApp = app;
      }
      //alert(sec + ":" + sectionManagement.oldSection + " " + app + " " + appChanged + " " + init);
      if (sectionManagement.oldSection != sec || appChanged || init)
      {

         if (sectionManagement.section)
         {
            sectionManagement.section.dispose();
         }
         if (appChanged)
         {
            if (!EW.getHashParameter("section", app))
            {
               sec = "app-conf";
            }
            else
               sec = EW.getHashParameter("section", app);
            EW.setHashParameter("section", sec);
            sectionManagement.loadAppSection(app, sec);
            sectionManagement.oldSection = sec;
         }
         else
         {
            
            if (!sec)
            {
               if (EW.getHashParameter("section", app))
                  sec = EW.getHashParameter("section", app);
               else
                  sec = "app-conf";
            }

            EW.setHashParameter("section", sec);
            sectionManagement.loadAppSection(app, sec);
            sectionManagement.oldSection = sec;
         }



      }

      return "SectionManagementHandler";
   });
   if (!EW.getHashParameter("app"))
      EW.setHashParameter("app", "dashboard");
</script>