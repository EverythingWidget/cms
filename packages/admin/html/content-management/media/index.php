

<script>
  var MediaStateHandler = Scope.import('html/admin/content-management/media/media.component.php');

  System.state('content-management/media', function (state) {
    System.entity('objects/media-state-handler', new MediaStateHandler(Scope, state));
  });
</script>
