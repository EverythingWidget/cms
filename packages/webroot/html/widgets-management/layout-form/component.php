<div class="header-pane thin tabs-bar">
  <h1 id="form-title">
    <span>tr{New}</span>tr{Layout Structure}
  </h1>  
  <ul id="ew-uis-editor-tabs" class="nav nav-pills xs-nav-tabs" >
    <li class="active"><a href="#inspector" data-toggle='tab'>tr{Structure}</a></li>
    <li class="disable"><a href="#template-control" data-toggle='tab'>tr{Template}</a></li>
    <li class=""><a href="#pref" data-toggle='tab'>tr{Settings}</a></li>
  </ul>
</div>

<div id="ew-uis-editor" class="form-content" style="padding:0px;">
  <div class="list-modal" style="left:-400px;" id="items-list">      
    <h1 class="pull-left">Select an item</h1>
    <a href='javascript:void(0)' onclick="this.parentNode.close()" class='close-icon pull-right' style="margin:5px;"></a>      
    <system-list id="items-list-content" class="content-pane" action="a">
      <a class='text-icon' data-label='{{title}}'>
        <h4>{{title}}</h4>
        <p>{{description}}</p>
      </a>
    </system-list>
  </div>

  <div class="uis-editor-tool-pane" id="uis-editor-tool-pane">

    <div class="tab-content">
      <div class="tab-pane active" id="inspector">
        <system-sortable-list name="inspector-editor" id="inspector-editor">

          <ul class="layout-components" >
          </ul>

        </system-sortable-list>
      </div>
      <div class="tab-pane" id="template-control">
        <form id="template_settings_form">

        </form>
      </div>
      <div class="tab-pane col-xs-12" id="pref">
        <form id="layout-form" onsubmit="return false;">
          <div class="row mt">
            <div class="col-xs-12" >
              <input class="text-field" data-label="UIS Name" name="name" id="name">
            </div>
          </div>

          <div class="block-row mt">
            <label class="checkbox">
              tr{Default UIS}
              <input type="checkbox" name="uis-default" id="uis-default" value="true"/><i></i>
            </label>
          </div>
          
          <div class="block-row mt">
            <label class="checkbox">
              tr{Home Page UIS}
              <input type="checkbox" name="uis-home-page" id="uis-home-page"  value="true"/><i></i>
            </label>             
          </div>
          
          <div class="row mt">
            <div class="col-xs-12" >

              <select data-label="UIS Template" id="template" name="template">
                <option value="">---</option>
                <?php
                $templates = EWCore::call_api("webroot/api/widgets-management/get-templates");
                //print_r($templates);
                foreach ($templates['data'] as $t) {
                  ?>
                  <option value="<?php echo $t["templatePath"] ?>"><?php echo $t["templateName"] ?></option>
                  <?php
                }
                ?>
              </select>

            </div>
          </div>

          <div id="layout-form-actions" class="actions-bar action-bar-items" ></div>
        </form>
      </div>
    </div>
  </div>

  <div id="uis-editor-preview-pane" class="uis-editor-preview-pane">
    <div class="col-xs-12" >
      <input class="text-field" data-label="UIS Perview URL" name="perview_url" id="perview_url">
    </div>
    <div id="editor-container" style="position:absolute;right:0px;top:68px;bottom:1px;overflow:hidden;left:auto;">
      <form id="neuis" style="padding:0;border:1px solid #aaa;overflow:hidden;" class="col-xs-12">
        <iframe id="fr" class="preview-iframe" src="">
        </iframe>
        <input type="submit" style="display: none;" value="">
      </form>
    </div>
  </div>
</div>

<div class="footer-pane actions-bar action-bar-items" style="border: none;">
  <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 pull-right" >
    <div class="btn-group btn-group-justified" data-toggle="buttons">
      <label class="btn btn-default ">
        <input type="radio" name="screen" id="hidden-on" value="mobile" onchange="EW.setHashParameter('screen', 'mobile', 'neuis')"> Mobile
      </label>
      <label class="btn btn-default ">
        <input type="radio" name="screen" id="hidden-on" value="tablet" onchange="EW.setHashParameter('screen', 'tablet', 'neuis')"> Tablet
      </label>
      <label class="btn btn-default ">
        <input type="radio" name="screen" id="hidden-on" value="normal" onchange="EW.setHashParameter('screen', 'normal', 'neuis')"> Normal
      </label>
      <label class="btn btn-default ">
        <input type="radio" name="screen" id="hidden-on" value="large" onchange="EW.setHashParameter('screen', 'large', 'neuis')"> HD
      </label>
    </div>
  </div>
</div>
<?php
//}
//}
// Begin of UI
?>
<div id="editor-css" style="display:none;">
  .focus-current-item {/*background-color:rgba(0,0,0,.6);*/}
  .focus-current-item .current-item, .current-item  {/*  visibility:hidden;  */}
  .current-element
  {
  border:1px solid #fff;
  border-radius:0px;
  background-color:rgba(200,240,240,.3);
  z-index:15;
  position:absolute;
  display:none;
  box-shadow:0px 0px 8px rgba(0,0,0,.7)
  }
  .highlight
  {
  box-shadow:0px 0px 10px 10px rgba(0,0,0,.8);
  outline:1px solid #fff;
  outline-offset:0px;
  opacity:1;
  z-index:15;
  }

  .widget-glass-pane
  {
  position:absolute;
  border: 2px solid #222;
  outline: 1px solid #fff;
  outline-offset: -1px;
  z-index:10;
  cursor: pointer;
  }

  .widget-glass-pane:hover
  {
  background-color:rgba(200,240,240,.3);
  }

  .widget-glass-pane .btn{float:left;margin:5px 0px 0px 5px;display:none;}
  .widget-glass-pane:hover .btn{display:block;}

  .wrapper
  {
  overflow:hidden;
  }

  .blue-shadow
  {
  background-color:#ddd;
  box-shadow: 0px 0px 5px 3px #3bd;
  }
</div>

<?= ew\ResourceUtility::load_js_as_tag('webroot/html/widgets-management/layout-form/component.js') ?>

<script>
  $(document).ready(function () {

    uisForm = new UISForm();
    EW.uisForm = uisForm;

<?php
if ($_REQUEST['uisId']) {
  $uis_info = \EWCore::call_api("webroot/api/widgets-management/layouts/{$_REQUEST['uisId']}");
}
echo 'EW.setFormData("#layout-form",' . (($uis_info != null) ? json_encode($uis_info['data']) : "null") . ');';
?>

  });
</script>




