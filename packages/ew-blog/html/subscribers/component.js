/* global System, Scope */

System.newStateHandler(Scope, function (state) {
  var vue;
  
  state.onInit = function () {
    vue = new Vue({
      el: Scope.views.subscribers_card,
      data: {
        card_title: 'Subscribers',
        subscribers: {
          url: 'api/ew-blog/subscribers/',
          page_size: 15
        }
      },
      methods: {
        reload: function () {
          vue.$broadcast('refresh');
        }
      }
    });
  };

  state.onStart = function () {

  };
});