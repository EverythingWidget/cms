<div  id="content-editor" style="" data-name="main-content"></div>

<script>
   var contentEditor;
   $(document).ready(function () {

      /*var ewEditor = new EWEditor({
       id: '#content-editor',
       bootstrap: './core/css/bootstrap.min.css',
       ew_media_url: "<?php echo EW_ROOT_URL; ?>admin/ContentManagement/link-chooser-media.php",
       });
       contentEditor = ewEditor;*/
      $("#<?= $form_id ?>").on("refresh", function (e, formData)
      {
         $("#content-editor .ct-content-container").html(formData["content"]);
         contentEditor.start();
      });
      
      $("#<?= $form_id ?>").on("error", function (e, formData)
      {
         contentEditor.destroy();
      });

      contentEditor = ContentTools.EditorApp.get();
      contentEditor.init('#content-editor');
      //ewEditor.start();
      EW.getParentDialog($("#<?php echo $form_id ?>")).on("beforeClose", function ()
      {
         contentEditor.destroy();
         contentEditor = null;
      });
      //var edi = new ContentEdit.Region(document.getElementById("content-editor"));
   });

   /*tinymce.EditorManager.execCommand('mceRemoveEditor', false, "content");
    setTimeout(function () {
    tinymce.EditorManager.init({
    //forced_root_block: false,
    mode: "exact",
    elements: 'content',
    relative_urls: false,
    remove_script_host: false,
    schema: "html5",
    theme: "modern",
    apply_source_formatting: true,
    height: 340,
    ew_media_url: "<?php echo EW_ROOT_URL; ?>admin-api/ContentManagement/Media.php",
    visualblocks_default_state: true,
    image_class_list: [
    {title: 'None', value: ''},
    {title: 'Image', value: 'image'},
    {title: 'Cover', value: 'cover'}
    ],
    menubar: "file edit view format",
    //content_css: "admin/styles/template.css",
    plugins: [
    "advlist autolink lists link image ewimage charmap print preview anchor textcolor",
    "searchreplace code fullscreen layer",
    "insertdatetime table contextmenu paste"
    ],
    toolbar: "undo redo | styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table charmap insertdatetime | ewimage | code | layer"
    // Example content CSS (should be your site CSS)
       
    });
       
       
    $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
    {
    $(tinymce.get('content').getBody()).html(formData["content"]);
    });
    }, 300);*/

</script>