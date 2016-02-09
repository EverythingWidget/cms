<div data-ui-template="folders-card" class="col-xs-12">
  <div id="folders-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
    <div  class='card-header'>
      <div class="card-title-action"></div>
      <div class="card-title-action-right"></div>
      <h1>
        tr{Contents}
      </h1>
    </div>

    <div class='card-content top-devider'>

      <div id="categories-list"  >

      </div>

      <div id="articles-list" class="mar-top">

      </div>
    </div>
  </div>
</div>

<!--<ew-float-menu id='folders-card-action-bar' position="css" parent="app-content" class="ew-float-menu">
  
</ew-float-menu>-->

<script>
  (function (System) {
    //var foldersCardTemplate = $("#folders-card-template")[0].outerHTML;

    function Documents(module) {
      var component = this;
      this.module = module;
      this.module.type = "app-section";

      this.module.onInit = function (templates) {
        component.init(templates);
      };

      this.module.onStart = function () {
        component.start();
      };
    }

    Documents.prototype.init = function (templates) {
      this.foldersCard = $(templates["folders-card"]);
      this.foldersCardTitle = this.foldersCard.find(".card-header h1");
      this.foldersCardTitleActionRight = this.foldersCard.find(".card-title-action-right");
      this.foldersList = this.foldersCard.find("#categories-list");
      this.articlesList = this.foldersCard.find("#articles-list");

      this.upAction = EW.addActionButton({
        text: "",
        class: "btn-text icon-back btn-circle",
        handler: $.proxy(this.preCategory, this),
        parent: this.foldersCard.find(".card-title-action")
      });

      this.seeFolderActivity = EW.getActivity({
        activity: "admin/html/content-management/folder-form.php_see",
        modal: {
          class: "center properties"
        },
        onDone: function () {
          EW.setHashParameters({
            folderId: null,
            articleId: null
          });
        }
      });

      this.seeArticleActivity = EW.getActivity({
        activity: "admin/html/content-management/article-form.php_see",
        onDone: function () {
          EW.setHashParameters({
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
          parent: this.foldersCardTitleActionRight
        }).hide();
      else
        this.bSee = $();

      this.deleteFolderActivity = EW.addActivity({
        title: "",
        defaultClass: "btn-text btn-circle icon-delete btn-danger",
        activity: "admin/api/content-management/delete-folder",
        parent: this.foldersCardTitleActionRight,
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

      this.installStateHandlers(this.module);
    };

    Documents.prototype.installStateHandlers = function (module) {
      var component = this;
      module.on("article", function (full, id) {
        if (!id) {
          component.currentItem.removeClass("selected");
        }
      });

      module.on("folder", function (full, id, command) {
        if (!id) {
          component.currentItem.removeClass("selected");
        }
      });

      module.on("dir", function (p, id, list) {
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
      });
    };

    Documents.prototype.start = function () {
      var _this = this;
      this.parentId = null;
      this.folderId = 0;
      this.articleId = 0;
      this.upParentId = 0;
      this.currentItem = $();

      this.foldersList.empty();
      this.articlesList.empty();
      System.UI.components.mainContent.append(this.foldersCard);

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
            },
                    "document");
          }

          if (eventData.data.type === "folder") {
            EW.setHashParameters({
              folderId: eventData.data.id,
              articleId: null
            },
                    "document");
          }
        }
      });

      this.bNewFile.comeIn();
      this.bNewFolder.comeIn();

      this.module.setParamIfNone("dir", "0/list");
      //alert("Document Started");
    };

    Documents.prototype.preCategory = function () {
      this.currentItem = null;
      this.module.setParam("dir", this.upParentId + "/list");
    };

    Documents.prototype.seeDetails = function () {
      var tFolderId = System.getHashParam("folder");
      var tArticleId = System.getHashParam("article");
      EW.activeElement = this.foldersCard.find(".card-header");
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

    Documents.prototype.listCategories = function () {
      var _this = this,
              pId = 0,
              hasNode = false,
              article = System.getHashParam("article"),
              folder = System.getHashParam("folder");
      var loader = $("<div class='loader top'></div>");
      this.foldersCard.find(".card-content").append(loader);

      System.addActiveRequest($.get('~admin/api/content-management/contents-folders', {
        parent_id: _this.parentId
      },
              function (data) {
                _this.foldersCardTitle.text(data.parent.title || "tr{Contents}");
                var startPoint = (_this.currentItem && _this.currentItem[0]) ?
                        _this.currentItem[0].getBoundingClientRect() : _this.upAction[0].getBoundingClientRect();

                System.UI.Animation.blastTo({
                  fromPoint: startPoint,
                  to: _this.foldersCard[0],
                  area: _this.foldersCard.find(".card-content")[0],
                  time: .5,
                  fade: .3,
                  color: "#eee",
                  onComplete: function () {
                    _this.foldersList.empty();
                    $.each(data.data, function (index, element) {
                      pId = element.up_parent_id;
                      hasNode = true;
                      var temp = _this.createFolderElement(element.title, element.round_date_created, element.id, element);
                      //temp.addClass("anim-scale-in");
                      if (element.id == folder) {
                        temp.addClass("selected");
                        _this.currentItem = temp;
                      }
                      _this.foldersList.append(temp);
                      //temp.addClass("in");
                    });

                    if (hasNode) {
                      _this.upParentId = pId;
                    }
                    loader.remove();
                    _this.articlesList.empty();
                    //var articleLoader = $("<div class='loader center'></div>");
                    //$("#articles-list").append(articleLoader);
                    //$("#articles-list").html("<div class='box-content anim-fade-in'></div>");
                    System.addActiveRequest($.get('~admin/api/content-management/contents-articles', {
                      parent_id: _this.parentId
                    },
                            function (response) {

                              //var articlesPane = $("#articles-list");
                              $.each(response.data, function (index, element) {
                                pId = element.up_parent_id;
                                hasNode = true;
                                var temp = _this.createArticleElement(element.title, element.round_date_created, element.id, element);
                                //temp.addClass("anim-scale-in");
                                if (element.id == article) {
                                  temp.addClass("selected");
                                  _this.currentItem = temp;
                                }
                                _this.articlesList.append(temp);
                                // setTimeout(function ()            {
                                //temp.addClass("in");
                                //}, 1);

                              });

                              if (hasNode) {
                                _this.upParentId = pId;
                              }
                              //$("#articles-list").find(".box-content").addClass("in");


                              //lockArticles.dispose();
                            }, "json"));
                  }
                });
                //$("#categories-list").find(".box-content").addClass("in");
                //lockFolders.dispose();
              }, "json"));




    };

    Documents.prototype.focusOn = function (item) {
      if (this.currentItem) {
        this.currentItem.removeClass("selected");
      }
      item.addClass("selected");
      this.currentItem = item;
    };

    Documents.prototype.createFolderElement = function (title, dateCreated, id, model) {
      var self = this;
      var div = $("<div tabindex='1' class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div[0].addEventListener("dblclick", function () {
        self.module.setParam("dir", id + "/list");
      });

      div[0].addEventListener('focus', function () {
        System.setHashParameters({
          article: null,
          folder: id
        });
        self.focusOn(div);
      });

      div.data("label", title);
      return div;
    };

    Documents.prototype.createArticleElement = function (title, dateCreated, id, model) {
      var self = this;
      var div = $("<div tabindex='1' class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
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
        self.focusOn(div);
      });
      div.data("label", title);
      return div;
    };

    var module = function () {
      new Documents(this);
    };

    System.module("content-management/documents", module);
  }(System));
</script>
