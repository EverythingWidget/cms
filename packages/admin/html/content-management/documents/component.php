<system-ui-view module="content-management/documents" name="folders-card" class="card card-big center-block z-index-1">
  <div class='card-header'
       v-bind:data-content-id="upParentId"
       v-on:drop="moveItem"
       v-on:dragover="isAllowed">
    <div class="card-title-action">
      <button type="button" class="btn btn-circle"
              transition="slide"
              v-if="parentId"
              v-on:click="goUp()"><i class="icon-left-open-1"></i></button>
    </div>

    <div class="card-title-action-right"></div>

    <h1 v-bind:class="{'inline-loader': loading}"> {{ card_title }} </h1>
  </div>

  <system-spirit animations="liveHeight,verticalShift" vertical-shift="content-item">


    <div class='card-content'>
      <div class="card-control-bar">
        <ew-pagination id="folders-pagination"
                       v-bind:auto-init="false"
                       v-bind:list.sync="folders"
                       v-bind:api-params="contentsAPIParams"></ew-pagination>
      </div>

      <div id="folders-list" class="mt">
        <div v-for="folder in folders.data" track-by="id" tabindex='1' class='content-item folder'
             v-bind:data-content-id="folder.id"
             v-on:drop="moveItem" v-on:dragover="isAllowed">
          <span></span>
          <p>{{ folder.title }}</p>
          <p class="date">{{ folder.round_date_created }}</p>
        </div>
      </div>

      <div class="card-control-bar">
        <ew-pagination id="articles-pagination"
                       v-bind:auto-init="false"
                       v-bind:list.sync="articles"
                       v-bind:api-params="contentsAPIParams"></ew-pagination>
      </div>

      <div id="articles-list" class="mt">
        <div tabindex='1' draggable="true" class='content-item article'
             v-bind:data-content-id="article.id"
             v-for="article in articles.data"
             v-on:dragstart="dragStart">
          <span></span>
          <p>{{ article.title }}</p>
          <p class='date'>{{ article.round_date_created }}</p>
        </div>
      </div>
    </div>

  </system-spirit>

</system-ui-view>

