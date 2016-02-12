<?php
/*
 * title: GitHub Buttons
 * description: Create a GitHub button
 * feeder_type: github_button_model
 */
?>
<div class="block-row mt">
   <div class="col-xs-6 ">      
      <input class="text-field" name="user" id="user" data-label="User" >    
   </div>
  <div class="col-xs-6 ">      
      <input class="text-field" name="repo" id="repo" data-label="Repo Name" >    
   </div>
</div>
<div class="block-row">
  <div class="col-xs-12" >
    <h2 class="ta-center">
      Buttons
    </h2>
  </div>
  <div class="btn-group-vertical col-xs-12" data-toggle="buttons">           
    <label class="btn btn-default ">
      <input type="checkbox" name="star" id="star" value="star"> Star
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="watch" id="watch" value="watch"> Watch
    </label>
    <label class="btn btn-default">
      <input type="checkbox" name="follow" id="follow" value="follow"> Follow
    </label>
  </div>
</div>