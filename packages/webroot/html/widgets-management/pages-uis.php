<?php
$home_page = json_decode(webroot\WidgetsManagement::get_path_uis("@HOME_PAGE"), true);
$user_home_page = json_decode(webroot\WidgetsManagement::get_path_uis("@USER_HOME_PAGE"), true);
$default_page = json_decode(webroot\WidgetsManagement::get_path_uis("@DEFAULT"), true);
//$path_uis_list = EWCore::process_command("admin", "WidgetsManagement", "get_all_pages_uis_list", null, false);
//http_response_code(200);
//header('Content-Type: text/html');
//print_r($path_uis_list);
?>
<div class="tab-pane-xs tab-pane-sm header-pane tabs-bar row">
   <ul class="nav nav-pills nav-blue-grey">
      <li class="active"><a href="#uis_list" data-toggle="tab">All Layouts</a></li>
      <li><a href="#pages-uis" data-toggle="tab">Contents Layouts</a></li>
   </ul>
</div>
<div class="tab-pane-xs tab-pane-sm form-content tabs-bar no-footer tab-content row">
   <div id="uis_list" class="tab-pane active col-lg-5 col-md-6 col-sm-12 col-xs-12 static-block">

   </div> 
   <div class="tab-pane col-lg-7 col-md-6 col-sm-12 col-xs-12 pull-right" id="pages-uis">
      <form id="apps-page-uis" onsubmit="return false;">         
         <div class="box box-white z-index-1">
            <div class="col-xs-12">
               <h2>Contents Layouts</h2>
            </div>
            <div class="col-xs-12">
               <div class="row">
                  <div class="col-xs-12 mar-bot">
                     <input type="hidden" class=""  name="@DEFAULTuisId" id="DEFAULT" value="<?php echo $default_page["id"] ?>">
                     <input class="text-field app-page-uis" data-label="Default Layout" name="@DEFAULT" id="DEFAULT" value="<?php echo $default_page["name"] ?>">
                  </div>
               </div>
               <div class="row">
                  <div class="col-xs-12 mar-bot">
                     <input type="hidden" class=""  name="@HOME_PAGEuisId" id="HOME_PAGE" value="<?php echo $home_page["id"] ?>">
                     <input class="text-field app-page-uis" data-label="Homepage Layout" name="@HOME_PAGE" id="HOME_PAGE" value="<?php echo $home_page["name"] ?>">
                  </div>
               </div>
               <div class="row">
                  <div class="col-xs-12 mar-bot">
                     <input type="hidden" class=""  name="@USER_HOME_PAGEuisId" id="USER_HOME_PAGE" value="<?php echo $user_home_page["id"] ?>">
                     <input class="text-field app-page-uis" data-label="User's Homepage Layout" name="@USER_HOME_PAGE" id="USER_HOME_PAGE" value="<?php echo $user_home_page["name"] ?>">
                  </div>
               </div>
               <?php
               $widgets_types_list = webroot\WidgetsManagement::get_widget_feeders("page");
               $pages = $widgets_types_list["data"];
               //Show list of pages and their layouts
               if (isset($pages))
               {
                  foreach ($pages as $page)
                  {
                     $uis = json_decode(webroot\WidgetsManagement::get_path_uis("{$page->url}"), true);
                     echo '<div class="row"><div class="col-xs-12 mar-bot">';
                     echo "<input type='hidden'  name='{$page->url}_uisId' id='{$page->url}_uisId' value='{$uis["id"]}'>";
                     echo "<input class='text-field app-page-uis' data-label='{$page->title}' name='{$page->url}' id='{$page->url}' value='{$uis["name"]}'>";
                     echo "</div></div>";
                  }
               }
               ?>
            </div>      
         </div>
      </form>
   </div>
