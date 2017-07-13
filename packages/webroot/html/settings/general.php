<system-ui-view name="webroot-settings-card" class="card z-index-1 card-medium">
  <div class='card-header'>
    <div class="card-title-action"></div>
    <div class="card-title-action-right"></div>
    <h1>
      Webroot
    </h1>
  </div>

  <div class='card-content'>
    <system-field class="field">
      <label>tr{Language}</label>
      <select name="webroot/language" v-model="activeLanguage">
        <option v-for="lang in languages" v-bind:value="lang.name">{{ lang.title }}</option>
      </select>
    </system-field>

    <system-field class="field">
      <label>tr{Title}</label>
      <input class="text-field" name="webroot/title"/>
    </system-field>

    <system-field class="field">
      <label>tr{Keywords}</label>
      <input class="text-field" name="webroot/keywords"/>
    </system-field>

    <system-field class="field">
      <label>tr{Description}</label>
      <textarea class="text-field" name="webroot/description"></textarea>
    </system-field>

    <div class="mt+">
      <label>
        tr{Favicon}
      </label>
      <input type="hidden" name="webroot/favicon" alt="Image" data-ew-plugin="image-chooser" style="max-height:32px;">
    </div>
  </div>

  <div class='card-header top-devider'>
    <h1>Google Services</h1>
  </div>
  <div class='card-content'>
    <h3>Accelerated Mobile Pages (AMP)</h3>
    <label class="checkbox">
      tr{Enable}
      <input type="checkbox" name="webroot/accelerated-mobile-pages" value="true" data-off-value/><i></i>
    </label>
  </div>

  <div class='card-content'>
    <h3>Analytics</h3>
    <system-field class="field">
      <label>tr{Google analytics ID}</label>
      <input class="text-field" name="webroot/google-analytics-id"/>
    </system-field>
  </div>

  <div class='card-content'>
    <h3>reCAPTCHA</h3>

    <system-field class="field">
      <label>tr{Site key}</label>
      <input class="text-field" name="webroot/google/recaptcha/site-key"/>
    </system-field>

    <system-field class="field">
      <label>tr{Secret key}</label>
      <input class="text-field" name="webroot/google/recaptcha/secret-key"/>
    </system-field>
  </div>

  <div class='card-header top-devider'>
    <h1>Facebook Services</h1>
  </div>
  <div class='card-content'>
    <h3>Social Plugins</h3>
    <system-field class="field">
      <label>tr{App ID}</label>
      <input class="text-field" name="webroot/facebook/app-id"/>
    </system-field>
  </div>
</system-ui-view>

<script>
  var languages = [
    {
      name: 'en',
      title: 'English'
    },
    {
      name: 'es',
      title: 'Spanish'
    },
    {
      name: 'de',
      title: 'German'
    },
    {
      name: 'ru',
      title: 'Russian'
    },
    {
      name: 'cmn',
      title: 'Mandarin'
    },
    {
      name: 'ar',
      title: 'Arabic'
    },
    {
      name: 'fa',
      title: 'Persian'
    },
    {
      name: 'nl',
      title: 'Dutch'
    }
  ];

  var languagesMap = {
    en: "English",
    es: "Spanish",
    de: "German",
    ru: "Russian",
    cmn: "Mandarin",
    ar: "Arabic",
    fa: "Persian",
    nl: 'Dutch'
  };

  var vue = new Vue({
    el: Scope.views.webroot_settings_card,
    data: {
      items: [],
      activeLanguage: 'en',
      languagesMap: languagesMap,
      languages: languages,
      contentId: null
    }
  });

  Scope.parentScope.html.find('#settings-cards').on('refresh', function (event, response) {
    vue.activeLanguage = response.data['webroot/language'];
  });
</script>
