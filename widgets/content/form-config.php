<?php/* * title: Content * description: Show an article, app page or select a page feeder. * feeder_type: page */session_start();?><div class="form-block">  <system-field class="field">        <label>      tr{Default Content}    </label>    <system-input-json name="feeder" id="feeder" v-bind:value="feeder"></system-input-json>    <div class="field-actions">      <button type="button" class="btn btn-info" v-on:click="selectFeeder()"><i class="icon-link"></i></button>    </div>  </system-field></div><div class="form-block">      <label class="checkbox">    tr{Priority With URL}    <input type="checkbox" name="priority-with-url" id="priority-with-url" value="yes"/><i></i>  </label></div><div class="form-block">      <label class="checkbox">    Include meta data    <input type="checkbox" name="include-meta" value="true"/><i></i>  </label></div><div class="form-block">  <system-field class="field">    <label>      tr{Link Address}    </label>    <system-input-json name="linkAddress" id="linkAddress" v-bind:value="linkContent"></system-input-json>    <div class="field-actions">      <button type="button" class="btn btn-info" v-on:click="selectContent()"><i class="icon-link"></i></button>    </div>  </system-field></div><div class="form-block">  <system-field class="field">    <label>      tr{Link Title}    </label>    <input class="text-field" id="linkName" name="linkName" >  </system-field></div><div class="form-block">  <system-field class="field">    <label>      tr{Animation}    </label>    <select class="" id="animation" name="animation" onchange="">      <option value="0">        tr{None}      </option>      <option value="1">        tr{Fade}      </option>           <option value="2">        tr{Height Slide}      </option>         </select>  </system-field></div><div class="form-block">     <h2 class="ta-center">    tr{Content Fields}  </h2>  <system-field class="field">    <label>tr{Content mason}</label>    <input class="text-field" name="content_mason">  </system-field>  <ul id="widget-list-content-fields" class="list arrangeable fields">    <li class="" style="">      <div class="wrapper">        <div class="handle"></div>        <input class="text-field floatlabel" data-label='Field name' name="content_fields"/>        </div>    </li>  </ul></div><script>  (function () {    var vue = new Vue({      el: '#widget-control-panel',      data: {        feeder: {},        linkContent: {}      },      methods: {        selectFeeder: function () {          var linkChooser = System.entity('ui/dialogs/link-chooser');          linkChooser.open(function (data) {            vue.feeder = {              feederId: data.feederId,              id: data.id,              params: {}            };          });        },        selectContent: function () {          var linkChooser = System.entity('ui/dialogs/link-chooser');          linkChooser.open(function (data) {            vue.linkContent = {              title: data.title,              feederId: data.feederId,              id: data.id,              params: {}            };          });        }      }    });    $('#uis-widget').on('refresh', function (e, data) {      vue.feeder = data.feeder;      $('#widget-list-content-fields').EW().dynamicList({        value: {          'content_fields': data['content_fields'] || []        }      });    });  })();</script>