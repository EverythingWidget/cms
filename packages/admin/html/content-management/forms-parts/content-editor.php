<div  id="content-editor" style="" data-name="main-content"></div>

<script>
   var contentEditor;
   $(document).ready(function () {
      $("#<?= $form_id ?>").on("refresh", function (e, formData) {
         $("#content-editor .ct-content-container").html(formData["content"]);
         contentEditor.start();
      });

      $("#<?= $form_id ?>").on("error", function (e, formData) {
         contentEditor.destroy();
      });

      contentEditor = ContentTools.EditorApp.get();
      contentEditor.init('#content-editor');

      //console.log($("#<?= $form_id ?>"));
      EW.getParentDialog($("#<?= $form_id ?>")).on("beforeClose", function () {
         //console.log(this);
         if(contentEditor.revert())
         {
            contentEditor.destroy();
            contentEditor = null;
            return true;
         }
         return false;
      });
   });
</script>