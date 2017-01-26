<div class="form-block">
  <system-field class="field">
    <label>tr{Publish date}</label>
    <input class="text-field" id="ew_blog/date_published" name="ew_blog/date_published" value=""/>
  </system-field>    
</div>

<div class="form-block">
  <label class="checkbox">
    Draft
    <input type="checkbox" name="ew_blog/draft" value="1"/><i></i>
  </label>
</div>

<div class="form-block">
  <system-field class="field">
    <label>tr{Show comments}</label>
    <select class="text-field" id="ew_blog/comments" name="ew_blog/comments" value="">
      <option v-for="option in options" v-bind:value="option.value" >{{ option.title }}</option>
    </select>
  </system-field> 
</div>

<script>
  (function () {
    $("#{{form_id}}").on('refresh', function () {
      var value = $("#ew_blog\\/date_published");
      value.datepicker({
        format: 'yyyy-mm-dd'
      });
    });

    new Vue({
      el: '#ew_blog\\/comments',
      data: {
        options: [
          {
            value: 0,
            title: 'Inherit'
          },
          {
            value: 1,
            title: 'Only confirmed comments'
          },
          {
            value: 2,
            title: 'All'
          },
          {
            value: -1,
            title: 'None, disable comments'
          }
        ]
      }
    });
  }());
</script>