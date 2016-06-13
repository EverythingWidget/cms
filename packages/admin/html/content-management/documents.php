<system-ui-view module="content-management/documents" name="folders-card" class="card card-big center-block z-index-1">
  <div  class='card-header'
        data-content-id='{{ upParentId }}'
        v-on:drop="moveItem"
        v-on:dragover="isAllowed">
    <div class="card-title-action">
      <button type="button" class="btn btn-text icon-back btn-circle"
              transition="slide"              
              v-if="parentId"
              v-on:click="goUp()"></button>
    </div>

    <div class="card-title-action-right"></div>

    <h1> {{ card_title }} </h1>
  </div>

  <div class='card-content'>
    <div id="folders-list" class="mt">
      <div v-for="folder in folders" tabindex='1' class='content-item folder' data-content-id='{{ folder.id }}'
           v-on:drop="moveItem"
           v-on:dragover="isAllowed">
        <span></span>
        <p class='date'>{{ folder.round_date_created }}</p>
        <p>{{ folder.title }}</p>          
      </div>
    </div>

    <div id="articles-list" class="mt">
      <div tabindex='1' draggable="true" class='content-item article' data-content-id='{{ article.id }}'
           v-for="article in articles" 
           v-on:dragstart="dragStart">
        <span></span>
        <p class='date'>{{ article.round_date_created }}</p>
        <p>{{ article.title }}</p>          
      </div>
    </div>    

  </div>
</system-ui-view>

