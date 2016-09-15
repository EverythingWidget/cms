<form id="apps-page-uis" onsubmit="return false;">         
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
      <?php
      $widgets_types_list = EWCore::call_api('webroot/api/widgets-management/get-widget-feeders', [
                  'type' => 'page'
      ]);

      $pages = $widgets_types_list['data'];

      //Show list of pages and their layouts
      if (isset($pages)) {
        foreach ($pages as $page) {
          $uis = webroot\WidgetsManagement::get_path_uis("/{$page->url}");
          echo '<div class="row">'
          . "<input type='hidden'  name='{$page->url}_uisId' id='{$page->url}_uisId' value='{$uis["uis_id"]}'>"
          . '<system-field class="field col-xs-12">'
          . "<label>{$page->title} : optional</label>";
          echo "<input class='text-field app-page-uis' name='/{$page->url}' id='{/$page->url}' value='{$uis["uis_name"]}'>";
          echo "</system-field></div>";
        }
      }
      ?>
    </div>      
  </div>
</form>

<?= \ew\ResourceUtility::load_js_as_tag(__DIR__ . '/component.js', [], true) ?>