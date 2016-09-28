<div id="ew-blog-settings" class="card z-index-1 card-medium">
  <div  class='card-header'>
    <h1>EW Blog</h1>
  </div>

  <div class='card-content'>
    <h2>Google reCAPTCHA</h2>

    <system-field class="field">
      <label>tr{Site key}</label>
      <input class="text-field" name="ew-blog/site-key" />
    </system-field>   

    <system-field class="field">
      <label>tr{Secret key}</label>
      <input class="text-field" name="ew-blog/secret-key" />
    </system-field>   
  </div>

  <div class='card-content'>
    <h2>Comments Feature</h2>

    <system-field class="field">
      <label>tr{Default comments feature status}</label>
      <select class="text-field" id="ew-blog/comments" name="ew-blog/comments" value="">
        <option v-for="option in options" v-bind:value="option.value" >{{ option.title }}</option>
      </select>
    </system-field> 
  </div> 
</div>
<script>
  (function () {

    new Vue({
      el: '#ew-blog-settings',
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