<?= ew\ResourceUtility::load_js_as_tag('admin/html/dashboard/app.service.js', [], TRUE) ?>
<?= ew\ResourceUtility::load_js_as_tag('admin/html/dashboard/app.behaviors.js', [], TRUE) ?>
<?= ew\ResourceUtility::load_js_as_tag('admin/html/dashboard/ui-components.js', [], TRUE) ?>
<script>
  window.addEventListener('load', function () {
    System.entity('stage/init-ui-components').call();

    var appBarComponent = System.entity('ui/app-bar');
    var appsComponent = System.entity('ui/apps');

    $.fn.textWidth = function () {
      var html_org = $(this).html();
      var html_calc = '<span style="white-space:nowrap">' + html_org + '</span>';
      $(this).html(html_calc);
      var width = $(this).find('span:first').width();
      $(this).html(html_org);
      return width;
    };

    $.fn.comeIn = function (dur) {
      if (!this.is(":visible") || this.css("visibility") !== "visible") {
        var orgClass = "";
        this.stop(true, true);

        if (this.prop("class")) {
          orgClass = this.prop("class").replace('btn-hide', '');
        }

        this.addClass("btn-hide").css({
          display: ""
        });

        this.animate({
          className: orgClass
        }, dur || 300, "Power2.easeInOut");
      }

      return this;
    };

    $.fn.comeOut = function (dur) {
      if (!this.hasClass("btn-hide")) {
        this.stop(true, true).animate({
          className: this.prop("class") + " btn-hide"
        }, dur || 300, "Power2.easeInOut", function () {
          this.hide();
        });
      }

      return this;
    };

    $.fn.loadingText = function (t) {
      return this;
    };

    ew_plugins.linkChooser = function (options) {
      var defaults = {
        callbackName: "function-reference"
      };
      var linkChooserDialog;
      function LinkChooser(element, options) {
        //var base = this;
        var $element = $(element);
        defaults.callback = function (link) {
          $element.val(link).change();
          linkChooserDialog.trigger("close");
        };
        //this.$element = $(element);
        var settings = $.extend({
        }, defaults, options);
        //$element.EW().putInWrapper();
        //var wrapper = this.$element.parent();
        if (linkChooserDialog)
          linkChooserDialog.remove();
        $element.EW().inputButton({
          title: '<i class="link-icon"></i>',
          label: 'tr{Link Chooser}',
          class: "btn-default",
          onClick: function (e) {
            linkChooserDialog = EW.createModal({
              class: "center slim"
            });
            $.post('html/admin/content-management/link-chooser/component.php', {
              callback: settings.callbackName,
              data: $element.val(),
              contentType: $element.data("content-type") || "all"
            }, function (data) {
              var functionRefrence = $("<div style='display:none;' id='function-reference'></div>");
              functionRefrence.data("callback", settings.callback);
              e = $(data);
              e.append(functionRefrence);
              linkChooserDialog.html(e);
            });
          }
        });
      }

      return this.each(function () {
        if (!$.data(this, "ew_plugin_link_chooser")) {
          $.data(this, "ew_plugin_link_chooser", true);
          new LinkChooser(this, options);
        }
      });
    };

    ew_plugins.imageChooser = function (options) {
      var ACTIVE_PLUGIN_ATTR = "data-active-plugin-image-chooser";
      var defaults = {
        callbackName: "function-reference"
      };
      var imageChooserDialog;
      function ImageChooser(element, options) {
        var base = this;
        var $element = $(element);
        $element.off("change.image-chooser");
        $element.on("change.image-chooser", function () {
          image.attr("src", $element.val() || "html/admin/content-management/media/no-image.png");
        });

        defaults.callback = function (link) {
          imageChooserDialog.dispose();
        };

        var settings = $.extend({
        }, defaults, options);
        if (!$element.parent().attr("data-element-wrapper"))
          $element.wrap('<div class="element-wrapper" style="position:relative;padding-bottom:30px;" data-element-wrapper="true"><div style="padding:5px 0;border:2px dashed #aaa;background-color:#fff;display:block;overflow:hidden;" data-element-wrapper="true"></div></div>');
        $element.attr("type", "hidden");
        var wrapper = $element.parent().parent();
        if (imageChooserDialog)
          imageChooserDialog.remove();
        var image = wrapper.find("img");
        if (image.length <= 0) {
          image = $("<img>");
          //console.log(image);
          wrapper.find("div").append(image);
        }

        image.css("max-height", $element.css("max-height"));
        var imageChooserBtn;
        // if the plugin has been called later again on same element
        if ($element.attr(ACTIVE_PLUGIN_ATTR)) {
          imageChooserBtn = wrapper.find("button.btn-image-chooser");
        }
        // If the plugin has been called for the first time
        else {
          image.attr("src", $element.val() || "asset/images/no-image.png");
          image.css({
            border: "none",
            outline: "none",
            minHeght: "128px",
            maxWidth: "720px",
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
          wrapper.append(imageChooserBtn);
          $element.attr(ACTIVE_PLUGIN_ATTR, true);
        }

        imageChooserBtn.click(function () {
          imageChooserDialog = EW.createModal({
            autoOpen: false,
            class: "center"
          });

          System.loadModule({
            //$.post("html/admin/content-management/link-chooser-media.php", {
            id: "forms/media-chooser",
            url: "html/admin/content-management/link-chooser/link-chooser-media.php",
            params: {
              callback: settings.callbackName
            }
          }, function (module) {
            //imageChooserDialog.find(".form-content:first").append(data);
            //imageChooserDialog.prepend("<div class='header-pane row'><h1 class='form-title'>Media</h1></div>");
            imageChooserDialog.html(module.html);
            imageChooserDialog[0].selectMedia = function (image) {
              $element.val(image.src).change();
              imageChooserDialog.dispose();
            };
          });

          imageChooserDialog.open();
        });
      }

      return this.each(function () {
        if (!$.data(this, ACTIVE_PLUGIN_ATTR)) {
          $.data(this, ACTIVE_PLUGIN_ATTR, new ImageChooser(this, options));
        }
      });
    };

    function initPlugins(element) {
      if (!element.innerHTML && element.nodeName.toLowerCase() !== 'input' &&
              element.nodeName.toLowerCase() !== 'textarea') {
        return;
      }

      EW.initPlugins($(element));
    }

    var mouseInNavMenu = false,
            enterOnLink = false,
            currentSectionIndex = null;

    System.ui.body = $("body")[0];
    System.ui.components = {
      appMainActions: $("#app-main-actions"),
      mainContent: $("#main-content"),
      body: $("body"),
      document: $(document),
      navigationMenu: $("#navigation-menu"),
      appsMenu: $("#apps-menu"),
      sectionsMenu: $("#sections-menu"),
      sectionsMenuList: $("#sections-menu-list")
    };

    System.ui.behaviors.highlightAppSection = function (index, section) {
      currentSectionIndex = index;

      if (EW.selectedSection) {
        System.ui.utility.removeClass(EW.selectedSection, "selected");
      }

      EW.selectedSection = section;
      System.ui.utility.addClass(EW.selectedSection, "selected");
    };

    System.ui.components.sectionsMenuList[0].onSetData = function (data) {
      if (data.length) {
        if (mouseInNavMenu) {
          TweenLite.to(System.ui.components.sectionsMenu[0], .3, {
            className: "sections-menu in",
            ease: "Power2.easeInOut"
          });
        }
      } else {
        //alert(System.UI.components.sectionsMenu.height());
        System.ui.components.sectionsMenu.css("height", System.ui.components.sectionsMenu.height());
        TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
          className: "sections-menu out",
          height: "94px",
          ease: "Power2.easeInOut",
          onComplete: function () {
            System.ui.components.sectionsMenu.css("height", "");
          }
        });
      }
    };

    System.ui.components.sectionsMenuList[0].addEventListener('item-selected', function (event) {
      if (event.detail.data.id === appBarComponent.currentApp + '/' + appBarComponent.currentSection) {
        return;
      }

      System.setHashParameters({
        app: event.detail.data.id
      });
    });

    System.ui.components.navigationMenu.on("mouseenter touchstart", function (e) {
      if (mouseInNavMenu)
        return;

      mouseInNavMenu = true;
      System.ui.components.navigationMenu.addClass("expand");

      if (System.ui.components.sectionsMenuList[0].data.length) {
        if (!enterOnLink) {
          System.ui.components.sectionsMenu[0].style.top = System.ui.components.appsMenu.find(".apps-menu-link.selected")[0].getBoundingClientRect().top + "px";
        }

        TweenLite.to(System.ui.components.sectionsMenu[0], .3, {
          className: "sections-menu in",
          ease: "Power2.easeInOut"
        });
      }
    });

    var moveAnim = null;

    System.ui.components.appsMenu.on('mouseenter touchstart', "a", function (e) {
      var app = e.currentTarget.dataset.app;
      EW.hoverApp = "system/" + app;

      var sections = System.modules["system/" + app] ? System.modules["system/" + app].data.sections : [];

      if (System.ui.components.sectionsMenuList[0].data !== sections) {
        System.ui.components.sectionsMenuList[0].data = sections;
      }

      if (EW.oldApp === app) {
        System.ui.behaviors.highlightAppSection(currentSectionIndex, System.ui.components.sectionsMenuList[0].links[currentSectionIndex]);
      }

      if (!mouseInNavMenu) {
        System.ui.components.sectionsMenu[0].style.top = e.currentTarget.getBoundingClientRect().top + "px";
        enterOnLink = true;
        return;
      }

      moveAnim = TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
        top: e.currentTarget.getBoundingClientRect().top
      });
    });

    System.ui.components.navigationMenu.on('click', function (event) {
      if (event.target === System.ui.components.navigationMenu[0]) {
        System.ui.components.navigationMenu.removeClass("expand");
        TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
          className: "sections-menu",
          marginTop: 0,
          ease: "Power2.easeInOut",
          onComplete: function () {
            if (!System.services.app_service.loading_app && currentSectionIndex !== System.ui.components.sectionsMenuList[0].value) {
              System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;
              System.ui.behaviors.highlightAppSection(currentSectionIndex, EW.selectedSection);
            }
          }
        });
      }
    });

    System.ui.components.navigationMenu.on('mouseleave', function () {
      mouseInNavMenu = false;
      enterOnLink = false;

      System.ui.components.navigationMenu.removeClass("expand");
      TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
        className: "sections-menu",
        marginTop: 0,
        ease: "Power2.easeInOut",
        onComplete: function () {
          if (!System.services.app_service.loading_app && currentSectionIndex !== System.ui.components.sectionsMenuList[0].value) {
            System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;
            System.ui.behaviors.highlightAppSection(currentSectionIndex, EW.selectedSection);
          }
        }
      });
    });

    // Hash handler for activities
    new hashHandler();

    EW.activities = <?php echo EWCore::read_activities(); ?>;
    EW.oldApp = null;
    EW.apps = {};

    // Init EW plugins
    initPlugins(document);

    var installModules = <?= EWCore::read_apps_sections(); ?>;

    installModules.forEach(function (item) {
      item.file = 'index.php';
      EW.apps[item.id] = item;
    });

    System.init(installModules);
    System.app.on('app', System.services.app_service.load);
    System.start();

    appsComponent.apps = installModules;
    appBarComponent.selectedTab = System.getHashParam('app');

    if (!System.getHashParam('app')) {
      System.setHashParameters({
        app: 'content-management'
      }, true);
    }

    var $document = $(document);

    // Notify error if an ajax request fail
    $document.ajaxError(function (event, data, status) {
      // Added to ignore aborted request and don't show them as a error
      if (data && data.statusText === 'abort') {
        return;
      }

      if (EW.customAjaxErrorHandler) {
        EW.customAjaxErrorHandler = false;
        return;
      }

      try {
        var errorsList = '<ul>';
        $.each(data.responseJSON.reason, function (current, i) {
          errorsList += '<li><h4>' + current + '</h4><p>' + i.join() + '</p></li>';
        });
        errorsList += '</ul>';
      } catch (e) {
        console.log("ajaxError: ", e);
        console.log(data);
        console.log(e.stack);
      }

      System.ui.components.body.EW().notify({
        message: {
          html: (!data.responseJSON) ? "---ERROR---" : data.responseJSON.message + errorsList
        },
        status: "error",
        position: "n",
        delay: "stay"
      }).show();
    });

    $('select').selectpicker({
      container: "body"
    });

    // select the target node
//    var target = document.getElementById('some-id');
//
// create an observer instance
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === 'childList' &&
                mutation.addedNodes.length &&
                mutation.addedNodes[0].nodeType === Node.ELEMENT_NODE) {
          initPlugins(mutation.target);
        }
      });
    });

// pass in the target node, as well as the observer options
    observer.observe(document.body, {
      attributes: false,
      childList: true,
      characterData: false,
      subtree: true
    });
  });
</script>