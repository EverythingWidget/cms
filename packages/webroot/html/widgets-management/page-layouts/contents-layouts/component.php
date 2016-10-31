<form id="apps-page-uis" onsubmit="return false;" system-ui-view="main">         
  <div class="card card-big center-block z-index-1">
    <div  class="card-header">
      <div class="card-title-action"></div>
      <div class="card-title-action-right"></div>

      <h1> Default Layouts </h1>
    </div>
    <div class="card-content">
      <div class="block-row">
        <system-field class="field">
          <label>tr{Default Layout}</label>          
          <input class="text-field app-page-uis" name="@DEFAULT" v-bind:value="getFeederLayout('@DEFAULT').name">
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout('@DEFAULT')"><i class="icon-menu"></i></button>
          </div>
        </system-field> 
      </div>
      <div class="block-row">
        <system-field class="field">
          <label>tr{Homepage Layout}</label>
          <input class="text-field app-page-uis" name="@HOME_PAGE" v-bind:value="getFeederLayout('@HOME_PAGE').name">
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout('@HOME_PAGE')"><i class="icon-menu"></i></button>
          </div>
        </system-field>
      </div>
      <div class="block-row">
        <system-field class="field">
          <label>tr{User's Homepage Layout}</label>          
          <input class="text-field app-page-uis" name="@USER_HOME_PAGE" v-bind:value="getFeederLayout('@USER_HOME_PAGE').name">
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout('@USER_HOME_PAGE')"><i class="icon-menu"></i></button>
          </div>
        </system-field>
      </div>
    </div>

    <div  class="card-header top-divider">
      <h1> App's pages </h1>
    </div>
    <div class="card-content">
      <div class="block-row" v-for="feeder in pageFeeders">        
        <system-field class="field">
          <label>{{ '/' + feeder.url }}</label>
          <input class='text-field app-page-uis' name='/{{ feeder.url }}' v-bind:value="getFeederLayout('/' + feeder.url).name">
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout('/' + feeder.url)"><i class="icon-menu"></i></button>
          </div>
        </system-field>
      </div>
    </div> 

    <div  class="card-header top-divider">
      <h1> Customs </h1>
    </div>
    <div class="card-content">
      <div class="block-row" v-for="url in pathLayouts">        
        <system-field class="field">
          <label>{{ url.path }}</label>
          <input class='text-field app-page-uis' name='{{ url.path }}' v-bind:value='getFeederLayout(url.path).name'>
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout(url.path)"><i class="icon-menu"></i></button>
          </div>
        </system-field>
      </div>
      <h2>Add new</h2>
      <div class="block-row">        
        <system-field class="field">
          <label>URL</label>
          <input class='text-field app-page-uis' v-model='custom.path'>
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout(custom.path)"><i class="icon-menu"></i></button>
          </div>
        </system-field>
      </div>
    </div> 
  </div>
</form>
<?php
$page_feeders = json_encode(EWCore::call_api('webroot/api/widgets-management/get-widget-feeders', [
            'type' => 'page'
        ]));

echo \ew\ResourceUtility::load_js_as_tag(__DIR__ . '/component.js', [
    'page_feeders' => $page_feeders,
    'url_layouts'  => json_encode(webroot\WidgetsManagement::path_layouts())
        ], true);
?>