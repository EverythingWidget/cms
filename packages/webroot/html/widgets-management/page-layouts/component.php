<?php
$home_page = webroot\WidgetsManagement::get_path_uis("@HOME_PAGE");
$user_home_page = webroot\WidgetsManagement::get_path_uis("@USER_HOME_PAGE");
$default_page = webroot\WidgetsManagement::get_path_uis("@DEFAULT");
?>
<div class="tabs-bar block-row">
  <ul class="nav nav-pills nav-black-text">
    <li class="active">
      <a href="#uis_list" data-toggle="tab">All Layouts</a>
    </li>

    <li>
      <a href="#pages-uis" data-toggle="tab">Contents Layouts</a>
    </li>
  </ul>
</div>
<div class="no-footer tab-content">
  <div id="uis_list" class="tab-pane active static-block col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-sm-offset-0 col-xs-12">
  </div> 

  <div class="tab-pane col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-sm-offset-0 col-xs-12" id="pages-uis">
    <form id="apps-page-uis" onsubmit="return false;">         
      <div class="box box-white z-index-1">
        <div class="col-xs-12">
          <h2>Contents Layouts</h2>
        </div>
        <div class="col-xs-12">
          <div class="row">
            <system-field class="field col-xs-12">
              <label>tr{Default Layout}</label>
              <input type="hidden" name="@DEFAULTuisId" id="DEFAULT" value="<?= $default_page["uis_id"] ?>">
              <input class="text-field app-page-uis" name="@DEFAULT" id="DEFAULT" value="<?= $default_page["uis_name"] ?>">
            </system-field> 
          </div>
          <div class="row">
            <div class="col-xs-12 mar-bot">
              <input type="hidden" class=""  name="@HOME_PAGEuisId" id="HOME_PAGE" value="<?= $home_page["uis_id"] ?>">
              <input class="text-field app-page-uis" data-label="Homepage Layout" name="@HOME_PAGE" id="HOME_PAGE" value="<?= $home_page["uis_name"] ?>">
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12 mar-bot">
              <input type="hidden" class=""  name="@USER_HOME_PAGEuisId" id="USER_HOME_PAGE" value="<?= $user_home_page["uis_id"] ?>">
              <input class="text-field app-page-uis" data-label="User's Homepage Layout" name="@USER_HOME_PAGE" id="USER_HOME_PAGE" value="<?= $user_home_page["uis_name"] ?>">
            </div>
          </div>
          <?php
          $widgets_types_list = webroot\WidgetsManagement::get_widget_feeders("page");
          $pages = $widgets_types_list->data[0];

          //Show list of pages and their layouts
          if (isset($pages)) {
            foreach ($pages as $page) {
              $uis = webroot\WidgetsManagement::get_path_uis("/{$page->url}");
              echo '<div class="row"><div class="col-xs-12 mar-bot">';
              echo "<input type='hidden'  name='{$page->url}_uisId' id='{$page->url}_uisId' value='{$uis["uis_id"]}'>";
              echo "<input class='text-field app-page-uis' data-label='{$page->title}' name='/{$page->url}' id='{/$page->url}' value='{$uis["uis_name"]}'>";
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
  function PageUIS() {
    var self = this;
    //this.bSelect = EW.addAction("Save Changes", $.proxy(this.save, this));
    $(".app-page-uis:not(.inited)").EW().inputButton({
      title: "<i class='uis-icon'></i>",
      class: "btn-default",
      onClick: function (e) {
        pageUIS.currentElement = e;
        pageUIS.uisListDialog(pageUIS.setPageUIS);
      }
    }).addClass("inited");
    //$(".app-page-uis").addClass("inited");

    this.allUISList = EW.createTable({
      name: "pages-and-uis-list",
      columns: [
        "path",
        "name"
      ],
      headers: {
        Path: {},
        "Layout Name": {}
      },
      rowCount: true,
      url: "api/webroot/widgets-management/get-all-pages-uis-list/",
      pageSize: 30,
      onDelete: function (id) {
        this.confirm("Are you sure?", function () {
          //EW.lock(pageUIS.allUISList.table, "");
          var row = this;
          $.post("api/webroot/api/widgets-management/set-uis", {
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
    $("#uis_list").html(this.allUISList.container);
    //this.allUISList.read();
    // Register event listener for all-uis-list table
    $(document).off("all-uis-list.refresh");
    $(document).on("all-uis-list.refresh", function () {
      pageUIS.allUISList.refresh();
    });
<?php
if ($path_uis_list) {
  echo "EW.setFormData('#apps-page-uis', " . stripslashes($path_uis_list) . ");";
}
?>
  }

  PageUIS.prototype.uisListDialog = function (onSelect) {
    var dialog = EW.createModal({
      class: "center slim"
    });
    this.table = EW.createTable({
      name: "uis-list",
      headers: {
        Name: {},
        Template: {}
      },
      rowCount: true,
      url: "api/webroot/widgets-management/get-uis-list/",
      pageSize: 30,
      columns: [
        "name",
        "template"
      ],
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
    dialog.append("<div class='header-pane'><h1 id='' class='col-xs-12'><span>Layouts</span> Select a layout</h1></div>");
    var d = $("<div id='' class='form-content'></div>");
    this.table.container.addClass("mt");
    d.append(this.table.container);
    dialog.append(d);
    dialog.append($("<div class='footer-pane actions-bar action-bar-items' ></div>").append(removeUISbtn));
    this.table.read();
  };

  PageUIS.prototype.setHomePageUIS = function (uisId) {
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

  PageUIS.prototype.setUserHomePageUIS = function (uisId) {
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

  PageUIS.prototype.setDefaultUIS = function (uisId) {
    $("#defaultUisId").val(uisId.data("field-id"));
    $("#default-uis").text("Loading...");
    $("#default-uis").text(uisId.data("field-name"));
    $.post("<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/set-uis", {
      path: "@DEFAULT",
      uisId: uisId.data("field-id")
    }, function (data) {
      $("body").EW().notify(data).show();
    }, "json");
  };

  PageUIS.prototype.setPageUIS = function (uisId) {
    if (pageUIS.currentElement)
    {
      $("#apps-page-uis [name='" + pageUIS.currentElement.prop("name") + "_uisId']").val(uisId.data("field-id"));
      pageUIS.currentElement.val("Loading...").change();
      var uisName = uisId.data("field-name");
      $.post("~webroot/api/widgets-management/set-uis", {
        path: pageUIS.currentElement.prop("name"),
        uisId: uisId.data("field-id")
      }, function (data) {
        pageUIS.currentElement.val(uisName).change();
        $(document).trigger("all-uis-list.refresh");
        $("body").EW().notify(data).show();
      }, "json");
    }
  };

  var pageUIS;

  (function () {
    System.entity('state-handlers/page-layouts', function (state) {
      var handler = {
        tab: 'all-layouts'
      };
//      state.type = "appSection";
      
      state.bind('init', function () {
      });

      state.bind('start', function () {
        pageUIS = new PageUIS();
        pageUIS.allUISList.read();
      });
    });

    System.state("widgets-management/pages-uis", function (state) {
      var pageLayoutsStateHandler = System.entity('state-handlers/page-layouts');
      new pageLayoutsStateHandler(state);
    });
  })();
</script>