<script data-name="documents">
  (function () {
    var tt = Scope.import('html/admin/content-management/test-file.php');
    var t2 = Scope.import('html/admin/users-management/index.php');
    console.log('tt->', tt,t2);
    function DocumentsStateHandler(state) {
      var component = this;
      this.state = state;
      this.states = {};
      this.ui = {
        components: {},
        behaviors: {}
      };

      this.state.type = "app-section";

      this.state.bind('init', function () {
        component.init();
      });

      this.state.bind('start', function () {
        component.start();
      });
    }

    DocumentsStateHandler.prototype.defineStates = function (states) {
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
            component.bSee.comeOut();
            component.deleteFolderActivity.comeOut();
          }

          if (id > 0) {
            component.bSee.comeIn();
            component.deleteFolderActivity.comeIn();
          }
        }
      };

      var modal;
      states.component = function (full, name) {
        if (full === 'forms/test-form') {
          modal = EW.createModal({
            onClose: function () {
              component.state.setParam('component', null);
              modal = null;
            }
          });
          component.state.loadModule({
            id: 'forms/test-form',
            url: '~admin/html/content-management/test-file.php'
          }, function (module) {
            modal.html(module.html);
            module.start();
          });
        }

        if (full === null && modal) {
          modal.dispose();
          modal = null;
        }
      };
    };

    DocumentsStateHandler.prototype.init = function () {
      var handler = this;

      Object.defineProperty(this, 'parentId', {
        set: function (value) {
          handler.ui.folders_card_vue.parentId = value;
          this.$parentId = value;
        },
        get: function () {
          return this.$parentId;
        }
      });

      Object.defineProperty(this, 'upParentId', {
        set: function (value) {
          handler.ui.folders_card_vue.upParentId = value;
          this.$upParentId = value;
        },
        get: function () {
          return this.$upParentId;
        }
      });

      handler.ui.components.folders_card = $(Scope.uiViews.folders_card);
      handler.ui.components.folders_card_title_action_right = handler.ui.components.folders_card.find(".card-title-action-right");
      handler.ui.components.folders_list = handler.ui.components.folders_card.find("#folders-list");
      handler.ui.components.articles_list = handler.ui.components.folders_card.find("#articles-list");

      handler.ui.folders_card_vue = new Vue({
        el: Scope.uiViews.folders_card,
        data: {
          upParentId: 0,
          parentId: 0,
          card_title: 'tr{Contents}',
          folders: [],
          articles: []
        },
        methods: {
          dragStart: function (event) {
            event.dataTransfer.setData('item', event.target.getAttribute('data-content-id'));
          },
          moveItem: function (event) {
            event.preventDefault();
            if (parseInt(event.currentTarget.getAttribute('data-content-id')) !== this.parentId) {
              handler.moveItem(event.dataTransfer.getData('item'), event.currentTarget.getAttribute('data-content-id'));
            }
          },
          isAllowed: function (event) {
            event.preventDefault();
          },
          goUp: function () {
            handler.preCategory.call(handler);
          }
        }
      });

//      this.upAction = EW.addActionButton({
//        text: "",
//        class: "btn-text icon-back btn-circle",
//        handler: $.proxy(this.preCategory, this),
//        parent: this.ui.components.folders_card.find(".card-title-action")
//      });

      this.seeFolderActivity = EW.getActivity({
        activity: "admin/html/content-management/folder-form.php_see",
        modal: {
          class: "center properties"
        },
        onDone: function () {
          System.setHashParameters({
            folderId: null,
            article: null
          });
        }
      });

      this.seeArticleActivity = EW.getActivity({
        activity: "admin/html/content-management/article-form.php_see",
        onDone: function () {
          System.setHashParameters({
            folderId: null
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
            id: handler.parentId
          };
        },
        onDone: function (response) {
          $("body").EW().notify(response).show();
          handler.preCategory();
        }
      }).hide();

      this.defineStates(this.states);
      System.Util.installModuleStateHandlers(this.state, this.states);
    };

    DocumentsStateHandler.prototype.start = function () {
      var component = this;
      this.folderId = 0;
      this.articleId = 0;
      this.currentItem = null;

      this.bNewFolder = EW.addActivity({
        title: "tr{New Folder}",
        activity: "admin/html/content-management/folder-form.php",
        parent: System.UI.components.mainFloatMenu,
        parameters: function (hash) {
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
        parameters: function (hash) {
          hash.parent = component.parentId;
          hash.article = null;
        },
        onDone: function (hash) {
          hash.article = null;
          hash.parent = null;
        }
      }).hide();

//      this.testBtn = EW.addActionButton({
//        text: "tr{test form}",
//        parent: System.UI.components.mainFloatMenu,
//        handler: function () {
//          component.state.setParam('component', 'forms/test-form');
//        }
//      });

      $(document).off("article-list.refresh").on("article-list.refresh", function (e, eventData) {
        component.listDocuments();
        if (eventData) {
          if (eventData.data.type === "article") {
            System.setHashParameters({
              folderId: null,
              article: eventData.data.id
            });
          }

          if (eventData.data.type === "folder") {
            EW.setHashParameters({
              folderId: eventData.data.id,
              article: null
            }, "document");
          }
        }
      });

      this.bNewFile.comeIn();
      this.bNewFolder.comeIn();

      this.state.setParamIfNull("dir", "0/list");

      component.ui.components.folders_list.off('click').on('click', '.folder', function (e) {
        System.setHashParameters({
          article: null,
          folder: e.currentTarget.getAttribute('data-content-id')
        });

        component.currentItem = System.ui.behaviors.selectElementOnly(e.currentTarget, component.currentItem);
      });

      component.ui.components.folders_list.off('dblclick').on('dblclick', '.folder', function (e) {
        component.state.setParam("dir", e.currentTarget.getAttribute('data-content-id') + "/list");
      });


      component.ui.components.articles_list.off('click').on('click', '.article', function (e) {
        component.state.setParam('folder', null);
        component.state.setParam('article', e.currentTarget.getAttribute('data-content-id'));
        component.currentItem = System.ui.behaviors.selectElementOnly(e.currentTarget, component.currentItem);
      });

      component.ui.components.articles_list.off('dblclick').on('dblclick', '.article', function (e) {
        component.seeArticleActivity({
          article: e.currentTarget.getAttribute('data-content-id')
        });
      });
    };

    DocumentsStateHandler.prototype.moveItem = function (id, toId) {
      var handler = this;
      var loader = System.ui.lock({
        element: handler.ui.components.folders_card[0],
        akcent: 'loader center'
      });

      $.ajax({
        type: 'PUT',
        url: 'api/admin/content-management/contents',
        data: {
          id: id,
          parent_id: toId
        },
        success: function (response) {
          if (response.status_code === 200) {
            handler.ui.folders_card_vue.articles.forEach(function (item, index) {
              if (item.id === response.data.id) {
                handler.ui.folders_card_vue.articles.splice(index, 1);
              }
            });
          }

          loader.dispose();
        }
      });
    };

    DocumentsStateHandler.prototype.preCategory = function () {
      this.currentItem = null;
      this.state.setParam("dir", this.upParentId + "/list");
    };

    DocumentsStateHandler.prototype.seeDetails = function () {
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
          article: tArticleId
        });
      }
    };

    DocumentsStateHandler.prototype.listDocuments = function () {
      var component = this,
              articlesLoaded = false,
              foldersLoaded = false,
              currentSelected = System.getHashParam("article") || System.getHashParam("folder");

      var loader = $("<div class='loader top'></div>");
      this.ui.components.folders_card.find(".card-content").append(loader);
      var foldersElements = [];
      System.addActiveRequest($.get('~admin/api/content-management/contents-folders', {
        parent_id: component.parentId
      }, function (response) {
        component.ui.folders_card_vue.card_title = response.parent ? response.parent.title : "tr{Contents}";

        if (response.parent) {
          component.upParentId = response.parent.parent_id;
        }

        foldersElements = response.data;

        foldersLoaded = true;
        done();
      }, "json"));

      var articlesElements = [];
      System.addActiveRequest($.get('~admin/api/content-management/contents-articles', {
        parent_id: component.parentId
      }, function (response) {
        if (response.parent) {
          component.upParentId = response.parent.parent_id;
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
                component.currentItem.getBoundingClientRect() :
                component.ui.components.folders_card[0].getBoundingClientRect();

        System.UI.Animation.blastTo({
          fromPoint: startPoint,
          to: component.ui.components.folders_card[0],
          area: component.ui.components.folders_card.find(".card-content")[0],
          time: .5,
          fade: .4,
          color: "#eee",
          onComplete: function () {
            component.ui.folders_card_vue.folders = foldersElements;
            component.ui.folders_card_vue.articles = articlesElements;

            component.ui.folders_card_vue.$nextTick(function () {
              var item = document.querySelector('[data-content-id="' + currentSelected + '"]');
              if (item) {
                component.currentItem = System.ui.behaviors.selectElementOnly(item, component.currentItem);
              }
            });

            loader.remove();
          }
        });
      };
    };

    System.state('content-management/documents', function (state) {
      new DocumentsStateHandler(state);
    });
  }());
</script>
