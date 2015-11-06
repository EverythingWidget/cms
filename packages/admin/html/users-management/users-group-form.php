<?php
session_start();

function get_ew_user_form()
{
   ob_start();
   ?>
   <input type="hidden" id="id" name="id" value="">
   <input type="hidden" id="group_id" name="group_id" value="">
   <div class="row mar-bot">
      <div class="col-xs-12 col-md-12 col-lg-12 mar-top">
         <input class="text-field" data-label="tr{Title}" value="" id="title" name="title"/>
      </div>    
      <div class="col-xs-12 col-md-12 col-lg-12 mar-top">
         <textarea class="text-field" id="description" data-label="tr{Description}" name="description"  /></textarea>
      </div>
   </div>  
   <?php
   return ob_get_clean();
}

function get_ew_users_permissions_form()
{
   ob_start();
   ?>
   <div class="row">
      <input type="hidden" id="permission" name="permission">
      <div class="col-xs-12">
         <h2>All Permissions</h2>
      </div>
      <div class="col-xs-12 content" id="all-permissions"  >
         <ul class="list permissions tree" data-toggle="buttons">
            <?php
            $permissions_titles = \EWCore::read_permissions_titles();
            if (isset($permissions_titles))
            {
               foreach ($permissions_titles as $app_name => $sections)
               {
                  ?>
                  <li><label data-value="<?php echo $app_name ?>"><i class="icon  pull-left"></i><h3 class="icon-header"><?php echo $sections["appTitle"]; ?></h3></label>
                     <ul class="row">
                        <?php
                        foreach ($sections["section"] as $section_name => $sections_permissions)
                        {
                           ?>
                           <li class="col-xs-6"><label data-value="<?php echo "$app_name.$section_name" ?>"><i class="icon  pull-left"></i><h3 class="icon-header"><?php echo $sections_permissions["sectionTitle"]; ?></h3></label>
                              <ul>
                                 <?php
                                 foreach ($sections_permissions["permission"] as $permission_name => $permission_info)
                                 {
                                    ?>
                                    <li class="permission-item">
                                       <label  data-value="<?php echo "$app_name.$section_name.$permission_name" ?>">
                                          <i class="icon  pull-left"></i>
                                          <h3 class="icon-header"><?php echo $permission_info["title"] ?></h3>
                                          <?php echo $permission_info["description"] ?>
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
   </div>

   <script  type="text/javascript">
      var UsersGroupsForm = (function () {
         function UsersGroupsForm()
         {
            var permissions = [];
            this.name = "ha";
            this.bSave = EW.addAction("tr{Save Changes}", $.proxy(this.updateGroup, this)).addClass("btn-success").hide();
            this.bAdd = EW.addAction("tr{Save}", $.proxy(this.addGroup, this)).hide();
            this.bDelete = EW.addAction("tr{Delete}", $.proxy(this.deleteGroup, this)).addClass("btn-danger").hide();

            this.readClasses();
         }
         UsersGroupsForm.prototype.readClasses = function ()
         {
            var $self = this;
            var givenPermissions = $("#permission").val().split(",");
            //alert($("#permission").text());
            $("#permission").empty();
            $.each($("#all-permissions").find("label"), function (k, v)
            {
               var a = $(v).find("input");
               if (!$(v).hasClass("btn"))
               {
                  a = $("<input type='checkbox'>");
                  a.val($(v).attr("data-value"));
                  a.hide();
                  //$(v).text($(v).text());
                  a.change(function (event)
                  {
                     var label = $(v);
                     if ($(this).is(":checked"))
                     {
                        label.find("i").addClass("correct");
                        label.parent().find("input:not(:checked)").prop('checked', true).parent().addClass("active").find("i").addClass("correct");
                     } else
                     {
                        label.find("i").removeClass("correct");
                        label.parent().find("input:checked").prop('checked', false).parent().removeClass("active").find("i").removeClass("correct");
                     }
                     //alert("call");
                     // Check parent items
                     $.each($("#all-permissions li:not(.permission-item)"), function (i, e) {
                        e = $(e);

                        if (e.find("li.permission-item input:checked").length == 0)
                        {
                           //alert("zero: "+e.children("label").html());
                           e.children("label").children("input:checked").prop('checked', false).parent().removeClass("active").find("i").removeClass("correct");
                        } else if (e.find("li.permission-item input:checked").length > 0)
                        {
                           //alert("length "+e.find("li.permission-item input:checked").length+", "+e.children("label").html());
                           e.children("label").children("input:not(:checked)").prop('checked', true).parent().addClass("active").find("i").addClass("correct");
                        }
                        //alert($self.readPermission());
                     });
                  });

                  $(v).prepend(a);
                  $(v).addClass("btn btn-white");
               }
               //
               $.each(givenPermissions, function (i, c)
               {
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

         UsersGroupsForm.prototype.readPermission = function ()
         {
            var permissions = [];
            //var $self = this;
            /*$("#used-classes").text("");
             $.each($("#panel-classes").find("input"), function(k, v) {
             $("#used-classes").append($(v).val() + " ");
             });
             $("#used-classes").append($("#style_class").val());*/
            $.each($("#users-group-form").find("li.permission-item input:checkbox:checked"), function (k, v) {
               if (!$(v).is(":disabled"))
                  permissions.push($(v).val());
            });
            return permissions;

            //alert($self.permissions.toString());
            //$("#used-classes").append($("#style_class").val());
         };



         UsersGroupsForm.prototype.updateGroup = function ()
         {
            var $self = this;
            if ($("#title").val())
            {
               //alert(media.itemId);
               var formParams = $.parseJSON($("#users-group-form").serializeJSON());
               formParams["permission"] = $self.readPermission().toString();
               EW.lock($("#users-group-form"), "Saving...");
               $.post('<?php echo EW_ROOT_URL; ?>~admin-api/UsersManagement/update_group', formParams, function (data) {
                  UsersGroups.usersGroupsList();
                  $("body").EW().notify(data).show();

                  EW.unlock($("#users-group-form"));
               }, "json");
            }
         };
         UsersGroupsForm.prototype.addGroup = function ()
         {
            var $self = this;
            if ($("#title").val())
            {
               //alert(media.itemId);
               var formParams = $.parseJSON($("#users-group-form").serializeJSON());
               formParams["permission"] = $self.readPermission().toString();
               EW.lock($("#users-group-form"), "Saving...");
               $.post('<?php echo EW_ROOT_URL; ?>~admin-api/UsersManagement/add_group', formParams, function (data) {
                  $.EW("getParentDialog", $("#users-group-form")).trigger("close");
                  UsersGroups.usersGroupsList();
                  $("body").EW().notify(data).show();

                  EW.unlock($("#users-group-form"));
               }, "json");
            }
         };

         UsersGroupsForm.prototype.deleteGroup = function ()
         {
            var $self = this;
            if (confirm("Are you sure of deleting of this group?"))
            {
               var formParams = $.parseJSON($("#users-group-form").serializeJSON());
               EW.lock($("#users-group-form"));
               $.post('<?php echo EW_ROOT_URL; ?>~admin-api/UsersManagement/delete_group', formParams, function (data) {
                  $.EW("getParentDialog", $("#users-group-form")).trigger("close");
                  UsersGroups.usersGroupsList();
                  $("body").EW().notify(data).show();

                  EW.unlock($("#users-group-form"));
               }, "json");
            }
         };
         return new UsersGroupsForm();
      })();
   <?php
   $row = EWCore::process_request_command("admin/api", "users-management", "get_user_group_by_id", [$_REQUEST["groupId"]]);
   ?>
      var formData = <?= isset($row) ? $row : 'null' ?>;
      EW.setFormData("#users-group-form", formData);
      if (formData)
      {
         $("#form-title").html("<span>tr{Group Info}</span>" + formData["title"]);
         UsersGroupsForm.readClasses();
         UsersGroupsForm.bSave.comeIn(300);
         UsersGroupsForm.bDelete.comeIn(300);
      } else {

         UsersGroupsForm.bAdd.comeIn(300);
      }
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-user-form-default", "ew-user-form", ["title" => "Group Info",
    "content" => get_ew_user_form()]);
EWCore::register_form("ew-user-form-default", "ew-user-permissions", ["title" => "Permissions",
    "content" => get_ew_users_permissions_form()]);
$tabsDefault = EWCore::read_registry("ew-user-form-default");
$tabs = EWCore::read_registry("ew-user-form");
?>
<form id="users-group-form"  action="#" method="POST" onsubmit="return false;">
   <div class="header-pane  tabs-bar row">
      <h1 id='form-title' class="col-xs-12">
         tr{New Group}
      </h1>
      <ul class="nav nav-pills">
         <?php
         foreach ($tabsDefault as $id => $tab)
         {
            if ($id == "ew-user-form")
               echo "<li class='active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
            else
               echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
         }
         if (isset($tabs))
         {
            foreach ($tabs as $id => $tab)
            {
               echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
            }
         }
         ?>
      </ul>
   </div>
   <div class="form-content  tabs-bar row">

      <div class="tab-content col-xs-12" style="height:100%;position:absolute;">
         <?php
         foreach ($tabsDefault as $id => $tab)
         {
            if ($id == "ew-user-form")
               echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
            else
               echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
         }
         if (isset($tabs))
         {
            foreach ($tabs as $id => $tab)
            {
               echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
            }
         }
         ?>
      </div>

   </div>
   <div class="footer-pane row actions-bar action-bar-items">
   </div>
</form>

