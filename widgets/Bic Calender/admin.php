<?php
/*
 * title: BIC Calender
 * description: BIC Calendar is a simple calendar widget to mark events and ranges of dates
 */
session_start();
?>
<div class="row">
  <div class="col-xs-12 ">      
    <input class="text-field" name="url" id="url" data-label="Ajax URL" data-ew-plugin="link-chooser" >    
  </div>  
  <div class="btn-group col-xs-12 mar-top" data-toggle="buttons">
    <label class="btn btn-primary btn-sm" >
      <input type="checkbox" name="monthControl" id="monthControl" value="true" >Month Control
    </label>
  </div>
</div>