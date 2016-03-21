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
      <div id="categories-list"  ></div>

      <div id="articles-list" class="mar-top"></div>
    </div>
  </div>
</system-ui-view>

<script data-name="documents">
  (function (System) {
    function DocumentsComponent(module) {
      var component = this;
      this.module = module;
      this.states = {};
      this.ui = {
        components: {},
        behaviors: {}
      };
      
      this.module.type = "app-section";

      this.module.bind('init', function (templates) {
        component.init(templates);
      });

      this.module.bind('start', function () {
        component.start();
      });
      
      this.ui.behaviors.selectItem = function (item) {
        if (component.currentItem) {
          component.currentItem.removeClass("selected");
        }

        item.addClass("selected");
        component.currentItem = item;
      };
    }

    DocumentsComponent.prototype.defineStateHandlers = function (states) {
      var component = this;

      // you can use either states.<state> or states['<state>']
      states.article = function (full, id) {
        if (!id) {
          component.currentItem.removeClass("selected");
        }
      };

      states.folder = function (full, id, command) {
        if (!id) {
          component.currentItem.removeClass("selected");
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

            component.listCategories();
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
    };

    DocumentsComponent.prototype.init = function (templates) {
      var _this = this;
      this.ui.components.folders_card = $(templates["folders-card"]);
      this.ui.components.folders_card_title = this.ui.components.folders_card.find(".card-header h1");
      this.ui.components.folders_card_title_action_right = this.ui.components.folders_card.find(".card-title-action-right");
      this.ui.components.folders_list = this.ui.components.folders_card.find("#categories-list");
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
          text: "tr{Properties}",
          class: "btn-text btn-default",
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
            id: _this.parentId
          };
        },
        onDone: function (response) {
          $("body").EW().notify(response).show();
          _this.preCategory();
        }
      }).hide();

      this.defineStateHandlers(this.states);
      System.Util.installModuleStateHandlers(this.module, this.states);
    };

    DocumentsComponent.prototype.start = function () {
      var _this = this;
      this.parentId = null;
      this.folderId = 0;
      this.articleId = 0;
      this.upParentId = 0;
      this.currentItem = $();

      this.ui.components.folders_list.empty();
      this.ui.components.articles_list.empty();

      this.ui.components.folders_card[0].show();

      this.bNewFolder = EW.addActivity({
        title: "tr{New Folder}",
        //class: "btn-text btn-primary",
        activity: "admin/html/content-management/folder-form.php",
        parent: System.UI.components.mainFloatMenu,
        hash: function (hash) {
          hash.parent = _this.parentId;
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
          hash.parent = _this.parentId;
        },
        onDone: function (hash) {
          hash.articleId = null;
          hash.parent = null;
        }
      }).hide();

      $(document).off("article-list");
      $(document).on("article-list.refresh", function (e, eventData) {
        _this.listCategories();
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

    DocumentsComponent.prototype.listCategories = function () {
      var _this = this,
              pId = 0,
              hasNode = false,
              articlesLoaded = false,
              foldersLoaded = false,
              article = System.getHashParam("article"),
              folder = System.getHashParam("folder");
      var loader = $("<div class='loader top'></div>");
      this.ui.components.folders_card.find(".card-content").append(loader);

      var foldersElements = [];
      System.addActiveRequest($.get('~admin/api/content-management/contents-folders', {
        parent_id: _this.parentId
      }, function (response) {
        _this.ui.components.folders_card_title.text(response.parent.title || "tr{Contents}");
        var temp = null;
        $.each(response.data, function (index, element) {
          pId = element.up_parent_id;
          hasNode = true;
          temp = _this.createFolderElement(element.title, element.round_date_created, element.id, element);
          if (element.id == folder) {
            temp.addClass("selected");
            _this.currentItem = temp;
          }
          foldersElements.push(temp);
        });

        if (hasNode) {
          _this.upParentId = pId;
        }

        foldersLoaded = true;
        done();
      }, "json"));


      var articlesElements = [];
      System.addActiveRequest($.get('~admin/api/content-management/contents-articles', {
        parent_id: _this.parentId
      }, function (response) {
        var temp = null;
        $.each(response.data, function (index, element) {
          _this.upParentId = element.up_parent_id;
          temp = _this.createArticleElement(element.title, element.round_date_created, element.id, element);
          if (element.id == article) {
            temp.addClass("selected");
            _this.currentItem = temp;
          }
          articlesElements.push(temp);
        });

        articlesLoaded = true;
        done();
      }, "json"));

      var done = function () {
        if (!articlesLoaded || !foldersLoaded) {
          return;
        }

        var startPoint = (_this.currentItem && _this.currentItem[0]) ?
                _this.currentItem[0].getBoundingClientRect() : _this.upAction[0].getBoundingClientRect();

        System.UI.Animation.blastTo({
          fromPoint: startPoint,
          to: _this.ui.components.folders_card[0],
          area: _this.ui.components.folders_card.find(".card-content")[0],
          time: .5,
          fade: .4,
          color: "#eee",
          onComplete: function () {
            _this.ui.components.folders_list.empty();
            _this.ui.components.articles_list.empty();
            _this.ui.components.folders_list.append(foldersElements);
            _this.ui.components.articles_list.append(articlesElements);
            loader.remove();
          }
        });
      };
    };


    DocumentsComponent.prototype.createFolderElement = function (title, dateCreated, id, model) {
      var _this = this;
      var divTemplate = System.ui.utility.populate("<div tabindex='1' class='content-item folder' data-category-id='{{id}}'>" +
              "<span></span><p>{{title}}</p><p class='date'>{{round_date_created}}</p></div>", model);

      var div = $(divTemplate);
      div[0].addEventListener("dblclick", function () {
        _this.module.setParam("dir", id + "/list");
      });

      div[0].addEventListener('focus', function () {
        System.setHashParameters({
          article: null,
          folder: id
        });
        _this.ui.behaviors.selectItem(div);
      });

      div.attr('data-label', title);

      return div;
    };

    DocumentsComponent.prototype.createArticleElement = function (title, dateCreated, id, model) {
      var self = this;
      var divTemplate = System.ui.utility.populate("<div tabindex='1' class='content-item article' data-article-id='{{id}}'>" +
              "<span></span><p>{{title}}</p><p class='date'>{{round_date_created}}</p></div>", model);

      var div = $(divTemplate);

      div[0].addEventListener("dblclick", function () {
        self.seeArticleActivity({
          articleId: id
        });
      });

      div[0].addEventListener('focus', function () {
        System.setHashParameters({
          folder: null,
          article: id
        });
        self.ui.behaviors.selectItem(div);
      });

      div.attr('data-label', title);

      return div;
    };

    var DocumentStatesManager = function () {
      new DocumentsComponent(this);
    };

    System.module("content-management/documents", DocumentStatesManager);
  }(System));
</script>
