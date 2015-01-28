<?php
/*
 * title: Owl Slider
 * description: Add interactive slider from the given list feeder.
 * feeder_type: list
 */
?>
<div class="row mar-top">
   <div class="col-xs-12 ">      
      <input class="text-field" name="feeder" id="feeder" data-label="Folder, article or list feeder" data-ew-plugin="link-chooser" >    
   </div>
   <div class="btn-group col-xs-12" data-toggle="buttons">
      <label class="btn btn-primary btn-sm pull-right" >
         <input type="checkbox" name="default-content" id="default-content" value="yes" > Default Content
      </label>
   </div>
</div>
<div class="row">
   <div class="col-lg-6 col-xs-12">
      <div class="row">
         <h4>
            tr{Items per slide} - tr{Large Screen}
         </h4>        
         <input class="col-xs-12" type="text" name="items-per-slide-lg" id="items-per-slide-lg" value="1" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
   </div>

   <div class="col-lg-6 col-xs-12">
      <div class="row">
         <h4>
            tr{Items per slide} - tr{Normal Screen}
         </h4>        
         <input class="col-xs-12" type="text" name="items-per-slide-md" id="items-per-slide-md" value="1" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
   </div>

   <div class="col-lg-6 col-xs-12">
      <div class="row">
         <h4>
            tr{Items per slide} - tr{Tablet Screen}
         </h4>        
         <input class="col-xs-12" type="text" name="items-per-slide-sm" id="items-per-slide-sm" value="1" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
   </div>   
   
   <div class="col-lg-6 col-xs-12">
      <div class="row">
         <h4>
            tr{Items per slide} - tr{Mobile Screen}
         </h4>        
         <input class="col-xs-12" type="text" name="items-per-slide-xs" id="items-per-slide-xs" value="1" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
   </div>   
</div>
<div class="row">
   <h4>
      tr{Options}
   </h4>  
   <div class="btn-group btn-group-justified  col-xs-12" data-toggle="buttons">
      <label class="btn btn-primary" >
         <input type="checkbox" name="auto-height" id="auto-height" value="true" > tr{Auto Height}
      </label>
      <label class="btn btn-primary" >
         <input type="checkbox" name="loop" id="loop" value="true" > tr{Loop}
      </label>
      <label class="btn btn-primary" >
         <input type="checkbox" name="center" id="center" value="true" > tr{Center}
      </label>
   </div>
</div>