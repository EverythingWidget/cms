<div class="col-xs-12" >
  <h2 class="ta-center">
    Read from template/Custom
  </h2>
  <div class="btn-group btn-group-justified" data-toggle="buttons">
    <label class="btn btn-default " >
      <input type="checkbox" name="custom-template" id="custom-template" value="no-size-layout"  > Disable Size & Layout
    </label>
    <label class="btn btn-default disabled" >
      <input type="checkbox" name="bootstrap-row" id="bootstrap-row" value="row" disabled="true"> Row
    </label>
  </div>
</div>

<div class="col-xs-12" >
  <h2 class="ta-center">
    Does not show on
  </h2>
  <div id="hiddenOnScreens" class="btn-group btn-group-justified" data-toggle="buttons">
    <label class="btn btn-default ">
      <input type="checkbox" name="hidden-on" id="hidden-on" value="hidden-xs" > Mobile
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="hidden-on" id="hidden-on" value="hidden-sm" > Tablet
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="hidden-on" id="hidden-on" value="hidden-md" > Normal Screen
    </label>
    <label class="btn btn-default ">
      <input type="checkbox" name="hidden-on" id="hidden-on" value="hidden-lg" > HD Screen
    </label>
  </div>
</div>
<div class="col-xs-12" >
  <h2 class="ta-center">
    Float
  </h2>
  <div class="btn-group btn-group-justified" data-toggle="buttons">
    <label class="btn btn-default active">
      <input type="radio" name="float" id="float" value="pull-left" checked="true"> Left
    </label>
    <label class="btn btn-default">
      <input type="radio" name="float" id="float" value="clearfix" > None
    </label>
    <label class="btn btn-default ">
      <input type="radio" name="float" id="float" value="pull-right" > Right
    </label>
  </div>
</div>  

<div class="col-xs-12">
  <div class="row">
    <h2 class="ta-center">
      Width and Offset
    </h2>
    <div class="col-lg-6 col-xs-12 mb+">
      <div class="row">
        <label class="ta-center">
          Width large Screen
        </label>        
        <input class="col-xs-12" type="text" name="col-lg-" id="col-lg-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>

      <div class="row">
        <label class="ta-center">
          Offset large Screen
        </label>        
        <input class="col-xs-12" type="text" name="col-lg-offset-" id="col-lg-offset-" data-slider="true" data-slider-range="0,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
    </div>

    <div class="col-lg-6 col-xs-12 mb+">
      <div class="row">
        <label class="ta-center">
          width normal Screen
        </label>
        <input class="col-xs-12" type="text" name="col-md-" id="col-md-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>

      <div class="row">
        <label class="ta-center">
          Offset normal Screen
        </label>        
        <input class="col-xs-12" type="text" name="col-md-offset-" id="col-md-offset-" data-slider="true" data-slider-range="0,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
    </div>

    <div class="col-lg-6 col-xs-12 mb+">
      <div class="row">
        <label class="ta-center">
          Width tablet Screen
        </label>
        <input class="col-xs-12" type="text" name="col-sm-" id="col-sm-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>

      <div class="row">
        <label class="ta-center">
          Offset tablet Screen
        </label>        
        <input class="col-xs-12" type="text" name="col-sm-offset-" id="col-sm-offset-" data-slider="true" data-slider-range="0,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
    </div>  

    <div class="col-lg-6 col-xs-12 mb+">
      <div class="row">
        <label class="ta-center">
          Width mobile Screen
        </label>
        <input class="col-xs-12" type="text" name="col-xs-" id="col-xs-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
      <div class="row">
        <label class="ta-center">
          Offset mobile Screen
        </label>        
        <input class="col-xs-12" type="text" name="col-xs-offset-" id="col-xs-offset-" data-slider="true" data-slider-range="0,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >
      </div>
    </div>
  </div>      
</div>
<script>

  $("#custom-template").change(function () {

    if ($("#custom-template").is(":checked"))
    {
      $("#size-layout input:not(#custom-template,#bootstrap-row,#hiddenOnScreens input)").prop("disabled", "disabled").parent().addClass("disabled");
      $("#bootstrap-row").prop("disabled", "").parent().removeClass("disabled");
    } else
    {
      $("#size-layout input").prop("disabled", "").parent().removeClass("disabled");
      $("#bootstrap-row").prop("disabled", "disabled").parent().addClass("disabled");
    }
    
    
    //$("#cutom-template").prop("disabled", "").parent().removeClass("disabled");
  });
</script>