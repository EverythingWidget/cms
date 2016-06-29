<?php
session_start();
$user_id = $_REQUEST['id'];

$data = $user_id ? EWCore::call_api("admin/api/users-management/users", ['id' => $user_id]) : [];

function get_ew_user_form($user_id, $data) {
  ob_start();
  ?>
  <input type="hidden" id="id" name="id" value="">
  <div class="row mt">
    <system-field class="field col-xs-12">
      <label>tr{Username}</label>
      <input class="text-field" value="" id="email" name="email" data-validate="r" required/>  
    </system-field>
  </div>
  <div class="row">
    <system-field class="field col-xs-12 col-md-6 col-lg-6">
      <label>tr{First Name}</label>
      <input class="text-field" value="" id="first_name" name="first_name" />  
    </system-field>

    <system-field class="field col-xs-12 col-md-6 col-lg-6">
      <label>tr{Last Name}</label>
      <input class="text-field" value="" id="last_name" name="last_name" />  
    </system-field>   
  </div>
  <?php if (!isset($user_id)) { ?>
    <div class="row">
      <system-field class="field col-xs-12">
        <label>tr{Password}</label>
        <input class="text-field" value="" id="password" name="password"/>
      </system-field>   

    </div>
  <?php } ?>
  <div class="row">    

    <system-field class="field col-xs-12">
      <label>tr{Group}</label>
      <select id="group_id" name="group_id" data-width="100%">
        <?php
        $users_groups = EWCore::call_api('admin/api/users-management/groups');
        foreach ($users_groups['data'] as $group) {
          echo "<option value='{$group['id']}'>{$group['title']}</option>";
        }
        ?>
      </select>
    </system-field>   
  </div>

  <script type="text/javascript">
    (function () {
      var dialog = $.EW("getParentDialog", $("#user-form"));
      var loader;

      function UserForm() {
        var _this = this;
        this.bAdd = EW.addActivity({
          title: "tr{Add}",
          modal: {
            class: "center"
          },
          verb: 'POST',
          activity: 'admin/api/users-management/users',
          parameters: function () {
            if (!$("#email").val()) {
              return false;
            }

            loader = System.UI.lock({
              element: dialog[0],
              akcent: 'loader center'
            });

            return $.parseJSON($("#user-form").serializeJSON());
          },
          onDone: function (response) {
            loader.dispose();
            $(document).trigger("users-list.refresh");
            $("body").EW().notify(response).show();
          }
        });

        this.bSave = EW.addActivity({
          title: "tr{Save}",
          modal: {
            class: "center"
          },
          defaultClass: 'btn-success',
          verb: 'PUT',
          activity: 'admin/api/users-management/users',
          parameters: function () {
            if (!$("#email").val()) {
              return false;
            }

            loader = System.UI.lock({
              element: dialog[0],
              akcent: 'loader center'
            });

            return $.parseJSON($("#user-form").serializeJSON());
          },
          onDone: function (response) {
            loader.dispose();
            $(document).trigger("users-list.refresh");
            $("body").EW().notify(response).show();
          }
        });

        $('#user-form').on('refresh', function (event, data) {
          if (data['id']) {
            $("#form-title").html("<span>tr{User Info}</span>" + data["first_name"]);
            _this.bAdd.comeOut();
            _this.bSave.comeIn();
          } else {
            $("#user-form #password").val("<?= admin\UsersManagement::random_password() ?>").change();
            _this.bAdd.comeIn();
            _this.bSave.comeOut();
          }
        });
      }

      new UserForm();
    })();

    var formData = <?= json_encode($data['data']); ?>;
    EW.setFormData("#user-form", formData);

  </script>
  <?php
  return ob_get_clean();
}

EWCore::register_form("ew-user-form-default", "ew-user-form", [
    "title"   => "User Info",
    "content" => get_ew_user_form($user_id, $data)
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
