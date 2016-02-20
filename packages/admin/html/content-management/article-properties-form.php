<?php ?>
<!-- Begin of form UI-->
<input type="hidden" id="id" name="id" value="">


<div class="row margin-bottom">
   <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" >
      <div class="row">
         <div class="col-xs-12 col-md-12 col-lg-12 mar-top">
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
      $input_groups = EWCore::read_registry("ew/ui/form/content/proerties");
      foreach ($input_groups as $id => $inputs)
      {
         echo "<div class=row><div class='col-xs-12'><h3>{$inputs["title"]}</h3></div></div>";
         echo $inputs["content"];
      }
      ?>


   </div>
   <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" ><div class="row"></div></div>
</div>