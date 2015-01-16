<?php
session_start();
require EW_ROOT_DIR . 'core/database_config.php';
//echo "asdasdasd";
$EW = new EWCore("admin/", $_REQUEST);
//EWCore::set_default_locale("admin");

/* if (isset($_POST['username']))
  {
  include "Login.php";
  } */
if (!isset($_SESSION['login']))
{
   include "Login.php";
   //if (!isset($_SESSION['login']))
   //{
   return;
   //}
}

$compId = $_REQUEST['compId'];
$secId = $_REQUEST['_section_name'];
$className = $_REQUEST['className'];
$cmd = $_REQUEST['cmd'];
$compPage = null;
$pageTitle = 'Administration';
/* if (isset($className) && class_exists($className))
  {
  if ($className == "EWCore")
  {
  echo $EW->processRequest();
  return;
  }
  $obj = new $className($className, $_REQUEST);
  if (isset($cmd))
  {
  echo $obj->processRequest();
  return;
  }
  } */
//echo $_app_name;
$sectionTitle = '';
if (!$compId)
{
   $compId = "AppsManagement";
}
//echo EW_APPS_DIR . '/admin/' . $compId . '/' ;
if (class_exists($app_name . "\\" . $compId))
{
   $ccc = $app_name . "\\" . $compId;
   $sc = new $ccc($ccc, $_REQUEST);
   //$compPage = EW_APPS_DIR . '/admin/' . $compId . '/' . $sc->get_index();
   //echo "inja";

   $compPage = EWCore::process_command("admin", $compId, null);
   $temp = json_decode($compPage, true);
   if ($temp["statusCode"] == "404")
   {
      http_response_code(200);
      header('Content-Type: text/html');
      $compPage = "<div class='box box-error'><label class='value'>" . $temp["message"] . "</label></div>";
   }
   $pageTitle = "tr{" . $sc->get_title() . "}";
}

