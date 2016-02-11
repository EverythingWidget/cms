<?php
/*
 * title: GitHub Buttons
 * description: Create a GitHub button
 * feeder_type: github_button_model
 */
?>
<div class="block-row mt">
   <div class="col-xs-12 ">      
      <input class="text-field" name="repo" id="repo" data-label="User/Repo Name" >    
   </div>
</div>
<div class="block-row">
  <div class="col-xs-12" >
    <h2 class="ta-center">
      Buttons
    </h2>
  </div>
  <div class="btn-group-vertical col-xs-12" data-toggle="buttons">       
    <label class="btn btn-default active">
      <input type="checkbox" name="follow" id="buttons" value="follow" checked="true"> Follow
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="watch" id="buttons" value="watch"> Watch
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="star" id="buttons" value="star"> Star
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="fork" id="buttons" value="fork"> Fork
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="issue" id="buttons" value="issue"> Issue
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="download" id="buttons" value="download"> Download
    </label>
  </div>
</div>