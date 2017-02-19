/* global Scope, System, $php */

System.newStateHandler(Scope, Handler);

function Handler(state, scope) {
  scope = scope || Scope;

  var vue = new Vue({
    el: scope.views.main,
    data: {
      pageFeeders: $php.page_feeders['data'] || [],
      pathLayouts: $php.url_layouts || [],
      custom: {
        path: ''
      }
    },
    methods: {
      getFeederLayout: function (feederURL) {
        return this.pathLayouts.filter(function (item) {
              return item.path === feederURL;
            })[0] || {};
      },
      selectLayout: function (url) {
        uisListDialog(url);
      }
    }
  });


  state.onInit = function () {

  };

  state.onStart = function () {

  };

  state.onStop = function () {

  };

  function setLayout(pageLayout) {
    $.post('api/webroot/widgets-management/set-uis', {
      path: pageLayout.url,
      uis_id: pageLayout.layoutId
    }, function (response) {
      var exist = vue.pathLayouts.filter(function (item) {
        return item.path === pageLayout.url;
      })[0];

      if (pageLayout.layoutId === null) {
        vue.pathLayouts.splice(vue.pathLayouts.indexOf(exist), 1);
        return;
      }

      if (exist) {
        exist.name = pageLayout.layoutName;
      } else {
        vue.pathLayouts.push({
          name: pageLayout.layoutName,
          path: pageLayout.url
        });
      }

      vue.custom = {};

      $('body').EW().notify(response).show();
    });
  }

  function uisListDialog(url) {
    var dialog = EW.createModal({
      class: 'center slim'
    });

    this.table = EW.createTable({
      name: 'uis-list',
      headers: {
        Name: {},
        Template: {}
      },
      rowCount: true,
      url: 'api/webroot/widgets-management/get-uis-list/',
      pageSize: 30,
      columns: [
        'name',
        'template'
      ],
      buttons: {
        'Select': function (row) {
          setLayout({
            url: url,
            layoutName: row[0].rowData.name,
            layoutId: row[0].rowData.id
          });

          dialog.dispose();
        }
      }
    });

    var clearLayout = $("<button class='btn btn-danger' type='button'>Clear Layout</button>");
    clearLayout.on('click', function () {
      setLayout({
        url: url,
        layoutId: null
      });

      dialog.dispose();
    });

    dialog.append("<div class='header-pane thin'><h1 id='' class='col-xs-12'><span>Layouts</span>Select a layout</h1></div>");
    var d = $("<div id='' class='form-content'></div>");
    this.table.container.addClass("mt");
    d.append(this.table.container);
    dialog.append(d);
    dialog.append($("<div class='footer-pane actions-bar action-bar-items'></div>").append(clearLayout));
    this.table.read();
  }
}