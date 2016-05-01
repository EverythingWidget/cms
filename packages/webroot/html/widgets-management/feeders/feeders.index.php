<system-ui-view module="content-management/media" name="feeders-list"></system-ui-view>

<script>
  (function () {    
    function FeedersComponent(module) {
      var component = this;
      component.states = {};
      component.module = module;

      component.module.stage('init', this.init);
      component.module.stage('start', this.start);

      component.defineStateHanlers(component.states);
      System.utility.installModuleStateHandlers(component.module, component.states);
    }

    FeedersComponent.prototype.init = function () {

    };

    FeedersComponent.prototype.start = function () {

    };

    FeedersComponent.prototype.defineStateHanlers = function (states) {
      states.alert = function (full) {
        if (full === null) {
          return;
        }

      };
    };

    // ------ //

    System.entity('components/widgets-management/feeders', {
      $service: null,
      create: function (module) {
        return new FeedersComponent(module);
      },
      service: function (module) {
        return this.$service !== null ? this.$service : this.$service = this.create(module);
      }
    });

    // ------- //

    System.state("widgets-management/feeders", function () {
      System.entity('components/widgets-management/feeders').create(this);
    });
  })();
</script>