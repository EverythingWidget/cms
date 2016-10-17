<?php
$home_page = webroot\WidgetsManagement::get_path_uis("@HOME_PAGE");
$user_home_page = webroot\WidgetsManagement::get_path_uis("@USER_HOME_PAGE");
$default_page = webroot\WidgetsManagement::get_path_uis("@DEFAULT");
?>

<div id="uis_list" class="">

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
        alert()
        this.confirm("Are you sure?", function () {
          //EW.lock(pageUIS.allUISList.table, "");
          var row = this;
          $.post("api/webroot/widgets-management/set-uis", {
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
    $.post('api/webroot/widgets-management/set-uis', {
      path: "@HOME_PAGE",
      uisId: uisId.data("field-id")
    }, function (data) {
      $("body").EW().notify(data).show();
    });
  };

  PageUIS.prototype.setUserHomePageUIS = function (uisId) {
    $("#homeUserPageUisId").val(uisId.data("field-id"));
    $("#user-home-page-uis").text("Loading...");
    $("#user-home-page-uis").text(uisId.data("field-name"));
    $.post("api/webroot/WidgetsManagement/set_uis", {
      path: "@USER_HOME_PAGE",
      uisId: uisId.data("field-id")
    }, function (data) {
      $("body").EW().notify(data).show();
    }, "json");
  };

  PageUIS.prototype.setDefaultUIS = function (uisId) {
    $("#defaultUisId").val(uisId.data("field-id"));
    $("#default-uis").text("Loading...");
    $("#default-uis").text(uisId.data("field-name"));
    $.post('api/webroot/widgets-management/set-uis', {
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
      $.post('api/webroot/widgets-management/set-uis', {
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
</script>
<?= ew\ResourceUtility::load_js_as_tag(__DIR__ . '/component.js', [], true) ?>