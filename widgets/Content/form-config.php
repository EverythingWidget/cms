<?php/* * title: Content * description: Show an article, app page or select a page feeder. * feeder_type: page */session_start();?><div class="block-row mt">  <div class="col-xs-12 ">          <input class="text-field" name="feeder" id="feeder" data-label="Default Content" data-ew-plugin="link-chooser" >      </div>  <div class="btn-group col-xs-12" data-toggle="buttons">    <label class="btn btn-primary btn-sm pull-right" >      <input type="checkbox" name="priority-with-url" id="priority-with-url" value="yes" > tr{Priority With URL}    </label>  </div>   </div> <div class="block-row">  <div class="col-xs-12">    <input class="text-field"  name="linkAddress" id="linkAddress" data-label="Link Address" data-ew-plugin="link-chooser">  </div></div><div class="block-row">  <div class="col-xs-12">    <input class="text-field" id="linkName" name="linkName" data-label="Link Title" >  </div></div><div class="block-row">  <div class="col-xs-12">    <select class="" id="animation" name="animation" onchange="" data-label="Animation">      <option value="0">        tr{None}      </option>      <option value="1">        tr{Fade}      </option>           <option value="2">        tr{Height Slide}      </option>         </select>  </div></div><div class="block-row">  <h2 class="ta-center">    tr{Content Fields}  </h2>  <ul id="widget-list-content-fields" class="list arrangeable">    <li class="" style="">      <div class="wrapper">        <div class="handle"></div>        <input class="text-field floatlabel" data-label='Field name' name="content_fields"/>        </div>    </li>  </ul></div><script>  (function () {    var d;    var element = null;    function selectLink(elmId) {      element = elmId;      d = EW.createModal();      $.post("~admin/content-management/file-chooser.php", {callback: "fileChooserCallbck"}, function (data) {        d.html(data);      });    }    function fileChooserCallbck(rowId) {      if (element)      {        $(element).val(rowId);        d.dispose();      }    }    function afterSelectPath(elmId) {      if (d)        d.dispose();      if (elmId == "path")      {      }    }    $("#uis-widget").on("refresh", function (e, data) {      $("#widget-list-content-fields").EW().dynamicList({        value: {          'content_fields': data['content_fields']        }      });    });  })();</script>