<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="<?php echo EW_ROOT_URL ?>">
<link rel="shortcut icon" href="public/admin/css/images/favicon.ico">  
<link rel="stylesheet" href="public/rm/css/bootstrap.css" >  
<link rel="stylesheet" href="public/admin/css/simple-slider.css"  >  
<link rel="stylesheet" href="public/admin/css/base.css"  type="text/css">
<!--<link rel="stylesheet" href="public/admin/css/theme/ew/theme.css"  type="text/css">-->
<link rel="stylesheet" href="public/admin/js/content-strike/content-tools.min.css">

<script>

  (function () {
    function getInternetExplorerVersion() {
      var rv = -1;
      if (navigator.appName == 'Microsoft Internet Explorer')
      {
        var ua = navigator.userAgent;
        var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
          rv = parseFloat(RegExp.$1);
      } else if (navigator.appName == 'Netscape')
      {
        var ua = navigator.userAgent;
        var re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
          rv = parseFloat(RegExp.$1);
      }

      return rv;
    }

    var ieVersion = getInternetExplorerVersion();
    if (ieVersion !== -1 && ieVersion < 11)
    {
      alert("Your browser (IE " + ieVersion + ") is not supported");
      window.location = "http://www.whatbrowser.org/";
    }
  })();

  // ------ //

  EW_APPS = <?= EWCore::read_apps(); ?>;
  EW_ACTIVITIES = <?= EWCore::read_activities() ?>;

</script>

<script src="public/rm/js/webcomponents/webcomponents-lite.min.js"></script>

<script src="public/rm/js/jquery/build.js"></script>     

<script src="public/rm/js/vue/vue.js"></script>

<script src="public/rm/js/x-tag/x-tag.min.js"></script>

<script src="public/rm/js/gsap/TweenLite.min.js"></script>
<script src="public/rm/js/gsap/TimelineLite.min.js"></script>
<script src="public/rm/js/gsap/plugins/CSSPlugin.min.js" defer></script>
<script src="public/rm/js/gsap/easing/EasePack.min.js" defer></script>
<script src="public/rm/js/gsap/jquery.gsap.min.js" defer></script>

<script src="public/admin/js/system/build/build.js"></script>
<script src="public/admin/js/ew-elements/build/build-min.js"></script>

<script src="public/admin/js/lib/sortable.js" defer></script>      
<script src="public/admin/js/lib/bootstrap-datepicker.js" defer></script>
<script src="public/admin/js/lib/autocomplete.js" defer></script>
<script src="public/admin/js/lib/simple-slider.js"></script>
<!--<script src="public/admin/js/lib/floatlabels.min.js" defer></script>-->

<script src="public/admin/js/lib/bootstrap.js" defer></script>
<script src="public/admin/js/content-strike/content-tools.min.js" defer></script>

<script src="public/admin/js/lib/ewscript.js"></script>      
