<form v-on:submit="postComment">
  <h2 class="title">
    Post Comment
  </h2>

  <label class="field field-name">
    <input name="name" v-model="name"/>
    <span class="field-label">Name</span>
  </label>

  <label class="field field-email">
    <input name="email" v-model="email" required/>
    <span class="field-label">E-Mail</span>
  </label>

  <label class="field field-content">
    <textarea name="content" v-model="content" required></textarea>
    <span class="field-label">Comment</span>
  </label>

  <div class="field">
    <div id="g-recaptcha-{$widget_id}" class="g-recaptcha"></div>
  </div>
  <div>
    <button type="submit" v-bind:disabled="disablePosting">Post</button>
  </div>
</form>


<script >
  (function () {
    var siteKey = '<?=
EWCore::call_api('admin/api/settings/read-settings', [
    'app_name' => 'ew-blog/site-key'
])['data']['ew-blog/site-key'];
?>'

    post_comment_$widget_id_js = function () {
      grecaptcha.render('g-recaptcha-{$widget_id}', {
        sitekey: siteKey,
        callback: postCommentForm.verifyCapcha,
        'expired-callback': function () {
          postCommentForm.disablePosting = true;
        }
      });
    };


    var postCommentForm = new Vue({
      el: '[data-widget-id="{$widget_id}"]',
      data: {
        name: '',
        email: '',
        commenter_id: '',
        content: '',
        content_id: '<?= $_REQUEST['_method_name'] ?>',
        recaptcha: null,
        canPost: true
      },
      methods: {
        verifyCapcha: function (captcha) {
          $.ajax({
            type: 'POST',
            url: 'api/ew-blog/comments/confirm-capcha',
            data: {
              response: captcha
            },
            success: success
          });

          function success(response) {
            if (response.data.success) {
              postCommentForm.disablePosting = false;
            } else {
              grecaptcha.reset();
              alert('Error: ' + response.data['error-codes'].join(', '));
            }
          }
        },
        postComment: function (event) {
          event.preventDefault();
          var _this = this;
          _this.$data.recaptcha = grecaptcha.getResponse();

          $.ajax({
            type: 'POST',
            url: 'api/ew-blog/comments/',
            data: _this.$data,
            success: success
          });

          function success(response) {
            if (response.status_code === 200) {
              _this.$data.name = '';
              _this.$data.email = '';
              _this.$data.content = '';
            }
          }
        }
      }
    });
  })();
</script>

<script src='https://www.google.com/recaptcha/api.js?onload=post_comment_$widget_id_js&render=explicit'></script>

