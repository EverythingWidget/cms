
/* global System, EW */

(function () {
  System.entity('stage/init-ui-components', init);

  function init() {
    var appsVue = new Vue({
      el: '#apps-menu',
      data: {
        apps: [],
        currentState: null,
        currentApp: null,
        currentSection: null,
        currentSubSection: null
      }
    });

    System.entity('ui/apps', appsVue);

    // ------ //

    var primaryActionsVue = new Vue({
      el: '#main-float-menu',
      data: {
        actions: []
      },
      methods: {
        callActivity: function (action) {
          var activityCaller = EW.getActivity(action);
          activityCaller(action.hash);
        }
      }
    });

    System.entity('ui/primary-actions', primaryActionsVue);

    // ------ //

    var appBarVue = new Vue({
      el: '#app-bar',
      data: {
        sectionsMenuTitle: '',
        isLoading: false,
        subSections: null,
        currentState: null,
        currentSubSection: appsVue.currentSubSection
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (this.subSections && this.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        }
      },
      methods: {
        goTo: function (tab, $event) {
          $event.preventDefault();

          System.app.setNav(appsVue.currentApp + '/' + appsVue.currentSection + '/' + tab.state);
        },
        goToState: function (state) {
          System.app.setNav(state);
        }
      }
    });

    System.entity('ui/app-bar', appBarVue);

    // ------ //

    var mainContentVue = new Vue({
      el: '#main-content',
      data: {
        show: false
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (appBarVue.subSections && appBarVue.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        }
      }
    });

    System.entity('ui/main-content', mainContentVue);
  }

})(System, EW);
