<?php
session_start();

//admin\UsersManagement::get_user_by_id($_REQUEST["userId"]);
function get_ew_user_form($user_id) {
  ob_start();
  ?>
  <input type="hidden" id="id" name="id" value="">
  <div class="row">
    <div class="col-xs-12 col-md-12 col-lg-12 mt">
      <input class="text-field" data-label="tr{Username}" value="" id="email" name="email"/>
    </div>    
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6">
      <input class="text-field" id="first_name" data-label="tr{First Name}" name="first_name"  />
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6">
      <input class="text-field" id="last_name" data-label="tr{Last Name}" name="last_name"  />
    </div>
  </div>
  <?php if (!isset($user_id)) { ?>
    <div class="row">
      <div class="col-xs-12 col-md-12 col-lg-12 mt">
        <input class="text-field" data-label="tr{Password}" value="" id="password" name="password"/>
      </div>    
    </div>
  <?php } ?>
  <div class="row">    
    <div class="col-xs-12 mt">
      <?php
      $users_groups = EWCore::call_api("admin/api/users-management/groups");
      //print_r($users_groups["result"]);
      ?>
      <select id="group_id" name="group_id" data-width="100%" data-label="tr{Group}">
        <?php
        foreach ($users_groups['data'] as $group) {
          echo "<option value='{$group['id']}'>{$group['title']}</option>";
        }
        ?>
      </select>
    </div>
  </div>
  <script  type="text/javascript">
    var UserForm = (function () {
      function UserForm() {
        this.bAdd = EW.addAction("tr{Add}", $.proxy(this.addUser, this)).hide();
        this.bSave = EW.addAction("tr{Save Changes}", $.proxy(this.updateUser, this)).addClass("btn-success").hide();
      }

      UserForm.prototype.addUser = function () {
        if ($("#email").val())
        {
          //alert(media.itemId);
          var formParams = $.parseJSON($("#user-form").serializeJSON());
          EW.lock($("#user-form"), "Saving...");
          $.ajax({
            type: 'POST',
            url: 'api/admin/users-management/users',
            data: formParams,
            success: success
          });

          function success(data) {
            debugger;
            if (data.status === "success")
            {
              $.EW("getParentDialog", $("#user-form")).trigger("close");
              $(document).trigger("users-list.refresh");
              $("body").EW().notify(data).show();
            } else
            {
              $("body").EW().notify(data).show();
            }
            EW.unlock($("#user-form"));
          }
        }
      };
      UserForm.prototype.updateUser = function () {
        if ($("#email").val())
        {
          //alert(media.itemId);
          var formParams = $.parseJSON($("#user-form").serializeJSON());
          EW.lock($("#user-form"), "Saving...");
          $.post('<?php echo EW_ROOT_URL; ?>~admin/api/users-management/update-user', formParams, function (data) {
            if (data.status === "success")
            {
              $(document).trigger("users-list.refresh");
              $("body").EW().notify(data).show();
            } else
            {
              $("body").EW().notify(data).show();
            }
            EW.unlock($("#user-form"));
          }, "json");
        }
      };
      return new UserForm();
    })();

  <?php
  if ($user_id) {
    $row = EWCore::call_api("admin/api/users-management/get-user-by-id", ["userId" => $user_id]);
    if (isset($row["data"])) {
      ?>
        var formData = <?= json_encode($row["data"]); ?>;
        $("#form-title").html("<span>tr{User Info}</span>" + formData["first_name"]);
        EW.setFormData("#user-form", formData);
        UserForm.bSave.comeIn(300);
      <?php
    }
  }
  else {
    ?>
      $("#user-form #password").val("<?php echo admin\UsersManagement::random_password() ?>").change();
      UserForm.bAdd.comeIn(300);
    <?php
  }
  ?>

  </script>
  <?php
  return ob_get_clean();
}

EWCore::register_form("ew-user-form-default", "ew-user-form", [
    "title"   => "User Info",
    "content" => get_ew_user_form($_REQUEST['userId'])
]);

$tabsDefault = EWCore::read_registry("ew-user-form-default");
$tabs = EWCore::read_registry("ew-user-form");
?>
<form id="user-form"  action="#" method="POST" onsubmit="return false;">
  <div class="block-row header-pane tabs-bar">
    <h1 id='form-title' class="col-xs-12">
      tr{New User}
    </h1>

    <ul class="nav nav-pills">
      <?php
      foreach ($tabsDefault as $id => $tab) {
        if ($id == "ew-user-form")
          echo "<li class='active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
        else
          echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
      }
      foreach ($tabs as $id => $tab) {
        echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
      }
      ?>
    </ul>
  </div>
  <div class="block-row form-content  tabs-bar">
    <div class="tab-content col-xs-12">
      <?php
      foreach ($tabsDefault as $id => $tab) {
        if ($id == "ew-user-form")
          echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
        else
          echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
      }
      foreach ($tabs as $id => $tab) {
        echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
      }
      ?>
    </div>
  </div>
  <div class="block-row footer-pane actions-bar action-bar-items">
  </div>
</form>
