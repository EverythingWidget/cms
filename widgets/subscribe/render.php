<form v-on:submit="subscribe">
  <h1>
    Subscribe
  </h1>

  <galaxy-field v-if="!success" class="field field-name">
    <label class="field-label">Email Address</label>
    <input class="field-text" type="email" name="email" v-model="email"/>    
  </galaxy-field>

  <p class="msg msg-success" v-if="success">
    {{ message }}
  </p>

  <div v-if="!success" class="field actions">
    <button class="btn btn-submit" type="submit">
      Subscribe
    </button>
  </div>
</form>

<script>

  window.addEventListener('load', function () {
    new Vue({
      el: '[data-widget-id="$php.widget_id"]',
      data: {
        email: '',
        success: false,
        message: 'Thank you for subscribing!'
      },
      methods: {
        subscribe: function (event) {
          event.preventDefault();
          var _this = this;

          $.ajax({
            type: 'POST',
            url: 'api/ew-blog/subscribers/',
            data: _this.$data,
            success: success,
            error: error
          });

          function success(response) {
            if (response.status_code === 200) {
              _this.$data.email = '';
              _this.$data.success = true;
            } else if (response.message_code === 'duplicate') {
              _this.$data.message = 'This email address has been already subscribed';
              _this.$data.success = true;
            }
          }

          function error(response) {

          }

          return false;
        }
      }
    });
  });

</script>