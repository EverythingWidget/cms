<div class="card z-index-1 card-medium">
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
    <h1>Google Services</h1>
  </div>
  <div class='card-content'>
    <h3>Analytics</h3>
    <system-field class="field">
      <label>tr{Google analytics ID}</label>
      <input class="text-field" name="webroot/google-analytics-id" />
    </system-field>    
  </div>
  
  <div class='card-content'>
    <h3>reCAPTCHA</h3>

    <system-field class="field">
      <label>tr{Site key}</label>
      <input class="text-field" name="webroot/google/recaptcha/site-key" />
    </system-field>   

    <system-field class="field">
      <label>tr{Secret key}</label>
      <input class="text-field" name="webroot/google/recaptcha/secret-key" />
    </system-field>   
  </div>
  
  <div  class='card-header top-devider'>
    <h1>Facebook Services</h1>
  </div>
  <div class='card-content'>
    <h3>Social Plugins</h3>
    <system-field class="field">
      <label>tr{App ID}</label>
      <input class="text-field" name="webroot/facebook/app-id" />
    </system-field>    
  </div>
  
</div>