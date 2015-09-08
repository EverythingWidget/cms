<?php
session_start();
include($_SESSION['ROOT_DIR'] . '/config.php');
include_once 'WidgetsManagementCore.php';
if (!$_SESSION['login'])
{
  include($_SESSION['ROOT_DIR'] . '/admin/LoginForm.php');
  return;
}
$uiStructureId = mysql_real_escape_string($_POST['uiStructureId']);
$widgetId = mysql_real_escape_string($_POST['widgetId']);
$widgetTypeId = mysql_real_escape_string($_POST['widgetTypeId']);
$widgetType = mysql_real_escape_string($_POST['widgetType']);
$title = mysql_real_escape_string($_POST['title']);
$showTitle = mysql_real_escape_string($_POST['showTitle']);
$accessLevel = mysql_real_escape_string($_POST['accessLevel']);
$parameters = mysql_real_escape_string($_POST['parameters']);
//$parameters = str_replace(',', '&', $parameters);
$styleId = mysql_real_escape_string($_POST['styleId']);
$class = mysql_real_escape_string($_POST['styleClass']);
$style = mysql_real_escape_string($_POST['style']);
$panelId = mysql_real_escape_string($_POST['panelId']);
$order = mysql_real_escape_string($_POST['order']);

if ($_REQUEST['cmd'] == 'Add' && trim($title))
{
  $result = mysql_query("INSERT INTO ui_structures_parts 
            (ui_structure_id, item_type, item_id, title, widget_type, widgets_parameters, style_id , style_class , style, container_id, ui_structures_parts.order) 
            VALUES ('$uiStructureId' , 'widget' , '0' , '$title', '$widgetType' , '$parameters' , '$styleId' , '$class' , '$style' , '$panelId', 0)") or die(mysql_error());
  if ($result)
  {
    $row["response-code"] = 200;
    echo json_encode($row);
  }
  else
  {
    $row["response-code"] = 500;
    echo json_encode($row);
  }
  return;
}
else if ($_REQUEST['cmd'] == 'Edit' && trim($title))
{
  $result = mysql_query("UPDATE ui_structures_parts SET title = '$title' 
            , widget_type = '$widgetType'
            , widgets_parameters = '$parameters'
            , style_id = '$styleId'
            , style_class = '$class' 
            , style = '$style' WHERE id = '$widgetId'");
  if ($result)
  {
    $result = mysql_query("SELECT * FROM ui_structures_parts WHERE id = '$widgetId'") or die(mysql_error());
    $row = mysql_fetch_array($result);
    $row["response-code"] = 200;
    if ($row)
    {
      echo json_encode($row);
    }
    return;
  }
  else
  {
    $row["response-code"] = 500;
    echo json_encode($row);
    return;
  }
}
else if ($_REQUEST['cmd'] == 'Delete' && $widgetId)
{
  $result = mysql_query("DELETE FROM widgets WHERE id = '$widgetId'");
  if ($result)
  {
    ?>
    <script  type="text/javascript">
      neWidget.bResult.addClass("green");
      neWidget.bResult.setText("Widget has been deleted successfully");
      widgetsList.listWidgets($("#query").val());
      EW.lock(widgetsManagement.currentTopPane);
      setTimeout(function() {
        EW.setHashParameter("widget-id", null);
        EW.setHashParameter("cmd", null);
      }, 5000);
    </script>
    <?php
    return;
  }
  else
  {
    ?>
    <span class="Title" style="color: #dd2200;" >
      خطا: ویجت حذف نشد
    </span>
    <?php
    return;
  }
}
?>
<!-- Begin of form UI-->
<h1 id="form-title">
  New Widget
</h1>
<form id="newidget" class="form-content">
  <input type="hidden" name="id" id="id" >
  <div class="row">
    <label for="title">
      Title
    </label>        
    <input class="text-field" value="<?php echo $title ?>" name="title" id="title" style="width: 450px;" >
  </div>
  <div class="row">
    <div class="text-field" >
      <label for="show_title" style="display: inline;">
        Visible title
      </label>
      <input type="checkbox" value="1" name="show_title" id="show_title" <?php echo $showTitle == '1' ? 'checked' : '' ?>>
    </div>
  </div>    
  <div class="row">
    <label for="widget_type">
      Type
    </label>      
    <div class="ComboBox" style="width: 450px; text-align: right;">
      <select id="widget_type" name="widget_type" onchange="neWidget.changeWidgetType()">
        <?php
        $widgetsTypesList = getWidgetsTypes();
        $result = opendir($HOST_ROOT_DIR . '/widgets/');
        while ($trow = readdir($result))
        {
          if (strpos($trow, '.') === 0)
            continue;
          ?>
          <option value="<?php echo $trow ?>" >
            <?php echo $trow ?>
          </option>
          <?php
        }
        ?>
      </select>
    </div>
  </div>              
  <div class="row">
    <label >
      Widgets Control Panel
    </label>
  </div>
  <div class="row" id="WidgetAdminPage" style="padding:5px;border:1px dashed #ccc;">
  </div>    
  <div class="row">
    <label for="style_id">
      id
    </label>
    <input class="text-field" value="<?php echo $styleId ?>" name="style_id" id="style_id" style="width: 450px;" >
  </div>      
  <div class="row">
    <label for="class">
      Class
    </label>                   
    <input class="text-field" value="<?php echo $class ?>" name="style_class" id="style_class" style="width: 450px;" >
  </div>
  <div class="row" style="min-height: 20px;display:none;">                    
    <span id="classValue" class="SmallLabel" style="width: 414px; direction: ltr; text-align: left;">
      <?php echo $widgetTypeInfo['class'] ?>
    </span>
  </div>
  <div class="row" style="display: none;">
    <label>
      style
    </label>
    <textarea class="text-field" id="style"
              style="width: 450px; 
              max-width: 450px; 
              min-width: 450px; 
              min-height: 200px; 
              direction: ltr;" onkeydown="ifShift(event)" onkeyup="setView() & nextLine(event)" ><?php echo $style ?></textarea>
  </div>
  <div class="row" style="display:none;">
    <label for="parameters">
      Parameters
    </label>
    <textarea style="width: 500px; 
              max-width: 500px; 
              min-width: 500px; 
              height: 46px;font-size:13px;" class="text-field" name="widgets_parameters" id="widgets_parameters" ><?php echo $parameters ?></textarea>
  </div>                           
</form>            
<div id="buttons" class="footer-pane" >
  <a href="javascript:void(0)" id="add" class="button green" style="margin-right:10px;display:none;" onclick="neWidget.addWidget()">
    Add
  </a>
  <a href="javascript:void(0)" id="save" class="button green" style="margin-right:10px;display:none;" onclick="neWidget.saveWidget()">
    Save
  </a>  
</div>
<!-- End of form UI-->
<script  type="text/javascript">
      function NEWidget()
      {
        this.bResult = EW.addNotification();
        //this.bAdd = EW.addAction("Add", this.addWidget, {display: "none"}).addClass("green");
        //this.bEdit = EW.addAction("Save", this.editWidget, {display: "none"}).addClass("green");
        //this.bDelete = EW.addAction("Delete Widget", this.deleteWidget, {display: "none"}).addClass("orange");
      }

      NEWidget.prototype.newWidgetForm = function()
      {
        //neWidget.bAdd.fadeIn(300);
        $("#add").fadeIn(300);
        neWidget.changeWidgetType();
      };

      NEWidget.prototype.editWidgetForm = function()
      {
        $("#newidget").attr({onsubmit: "return neWidget.editWidget()"});
        $("#save").fadeIn(300);
        $("#remove").fadeIn(300);
        neWidget.changeWidgetType();
      };

      NEWidget.prototype.dispose = function()
      {
        /*neWidget.bResult.remove();
         neWidget.bAdd.remove();
         neWidget.bEdit.remove();
         neWidget.bDelete.remove();*/
      };

      NEWidget.prototype.addWidget = function()
      {
        $('#title').removeClass("Red");
        if (!$('#title').val())
        {
          $('#title').addClass("Red");
          return;
        }
        st = $("#show_title").checked == true ? 1 : 0;
        //neWidget.bAdd.hide();
        neWidget.bResult.setText("Please wait...");
        $.post("WidgetsManagement/NEWidgetForm.php", {cmd: "Add", title: $("#title").val(), showTitle: st, widgetType: $("#widget_type").val()
                  , styleId: $("#style_id").val()
                  , styleClass: $("#style_class").val()
                  , style: $("#style").val()
                  , parameters: $("#widgets_parameters").val()
                  , uiStructureId: "<?php echo $uiStructureId ?>"
                  , panelId: "<?php echo $panelId ?>"}, function(data) {
          formData = $.parseJSON(data);
          if (formData["response-code"] === 200)
          {
            neWidget.bResult.addClass("green");
            neWidget.bResult.setText("Widget has been added successfully" , 3000);
            neuis.currentDialog.dispose();
            neuis.reloadFrame();
          }
        });
        return false;
      };

      NEWidget.prototype.saveWidget = function()
      {
        $('#title').removeClass("Red");
        if (!$('#title').val())
        {
          $('#title').addClass("Red");
          return;
        }

        neWidget.bResult.setText("Please wait...");
        $.post("WidgetsManagement/NEWidgetForm.php", {cmd: "Edit", widgetId: $("#id").val(), title: $("#title").val()
                  , widgetType: $("#widget_type").val()
                  , styleId: $("#style_id").val()
                  , styleClass: $("#style_class").val()
                  , style: $("#style").val()
                  , parameters: $("#widgets_parameters").val()}, function(data) {
          formData = $.parseJSON(data);
          if (formData["response-code"] === 200)
          {
            neWidget.bResult.addClass("green");
            neWidget.bResult.setText("Widget has been edited successfully",3000);
            $("#newidget #form-title").html("Edit Widget: " + formData.title);
            EW.setFormData("#newidget", formData);
            neuis.currentDialog.dispose();
            neuis.reloadFrame();
          }
        });
        return false;
      };

      NEWidget.prototype.deleteWidget = function()
      {
        neWidget.bEdit.hide();
        neWidget.bDelete.hide();
        neWidget.bResult.setText("Please wait...");
        $.post("WidgetsManagement/NEWidgetForm.php", {cmd: "Delete", widgetId: $("#id").val()}, function(data) {
          $("#newidget").append(data);
        });
        return false;
      };

      NEWidget.prototype.changeWidgetType = function()
      {
        var height = 0;
        //$('#WidgetAdminPage').stop();
        $('#WidgetAdminPage').html("<label>Loading Widget Control Panel...</label>");
        $('#WidgetAdminPage').ajaxError(function() {
          $('#WidgetAdminPage').html("<label>Nothing for managing</label>");
        });

        $('#WidgetAdminPage').ajaxComplete(function() {

          height = 0;
          $('#WidgetAdminPage').children().each(function()
          {
            height += $(this).outerHeight();
          });
          $('#WidgetAdminPage').stop().animate({height: height}, 500);
        });


        $.post('<?php echo $HOST_ROOT_URI ?>/widgets/' + $('#widget_type').val() + '/admin.php', function(data) {
          $('#WidgetAdminPage').html(data);
        });
      };

      var neWidget = new NEWidget();

<?php
if ($_REQUEST['cmd'] == 'See' || $_REQUEST['cmd'] == 'SeeDialog')
{
  $result = mysql_query("SELECT * FROM ui_structures_parts WHERE id = '$widgetId'") or die(mysql_error());
  $row = mysql_fetch_array($result);
  if ($row)
  {
    ?>
          formData = <?php echo json_encode($row); ?>;
          $("#newidget #form-title").html("Edit Widget: " + formData.title);
          $("#newidget").attr({onsubmit: "return neWidget.editWidget()"});
          EW.setFormData("#newidget", formData);
    <?php
  }
  else
  {
    /* ?>
      neWidget.dispose();
      EW.setHashParameter("widget-id", null);
      EW.lock(widgetsManagement.currentTopPane, "Widget not found");
      <?php */
  }
}
?>

</script>