<script>
  Scope.export = DocumentsStateHandler;

  function DocumentsStateHandler(scope, state) {
    var component = this;
    this.scope = scope;
    this.state = state;
    this.states = {};
    this.ui = {
      components: {},
      behaviors: {}
    };

    this.state.onInit = function () {
      component.init();
    };

    this.state.onStart = function () {
      component.start();
    };
  }

  DocumentsStateHandler.prototype.defineStates = function (states) {
    var component = this;

    // you can use either states.<state> or states['<state>']
    states.article = function (full, id) {
      component.ui.documents_card_vue.selectedId = id;
    };

    states.folder = function (full, id, command) {
      component.ui.documents_card_vue.selectedId = id;
    };

    states.dir = function (full, id, list) {
      if (!id) {
        id = 0;
      }

      if (list) {
        if (component.parentId !== parseInt(id)) {
          component.preParentId = component.parentId;
          component.parentId = parseInt(id);

          component.listDocuments();
        }

        if (id === 0) {
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
          url: 'html/admin/content-management/test-file.php'
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
    var component = this;

    Object.defineProperty(this, 'parentId', {
      set: function (value) {
        component.ui.documents_card_vue.parentId = value;
        this.$parentId = value;
      },
      get: function () {
        return this.$parentId;
      }
    });

    Object.defineProperty(this, 'upParentId', {
      set: function (value) {
        component.ui.documents_card_vue.upParentId = value;
        this.$upParentId = value;
      },
      get: function () {
        return this.$upParentId;
      }
    });

    component.ui.components.folders_card = $(component.scope.uiViews.folders_card);
    component.ui.components.folders_card_title_action_right = component.ui.components.folders_card.find(".card-title-action-right");
    component.ui.components.folders_list = component.ui.components.folders_card.find("#folders-list");
    component.ui.components.articles_list = component.ui.components.folders_card.find("#articles-list");

    component.ui.documents_card_vue = new Vue({
      el: component.scope.uiViews.folders_card,
      data: {
        loading: false,
        upParentId: 0,
        parentId: 0,
        card_title: 'tr{Documents}',
        folders: {
          url: 'api/admin/content-management/contents-folders/',
          start: 0,
          page_size: 12
        },
        articles: {
          url: 'api/admin/content-management/contents-articles/',
          page_size: 30
        },
        contentsAPIParams: {
          parent_id: 0
        }
      },
      computed: {
        selectedId: {
          set: function (value) {
            var _this = this;
            if (value) {
              component.ui.documents_card_vue.$nextTick(function () {
                var item = document.querySelector('[data-content-id="' + value + '"]');
                item ? item.classList.add('selected') : '';
                _this._selectedItem ? _this._selectedItem.classList.remove('selected') : '';
                _this._selectedItem = item;
              });
            }

            this._selectedId = value;
          }, get: function () {
            return this._selectedId;
          }
        }
      },
      methods: {
        dragStart: function (event) {
          event.dataTransfer.setData('item', event.target.getAttribute('data-content-id'));
        },
        moveItem: function (event) {
          event.preventDefault();
          if (parseInt(event.currentTarget.getAttribute('data-content-id')) !== this.parentId) {
            component.moveItem(event.dataTransfer.getData('item'), event.currentTarget.getAttribute('data-content-id'));
          }
        },
        isAllowed: function (event) {
          event.preventDefault();
        },
        goUp: component.goUp.bind(component)
      }
    })
    ;

    this.seeFolderActivity = EW.getActivity({
      activity: "admin/html/content-management/folder-form/component.php_see",
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
      activity: "admin/html/content-management/article-form/component.php_see",
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
      verb: 'DELETE',
      activity: "admin/api/content-management/folder",
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
        component.goUp();
      }
    }).hide();

    this.defineStates(this.states);
    System.utility.installModuleStateHandlers(this.state, this.states);
  };

  DocumentsStateHandler.prototype.start = function () {
    var component = this;
    this.folderId = 0;
    this.articleId = 0;
    this.currentItem = null;

    System.entity('ui/primary-menu').actions = [
      {
        title: 'tr{New Folder}',
        activity: 'admin/html/content-management/folder-form/component.php',
        parameters: function (hash) {
          hash.parent = component.parentId;
        },
        onDone: function (hash) {
          hash.parent = null;
          hash.folderId = null;
        }
      }, {
        title: 'tr{New Article}',
        activity: 'admin/html/content-management/article-form/component.php_new',
        parameters: function (hash) {
          hash.parent = component.parentId;
          hash.article = null;
        },
        onDone: function (hash) {
          hash.article = null;
          hash.parent = null;
        }
      }
    ];

    $(document).off('article-list.refresh').on('article-list.refresh', function (event, data) {
      component.ui.documents_card_vue.$broadcast('refresh');
      if (data) {
        if (data.data.type === "article") {
          System.setHashParameters({
            folderId: null,
            article: data.data.id
          });
        }

        if (data.data.type === "folder") {
          EW.setHashParameters({
            folderId: data.data.id,
            article: null
          }, "document");
        }
      }
    });

    component.ui.components.folders_list.off('click').on('click', '.folder', function (event) {
      System.setHashParameters({
        article: null,
        folder: event.currentTarget.getAttribute('data-content-id')
      });

      component.currentItem = event.currentTarget;
    });

    component.ui.components.folders_list.off('dblclick touchstart').on('dblclick touchstart', '.folder', function (event) {
      component.state.setParam('dir', event.currentTarget.getAttribute('data-content-id') + '/list');
    });


    component.ui.components.articles_list.off('click').on('click', '.article', function (event) {
      component.state.setParam('folder', null);
      component.state.setParam('article', event.currentTarget.getAttribute('data-content-id'));
      component.currentItem = event.currentTarget;
    });

    component.ui.components.articles_list.off('dblclick touchstart').on('dblclick touchstart', '.article', function (event) {
      component.seeArticleActivity({
        article: event.currentTarget.getAttribute('data-content-id')
      });
    });

    this.state.setParamIfNull('dir', '0/list');
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
          handler.ui.documents_card_vue.articles.data.forEach(function (item, index) {
            if (item.id === response.data.id) {
              handler.ui.documents_card_vue.articles.data.splice(index, 1);
            }
          });
        }

        loader.dispose();
      }
    });
  };

  DocumentsStateHandler.prototype.goUp = function () {
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

    component.ui.documents_card_vue.loading = true;

    var loader = $("<div class='loader top'></div>");
    component.ui.documents_card_vue.contentsAPIParams.parent_id = component.parentId;
    var foldersElements = [];
    System.addActiveRequest($.get('api/admin/content-management/contents-folders/', {
      parent_id: component.parentId,
      page_size: component.ui.documents_card_vue.folders.page_size,
      start: component.ui.documents_card_vue.folders.start
    }, function (response) {
      component.ui.documents_card_vue.card_title = response.parent ? response.parent.title : 'tr{Documents}';

      if (response.parent) {
        component.upParentId = response.parent.parent_id;
      }

      foldersElements = response;

      foldersLoaded = true;
      done();
    }));

    var articlesList = {};

    System.addActiveRequest($.get('api/admin/content-management/contents-articles/', {
      parent_id: component.parentId,
      page_size: component.ui.documents_card_vue.articles.page_size
    }, function (response) {
      if (response.parent) {
        component.upParentId = response.parent.parent_id;
      }

      articlesList = response;

      articlesLoaded = true;
      done();
    }));

    var done = function () {
      if (!articlesLoaded || !foldersLoaded) {
        return;
      }

      component.ui.documents_card_vue.loading = false;
      var startPoint = (component.currentItem) ?
          component.currentItem.getBoundingClientRect() :
          component.ui.components.folders_card.find('.card-header')[0].getBoundingClientRect();

      System.ui.animations.blastTo({
        fromPoint: startPoint,
        to: component.ui.components.folders_card[0],
        area: component.ui.components.folders_card.find(".card-content")[0],
        time: .5,
        fade: .4,
        color: '#eee',
        toColor: '#fff',
        onComplete: function () {
          component.ui.documents_card_vue.folders = foldersElements;
          component.ui.documents_card_vue.articles = articlesList;
          component.ui.documents_card_vue.selectedId = currentSelected;

          loader.remove();
        }
      });
    };
  };

  // ------ Registring the state handler ------ //

  if (Scope._stateId === 'content-management/documents') {
    System.state('content-management/documents', function (state) {
      new DocumentsStateHandler(Scope, state);
    });
  }

</script>
