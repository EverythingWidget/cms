<?php ?>
<script >
  (function () {

    function UsersGroups() {
      var _this = this;
      this.bNewGroup = EW.addActivity({
        title: "tr{New Group}",
        parent: System.UI.components.mainFloatMenu,
        parameters: function () {
          return {
            groupId: null
          };
        },
        activity: 'admin/html/users-management/users-group-form.php'
      });
      $(document).off("users-groups-list.refresh");
      $(document).on("users-groups-list.refresh", function () {
        _this.usersGroupsList();
      });

      this.editGroupActivity = EW.getActivity({
        activity: 'admin/html/users-management/users-group-form.php_edit',
        onDone: function () {
          System.setHashParameters({
            groupId: null
          });
        }
      });

      this.usersGroupsList();
    }

    UsersGroups.prototype.usersGroupsList = function () {
      var self = this;
      if (this.table)
      {
        this.table.refresh();
        return;
      }
      this.table = EW.createTable({
        name: "users-groups-list",
        rowLabel: "{title}",
        columns: [
          'title',
          'description',
          'date_created'
        ],
        headers: {
          "tr{Title}": {
          },
          "tr{Description}": {
          },
          "tr{Date Created}": {
          }
        },
        rowCount: true,
        url: "<?php echo EW_ROOT_URL; ?>~admin/api/users-management/groups/",
        pageSize: 30,
        onDelete: function (id) {
          this.confirm("tr{Are you sure of deleting of this group?}", function () {
            $.ajax({
              type: 'DELETE',
              url: 'api/admin/users-management/groups/',
              data: {
                id: id
              },
              success: function (data) {
                self.usersGroupsList();
                $("body").EW().notify(data).show();
                //EW.unlock($("#main-content"));
              }
            });

            return true;
          });
        },
        onEdit: function (id) {

          if (self.editGroupActivity) {
            self.editGroupActivity({
              groupId: id
            });
          }
        }
      });
      $("#main-content").html(this.table.container);
      this.table.read();
    };


    System.state("users-management/users-groups", function (state) {
      state.onStart = function () {
        new UsersGroups();
      };
    });
  })();

</script>
