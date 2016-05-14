(function () {
  window.addEventListener('load', onLoad);

  function onLoad() {

    var apps = new Vue({
      el: '#apps',
      data: {
        apps: [{
            title: 'Contents',
            path: 'contents'
          }, {
            title: 'Settings',
            path: 'settings'
          }, {
            title: 'Users',
            path: 'users'
          }, {
            title: 'Design',
            path: 'design'
          }]
      }
    });
  }
})();