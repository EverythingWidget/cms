<div class="row mar-top">
   <div class="col-xs-12 margin-bottom" >
      <h4>
         Title
      </h4>
      <div class="btn-group btn-group-justified" data-toggle="buttons">
         <label class="btn btn-default active">
            <input type="radio" name="title" id="col-hidden" value="" checked="true"> tr{None}
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="title" id="col-hidden" value="h1"> H1
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="title" id="col-hidden" value="h2"> H2
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="title" id="col-hidden" value="h3"> H3
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="title" id="col-hidden" value="h4"> H4
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="title" id="col-hidden" value="h5"> H5
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="title" id="col-hidden" value="h6"> H6
         </label>
      </div>
      <input class="text-field" name="title-text" id="title-text" disabled="true">
   </div>
</div>
<div class="row mar-top">
   <div class="col-xs-12 col-md-6" >
      <h4>
         Width
      </h4>
      <div class="btn-group btn-group-justified" data-toggle="buttons">
         <label class="btn btn-default active">
            <input type="radio" name="width-opt" id="width-opt" value="" checked="true"> Default
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="width-opt" id="width-opt" value="custom"> Custom
         </label>
      </div>
      <input class="text-field" name="width" id="width" disabled="true">
   </div>
   <div class="col-xs-12 col-md-6" >
      <h4>
         Margin
      </h4>
      <div class="btn-group btn-group-justified" data-toggle="buttons">
         <label class="btn btn-default active">
            <input type="radio" name="margin-opt" id="margin-opt" value="" checked="true"> Default
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="margin-opt" id="margin-opt" value="custom"> Custom
         </label>
      </div>
      <input class="text-field" name="margin" id="margin" disabled="true">
   </div>
</div>
<div class="row mar-top">
   <div class="col-xs-12 col-md-6" >   
      <h4>
         Padding
      </h4>
      <div class="btn-group btn-group-justified" data-toggle="buttons">
         <label class="btn btn-default active">
            <input type="radio" name="padding-opt" id="padding-opt" value="" checked="true"> Default
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="padding-opt" id="padding-opt" value="custom"> Custom
         </label>
      </div>
      <input class="text-field" name="padding" id="padding" disabled="true">
   </div>
</div>
<script  type="text/javascript">
   $("input:radio[name='width-opt']").change(function (event) {
      if ($(this).val())
         $("#width").prop("disabled", false);
      else
         $("#width").prop("disabled", true);
   });
   $("input:radio[name='margin-opt']").change(function (event) {
      if ($(this).val())
         $("#margin").prop("disabled", false);
      else
         $("#margin").prop("disabled", true);
   });
   $("input:radio[name='padding-opt']").change(function (event) {
      if ($(this).val())
         $("#padding").prop("disabled", false);
      else
         $("#padding").prop("disabled", true);
   });
</script>