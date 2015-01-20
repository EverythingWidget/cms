<?php
/*
 * title: Collection
 * description: Create a list of pages, layouts or contents to be included inside you layout
 */
?>
<div class="row">
   <div class="col-xs-12">
      <ul id="pages" class="list arrangeable">
         <li class="" style="">
            <div class="wrapper">
               <div class="handle"></div>
               <div class="row">
                  <div class="col-xs-12" >
                     <input class="text-field test" data-label='Page URL, Layout or Content' data-ew-plugin="link-chooser" name="feeder"/>
                  </div>
               </div>      
               <div class="row">
                  <div class="col-xs-12" >
                     <input type="text" class="text-field test" data-label='Container ID' id="container_id" name="container_id"/>
                  </div>
               </div>
            </div>
         </li>
      </ul>
   </div>
</div>
<script>

   $("#pages").EW().dynamicList({
      value: uisWidget.widgetParameters
   });

</script>