<div class="form">    
  <system-field class="field">
    <label>Corresponding post</label>
    <input class="text-field" name="post" id="post" data-ew-plugin="link-chooser" >    
  </system-field>
  <label class="checkbox">
    Default post
    <input type="checkbox" name="default_post" value="1"/><i></i>
  </label>
  <system-field class="field">
    <label for="for_widget">
      For widget
      <span>- only widgets with id are shown</span>
    </label> 
    <select name="for_widget" id="for_widget">      
      <option value=""></option>
      <option v-for="item in widgets" v-bind:value="item.id">{{ item.title }}</li>  
    </select>
    
    
  </system-field>
</div>
<script>
  (function () {

    var commentConfigForm = new Vue({
      el: '#uis-widget',
      data: {},
      computed: {
        widgets: function () {
          var widgets = [];
          System.ui.forms.uis_form.getLayoutWidgets().each(function (i, item) {
            if (item.id) {
              widgets.push({
                title: item.dataset.widgetTitle,
                id: item.dataset.widgetId
              });
            }
          });

          return widgets;
        }
      }
    });

  })();
</script>