</div>
<script  type="text/javascript">
   function PageUIS()
   {
      var self = this;
      //this.bSelect = EW.addAction("Save Changes", $.proxy(this.save, this));
      $(".app-page-uis").EW().inputButton({
         title: "<i class='uis-icon'></i>",
         onClick: function (e) {
            pageUIS.currentElement = e;
            pageUIS.uisListDialog(pageUIS.setPageUIS);
         }
      });

      this.allUISList = EW.createTable({
         name: "pages-and-uis-list",
         columns: ["path", "name"],
         headers: {
            Path: {},
            "Layout Name": {}
         },
         rowCount: true,
         url: "<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/get-all-pages-uis-list",
         pageSize: 30,
         onDelete: function (id) {
            this.confirm("Are you sure?", function () {
               //EW.lock(pageUIS.allUISList.table, "");
               var row = this;
               $.post("<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/set-uis", {
                  path: row.data("field-path")
               }, function (data) {
                  $("input[name='" + row.data("field-path") + "']").val("").change();
                  $("body").EW().notify(data).show();
                  self.allUISList.removeRow(id);
                  row._messageRow.remove();
                  //$(document).trigger("all-uis-list.refresh");
               }, "json");
            });
            //uisList.deleteUIS(id);
         }

      });
      //this.allUISList.container.css({margin: "5px 15px"});
      $("#uis_list").append(this.allUISList.container);
      //this.allUISList.read();
      // Register event listener for all-uis-list table
      $(document).off("all-uis-list.refresh");
      $(document).on("all-uis-list.refresh", function () {
         pageUIS.allUISList.refresh();
      });
<?php
if ($path_uis_list)
{
   echo "EW.setFormData('#apps-page-uis', " . stripslashes($path_uis_list) . ");";
}
?>
   }

   PageUIS.prototype.uisListDialog = function (onSelect) {
      var dialog = EW.createModal();
      this.table = EW.createTable({
         name: "uis-list",
         headers: {
            Name: {},
            Template: {}
         },
         rowCount: true,
         url: "<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/get-uis-list",
         pageSize: 30,
         columns: ["name", "template"],
         buttons: {
            "Select": function (row) {
               if (onSelect)
                  onSelect.apply(null, new Array(row));
               dialog.dispose();
            }
         }
      });
      var removeUISbtn = $("<button class='btn btn-danger' type='button'>Clear UIS</button>");
      removeUISbtn.on("click", function () {
         if (onSelect)
         {
            var data = $();
            data.data("field-id", "");
            data.data("field-name", "");
            onSelect.apply(null, new Array(data));
         }
         dialog.dispose();
      });
      dialog.append("<div class='header-pane row'><h1 id='' class='col-xs-12'><span>Layouts</span> Select a layout</h1></div>");
      var d = $("<div id='' class='form-content'></div>");
      this.table.container.addClass("mar-top");
      d.append(this.table.container);
      dialog.append(d);
      dialog.append($("<div class='footer-pane row actions-bar action-bar-items' ></div>").append(removeUISbtn));
      this.table.read();
   };

   PageUIS.prototype.setHomePageUIS = function (uisId)
   {
      $("#homePageUisId").val(uisId.data("field-id"));
      $("#home-page-uis").text("Loading...");
      $("#home-page-uis").text(uisId.data("field-name"));
      $.post("<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/set-uis", {
         path: "@HOME_PAGE",
         uisId: uisId.data("field-id")
      },
              function (data) {
                 $("body").EW().notify(data).show();
              }, "json");
   };

   PageUIS.prototype.setUserHomePageUIS = function (uisId)
   {
      $("#homeUserPageUisId").val(uisId.data("field-id"));
      $("#user-home-page-uis").text("Loading...");
      $("#user-home-page-uis").text(uisId.data("field-name"));
      $.post("/webroot/api/WidgetsManagement/set_uis", {
         path: "@USER_HOME_PAGE",
         uisId: uisId.data("field-id")
      },
              function (data) {
                 $("body").EW().notify(data).show();
              }, "json");
   };

   PageUIS.prototype.setDefaultUIS = function (uisId)
   {
      $("#defaultUisId").val(uisId.data("field-id"));
      $("#default-uis").text("Loading...");
      $("#default-uis").text(uisId.data("field-name"));
      $.post("<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/set-uis", {
         path: "@DEFAULT",
         uisId: uisId.data("field-id")
      },
              function (data) {
                 $("body").EW().notify(data).show();
              }, "json");
   };

   PageUIS.prototype.setPageUIS = function (uisId)
   {
      if (pageUIS.currentElement)
      {
         $("#apps-page-uis [name='" + pageUIS.currentElement.prop("name") + "_uisId']").val(uisId.data("field-id"));
         pageUIS.currentElement.val("Loading...").change();
         var uisName = uisId.data("field-name");
         $.post("<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/set-uis", {
            path: pageUIS.currentElement.prop("name"),
            uisId: uisId.data("field-id")
         },
                 function (data) {
                    pageUIS.currentElement.val(uisName).change();
                    $(document).trigger("all-uis-list.refresh");
                    $("body").EW().notify(data).show();
                 }, "json");
      }
   };

   var pageUIS;

   (function (System) {
      var Section = function () {
         this.type = "appSection";
         this.onInit = function () {
            pageUIS = new PageUIS();
            pageUIS.allUISList.read();
         };

         this.onStart = function () {
         };
      };

      System.module("widgets-management").module("pages-uis", Section);
   }(System));
</script>
