/* global Scope, System, $php */

System.newStateHandler(Scope, Handler);

function Handler(state, scope) {
  scope = scope || Scope;
  console.log($php.url_layouts, $php.page_feeders);
  var vue = new Vue({
    el: scope.views.main,
    data: {
      pageFeeders: $php.page_feeders['data'] || [],
      pathLayouts: $php.url_layouts || []
    },
    methods: {
      getFeederLayout: function (feederURL) {
        return this.pathLayouts.filter(function (item) {
          return item.path === '/' + feederURL;
        })[0] || {};
      }
    }
  });


  state.onInit = function () {
    console.log('init');
  };

  state.onStart = function () {
    console.log('start');
  };

  state.onStop = function () {
    console.log('stop');
  };

}