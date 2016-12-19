<div  id="content-editor" style="" data-name="main-content"></div>
<script>
  var contentEditor;
  contentEditor = ContentTools.EditorApp.get();

  (function () {
    var formId = '#$php.form_id';

    contentEditor.init('#content-editor');

    $(document).ready(function () {
      $(formId).on("refresh", function (e, content) {
        if (content) {
          $("#content-editor .ct-content-container").html(content.content);
        }
        contentEditor.start();
      });

      $(formId).on("error", function (e, formData) {
        contentEditor.destroy();
      });

      EW.getParentDialog($(formId)).off('beforeClose.editor');
      EW.getParentDialog($(formId)).on('beforeClose.editor', function (e) {
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
  })();

</script>