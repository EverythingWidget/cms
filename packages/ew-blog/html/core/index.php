<script>

  function EWBlog(state) {
    var component = this;
    this.state = state;
    this.state.type = "app";
    this.data = {};

    this.state.onInit = component.init.bind(component);

    this.state.onStart = component.start.bind(component);
  }

  EWBlog.prototype.init = function () {
    var component = this;
    this.state.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/ew-blog/navs') ?>;

    this.state.on('app', System.ui.behave(System.services.app_service.select_app_section, component));
  };

  EWBlog.prototype.start = function () {
    this.data.tab = null;
  };

  System.state('ew-blog', function (state) {
    new EWBlog(state);
  });

</script>