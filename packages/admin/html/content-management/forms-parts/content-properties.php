<div id="properties-form" class="col-lg-8 col-md-8 col-sm-12 col-xs-12" >        
   <div class="row mar-top">
      <input type="hidden" id="id" name="id" value="">
      <input type="hidden" id="type" name="type" value="<?php echo $form_config["contentType"] ?>">

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
   $content_components = EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT);

   foreach ($content_components as $comp_id => $label_object)
   {//print_r($value);
      $data_array = json_decode($form_config["data"], true);
      $labels = $data_array["labels"];

      if (isset($labels))
      {
         foreach ($labels as $label)
         {
            if ($label["key"] == $comp_id)
            {
               $value = $label["value"];
               break;
            }
         }
      }

      $form = $label_object->get_form($comp_id, compact("comp_id", "value", "form_id"));
      ?>
      <div class=row>
         <div class='col-xs-12'>
            <div class='box box-grey content-label disabled' data-activated="false">
               <div class='row'>
                  <div class='col-xs-12'>
                     <h3 class="pull-left"><?php echo $form["title"] ?></h3>
                     <div class="btn-group pull-right" data-toggle="buttons">
                        <label class="btn btn-default btn-sm">
                           <input type="checkbox" id="<?php echo $comp_id ?>_control_button" class="label-control-button"  ><span>Turned Off</span>
                        </label>
                     </div>
                  </div>
               </div>
               <div class='row'>
                  <?php echo $form["html"] ?>
               </div>
            </div>
         </div>
      </div>
      <?php
      //echo $inputs["content"];
   }
   ?>
</div>
