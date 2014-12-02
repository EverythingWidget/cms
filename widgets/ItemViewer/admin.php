<?php
/*
 * title: Item Viewer
 * description: Show multiple items
 */
session_start();
?>
<div class="row">
  <div class="col-xs-12 ">      
    <input class="text-field" name="feeder" id="feeder" data-label="Content" data-ew-plugin="link-chooser" >    
  </div>
  <div class="btn-group col-xs-12 mar-top" data-toggle="buttons">
    <label class="btn btn-primary btn-sm pull-right" >
      <input type="checkbox" name="default-content" id="default-content" value="yes" > Default Content
    </label>
  </div>
</div>