<script >
   var Users = (function () {
      function Users()
      {
         this.table = null;
         this.bAddUser = EW.addActivity({title: "tr{New User}", activity: "admin-html/users-management/user-form.php", hash: {userId: null}}).hide().comeIn(300);


         this.usersList();
      }
      Users.prototype.usersList = function ()
      {
         var self = this;
         if (this.table)
         {
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
            url: "<?php echo EW_ROOT_URL; ?>admin-api/UsersManagement/",
            pageSize: 30,
            onDelete: function (id)
            {
               this.confirm("tr{Are you sure of deleting of this user?}", function ()
               {
                  //EW.lock($("#main-content"));
                  $.post('<?php echo EW_ROOT_URL; ?>admin-api/users-management/delete-user', {id: id}, function (data)
                  {
                     $(document).trigger("users-list.refresh");
                     $("body").EW().notify(data).show();
                     //EW.unlock($("#main-content"));
                  }, "json");
               });
            },
            onEdit: ((editActivity = EW.getActivity({activity: "admin-html/users-management/user-form.php-see", onDone: function (hash) {
                  hash["userId"] = null;
               }})) ? function (id) {
               editActivity({userId: id});
            } : null)
         });
         $("#main-content").html(this.table.container);
         $(document).off("users-list.refresh");
         $(document).on("users-list.refresh", function () {
            self.table.refresh();
         });
      };
      return new Users();
   })();



</script>
