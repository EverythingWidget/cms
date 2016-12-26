<div class="form-block">
  <input type="hidden" id="id" name="id" value="">
  <system-field class="field">
    <label>tr{Username}</label>
    <input class="text-field" value="" id="email" name="email" data-validate="r" required/>  
  </system-field>


  <system-field class="field">
    <label>tr{First Name}</label>
    <input class="text-field" value="" id="first_name" name="first_name" />  
  </system-field>

  <system-field class="field">
    <label>tr{Last Name}</label>
    <input class="text-field" value="" id="last_name" name="last_name" />  
  </system-field>   

  <?php if (!isset($view_data['user_id'])) { ?>
    <system-field class="field ">
      <label>tr{Password}</label>
      <input class="text-field" value="" id="password" name="password"/>
    </system-field>   
  <?php } ?>
  <system-field class="field">
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

          loader = System.ui.lock({
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

          loader = System.ui.lock({
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

  var formData = <?= json_encode($view_data['data']); ?>;
  EW.setFormData("#user-form", formData);

</script>