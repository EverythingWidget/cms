<?php
session_start();
include($_SESSION['ROOT_DIR'] . '/config.php');
if (!$_SESSION['login'])
{
  header('Location: Login.php');
  return;
}
$widgetId = mysql_real_escape_string($_POST['widgetId']);
$query = mysql_real_escape_string($_POST['query']);
if ($_REQUEST['cmd'] == 'Delete' && $widgetId)
{
  $result = mysql_query("DELETE FROM widgets WHERE id = '$widgetId'") or die(mysql_error());
  if ($result)
  {
    ?>
    <script  type="text/javascript">
      listWidgets($("#query").val());
    </script>
    <span class="Title" style="color: #339900;" >
      ویجت با موفقیت حذف شد
    </span>        
    <?php
  }
  else
  {
    ?>
    <span class="Title" style="color: #dd2200;" >
      خطا: ویجت حذف نشد
    </span>
    <?php
  }
  return;
}
else if ($_REQUEST['cmd'] == 'WidgetsList')
{
  ?>
  <table class="data">
    <tr>
      <th style="width: 50px;">        
      </th>
      <th>
        Name
      </th> 
      <th>
        Type
      </th> 
      <th>
        Class
      </th> 
    </tr>
    <?php
    $result = mysql_query("SELECT widgets.id , widget_type , title , widgets.class 
            FROM widgets WHERE title LIKE '%$query%'
            OR widget_type LIKE '%$query%'") or die(mysql_error());
    $rowNum = 1;
    while ($row = mysql_fetch_array($result))
    {
      ?>		
      <tr data-id="<?php echo $row['id'] ?>" onclick="widgetsList.selectRow(this) & EW.setHashParameter('widget-id',<?php echo $row['id'] ?>)" ondblclick="seeWidgetInfo()">                        
        <td>
          <?php echo $rowNum++ ?>
        </td>
        <td>
          <?php echo $row['title'] ?>
        </td>
        <td>
          <?php echo $row['widget_type'] ?>
        </td>
        <td>
          <?php echo $row['class'] ?>
        </td>
      </tr>
      <?php
    }
    ?>
  </table>
  <?php
  return;
}
else
{
  ?>    
  <div id="main-content" > 
  </div>    
  <script  type="text/javascript">
      function WidgetsList()
      {
        this.oldRow;
        this.bAdd = EW.addAction("New Widget", this.newWidget, {display: "none"});
        this.bEdit = EW.addAction("Edit Widget", this.editWidget, {display: "none"});
        //this.bEdit = EW.addAction("Edit", this.editUIS, {display: "none"});
        //this.bAddItem = EW.addAction("Add Item", this.editUIS, {display: "none"});
        //this.bDelete = EW.addAction("Delete", this.deleteUIS, {display: "none"});
        this.bAdd.fadeIn(300);
        $("#query").keypress(function(e) {
          if (e.which === 13) {
            widgetsList.listWidgets($("#query").val());
          }
        });
      }

      var widgetId;
      WidgetsList.prototype.selectRow = function(rowElm)
      {
        $(widgetsList.oldRow).removeClass("selected");
        $(rowElm).addClass("selected");
        widgetsList.oldRow = rowElm;
      };

      function showTargetForm(flag)
      {
        if (flag)
        {
          obj('TargetForm').style.display = 'block';
          obj('ButtonsPane').style.display = 'none';
        }
        else
        {
          obj('TargetForm').style.display = 'none';
          obj('ButtonsPane').style.display = 'block';
        }
      }

      function showMoreOptions(flag)
      {
        if (flag)
        {
          showTargetForm(false);
          obj('ButtonsPane').style.visibility = 'visible';
        }
        else
        {
          obj('ButtonsPane').style.visibility = 'hidden';
          if (oldRow)
            oldRow.className = "";
        }
      }
      WidgetsList.prototype.newWidget = function()
      {
        EW.setHashParameter('cmd', "new-widget");
      };

      WidgetsList.prototype.editWidget = function()
      {
        EW.setHashParameter('cmd', "edit-widget");
      };

      WidgetsList.prototype.newWidgetForm = function()
      {
        widgetsList.bAdd.fadeOut(0);
        widgetsList.bEdit.fadeOut(0);
        tp = EW.newTopPane();
        widgetsManagement.currentTopPane = tp;
        $.post("WidgetsManagement/NEWidgetForm.php", function(data) {
          tp.html(data);
          neWidget.newWidgetForm();
          tp.onClosed = function()
          {
            EW.setHashParameter('cmd', null);
            neWidget.dispose();
            widgetsList.bAdd.fadeIn(300);
            widgetsManagement.currentTopPane = null;
          };
        });
      };

      WidgetsList.prototype.editWidgetForm = function()
      {
        widgetsList.bAdd.fadeOut(0);
        widgetsList.bEdit.fadeOut(0);
        tp = EW.newTopPane();
        widgetsManagement.currentTopPane = tp;
        $.post("sections/WidgetsManagement/NEWidgetForm.php", {cmd: "See", widgetId: EW.getHashParameter("widget-id")}, function(data) {
          tp.html(data);
          neWidget.editWidgetForm();
          tp.onClosed = function()
          {
            EW.setHashParameter('cmd', null);
            neWidget.dispose();
            widgetsList.bAdd.fadeIn(300);
            widgetsManagement.currentTopPane = null;
          };
        });
      };

      widgetsManagement.onBackToWM = function()
      {
        widgetsList.bAdd.remove();
        widgetsList.bEdit.remove();
        EW.setHashParameter("cmd", null);
        EW.setHashParameter("widget-id", null);
      };

      function seeWidgetInfo(wId)
      {
        if (wId)
        {
          widgetId = wId;
        }
        showTargetForm(true);
        $("#TargetForm").html("<span class='LoadingAnimation'></span>");
        $.post("WidgetsManagement/NEWidgetForm.php", {cmd: "See", "widgetId": widgetId}, function(data) {
          slideOut("#TargetForm", 500);
          $("#TargetForm").html(data);
          $("#TargetForm").prepend('<span class="CloseButton" onclick="showTargetForm(false)"></span>');
        });
      }

      function deleteWidget()
      {
        if (confirm('آیا از حذف این ویجت اطمینان دارید؟'))
        {
          showTargetForm(true);
          loadPage('WidgetsManagement/WidgetsList.php', 'TargetForm', 'cmd=Delete&widgetId=' + widgetId, '<span class="CloseButton" onclick="showTargetForm(false)"></span>');
          showMoreOptions(false);
        }
      }

      WidgetsList.prototype.listWidgets = function(query)
      {
        if (!query)
          query = '';
        $.post('WidgetsManagement/WidgetsList.php', {cmd: "WidgetsList", query: query}, function(data)
        {
          $("#main-content").html(data);
        });
      };
      //showMoreOptions(false);
      var widgetsList = new WidgetsList();
      EW.addURLHandler(function()
      {
        if (EW.getHashParameter("cmd") === "new-widget")
        {
          widgetsList.newWidgetForm();
        }
        if (EW.getHashParameter("cmd") === "edit-widget" && !widgetsManagement.currentTopPane)
        {
          widgetsList.editWidgetForm();
        }
        if (EW.getHashParameter("widget-id") && !EW.getHashParameter("cmd"))
        {
          widgetsList.bEdit.fadeIn(300);
        }
        else
        {
          widgetsList.bEdit.fadeOut(300);
        }
        if (!EW.getHashParameter("cmd"))
        {
          if (widgetsManagement.currentTopPane)
          {
            widgetsManagement.currentTopPane.dispose();
            widgetsManagement.currentTopPane = null;
          }
        }
        return "WidgetsListHandler";
      });
      widgetsList.listWidgets($("#query").val());
  </script>
  <?php
}
?>

