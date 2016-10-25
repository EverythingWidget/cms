<form id="apps-page-uis" onsubmit="return false;" system-ui-view="main">         
  <div class="card card-big center-block z-index-1">
    <div  class="card-header">
      <div class="card-title-action"></div>
      <div class="card-title-action-right"></div>

      <h1> Contents Layouts </h1>
    </div>
    <div class="card-content">
      <div class="row">
        <input type="hidden" name="@DEFAULTuisId" value="<?= $default_page["uis_id"] ?>">
        <system-field class="field col-xs-12">
          <label>tr{Default Layout}</label>          
          <input class="text-field app-page-uis" name="@DEFAULT" value="<?= $default_page["uis_name"] ?>">
        </system-field> 
      </div>
      <div class="row">
        <input type="hidden" class=""  name="@HOME_PAGEuisId" value="<?= $home_page["uis_id"] ?>">
        <system-field class="field col-xs-12">
          <label>tr{Homepage Layout}</label>
          <input class="text-field app-page-uis" name="@HOME_PAGE" value="<?= $home_page["uis_name"] ?>">
        </system-field>
      </div>
      <div class="row">
        <input type="hidden" class=""  name="@USER_HOME_PAGEuisId" value="<?= $user_home_page["uis_id"] ?>">
        <system-field class="field col-xs-12">
          <label>tr{User's Homepage Layout}</label>          
          <input class="text-field app-page-uis" name="@USER_HOME_PAGE"  value="<?= $user_home_page["uis_name"] ?>">
        </system-field>
      </div>
    </div>

    <div  class="card-header top-divider">
      <h1> App's pages </h1>
    </div>
    <div class="card-content">
      <div class="block-row" v-for="feeder in pageFeeders">        
        <system-field class="field">
          <label>{{ feeder.url }}</label>
          <input class='text-field app-page-uis' name='/{{ feeder.url }}' v-bind:value='getFeederLayout(feeder.url).name'>
          <div class="field-actions">
            <button class="btn btn-info" v-on:click="selectLayout(feeder.url)"><i class="icon-menu"></i></button>
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