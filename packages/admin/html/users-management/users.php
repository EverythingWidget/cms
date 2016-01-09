<script >
   (function (System) {

      function UsersComponent(module) {
         var component = this;
         this.module = module;

         this.module.onInit = function () {
            component.init();
         };

         this.module.onStart = function () {
            component.start();
         };


      }

      UsersComponent.prototype.init = function () {

      };

      UsersComponent.prototype.start = function () {
         this.table = null;
         this.bAddUser = EW.addActivity({
            title: "tr{New User}",
            parent: "action-bar-items",
            modal: {
               class: "center"
            },
            activity: "admin/html/users-management/user-form.php",
            hash: {userId: null}
         }).hide().comeIn(300);

         this.usersList();
      };

      UsersComponent.prototype.usersList = function () {
         var component = this,
                 editActivity;

         if (this.table) {
            this.table.refresh();
            return;
         }

         this.table = EW.createTable({
            name: "users-list",
            rowLabel: "{first_name} {last_name}",
            columns: ["email", "first_name", "last_name", "title"],
            headers: {
               "tr{Username}": {
               },
               "tr{First Name}": {
               },
               "tr{Last Name}": {
               },
               "tr{Group}": {
               }
            },
            rowCount: true,
            url: "<?php echo EW_ROOT_URL; ?>~admin/api/users-management/users",
            pageSize: 30,
            onDelete: function (id) {
               this.confirm("tr{Are you sure of deleting of this user?}", function () {
                  $.post('<?php echo EW_ROOT_URL; ?>~admin/api/users-management/delete-user', {id: id}, function (data) {
                     System.UI.components.document.trigger("users-list.refresh");
                     System.UI.components.body.EW().notify(data).show();
                  }, "json");
               });
            },
            onEdit: ((editActivity = EW.getActivity({
               verb: "get",
               activity: "admin/html/users-management/user-form.php-see",
               modal: {
                  class: "center"
               },
               onDone: function (hash) {
                  hash["userId"] = null;
               }
            })) ? function (id) {
               editActivity({
                  userId: id
               });
            } : null)
         });

         System.UI.components.mainContent.html(this.table.container);
         this.table.read();

         System.UI.components.document.off("users-list.refresh");
         System.UI.components.document.on("users-list.refresh", function () {
            component.table.refresh();
         });
      };

      System.module("users-management").module("users", function () {
         new UsersComponent(this);
      });

   })(System);
</script>
