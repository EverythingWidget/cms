<?php
session_start();
$form_data = EWCore::process_command("admin", "ContentManagement", "get_album", array("albumId" => $_REQUEST["albumId"]));

function get_ew_album_properties_form()
{
   ob_start();
   ?>
   <input type="hidden" id="id" name="id" value="">
   <div class="row margin-bottom">
      <div class="col-xs-12 col-md-12 col-lg-12 mar-top">
         <input class="text-field" data-label="tr{Title}" value="" id="title" name="title">
      </div>    
      <div class="col-xs-12 col-md-6 col-lg-6 mar-top">
         <textarea class="text-field" id="keywords" data-label="tr{Keywords}" name="keywords"  ></textarea>
      </div>
      <div class="col-xs-12 col-md-6 col-lg-6 mar-top">
         <textarea class="text-field" id="description" data-label="tr{Description}" name="description"  ></textarea>
      </div>
   </div>
   <script type="text/javascript" src="<?php echo EW_ROOT_URL ?>app-admin/Tools/tinymce/tinymce.min.js">
   </script>
   <script>

      function AlbumForm()
      {
         var self = this;
         this.bAdd = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "app-admin/ContentManagement/add_album",
            postData: function ()
            {
               if (!$("#album-form").EW().validate())
               {
                  return false;
               }
               var data = ContentForm.getFormData();
               //data.content = tinymce.activeEditor.getContent();
               return data;
            },
            onDone: function (data)
            {
               $.EW("getParentDialog", $("#album-form")).trigger("close");
               $("body").EW().notify(data).show();
               $(document).trigger("media-list.refresh", [data]);
            }}).hide();
         this.bEdit = EW.addAction("tr{Save Changes}", this.editAlbum).addClass("btn-success").hide();

         $("#album-form").on('refresh', function (e, data)
         {
            if (data["id"])
            {
               $("#form-title").html("<span>tr{Edit}</span>" + data.title);
               self.bEdit.comeIn();
               self.bAdd.comeOut();
               //bDelete.comeIn(300)
            }
            else
            {
               self.bEdit.comeOut();
               self.bAdd.comeIn();
            }
         });
      }

      AlbumForm.prototype.editAlbum = function ()
      {
         if ($("#title").val())
         {
            //alert(media.itemId);
            EW.lock(media.currentTopPane, "Saving...");
            $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/update_album', {
               id: media.itemId,
               title: $('#title').val()}, function (data) {
               media.listMedia();
               $("body").EW().notify(data).show();
               EW.unlock(media.currentTopPane);
            }, "json");
         }
         return false;
      };
      var AlbumForm = new AlbumForm();


      $("#title").focus();

      tinymce.EditorManager.execCommand('mceRemoveEditor', false, "html_content");
      setTimeout(function () {
         tinymce.init({
            mode: "exact",
            elements: 'html_content',
            relative_urls: false,
            remove_script_host: false,
            schema: "html5",
            theme: "modern",
            apply_source_formatting: true,
            height: 400,
            //content_css: "admin/styles/template.css",
            plugins: [
               "advlist autolink lists link image ewimage charmap print preview anchor",
               "searchreplace visualblocks code fullscreen",
               "insertdatetime table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | ewimage"
                    // Example content CSS (should be your site CSS)

         });
      }, 500);

   </script>
   <?php
   return ob_get_clean();
}

function get_editor()
{
   ob_start();
   ?>
   <div class="row">
      <div class="col-lg-12">
         <label for="html_content">
            tr{Content}
         </label>
         <textarea id="html_content" name="html_content" style="padding:0px 2px; width:100%;min-height:500px;" ></textarea>
      </div>
   </div>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-album-form-default", "ew-ne-album-main-form", ["title" => "Properties", "content" => get_ew_album_properties_form()]);
EWCore::register_form("ew-album-form-default", "album-content", ["title" => "Content", "content" => get_editor()]);
$tabsDefault = EWCore::read_registry("ew-album-form-default");
$tabs = EWCore::read_registry("ew-album-form");
?>
<form id="album-form"  action="#" method="POST">
   <div class="header-pane  tabs-bar row">
      <h1 id='form-title' class="col-xs-12">
         <span>tr{New}</span>tr{Album}
      </h1>
      <ul class="nav nav-tabs">
         <?php
         foreach ($tabsDefault as $id => $tab)
         {
            if ($id == "ew-ne-album-main-form")
               echo "<li class='active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
            else
               echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
         }
         foreach ($tabs as $id => $tab)
         {
            echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
         }
         ?>
      </ul>
   </div>
   <div class="form-content  tabs-bar row">

      <div class="tab-content col-xs-12">
         <?php
         foreach ($tabsDefault as $id => $tab)
         {
            if ($id == "ew-ne-album-main-form")
               echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
            else
               echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
         }
         foreach ($tabs as $id => $tab)
         {
            echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
         }
         ?>
      </div>

   </div>
   <div class="footer-pane row actions-bar action-bar-items">
   </div>
</form>
<script>
   $(document).ready(function ()
   {
      EW.setFormData("#album-form",<?php echo $form_data ?>);
   });
</script>
