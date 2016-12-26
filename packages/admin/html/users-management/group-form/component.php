<?php
session_start();

$response = [];
if ($_REQUEST["groupId"]) {
  $response = EWCore::call_api('admin/api/users-management/groups', ['id' => $_REQUEST["groupId"]]);
}

$tabs = EWCore::read_registry('ew/ui/forms/user-group/tabs');
?>
<form id="users-group-form"  action="#" method="POST" onsubmit="return false;">
  <div class="header-pane tabs-bar thin">
    <h1 id="form-title">
      tr{New Group}
    </h1>
    <ul class="nav nav-pills">
      <?php
      $active = 'active';
      foreach ($tabs as $id => $tab) {
        echo "<li class='$active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab['title'] . "}</a></li>";
        $active = '';
      }
      ?>
    </ul>
  </div>
  <div class="block-row form-content tabs-bar">
    <div class="tab-content" >
      <?php
      $active = 'active';
      foreach ($tabs as $id => $tab) {
        echo "<div class='tab-pane $active' id='{$id}'>" . EWCore::get_view($tab['template_url'], [
            'group_id' => $_REQUEST['groupId'],
            'data'     => $response['data']
        ]) . "</div>";
        $active = '';
      }
      ?>
    </div>
  </div>
  <div class="block-row footer-pane actions-bar action-bar-items">
  </div>
</form>

<script  type="text/javascript">
  var UsersGroupsForm = (function () {
    function UsersGroupsForm() {
      var _this = this;

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
          if (a.val() === (c) && a.prop('checked') !== true)          {
            a.prop('checked', true);
            a.click();
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

  var formData = <?= json_encode($response['data']) ?>;
  EW.setFormData("#users-group-form", formData);
</script>