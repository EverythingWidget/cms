<?php
$page_info = webroot\WidgetsManagement::get_page_info();

$facebook_app_id = EWCore::call_api('admin/api/settings/read-settings', [
            'app_name' => 'webroot/webroot/facebook/app-id'
        ])['data']['webroot/facebook/app-id'];

webroot\WidgetsManagement::set_meta_tag([
    'property' => 'og:url',
    'content'  => $page_info['webroot/url']
]);

webroot\WidgetsManagement::set_meta_tag([
    'property' => 'og:type',
    'content'  => 'article'
]);

webroot\WidgetsManagement::set_meta_tag([
    'property' => 'og:title',
    'content'  => $page_info['webroot/title']
]);

webroot\WidgetsManagement::set_meta_tag([
    'property' => 'og:description',
    'content'  => $page_info['webroot/description']
]);

webroot\WidgetsManagement::set_meta_tag([
    'property' => 'og:image',
    'content'  => ''
]);
?>
<div class="fb-like" 
     data-href="<?= $page_info['webroot/url'] ?>" 
     data-layout="box_count" 
     data-action="like" 
     data-size="large" 
     data-show-faces="true" 
     data-share="false"></div>


<div class="fb-share-button" 
     data-href="<?= $page_info['webroot/url'] ?>" 
     data-layout="box_count" 
     data-size="large" 
     data-mobile-iframe="false">
</div>

<div id="fb-root"></div>

<script>
  (function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
      return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
