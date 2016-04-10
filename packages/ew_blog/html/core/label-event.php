<div class="col-xs-12">
  <input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
  <input class="text-field" data-label="Select a date" id="{{comp_id}}_value" name="value" value=""/>
</div>
<div class="col-xs-12">
  <system-list class="list" id="{{comp_id}}_fields_list" action="a">
    <li>
      <a href="." class="link">{{title}}</a>
    </li>    
  </system-list>
</div>

<script>
  (function () {
    var value = $("#{{comp_id}}_value");
    value.datepicker({
      format: 'yyyy-mm-dd'
    });

    value.EW().inputButton({
      title: "<i class='link-icon'></i>",
      class: "btn-default",
      onClick: function (e) {
        readContentFields();
      }
    });

    $("#{{form_id}}").on("refresh", function (e, formData) {
      value.change();
    });

    $("#{{comp_id}}_fields_list")[0].addEventListener('item-selected', function (e) {
      value.val(e.detail.data.value).change();
    });

    function readContentFields() {
      var contentFields = [];

      [].forEach.call($("#content-editor .ct-content-container")[0].querySelectorAll('[content-field]'), function (item) {
        if (!/\d\d\d\d-\d\d-\d\d/.test(item.innerHTML)) {
          return;
        }

        contentFields.push({
          title: item.getAttribute('content-field'),
          value: '{@fields/' + item.getAttribute('content-field') + '}'
        });
      });

      $("#{{comp_id}}_fields_list")[0].data = contentFields;
    }
  }());
</script>