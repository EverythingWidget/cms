<?php
/*
 * title: Menu
 * description: Create a menu or select a menu feeder
 * feeder_type: menu
 */
?>
<div class="row mar-top">
   <div class="col-xs-12 ">      
      <input class="text-field" name="feeder" id="feeder" data-label="Default Content" data-ew-plugin="link-chooser" >    
   </div>
</div>
<div class="row">
   <div class="col-xs-12 ">      
      <ul id="menu" class="list arrangeable">
         <li class="" style="">
            <div class="wrapper">
               <div class="handle"></div>
               <div class="row">
                  <div class="col-xs-12 col-md-6" >
                     <input class="text-field floatlabel" data-label='Menu Tile' name="title"/>  
                  </div>
                  <div class="col-xs-12 col-md-6" >
                     <input class="text-field test" data-label='Link' data-ew-plugin="link-chooser" name="link"/>
                  </div>
               </div>      
               <div class="row">
                  <div class="col-xs-12" >
                     <input type="text" class="text-field test" data-label='Select Icon' id="icon" name="icon"/>
                  </div>
               </div>
            </div>
         </li>
      </ul>
   </div>
</div>

<script>

   $("#menu").EW().dynamicList({
      value: uisWidget.widgetParameters
   });

</script>