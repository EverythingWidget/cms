<div  id="content-editor" style="" data-name="main-content"></div>
<script>
  var contentEditor;
  contentEditor = ContentTools.EditorApp.get();
  contentEditor.init('#content-editor');

  $(document).ready(function () {
    $("#<?= $form_id ?>").on("refresh", function (e, content) {
      if (content) {
        $("#content-editor .ct-content-container").html(content.content);
      }
      contentEditor.start();

      var firstRegion = contentEditor.orderedRegions()[0];
      if (firstRegion.children.length > 0) {
        var firstElement = firstRegion.children[0];
        firstElement.focus();
        contentEditor._rootLastModified = ContentEdit.Root.get().lastModified();
      }
    });

    $("#<?= $form_id ?>").on("error", function (e, formData) {
      contentEditor.destroy();
    });

    EW.getParentDialog($("#<?= $form_id ?>")).off("beforeClose.editor");
    EW.getParentDialog($("#<?= $form_id ?>")).on("beforeClose.editor", function (e) {
      if (ContentEdit.Root.get().lastModified() !== contentEditor._rootLastModified)
      {
        var confirmMessage = ContentEdit._('Your changes have not been saved, do you really want to lose them?');
        if (window.confirm(confirmMessage)) {
          contentEditor.destroy();
          contentEditor = null;
          return true;
        }
      } else {
        contentEditor.destroy();
        contentEditor = null;
        return true;
      }

      return false;
    });
  });
</script>