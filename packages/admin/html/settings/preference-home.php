<system-spirit animations="verticalShift" vertical-shift="card">
  <form id="preference-cards">

  </form>
</system-spirit>

<script type="text/javascript">
  function Preference(state) {
    var _this = this;

    state.onStart = function () {
      this.refresh = EW.addActionButton({
        text: '<i class="icon-cw-1"></i>',
        handler: function () {
          _this.load(<?= json_encode(EWCore::read_registry('ew/ui/settings/preference')) ?>);
        },
        class: 'btn-float priority-1 btn-primary',
        parent: System.ui.components.appMainActions
      });
    }
  }

  Preference.prototype.load = function () {

  };

  System.newStateHandler(Scope, Preference);
</script>
