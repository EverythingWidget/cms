<script >

  function UsersStateHandler(state) {
    var component = this;
    this.state = state;
    this.handlers = {};

    this.state.onInit = function () {
      component.init();
    };

    this.state.onStart = function () {
      component.start();
    };
  }

  UsersStateHandler.prototype.defineStates = function (handlers) {
    var component = this;

    handlers.app = function (full, value) {
      if (parseInt(value)) {
        component.editActivity(false, {id: value});
      }
    };
  };

  UsersStateHandler.prototype.init = function () {
    var component = this;
    this.editActivity = EW.getActivity({
      activity: "admin/html/users-management/users/user-form/component.php_edit",
      modal: {
        class: "center"
      },
      onDone: function () {
        component.state.setNav(null);
      }
    });

    this.defineStates(this.handlers);
    System.utility.installModuleStateHandlers(this.state, this.handlers);
  };

  UsersStateHandler.prototype.start = function () {
    this.table = null;
    //this.bAddUser = EW.addActivity().hide().comeIn(300);

    System.entity('ui/primary-menu').actions = [
      {
        title: "tr{New User}",
        parent: System.UI.components.mainFloatMenu,
        modal: {
          class: "center"
        },
        activity: "admin/html/users-management/users/user-form/component.php",
        paramters: {
          userId: null
        }
      }
    ];

    this.usersList();
  };

  UsersStateHandler.prototype.usersList = function () {
    var component = this,
            editActivity;

    if (this.table) {
      this.table.refresh();
      return;
    }

    this.table = EW.createTable({
      name: "users-list",
      rowLabel: "{first_name} {last_name}",
      columns: ["email", "first_name", "last_name", "group.title"],
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
      url: "api/admin/users-management/users/",
      pageSize: 30,
      onDelete: function (id) {
        this.confirm("tr{Are you sure of deleting of this user?}", function () {
          $.ajax({
            type: 'DELETE',
            url: 'api/admin/users-management/users/',
            data: {id: id},
            success: function (response) {
              System.UI.components.document.trigger("users-list.refresh");
              System.UI.components.body.EW().notify(response).show();
            }
          });

          return true;
        });
      },
      onEdit: function (id) {
        component.state.setNav(id);
      }
    });

    System.UI.components.mainContent.html(this.table.container);
    this.table.read();

    System.UI.components.document.off("users-list.refresh");
    System.UI.components.document.on("users-list.refresh", function () {
      component.table.refresh();
    });
  };

  System.state('users-management/users', function (state) {
    new UsersStateHandler(state);
  });

</script>
