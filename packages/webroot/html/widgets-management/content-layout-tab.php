<div class="form-block">
  <h2>
    Page UIS
  </h2>
  <h3 class="mar-bot">
    {{ name }}
  </h3>
  
  <input type="hidden" class="text-field" v-model="id" name="webroot/page_uis_id" value="">
  
  <button type="button" class="btn btn-default" v-on:click="showLayoutsDialog()">
    Change
  </button>  
  <button type="button" id="remove-uis-btn" class="btn btn-danger" v-if="id" v-on:click="removeLayout()">
    tr{Remove}
  </button>
</div>

<script  type="text/javascript">
  (function () {
    var vue = new Vue({
      el: '#content-layout',
      data: {
        id: null,
        name: 'Inherit/Default'
      },
      methods: {
        showLayoutsDialog: function () {
          var dp = EW.createModal();
          this.table = EW.createTable({
            name: "uis-list",
            headers: {Name: {}, Template: {}},
            columns: ["name", "template"],
            rowCount: true,
            url: 'api/webroot/widgets-management/get-uis-list',
            pageSize: 30,
            buttons: {
              "Select": function (row) {
                vue.id = row.data('field-id');
                vue.name = row.data('field-name');

                dp.dispose();
              }
            }
          });
          dp.append("<div class='header-pane row'><h1 id='' class='col-xs-12'> UIS List: Select UIS</h1></div>");
          dp.append($("<div id='' class='form-content no-footer' ></div>").append(this.table.container));
          this.table.read();
        },
        removeLayout: function () {
          vue.id = null;
          vue.name = 'Inherit/Default';
        }
      }
    });

    $('#$php.form_id').on('refresh', function (e, formData) {
      vue.id = formData['webroot/page_uis_id'];
      vue.name = formData['webroot/name'];
    });
  })();
</script>