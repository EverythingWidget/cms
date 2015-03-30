<?php
session_start();
$content_data = $form_config["data"];

// Set form id to 'content-form' if it is not specified
$form_id = ($form_config["formId"]) ? $form_config["formId"] : "content-form";

// Set content type to the default content type if it is not specified. Default content type is article
if (!$form_config["contentType"])
   $form_config["contentType"] = "article";

// Set default form title to 'Article'
if (!$form_config["formTitle"])
   $form_config["formTitle"] = "Article";

//$content_type = ($form_config["contentType"]) ? $form_config["contentType"] : "article";
//echo $content_data;
function get_editor($form_config, $form_id)
{
   ob_start();
   ?>

   <div  id="content-editor" style="" ></div>
   <!--<div class="col-lg-12 mar-top">
      <textarea  id="content" name="content" style="" ></textarea>
   </div>-->

         <!--<script src="<?php echo EW_ROOT_URL ?>app-admin/Tools/tinymce/tinymce.min.js"></script>-->
      <script src="<?php echo EW_ROOT_URL ?>app-admin/Tools/EWEditor/eweditor.js"></script>
      <!--<script src="<?php echo EW_ROOT_URL ?>app-admin/Tools/ckeditor/ckeditor.js"></script>-->
   <script>
      $(document).ready(function () {
       
       var test = new EWEditor({
       id: '#content-editor',
       bootstrap: './core/css/bootstrap.min.css'
       });
       $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
       {
       test.setContent(formData["content"]);
       });
       });
      /*$(document).ready(function () {
       setTimeout(function () {
       CKEDITOR.replace('content', {height: "500px"});
       //alert("asdasd");
       }, 500);
       });*/
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
            ew_media_url: "<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/Media.php",
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
   <?php
   return ob_get_clean();
}

function get_properties($form_config, $form_id)
{
   ob_start();
   ?>
   <div class="col-lg-12">
      <div id="content-data" class="row margin-bottom">
         <div id="properties-form" class="col-lg-8 col-md-8 col-sm-12 col-xs-12" >
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="type" name="type" value="<?php echo $form_config["contentType"] ?>">
            <div class="row mar-top">
               <div class="col-xs-12 col-md-12 col-lg-12">

                  <input class="text-field" data-label="tr{Title}" value="" id="title" name="title" data-validate="r"/>
               </div>
            </div>
            <div class="row">
               <div class="col-xs-12 col-md-6 col-lg-6">
                  <textarea class="text-field" id="keywords" data-label="tr{Keywords}" name="keywords"  ></textarea>
               </div>
               <div class="col-xs-12 col-md-6 col-lg-6">
                  <textarea class="text-field" id="description" data-label="tr{Description}" name="description"  ></textarea>
               </div>
            </div>
            <?php
            // App custom inputs
            $input_groups = EWCore::read_registry("ew-content-form-proerties");
            foreach ($input_groups as $id => $inputs)
            {
               echo "<div class=row><div class='col-xs-12'><h3>{$inputs["title"]}</h3></div></div>";
               echo $inputs["content"];
            }
            ?>
         </div>

         <div id="content-labels" class="col-lg-4 col-md-4 col-sm-12 col-xs-12" >
            <?php
            // Load content labels
            $content_labels = EWCore::read_registry("ew-content-labels");
            foreach ($content_labels as $key => $value)
            {
               $data_array = json_decode($form_config["data"], true);
               $labels = $data_array["labels"];
               //$labels = json_decode($data_array["labels"], true);
               foreach ($labels as $label_data)
               {
                  if ($label_data["key"] == $key)
                  {
                     $label_value = $label_data["value"];
                     break;
                  }
               }
               //$listener_method_object = new ReflectionMethod($value["object"], $value["function"]);
               // Call label method and pass key and content data to it
               $label = json_decode(EWCore::process_command($value["app"], $value["section"], $value["command"], ["key" => $key, "value" => $label_value, "data" => ($form_config["data"]), "form_id" => $form_id]), true);
               ?>
               <div class=row>
                  <div class='col-xs-12'>
                     <div class='box box-grey content-label disabled' data-activated="false">
                        <div class='row'>
                           <div class='col-xs-12'>
                              <h3 class="pull-left"><?php echo $value["title"] ?></h3>
                              <div class="btn-group pull-right" data-toggle="buttons">
                                 <label class="btn btn-default btn-sm">
                                    <input type="checkbox" id="<?php echo $key ?>_control_button" class="label-control-button"  ><span>Turned Off</span>
                                 </label>
                              </div>
                           </div>
                        </div>
                        <div class='row'>
                           <?php echo $label["html"] ?>
                        </div>
                     </div>
                  </div>
               </div>
               <?php
               //echo $inputs["content"];
            }
            ?>
         </div>
      </div>
   </div>
   <?php
   return ob_get_clean();
}

