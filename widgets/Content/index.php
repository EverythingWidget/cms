<?php//\admin\WidgetsManagement::add_html_script("", widget_script($widget_id));$feeder_name = $_REQUEST["_section_name"];$priority_with_url = $widget_parameters["priority-with-url"];$language = "en";if ($_REQUEST["_language"])   $language = $_REQUEST["_language"];//echo $_REQUEST["_lang"];if ($widget_parameters['path']){   $feeder = json_decode($widget_parameters['path'], TRUE);   // load widget feeder   if ($feeder["type"] == "widget-feeder")   {      $feeder_name = $feeder["feederName"];   }   // Load article   else if ($feeder["type"] == "article")   {      $feeder_name = "article";      $parameter = $feeder["id"];   }}// if default content set to true, then read the page feeder name from the URLif ($priority_with_url == "yes" && $_REQUEST["_file"]){   $feeder_name = $_REQUEST["_section_name"];   $parameter = $_REQUEST["_file"];}//if ($articleId)//{//echo $parameter;if ($feeder_name && EWCore::is_widget_feeder("page", $feeder_name)){   // Load the page specified by URL   $page = json_decode(EWCore::get_widget_feeder("page", $feeder_name, [$parameter, $language]), true);   //echo $page["content"];   if ($page["html"] == "WIDGET_DATA_MODEL")   {      $pageTitle = $page['title'];      $pageContent = $page['content'];   }   else   {      echo $page["html"];      return;   }}//if ($_REQUEST["_function_name"])//$articleId = $_REQUEST["_function_name"];//if ($_REQUEST["category"])//$categoryId = $_REQUEST["category"];//}/* if ($articleId)  {  $result = json_decode(\EWCore::process_command("admin", "ContentManagement", "get_content_with_label", array("content_id" => $articleId, "key" => "admin_ContentManagement_language", "value" => $language)), true);  //$result = $MYSQLI->query("SELECT * FROM ew_contents WHERE id = '$articleId' LIMIT 1") or die(mysql_error());  // while ($row = $result->fetch_assoc())  //{  //print_r($result);  $pageTitle = $result[0]['title'];  $pageContent = $result[0]['content'];  //}  } *//* else if ($categoryId)  {  $result = query("SELECT * FROM content_categories WHERE id = '$categoryId' LIMIT 1") or die(mysql_error());  while ($row = $result->fetch_assoc())  {  $pageTitle = $row['title'];  $pageContent = $row['description'];  }  } *//* if ($widget_parameters['uis'])  {  if ($_REQUEST["_uis"] == $widget_parameters['uis'])  return;  $WM = new WidgetsManagement();  $uisId = $widget_parameters['uis'];  $panels = $MYSQLI->query("SELECT * FROM ui_structures_parts WHERE ui_structure_id = '$uisId' AND item_type = 'panel' ORDER BY ui_structures_parts.order") or die(null);  while ($rows = $panels->fetch_assoc())  {  $WM->open_panel($rows["id"], $rows["style_class"], $rows["style_id"]);  $widgets = $MYSQLI->query("SELECT * FROM ui_structures_parts WHERE container_id = {$rows['id']} AND item_type = 'widget' ORDER BY ui_structures_parts.order") or die(null);  while ($w = $widgets->fetch_assoc())  {  $WM->open_widget($w["id"], $w["widget_type"], $w["style_class"], $w["style_id"], $w["widgets_parameters"]);  $WM->close_widget();  }  $WM->close_panel();  }  return;  }  else if (($articleId || $categoryId) && !$_REQUEST['secId']) *///{//echo $widget_parameters['animation'];if ($widget_parameters["title"] && $widget_parameters["title"] != "false"){   echo "<div class='panel-header'><{$widget_parameters["title"]}>" . $pageTitle . "</{$widget_parameters["title"]}></div>";}?><script>   $(document).ready(function ()   {      var animation;<?phpecho ($widget_parameters['animation']) ? "animation='{$widget_parameters['animation']}';" : "";?>      switch (animation)      {         case "1":            $("[data-widget-id={$widget_id}]").hide();            $("[data-widget-id={$widget_id}]").fadeIn(600);            break;         case "2":            $("[data-widget-id={$widget_id}]").hide();            $("[data-widget-id={$widget_id}]").animate({height: "toggle"}, 600, "Power2.easeOut");            break;      }   });</script><div class="widget-content">           <?php echo ($pageContent); ?>        </div><?phpif ($widget_parameters['linkAddress']){   ?>   <a class="SeeMore EnterIcon" href="<?php echo $widget_parameters['linkAddress'] ?>">      <?php echo $widget_parameters['linkName'] ?>   </a>   <?php}?><?php/* }  else  {  include $fileAddress;  } */   