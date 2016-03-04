<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="<?php echo EW_ROOT_URL ?>">
<link rel="shortcut icon" href="~admin/public/css/images/favicon.ico">  
<link rel="stylesheet" href="~rm/public/css/bootstrap.css" >  
<link rel="stylesheet" href="~admin/public/css/simple-slider.css"  >  
<link rel="stylesheet" href="~admin/public/css/base.css"  type="text/css">
<link rel="stylesheet" href="~admin/public/css/theme/ew/theme.css"  type="text/css">
<link rel="stylesheet" href="~admin/public/js/ContentStrike/content-tools.min.css">

<script>

  (function () {
    function getInternetExplorerVersion()
    {
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

</script>
<script src="~rm/public/js/ui-utility/ui-utility.js" ></script>   
<script src="https://code.jquery.com/jquery-2.1.0.min.js"></script>       
<script src="~rm/public/js/gsap/TweenLite.min.js"></script>
<script src="~rm/public/js/gsap/TimelineLite.min.js"></script>
<script src="~admin/public/js/lib/system.js"></script>  
<script src="~admin/public/js/lib/system-module.js"></script>  
<script src="~admin/public/js/lib/system-domain.js"></script>  

<script src="~rm/public/js/gsap/plugins/CSSPlugin.min.js" defer></script>
<script src="~rm/public/js/gsap/easing/EasePack.min.js" defer></script>
<script src="~rm/public/js/gsap/jquery.gsap.min.js" defer></script>

<script src="~rm/public/js/x-tag/x-tag-core.min.js" defer></script>
<script src="~admin/public/js/lib/system-tags.js" defer></script>

<script src="~admin/public/js/lib/sortable.js" defer></script>      
<script src="~admin/public/js/lib/bootstrap-datepicker.js" defer></script>
<script src="~admin/public/js/lib/autocomplete.js" defer></script>
<script src="~admin/public/js/lib/floatlabels.min.js" defer></script>
<script src="~admin/public/js/lib/ewscript.js"></script>      
<script src="~admin/public/js/lib/simple-slider.js"></script>

<script src="~admin/public/js/lib/system-ui.js" defer></script>   
<script src="~admin/public/js/lib/bootstrap.js" defer></script>
<script src="~admin/public/js/ContentStrike/content-tools.js" defer></script>

