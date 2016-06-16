<div id="folders-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
  <div  class='card-header'>
    <div class="card-title-action"></div>
    <div class="card-title-action-right"></div>
    <h1>
      Webroot
    </h1>
  </div>

  <div class='card-content'>

    <system-field class="field">
      <label>tr{Title}</label>
      <input class="text-field" name="webroot/title" />
    </system-field>

    <system-field class="field">
      <label>tr{Keywords}</label>
      <input class="text-field" name="webroot/keywords" />
    </system-field>

    <system-field class="field">
      <label>tr{Description}</label>
      <textarea class="text-field" name="webroot/description" ></textarea>
    </system-field>

    <div class="mt+">
      <label>
        tr{Favicon}
      </label>
      <input type="hidden" name="webroot/favicon" alt="Image" data-ew-plugin="image-chooser" style="max-height:32px;">
    </div>
  </div>

  <div  class='card-header top-devider'>
    <h1>Google Analytics</h1>
  </div>
  <div class='card-content'>
    <system-field class="field">
      <label>tr{Google analytics ID}</label>
      <input class="text-field" name="webroot/google-analytics-id" />
    </system-field>    
  </div>
</div>