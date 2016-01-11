<div  id="content-editor" style="" data-name="main-content"></div>

<script>
   var contentEditor;
   $(document).ready(function () {
      $("#<?= $form_id ?>").on("refresh", function (e, content) {
         if (content) {
            $("#content-editor .ct-content-container").html(content.content);
         }
         contentEditor.start();
      });

      $("#<?= $form_id ?>").on("error", function (e, formData) {
         contentEditor.destroy();
      });

      contentEditor = ContentTools.EditorApp.get();
      contentEditor.init('#content-editor');

      //console.log($("#<?= $form_id ?>"));   
      EW.getParentDialog($("#<?= $form_id ?>")).one("beforeClose", function (e) {
         if (contentEditor.revert())
         {
            contentEditor.destroy();
            contentEditor = null;
            return true;
         }
         return false;
      });
   });
</script>