<?php
session_start();

function get_ew_user_form() {
  ob_start();
  ?>
  <input type="hidden" id="id" name="id" value="">
  <input type="hidden" id="group_id" name="group_id" value="">
  
  <div class="row mt">
    <system-field class="field col-xs-12">
      <label>tr{Title}</label>
      <input class="text-field" value="" id="title" name="title"/>
    </system-field>  

    <system-field class="field col-xs-12">
      <label>tr{Description}</label>
      <textarea class="text-field" id="description" name="description"  /></textarea>
    </system-field> 
  </div>  
  <?php
  return ob_get_clean();
}

function get_ew_users_permissions_form() {
  ob_start();
  ?>
  <input type="hidden" id="permission" name="permission">
  <div class="col-lg-offset-2 col-lg-8 col-xs-12">
    <h3 class="form-title">All Permissions</h3>
  </div>
  <div class="col-lg-offset-2 col-lg-8 col-xs-12 content" id="all-permissions"  >
    <ul class="list permissions tree" data-toggle="buttons">
      <?php
      $permissions_titles = \EWCore::read_permissions_titles();
      if (isset($permissions_titles)) {
        foreach ($permissions_titles as $app_name => $sections) {
          ?>
          <li>
            <label data-value="<?= $app_name ?>">
              <i class="icon circle"></i>
              <h3 class="icon-header">
                <?= $sections["appTitle"]; ?>
              </h3>
            </label>
            <ul class="row">
              <?php
              foreach ($sections["section"] as $section_name => $sections_permissions) {
                ?>
                <li >
                  <label data-value="<?= "$app_name.$section_name" ?>">
                    <i class="icon circle"></i><h3 class="icon-header">
                      <?= $sections_permissions["sectionTitle"]; ?>
                    </h3>
                  </label>
                  <ul>
                    <?php
                    foreach ($sections_permissions["permission"] as $permission_name => $permission_info) {
                      ?>
                      <li class="permission-item">
                        <label  data-value="<?php echo "$app_name.$section_name.$permission_name" ?>">
                          <i class="icon circle"></i>
                          <h3 class="icon-header"><?= $permission_info["title"] ?></h3>
                          <p class="icon-header"><?= $permission_info["description"] ?></p>
                        </label>
                      </li>
                      <?php
                    }
                    ?>
                  </ul>
                </li>
                <?php
              }
              ?>
            </ul>
          </li>
          <?php
        }
      }
      ?>
    </ul>
  </div>
  <script  type="text/javascript">
    var UsersGroupsForm = (function () {
      function UsersGroupsForm() {
        var _this = this;
        var permissions = [
        ];
        this.name = "ha";
        this.bSave = EW.addAction("tr{Save Changes}", $.proxy(this.updateGroup, this)).addClass("btn-success").hide();
        this.bAdd = EW.addAction("tr{Save}", $.proxy(this.addGroup, this)).hide();
        this.bDelete = EW.addAction("tr{Delete}", $.proxy(this.deleteGroup, this)).addClass("btn-danger").hide();

        $("#users-group-form").on('refresh', function (e, formData) {
          if (formData['id']) {
            $("#form-title").html("<span>tr{Group Info}</span>" + formData["title"]);
            _this.readClasses();
            _this.bSave.comeIn(300);
            _this.bDelete.comeIn(300);
          } else {
            _this.bAdd.comeIn(300);
          }
        });

        this.readClasses();
      }
      UsersGroupsForm.prototype.readClasses = function () {
        var $self = this;
        var givenPermissions = $("#permission").val().split(",");
        //alert($("#permission").text());
        $("#permission").empty();
        $.each($("#all-permissions").find("label"), function (k, v) {
          var a = $(v).find("input");
          if (!$(v).hasClass("btn"))
          {
            a = $("<input type='checkbox'>");
            a.val($(v).attr("data-value"));
            a.hide();
            //$(v).text($(v).text());
            a.change(function (event) {
              var label = $(v);
              if ($(this).is(":checked"))
              {
                //label.find("i").addClass("circle");
                label.parent().find("input:not(:checked)").prop('checked', true).parent().addClass("active");
              } else
              {
                //label.find("i").removeClass("circle");
                label.parent().find("input:checked").prop('checked', false).parent().removeClass("active");
              }
              //alert("call");
              // Check parent items
              $.each($("#all-permissions li:not(.permission-item)"), function (i, e) {
                e = $(e);

                if (e.find("li.permission-item input:checked").length == 0)
                {
                  //alert("zero: "+e.children("label").html());
                  e.children("label").children("input:checked").prop('checked', false).parent().removeClass("active");
                } else if (e.find("li.permission-item input:checked").length > 0)
                {
                  //alert("length "+e.find("li.permission-item input:checked").length+", "+e.children("label").html());
                  e.children("label").children("input:not(:checked)").prop('checked', true).parent().addClass("active");
                }
                //alert($self.readPermission());
              });
            });

            $(v).prepend(a);
            $(v).addClass("btn btn-white");
          }
          //
          $.each(givenPermissions, function (i, c) {
            if (a.val() === (c))
            {
              //a.click();
              a.prop('checked', true);
              a.click();
              //$("#permission").append($(v));
              givenPermissions[i] = null;
            }
          });
        });
      };

      UsersGroupsForm.prototype.readPermission = function () {
        var permissions = [
        ];

        $.each($("#users-group-form").find("li.permission-item input:checkbox:checked"), function (k, v) {
          if (!$(v).is(":disabled"))
            permissions.push($(v).val());
        });

        return permissions;
      };



      UsersGroupsForm.prototype.updateGroup = function () {
        var $self = this;
        if ($("#title").val())
        {
          //alert(media.itemId);
          var formParams = $.parseJSON($("#users-group-form").serializeJSON());
          formParams["permission"] = $self.readPermission().toString();
          var locker = System.ui.lock({
            element: $("#users-group-form").parent()[0],
            akcent: 'loader center'
          });

          $.ajax({
            type: 'PUT',
            url: 'api/admin/users-management/groups', data: formParams, success: function (response) {
              $(document).trigger("users-groups-list.refresh");
              $("body").EW().notify(response).show();
              EW.setFormData("#users-group-form", response.data);
              locker.dispose();
            }
          });
        }
      };
      UsersGroupsForm.prototype.addGroup = function () {
        var $self = this;
        if ($("#title").val())
        {
          //alert(media.itemId);
          var formParams = $.parseJSON($("#users-group-form").serializeJSON());
          formParams["permission"] = $self.readPermission().toString();
          EW.lock($("#users-group-form"), "Saving...");
          $.ajax({
            type: 'POST',
            url: 'api/admin/users-management/groups/',
            data: formParams,
            success: function (data) {
              $.EW("getParentDialog", $("#users-group-form")).trigger("close");
              $(document).trigger('users-groups-list.refresh');
              $("body").EW().notify(data).show();

              EW.unlock($("#users-group-form"));
            }
          });
        }
      };

      UsersGroupsForm.prototype.deleteGroup = function () {
        if (confirm("Are you sure of deleting of this group?"))
        {
          var formParams = $.parseJSON($("#users-group-form").serializeJSON());
          $.ajax({
            type: 'DELETE',
            url: 'api/admin/users-management/groups',
            data: formParams,
            success: function (data) {
              $.EW("getParentDialog", $("#users-group-form")).trigger('destroy');
              $("body").EW().notify(data).show();
              $(document).trigger('users-groups-list.refresh');
            }
          });
        }
      };
      return new UsersGroupsForm();
    })();
  <?php
  $row = [];
  if ($_REQUEST["groupId"]) {
    $row = EWCore::call_api('admin/api/users-management/groups', ['id' => $_REQUEST["groupId"]]);
  }
  ?>
    var formData = <?= isset($row) ? json_encode($row["data"]) : 'null' ?>;
    EW.setFormData("#users-group-form", formData);

  </script>
  <?php
  return ob_get_clean();
}

EWCore::register_form("ew/ui/user-form", "ew-user-form", ["title"   => "Group Info",
    "content" => get_ew_user_form()]);
EWCore::register_form("ew/ui/user-form", "ew-user-permissions", ["title"   => "Permissions",
    "content" => get_ew_users_permissions_form()]);

$tabs = EWCore::read_registry("ew/ui/user-form");
?>
<form id="users-group-form"  action="#" method="POST" onsubmit="return false;">
  <div class="block-row header-pane tabs-bar">
    <h1 id='form-title' class="col-xs-12">
      tr{New Group}
    </h1>
    <ul class="nav nav-pills">
      <?php
      foreach ($tabs as $id => $tab) {
        if ($id == "ew-user-form")
          echo "<li class='active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
        else
          echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
      }
      ?>
    </ul>
  </div>
  <div class="block-row form-content tabs-bar">

    <div class="tab-content col-xs-12" style="height:100%;position:absolute;">
      <?php
      foreach ($tabs as $id => $tab) {
        if ($id == "ew-user-form")
          echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
        else
          echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
      }
      ?>
    </div>

  </div>
  <div class="block-row footer-pane actions-bar action-bar-items">
  </div>
</form>