//EWCore::register_form("ew-article-form-default", "article-properties", "Properties", get_ew_article_properties_form());
//EWCore::register_form("ew-article-form-default", "article-content", "Content", get_editor());
$tabsDefault = EWCore::read_registry("ew-article-form-default");
$tabs = EWCore::read_registry("ew-article-form-tab");
?>
<form id="<?php echo $form_id ?>"  action="#" method="POST">
   <div class="header-pane tabs-bar row">
      <h1 id="form-title" class="col-xs-12">
         <span>tr{New}</span>tr{<?php echo $form_config["formTitle"] ?>}
      </h1>  
      <ul class="nav nav-tabs xs-nav-tabs">
         <li class="active"><a href="#content-properties" data-toggle='tab'>tr{Properties}</a></li>
         <li class=""><a href="#content-html" data-toggle='tab'>tr{Content}</a></li>
         <?php
         foreach ($tabsDefault as $id => $tab)
         {
            /* if ($id == "article-properties")
              echo "<li class='active '><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
              else */
            echo "<li class=''><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
         }
         foreach ($tabs as $id => $tab)
         {
            echo "<li class='' ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
         }
         ?>
      </ul>
   </div>
   <div class="form-content  tabs-bar">
      <div class="tab-content">
         <div class="tab-pane active" id="content-properties">
            <?php echo get_properties($form_config, $form_id); ?>
         </div>
         <div class="tab-pane" id="content-html">
            <?php echo get_editor($form_config, $form_id); ?>
         </div>
         <?php
         foreach ($tabsDefault as $id => $tab)
         {
            /* if ($id == "article-properties")
              echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
              else */
            echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
         }
         foreach ($tabs as $id => $tab)
         {
            $tab_object = json_decode(EWCore::process_command($tab["app"], $tab["section"], $tab["command"], ["form_config" => $form_config, "form_id" => $form_id]), true);
            echo "<div class='tab-pane' id='{$id}'>" . $tab_object["html"] . "</div>";
         }
         ?>
      </div>
   </div>
   <div class="footer-pane row actions-bar action-bar-items">
   </div>
</form>
<script>
   // ContentForm predefined functions
   var ContentForm = {
      initLabels: function (labels)
      {
         //alert(JSON.stringify(labels));
         $(".content-label .label-control-button:checked").click();
         $(".content-label .label-control-button").prop("checked", false);
         if (labels)
            $.each(labels, function (i, el)
            {
               $("#" + el.key + "_control_button:not(:checked)").click();
               $("#" + el.key + "_control_button").prop("checked", true);
            });
      },
      /**
       * Active specified label
       * @param {string} label Name of the label
       * @param {boolean} flag If true then active the label only for the new content. Default is false
       */
      activeLabel: function (label, flag)
      {
         if (!flag)
         {
            $("#" + label + "_control_button:not(:checked)").click();
            $("#" + label + "_control_button").prop("checked", true);
            return;
         }
         if (!this.getFormData().id)
         {
            $("#" + label + "_control_button:not(:checked)").click();
            $("#" + label + "_control_button").prop("checked", true);
         }

      },
      /**
       * Get content label as json object
       * 
       * @returns {json} return a json object contained of content labels in the {key:value} format
       */
      getLabels: function ()
      {
         var labels = {};
         $.each($("#<?php echo $form_id ?> #content-labels .content-label"), function (i, el)
         {
            el = $(el);
            if (el.attr("data-activated") === "false")
            {
               labels[el.find("input[name='key']").val()] = null;
            }
            else if (!el.find("input[name='key']").is(":disabled") && !el.find("[name='value']").is(":disabled"))
            {
               labels[el.find("input[name='key']").val()] = el.find("[name='value']").val();
            }
         });
         return JSON.stringify(labels);
      },
      /**
       * Get content label as json object
       * 
       * @returns {json} return a json object contained of content labels in the {key:value} format
       */
      getLabel: function (key)
      {
         var value = null;
         $.each($("#<?php echo $form_id ?> #content-labels .content-label[data-activated='true']"), function (i, el)
         {
            el = $(el);
            if (el.find("input[name='key']:not(:disabled)").val() == key)
            {
               value = el.find("[name='value']").val();
               return;
            }
         });
         return value;
      },
      setLabels: function (labels)
      {
         //alert(JSON.stringify(labels));
         $("#<?php echo $form_id ?> #content-labels .content-label input[name='value']").val("");
         $.each(labels, function (i, el)
         {
            $("#" + el.key + "_value").val(el.value);
         });
      },
      /**
       * Get the content form data as json
       * 
       * @returns {json} return a json object of form data
       */
      getFormData: function ()
      {
         var formData = $.parseJSON($("#<?php echo $form_id ?>").serializeJSON());
         delete formData.key;
         delete formData.value;
         formData['labels'] = this.getLabels();
         //formData["content"] = CKEDITOR.instances.content.getData();
         /*if (tinymce && tinymce.activeEditor)
          formData["content"] = tinymce.activeEditor.getContent();*/
         return formData;
      },
      setData: function (data)
      {
         if (data && data.labels)
         {
            //var labels = $.parseJSON(data.labels);
            ContentForm.initLabels(data.labels);
            ContentForm.setLabels(data.labels);
         }
         EW.setFormData("#<?php echo $form_id ?>", data);
         $("#content").change();
         //$(ContentForm).trigger("refresh");
      }
   };
   $.each($(".content-label"), function (i, e)
   {
      e = $(e);
      e.find(".label-control-button").on("change", function ()
      {
         //console.log(e.find(".label-control-button"));
         var label = e.find(".label-control-button").next("span");
         if (e.find(".label-control-button").is(":checked"))
         {
            e.attr("data-activated", true);
            label.text("Turned On");
            //alert("click: "+e.attr("data-activated"));
            e.find(".label-control-button").parent().addClass("btn-success");
            e.find(".label-control-button").parent().removeClass("btn-default");
            e.stop().animate({className: "box box-grey content-label"}, 200);
         }
         else
         {
            e.attr("data-activated", false);
            //alert("click: "+e.attr("data-activated"));
            label.text("Turned Off");
            e.find(".label-control-button").parent().removeClass("btn-success");
            e.find(".label-control-button").parent().addClass("btn-default");
            e.stop().animate({className: "box box-grey content-label disabled"}, 200);
         }
      });
   });
   //ContentForm.init();
   $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
   {
      //alert(formData);
      //ContentForm.init();
   });</script>
<?php echo $form_config["script"] ?>
<script>
   // Set form data when the form is completely loaded
   $(document).ready(function ()
   {
      ContentForm.setData(<?php echo $content_data; ?>);
   });
</script> 