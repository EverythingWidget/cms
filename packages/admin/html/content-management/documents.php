<system-ui-view module="content-management/documents" name="folders-card" class="block-row">
  <div id="folders-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
    <div  class='card-header'>
      <div class="card-title-action"></div>

      <div class="card-title-action-right"></div>

      <h1>
        tr{Contents}
      </h1>
    </div>

    <div class='card-content top-devider'>
      <system-list id="folders-list" class="mt">
        <div tabindex='1' class='content-item folder' data-content-id='{{id}}'>
          <span></span>
          <p>{{title}}</p>
          <p class='date'>{{round_date_created}}</p>
        </div>
      </system-list>

      <system-list id="articles-list" class="mt">
        <div tabindex='1' class='content-item article' data-content-id='{{id}}'>
          <span></span>
          <p>{{title}}</p>
          <p class='date'>{{round_date_created}}</p>
        </div>
      </system-list>
    </div>
  </div>
</system-ui-view>

<script data-name="documents">
  (function () {
    function DocumentsComponent(module) {
      var component = this;
      this.module = module;
      this.states = {};
      this.ui = {
        components: {},
        behaviors: {}
      };

      this.module.type = "app-section";

      this.module.bind('init', function () {
        component.init();
      });

      this.module.bind('start', function () {
        component.start();
      });
    }

    DocumentsComponent.prototype.defineStateHandlers = function (states) {
      var component = this;

      // you can use either states.<state> or states['<state>']
      states.article = function (full, id) {
        if (!id) {
          System.ui.utility.removeClass(component.currentItem, 'selected');
        }
      };

      states.folder = function (full, id, command) {
        if (!id) {
          System.ui.utility.removeClass(component.currentItem, 'selected');
        }
      };

      states.dir = function (full, id, list) {
        if (!id) {
          id = 0;
        } else {
          component.bNewFolder.comeIn();
        }

        if (list) {
          if (component.parentId !== parseInt(id)) {
            component.preParentId = component.parentId;
            component.parentId = parseInt(id);

            component.listDocuments();
          }

          if (id === "0") {
            component.upAction.comeOut(300);
            component.bSee.comeOut();
            component.deleteFolderActivity.comeOut();
          }

          if (id > 0) {
            component.upAction.comeIn(300);
            component.bSee.comeIn();
            component.deleteFolderActivity.comeIn();
          }
        }
      };

      states.component = function (full, name) {
        if (full === 'forms/test-form') {
          var modal = EW.createModal({
            onClose: function () {
              component.module.setParam('component', null);
            }
          });
          component.module.loadModule({
            id: 'forms/test-form',
            url: 'http://127.0.0.1:8000/EverythingWidget/~admin/html/content-management/test-file.php'
          }, function (module) {
            modal.html(module.html);
            module.start();
          });
        }
      }
    };

    DocumentsComponent.prototype.init = function () {
      var component = this;

      var templates = System.ui.templates['system/content-management/documents'];
      this.ui.components.folders_card = $(templates["folders-card"]);
      this.ui.components.folders_card_title = this.ui.components.folders_card.find(".card-header h1");
      this.ui.components.folders_card_title_action_right = this.ui.components.folders_card.find(".card-title-action-right");
      this.ui.components.folders_list = this.ui.components.folders_card.find("#folders-list");
      this.ui.components.articles_list = this.ui.components.folders_card.find("#articles-list");

      this.upAction = EW.addActionButton({
        text: "",
        class: "btn-text icon-back btn-circle",
        handler: $.proxy(this.preCategory, this),
        parent: this.ui.components.folders_card.find(".card-title-action")
      });

      this.seeFolderActivity = EW.getActivity({
        activity: "admin/html/content-management/folder-form.php_see",
        modal: {
          class: "center properties"
        },
        onDone: function () {
          System.setHashParameters({
            folderId: null,
            articleId: null
          });
        }
      });

      this.seeArticleActivity = EW.getActivity({
        activity: "admin/html/content-management/article-form.php_see",
        onDone: function () {
          System.setHashParameters({
            folderId: null,
            articleId: null
          });
        }
      });

      if (this.seeArticleActivity || this.seeFolderActivity)
        this.bSee = EW.addActionButton({
          text: "",
          class: "btn-text btn-circle btn-default icon-edit",
          handler: $.proxy(this.seeDetails, this),
          parent: this.ui.components.folders_card_title_action_right
        }).hide();
      else
        this.bSee = $();

      this.deleteFolderActivity = EW.addActivity({
        title: "",
        defaultClass: "btn-text btn-circle icon-delete btn-danger",
        activity: "admin/api/content-management/delete-folder",
        parent: this.ui.components.folders_card_title_action_right,
        parameters: function () {
          if (!confirm("tr{Are you sure of deleting this folder?}")) {
            return false;
          }

          return {
            id: component.parentId
          };
        },
        onDone: function (response) {
          $("body").EW().notify(response).show();
          component.preCategory();
        }
      }).hide();

      this.defineStateHandlers(this.states);
      System.Util.installModuleStateHandlers(this.module, this.states);
    };

    DocumentsComponent.prototype.start = function () {
      var component = this;
      this.parentId = null;
      this.folderId = 0;
      this.articleId = 0;
      this.upParentId = 0;
      this.currentItem = null;

      this.ui.components.folders_list.empty();
      this.ui.components.articles_list.empty();

      //this.ui.components.folders_card[0].hide();

      this.bNewFolder = EW.addActivity({
        title: "tr{New Folder}",
        //class: "btn-text btn-primary",
        activity: "admin/html/content-management/folder-form.php",
        parent: System.UI.components.mainFloatMenu,
        hash: function (hash) {
          hash.parent = component.parentId;
        },
        onDone: function (hash) {
          hash.parent = null;
          hash.folderId = null;
        }
      }).hide();

      this.bNewFile = EW.addActivity({
        title: "tr{New Article}",
        //class: "btn-text btn-primary",
        activity: "admin/html/content-management/article-form.php_new",
        parent: System.UI.components.mainFloatMenu,
        hash: function (hash) {
          hash.parent = component.parentId;
        },
        onDone: function (hash) {
          hash.articleId = null;
          hash.parent = null;
        }
      }).hide();

      this.testBtn = EW.addActionButton({
        text: "tr{test form}",
        //class: "btn-text btn-primary",
        parent: System.UI.components.mainFloatMenu,
        handler: function () {
          component.module.setParam('component', 'forms/test-form');
        }
      });

      $(document).off("article-list");
      $(document).on("article-list.refresh", function (e, eventData) {
        component.listDocuments();
        if (eventData) {
          if (eventData.data.type === "article") {
            EW.setHashParameters({
              folderId: null,
              articleId: eventData.data.id
            }, "document");
          }

          if (eventData.data.type === "folder") {
            EW.setHashParameters({
              folderId: eventData.data.id,
              articleId: null
            }, "document");
          }
        }
      });

      this.bNewFile.comeIn();
      this.bNewFolder.comeIn();

      this.module.setParamIfNull("dir", "0/list");

      this.ui.components.folders_list.off('click').on('click', '.folder', function (e) {
        System.setHashParameters({
          article: null,
          folder: e.currentTarget.getAttribute('data-content-id')
        });

        component.currentItem = System.ui.behaviors.selectElementOnly(e.currentTarget, component.currentItem);
      });

      this.ui.components.folders_list.off('dblclick').on('dblclick', '.folder', function (e) {
        component.module.setParam("dir", e.currentTarget.getAttribute('data-content-id') + "/list");
      });


      this.ui.components.articles_list.off('click').on('click', '.article', function (e) {
        component.module.setParam('folder', null);
        component.module.setParam('article', e.currentTarget.getAttribute('data-content-id'));
        component.currentItem = System.ui.behaviors.selectElementOnly(e.currentTarget, component.currentItem);
      });

      this.ui.components.articles_list.off('dblclick').on('dblclick', '.article', function (e) {
        component.seeArticleActivity({
          articleId: e.currentTarget.getAttribute('data-content-id')
        });
      });
    };

    DocumentsComponent.prototype.preCategory = function () {
      this.currentItem = null;
      this.module.setParam("dir", this.upParentId + "/list");
    };

    DocumentsComponent.prototype.seeDetails = function () {
      var tFolderId = System.getHashParam("folder");
      var tArticleId = System.getHashParam("article");
      EW.activeElement = this.ui.components.folders_card.find(".card-header");
      if (this.parentId) {
        this.folderId = tFolderId;
        this.seeFolderActivity({
          folderId: this.parentId
        });
      } else if (tArticleId) {
        this.articleId = tArticleId;
        this.seeArticleActivity({
          articleId: tArticleId
        });
      }
    };

    DocumentsComponent.prototype.listDocuments = function () {
      var component = this,
              pId = 0,
              hasNode = false,
              articlesLoaded = false,
              foldersLoaded = false,
              currentSelected = System.getHashParam("article") || System.getHashParam("folder");

      var loader = $("<div class='loader top'></div>");
      this.ui.components.folders_card.find(".card-content").append(loader);

      var foldersElements = [];
      System.addActiveRequest($.get('~admin/api/content-management/contents-folders', {
        parent_id: component.parentId
      }, function (response) {
        component.ui.components.folders_card_title.text(response.parent.title || "tr{Contents}");

        if (response.data[0]) {
          component.upParentId = response.data[0].up_parent_id;
        }

//        var temp = null;
//        $.each(response.data, function (index, element) {
//          pId = element.up_parent_id;
//          hasNode = true;
//          //temp = component.createFolderElement(element.title, element.round_date_created, element.id, element);
//          /*if (element.id == folder) {
//           temp.addClass("selected");
//           _this.currentItem = temp[0];
//           }*/
//          foldersElements.push(element);
//        });
//
//        if (hasNode) {
//          component.upParentId = pId;
//        }
        foldersElements = response.data;

        foldersLoaded = true;
        done();
      }, "json"));


      var articlesElements = [];
      System.addActiveRequest($.get('~admin/api/content-management/contents-articles', {
        parent_id: component.parentId
      }, function (response) {
        if (response.data[0]) {
          component.upParentId = response.data[0].up_parent_id;
        }

        articlesElements = response.data;

        articlesLoaded = true;
        done();
      }, "json"));

      var done = function () {
        if (!articlesLoaded || !foldersLoaded) {
          return;
        }

        var startPoint = (component.currentItem) ?
                component.currentItem.getBoundingClientRect() : component.upAction[0].getBoundingClientRect();

        System.UI.Animation.blastTo({
          fromPoint: startPoint,
          to: component.ui.components.folders_card[0],
          area: component.ui.components.folders_card.find(".card-content")[0],
          time: .5,
          fade: .4,
          color: "#eee",
          onComplete: function () {
            component.ui.components.folders_list[0].data = foldersElements;
            component.ui.components.articles_list[0].data = articlesElements;
            var item = document.querySelector('[data-content-id="' + currentSelected + '"]');
            if (item) {
              component.currentItem = System.ui.behaviors.selectElementOnly(item, component.currentItem);
            }
            loader.remove();
          }
        });
      };
    };

    var DocumentStatesManager = function () {
      new DocumentsComponent(this);
    };

    System.state("content-management/documents", DocumentStatesManager);
  }());
</script>