if ($secId)
{
   $result = mysql_query("SELECT * FROM sections WHERE id = '$secId'") or die(mysql_error());
   while ($row = mysql_fetch_array($result))
   {
      $compPage = '../sections/' . $row['root_dir'] . '/admin/index.php';
      $pageTitle = $pageTitle . ' : ' . $row['title'];
      $sectionTitle = ': ' . $row['title'];
   }
}
//define(EW_ROOT_URL, EW_ROOT_URL . ($_language) ? $_language . '/' : '');
?> 
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" href="<?php echo EW_ROOT_URL ?>templates/default/favicon.ico">  
      <title>
         <?php
         echo $pageTitle;
         ?>            
      </title>    
      <base href="<?php echo EW_ROOT_URL ?>">
      <link type="text/css" href="<?php echo EW_ROOT_URL ?>core/css/custom-theme/jquery-ui-1.8.21.custom.css" rel="Stylesheet" />	
      <link type="text/css" href="<?php echo EW_ROOT_URL ?>core/css/bootstrap.css" rel="stylesheet" >  
      <link type="text/css" href="<?php echo EW_ROOT_URL ?>core/css/simple-slider.css" rel="stylesheet" >  
      <link href="<?php echo EW_ROOT_URL ?>templates/default/template.css" rel="stylesheet" type="text/css">
      <script src="<?php echo EW_ROOT_URL ?>core/js/jquery/jquery-2.1.1.min.js"></script>        
      <script src="<?php echo EW_ROOT_URL ?>core/js/jquery/sortable.js"></script>      
      <script src="<?php echo EW_ROOT_URL ?>core/js/bootstrap-datepicker.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/autocomplete.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/floatlabels.min.js" ></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/ewscript.js"></script>      
      <script src="<?php echo EW_ROOT_URL ?>core/js/simple-slider.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/gsap/plugins/CSSPlugin.min.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/gsap/TweenLite.min.js" ></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/gsap/jquery.gsap.min.js"></script>
      <!--<script type="text/javascript" src="<?php echo EW_ROOT_URL ?>core/js/jquery/jquery-ui-1.10.3.custom.js"></script>-->
      <script>
         //var EW = new EverythingWidgets();
         EverythingWidgets.prototype.loadSections = function ()
         {
            $.post('<?php echo EW_ROOT_URL; ?>app-admin/AppsManagement/get_app_sections', {appDir: "admin"}, function (data)
            {
               var items = [];
               $.each(data, function (key, val) {
                  var selected = ("<?php echo ($compId) ?>" == val['className']) ? "selected" : "";
                  items.push('<li class="col-xs-6 col-sm-6 ' + selected + '"><a href="<?php echo EW_ROOT_URL; ?>app-admin/index.php?compId=' + val['className'] + '"><label>' + val['title'] + '</label><p class="hidden-xs">' + val['description'] + '</p></a></li>');
               });
               $(items.join('')).appendTo("#components-pane ul");
               //alert($(items.join('')).html());
            }, "json");
         };

         $.fn.textWidth = function () {
            var html_org = $(this).html();
            var html_calc = '<span style="white-space:nowrap">' + html_org + '</span>';
            $(this).html(html_calc);
            var width = $(this).find('span:first').width();
            $(this).html(html_org);
            return width;
         };

         $.fn.comeIn = function (t) {
            if (!this.is(":visible") || this.css("visibility") != "visible")
            {
               var orgClass = "";
               $(this).stop(true, true);
               //$(this).removeClass("btn-hide");
               if ($(this).prop("class"))
                  orgClass = $(this).prop("class").replace('btn-hide', '');
               $(this).addClass("btn-hide");
               var pl = $(this).css("padding-left");
               var pr = $(this).css("padding-right");
               var ml = $(this).css("margin-left");
               var mr = $(this).css("margin-right");
               $(this).css({
                  display: ""
               });

               var w = $(this).outerWidth();
               if ($(this).data("last-width"))
                  w = $(this).data("last-width");
               //$(this).css({transform: "scale(.7)", opacity: "0",visibility:"visible"});
               /*$(this).css({
                display: "block",
                visibility: "visible",
                paddingLeft: "0px",
                paddingRight: "0px",
                marginLeft: "0px",
                marginRight: "0px",
                width: "0px"
                });
                //var w= $(this).width();
                //$(this).addClass("transition scale");
                //$(this).hide();
                $(this).animate({
                width: w,
                paddingLeft: pl,
                paddingRight: pr,
                marginLeft: ml,
                marginRight: mr
                },
                t);*/
               //alert(orgClass);
               $(this).animate({className: orgClass}, t, "Power3.easeOut");
            }
            //$(this).css({transform: "scale(1)", opacity: "1"});
            //this.show();
            return this;
            // Open popup code.
         };

         $.fn.comeOut = function (t) {
            if (!this.hasClass("btn-hide"))
            {
               $(this).stop(true, true);
               var pl = $(this).css("padding-left");
               var pr = $(this).css("padding-right");
               var ml = $(this).css("margin-left");
               var mr = $(this).css("margin-right");
               var w = $(this).outerWidth();
               var mw = $(this).css("min-width");
               //$(this).addClass("btn-hide");
               /*$(this).animate({
                minWidth: 0,
                width: 0,
                paddingLeft: 0,
                paddingRight: 0,
                marginLeft: 0,
                marginRight: 0,
                opacity: 0
                },
                t, "linear", function ()
                {
                $(this).data("last-width", w);
                $(this).css({
                display: "none",
                visibility: "",
                minWidth: mw,
                width: w,
                paddingLeft: pl,
                paddingRight: pr,
                marginLeft: ml,
                marginRight: mr,
                opacity: ""
                });
                });*/
               $(this).animate({className: $(this).prop("class") + " btn-hide"}, t, "Power2.easeIn", function () {
                  $(this).hide();
               });
            }
            //$(this).css({transform: "scale(.7)", opacity: "0"});
            return this;
            // Close popup code.
         };

         $.fn.loadingText = function (t) {
            return this;
         };

         ew_plugins.linkChooser = function (options)
         {
            var defaults = {
               callbackName: "function-reference"
            };
            var linkChooserDialog;
            function LinkChooser(element, options)
            {
               var base = this;
               var $element = $(element);
               defaults.callback = function (link)
               {
                  $element.val(link).change();
                  linkChooserDialog.trigger("close");
               };
               this.$element = $(element);
               var settings = $.extend({
               }, defaults, options);

               //$element.EW().putInWrapper();
               //var wrapper = this.$element.parent();
               if (linkChooserDialog)
                  linkChooserDialog.remove();

               $element.EW().inputButton({
                  title: '<i class="link-icon"></i>',
                  label: 'tr{Link Chooser}',
                  onClick: function (e)
                  {
                     //if (!linkChooserDialog)
                     //{
                     linkChooserDialog = EW.createModal();
                     $.post("<?php echo EW_DIR ?>app-admin/ContentManagement/file-chooser.php", {
                        callback: settings.callbackName,
                        data: $element.val()
                     },
                     function (data) {
                        var functionRefrence = $("<div style='display:none;' id='function-reference'></div>");
                        functionRefrence.data("callback", settings.callback);
                        e = $(data);
                        e.append(functionRefrence);
                        linkChooserDialog.html(e);
                     });
                     //}
                     //linkChooserDialog.open();
                  }
               });
            }
            return this.each(function () {
               //alert($.data(this, "ew-plugin-link-chooser"));
               if (!$.data(this, "ew_plugin_link_chooser")) {
                  $.data(this, "ew_plugin_link_chooser", true);
                  new LinkChooser(this, options);
               }
            });
         };

         ew_plugins.imageChooser = function (options)
         {
            var ACTIVE_PLUGIN_ATTR = "data-active-plugin-image-chooser";
            var defaults = {
               callbackName: "function-reference"
            };
            var imageChooserDialog;
            function ImageChooser(element, options)
            {
               var base = this;
               var $element = $(element);
               defaults.callback = function (link)
               {
                  //alert($element.html());
                  $element.val(link).change();
                  imageChooserDialog.dispose();
               };
               //this.$element = $(element);
               var settings = $.extend({
               }, defaults, options);

               if (!$element.parent().attr("data-element-wrapper"))
                  $element.wrap('<div class="element-wrapper" style="position:relative;padding-bottom:30px;" data-element-wrapper="true"><div style="border:1px dashed #ddd;background-color:#eee;display:block;overflow:hidden;"></div></div>');
               var wrapper = $element.parent().parent();
               if (imageChooserDialog)
                  imageChooserDialog.remove();
               var image = wrapper.find("img");
               var imageChooserBtn;
               // if the plugin has been called after again on same element
               if ($element.attr(ACTIVE_PLUGIN_ATTR))
               {
                  imageChooserBtn = wrapper.find("button.btn-image-chooser");
               }
               // If the plugin has been called for the first time
               else
               {
                  image.attr("src", "asset/images/no-image.png")
                  image.css({
                     border: "none",
                     outline: "none",
                     minHeght: "128px"
                  });
                  //dashed = $("<div style='border:2px dashed #aaa;display:block;overflow:hidden;'></div>");
                  $element.css({
                     display: "block",
                     float: "",
                     margin: "2px auto 2px auto"
                  });
                  imageChooserBtn = $("<button type='button' class='btn btn-xs btn-link btn-link-chooser' >Choose Image</button>");
                  imageChooserBtn.css({
                     position: "absolute",
                     right: "2px",
                     bottom: "2px"
                  });
                  wrapper.prepend(imageChooserBtn);
                  //wrapper.append(dashed);
                  $element.attr(ACTIVE_PLUGIN_ATTR, true);
               }

               imageChooserBtn.click(function ()
               {
                  //if (!imageChooserDialog)
                  {
                     imageChooserDialog = EW.createModal({
                        //closeAction: "hide",
                        autoOpen: false,
                        class: "center-big"
                     });

                     imageChooserDialog.append("<div class='form-content'></div><div class='footer-pane row actions-bar action-bar-items' ></div>");
                     $.post("<?php echo EW_DIR ?>app-admin/ContentManagement/Media.php", {
                        callback: settings.callbackName
                     },
                     function (data) {

                        imageChooserDialog.find(".form-content:first").append(data);
                        imageChooserDialog.prepend("<h1>Media</h1>");
                        /*var functionRefrence = $("<div style='display:none;' id='function-reference'></div>");
                         functionRefrence.data("callback", settings.callback);
                         imageChooserDialog.find("#link-chooser").append(functionRefrence);*/

                        var bSelectPhoto = EW.addAction("Select Photo", function () {
                           EW.setHashParameter("select-photo", true, "Media");
                        }, {
                           display: "none"
                        }).addClass("btn-success");
                        // create handler to track selected
                        var EWhandler = function ()
                        {
                           var url = EW.getHashParameter("absUrl", "Media");
                           if (url)
                              bSelectPhoto.comeIn(300);
                           else
                              bSelectPhoto.comeOut(200);
                           if (EW.getHashParameter("select-photo", "Media"))
                           {
                              EW.setHashParameter("select-photo", null, "Media");
                              imageChooserDialog.dispose();
                              //if (EW.getHashParameter("url", "Media"))
                              $element.attr("src", EW.getHashParameter("absUrl", "Media"));
                              $element.attr("data-filename", EW.getHashParameter("filename", "Media"));
                              $element.attr("data-file-extension", EW.getHashParameter("fileExtension", "Media"));
                           }
                        };
                        EW.addURLHandler(EWhandler, "Media.ImageChooser");
                     });
                  }
                  imageChooserDialog.open();
               });
            }
            return this.each(function () {
               if (!$.data(this, ACTIVE_PLUGIN_ATTR)) {
                  $.data(this, ACTIVE_PLUGIN_ATTR, new ImageChooser(this, options));
               }
            });
         };

         function initPlugins(element)
         {
            var $element = $(element);
            EW.initPlugins($element);

            // Bootstraps Plugins
            // Begin
            $("[data-toggle='tooltip'],[data-tooltip]").tooltip();
            // End

            $element.find("a[rel='ajax']").each(function ()
            {
               var a = $(this);
               if (a.attr("rel") === "ajax")
               {
                  a.click(function (event)
                  {
                     event.preventDefault();
                     var params = a.attr("href").split(",");
                     $.each(params, function (k, v) {
                        if (v)
                        {
                           var kv = v.split("=");
                           EW.setHashParameter(kv[0], kv[1]);
                        }
                     });
                  });
               }
            });
         }

         function initSideBar()
         {
            this.currentTab = null;
            var oldHref = null
            this.setCurrentTab = function (element)
            {
               if (this.currentTab)
               {
                  this.currentTab.removeClass("selected");
                  oldHref = this.currentTab.attr("href")
               }
               if (element)
               {
                  
                  if (element !== this.currentTab)
                  {
                     $("#side-bar-btn").text(element.text());
                     if (element.attr("data-ew-nav") && element.attr("href") != oldHref)
                     {
                        //alert("aaaaaaa");
                        $("#action-bar-items").find("button").remove();
                        $("#main-content").empty();
                        EW.lock($("#main-content"), "");
                        $.post(element.prop("href"), function (data) {
                           //$("#action-bar-items").find("button").remove();
                           //EW.unlock($("#main-content"));
                           $("#main-content").html(data);
                        });
                     }
                  }
               }
               element.addClass("selected");
               this.currentTab = element;
            };
            var base = this;
            if ($("#sidebar").length == 0)
            {
               //$("#side-bar-btn").remove();
            }
            if ($("#components-list").length == 1)
            {
               $("#component-chooser-btn").remove();
            }
            $("#sidebar a, .sidebar a").each(function ()
            {
               var a = $(this);
               if (a.attr("rel") === "ajax")
               {
                  var kv = a.attr("href").split("=");
                  a.click(function (event)
                  {
                     event.preventDefault();
                     if (a.attr("data-ew-nav"))
                     {
                        EW.setHashParameter("nav", a.attr("data-ew-nav"));
                     }
                     else
                     {
                        EW.setHashParameter(kv[0], kv[1]);
                     }
                     
                     base.setCurrentTab(a);
                  });
                  var currentNav = EW.getHashParameter("nav");
                  if (window.location.hash.indexOf(a.attr("href")) != -1 || currentNav === a.attr("data-ew-nav"))
                  {
                     base.setCurrentTab(a);
                  }
                  if (a.attr("data-default") && !EW.getHashParameter(kv[0]))
                  {
                     EW.setHashParameter(kv[0], kv[1]);
                     base.setCurrentTab(a);
                  }
                  /*var defaultLink = EW.getHashParameter(kv[0]);
                   if (window.location.hash.indexOf(a.attr("href")) != -1 || defaultLink === kv[1])
                   {
                   base.setCurrentTab(a);
                   }*/
               }
            });

            // Init nav bar handler
            EW.addURLHandler(function ()
            {
               if (EW.getHashParameter("nav"))
               {
                  base.setCurrentTab($("a[data-ew-nav='" + EW.getHashParameter("nav") + "']"));
               }
            });
         }

         // Plugins which initilize when document is ready
         //var EW = null;
         $(document).ready(function ()
         {
            var hashDetection = new hashHandler();
            EW.activities = <?php echo json_encode(EWCore::read_activities()); ?>;
            console.log(EW.activities);

            // Init EW plugins
            initPlugins(document);

            initSideBar() & EW.loadSections();

            var currentButton = null;
            var buttons = null;
            var currentBtnForm = null;
            $(document).ajaxStart(function (event, data)
            {
               if (event.target.activeElement)
               {
               }
            });

            $(document).ajaxComplete(function (event, data)
            {
            });

            // Notify error if an ajax request fail
            $(document).ajaxError(function (event, data)
            {
               if (EW.customAjaxErrorHandler)
               {
                  EW.customAjaxErrorHandler = false;
                  return;
               }
               //console.log(data);
               $("body").EW().notify({
                  "message": {
                     html: (!data.responseJSON) ? "---ERROR---" : data.responseJSON.message
                  },
                  status: "error",
                  position: "n",
                  delay: "stay"
               }).show();
               EW.unlock($(".glass-pane-lock").parent());
            });

            $('select').selectpicker({
               container: "body"
            });
            //$("#sidebar").hide();
            var sidebar = $("#sidebar");

            sidebar.on("mouseleave", function ()
            {
               sidebar.stop().css({overflowY: "hidden"});
               $("#sidebar.in").stop().animate({className: "sidebar", width: $("#side-bar-btn").outerWidth()}, 360, "Power3.easeOut");
               //$("#sidebar").fadeOut(300);
            });
            sidebar.on("click", function (e)
            {
               e.stopPropagation();
            });

            $("#side-bar-btn").on("click mouseenter focus", function (event)
            {
               //event.preventDefault();
               sidebar.css({maxHeight: $(window).height() - 100});
               $("#sidebar:not(.in)").stop().animate({className: "sidebar in", width: "220px"}, 360, "Power4.easeOut", function () {
                  sidebar.stop().css({overflowY: "auto"});
               });
               //$("#sidebar").stop().fadeIn(300);
               //alert($("#sidebar").html());
               event.stopPropagation();
               $(window).on("click.sidebar", function ()
               {

                  $("#sidebar").trigger("mouseleave");
                  $(window).off("click.sidebar");
               });
            });
            $("#sidebar").prepend($("#side-bar-btn").detach());

            document.addEventListener("DOMNodeInserted", function (event)
            {
               //var $elementJustAdded = $(event.target);
               if (event.target)
               {
                  initPlugins(event.target);
                  /*$elementJustAdded.find("select").selectpicker({
                   container: "body"
                   });*/
               }

               $(".nav.xs-nav-tabs").data("xs-nav-bar-active", function (e) {
                  if ($(e).hasClass("xs-nav-tabs-active"))
                     return;
                  $(e).addClass("xs-nav-tabs-active");
                  var items = $(e).children("li");
                  $(e).data("element-id", $(e).attr("id"));

                  items = $(e).children("li").detach();
                  $(e).prop("id", "xs-nav-" + $(e).data("element-id"));
                  $(e).empty();

                  var dropDown = $("<li></li>").addClass("dropdown active");
                  var a = $("<a id='tabs-btn' data-toggle='tab' href='#'></a>");
                  dropDown.append(a);
                  var ul = $("<ul class='nav nav-pills nav-stacked'></ul>");
                  items.appendTo(ul);
                  ul.hide();
                  dropDown.append(ul);
                  ul.prop("id", $(e).data("element-id"));
                  $(e).append(dropDown);

                  a.popover({
                     animation: false,
                     container: 'body',
                     placement: "bottom",
                     html: true,
                     //trigger: "manual",
                     content: function () {
                        ul.show();
                        a.data("items", ul);
                        return ul;
                     }
                  });

                  a.on('hide.bs.popover', function () {
                     ul.hide();
                     dropDown.append(ul.detach());
                     ul.unbind("click");
                     //ul = a.data("items");
                  });

                  a.on('shown.bs.popover', function () {
                     ul.bind("click", function () {
                        a.popover("toggle");
                        ul.unbind("click");
                     });
                     a.data("items", ul);
                  });
               });
               $(".nav.xs-nav-tabs").data("xs-nav-bar-deactive", function (e) {
                  if ($(e).hasClass("xs-nav-tabs-active"))
                  {
                     $(e).removeClass("xs-nav-tabs-active");
                     $(e).addClass($(e).data("removed-class"));
                     $(e).prop("id", $(e).data("element-id"));
                     $(e).find("#tabs-btn").popover("destroy");
                     var items = $(e).find("ul.nav-pills").children("li").detach();
                     //var items = $(e).find("#tabs-btn").data("items");
                     $(e).empty();
                     $(e).append(items);

                     $(".popover-content:empty").parent().remove();
                  }
               });
              /* if ($(window).width() < 768)
               {
                  $(window).trigger("ew.screen.xs");
               }
               else if ($(window).width() >= 768 && $(window).width() < 992)
               {
                  $(window).trigger("ew.screen.sm");
               }
               else if ($(window).width() >= 992 && $(window).width() < 1360)
               {
                  $(window).trigger("ew.screen.md");
               }
               else if ($(window).width() >= 1360)
               {
                  $(window).trigger("ew.screen.lg");
               }*/
               //$(window).resize();
               //alert("ass");
            });
         });

         $(window).on("ew.screen.xs", function ()
         {
            $(".nav.xs-nav-tabs:not(.xs-nav-tabs-active)").each(function (i) {
               $(this).data("xs-nav-bar-active")(this);
            });

         });
         $(window).on("ew.screen.sm ew.screen.md ew.screen.lg", function ()
         {
            $(".nav.xs-nav-tabs-active").each(function (i) {
               $(this).data("xs-nav-bar-deactive")(this);
            });
            $(".tab-pane-xs.tab-pane").each(function (i)
            {
            });
         });



      </script>
   </head>
   <body class="Admin <?php echo EWCore::get_language_dir($_REQUEST["_language"]) ?>" onload="">

      <div id="components-pane" class="col-xs-12" >
         <ul class="component row">
            <!--<li class="col-xs-6 col-sm-6">
              <a href="index.php">
                <label style="text-align:center;">HOME</label>
                <p class="hidden-xs" style="text-align:center;">Administration</p>
              </a>
            </li>-->
         </ul>
      </div>

      <div id="base-pane" class="container">      


         <script >

         </script>
         <?php
         if ($compPage)
         {
            ?>
            <div id="component-content" class="row" style="">

               <div id="header-pane" class="col-xs-12">
                  <div  class="row bar">
                     <button type="button" id="component-chooser-btn" class="btn btn-text component-chooser comp-btn" id="" onclick="EW.showAllComponents();" >
                        <?php
                        echo $pageTitle;
                        ?>
                     </button>                     
                     <div  class="col-xs-2 col-sm-2 col-md-2 col-lg-1 pull-right">
                        <?php
                        if ($_SESSION['login'])
                        {
                           ?>
                           <a class="ExitBtn" href="./app-admin/logout.php" ></a>    
                           <?php
                        }
                        ?>
                     </div>
                  </div>
                  <div  class="row bar">
                     <button class="btn sidebar comp-btn" id="side-bar-btn" >  
                     </button>   
                     <!--<a id="title-big" class="title hidden-xs" href="./app-admin/?compId=<?php echo $compId ?>">

                     </a>-->

                     <div class="action-pane" >
                        <div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                           <div id="action-bar-items" class="actions-bar action-bar-items" style="display:block;float:none;">
                           </div>
                        </div>

                     </div>
                  </div>


               </div>
               <?php
               echo ($compPage);
               ?>
            </div>
            <?php
         }
         else
         {
            $_REQUEST["appRootDir"] = "admin/";
            $EW = new EWCore("admin/", $_REQUEST);
            $sections = $EW->get_sections();
            $sections = json_decode($sections);
            ?>
            <div id="main-content" class="full texture squre no-action-bar">
               <ul id="components-list" class="component row">
                  <?php
                  foreach ($sections as $sect)
                  {
                     ?>
                     <li class="col-xs-6 col-sm-6">
                        <a href="./app-admin?compId=<?php echo $sect->className ?>">
                           <div>
                              <label><?php echo $sect->title ?></label>
                              <p class="hidden-xs">
                                 <?php echo $sect->description ?>
                              </p>
                           </div>
                        </a>
                     </li>
                     <?php
                  }
                  ?>
               </ul>
            </div>
            <?php
         }
         ?>
      </div>

      <div id="notifications-panel"></div>   
      <script src="<?php echo EW_ROOT_URL ?>core/js/bootstrap.min.js" ></script>

   </body>
</html>