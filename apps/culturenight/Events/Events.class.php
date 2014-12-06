<?php

namespace culturenight;

use Section;
use EWCore;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class Events extends Section
{

   private $add_event_rules = array();

   public function init_plugin()
   {

      EWCore::register_widget_feeder("news", "latest-events", array($this, "feeder_latest_events"));
      EWCore::register_widget_feeder("calender-events", "events-list", array($this, "event_items_list"));
      EWCore::register_widget_feeder("list", "events-list", array($this, "event_items_list"));
      EWCore::register_widget_feeder("list", "events-with-tag", array($this, "event_with_tag"));
      EWCore::register_widget_feeder("menu", "events-styles", array($this, "feeder_event_sub_menus"));
      EWCore::register_widget_feeder("menu", "events-locations", array($this, "feeder_location_sub_menus"));
      // new event form
      EWCore::register_widget_feeder("page", "event-form", EW_ROOT_DIR . "apps/culturenight/sections/Events/event-form.php");

      EWCore::register_widget_feeder("page", "event-info", array($this, "feeder_event_info"));
      EWCore::register_widget_feeder("page", "event-search-form", array($this, "event_search_form"));
      EWCore::register_widget_feeder("page", "events-list", array($this, "event_items_list"));
      EWCore::register_widget_feeder("page", "events-with-tag", array($this, "event_with_tag"));
      EWCore::register_widget_feeder("page", "login", EW_ROOT_DIR . "apps/culturenight/Events/login-register-form.php");
      // Events permissions
      $this->register_permission("see-event", "User can see the events", array("get_event", "get_event_by_slug", "get_events_list", "event-form.php_see", $this->get_index()));
      $this->register_permission("manipulate-event", "User can add and edit events", array("add_event", "update_event", "event-form.php: tr:culturenight{Add Event}", $this->get_index()));
      $this->register_activity("event-form", array("title" => "New Event", "form" => "event-form.php"));
      
   }

   public function get_tags_list()
   {
      $MYSQLI = get_db_connection();
      $token = $MYSQLI->real_escape_string($_REQUEST["token"]);
      $size = $MYSQLI->real_escape_string($_REQUEST["size"]);

      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM tags") or die($MYSQLI->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $MYSQLI->query("SELECT *  FROM tags LIMIT $token,$size") or die($MYSQLI->error);

      //$out = array();
      $rows = array();

      while ($r = $result->fetch_assoc())
      {

         $rows[] = $r["name"];
      }
      $MYSQLI->close();
      $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($out);
   }

   public function get_events_list()
   {
      $MYSQLI = get_db_connection();
      //$parentId = $MYSQLI->real_escape_string($this->get_param("parentId"));
      $token = $MYSQLI->real_escape_string($_REQUEST["token"]);
      $size = $MYSQLI->real_escape_string($_REQUEST["size"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM events ") or die($MYSQLI->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $MYSQLI->query("SELECT id,name,slug,web,DATE_FORMAT(start_date,'%Y-%m-%d') as sdate ,DATE_FORMAT(end_date,'%Y-%m-%d') as edate , IF(published=1,'Published','Unpablished') AS published  FROM `events` ORDER BY id DESC  LIMIT $token,$size") or die($MYSQLI->error);

      //$out = array();
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $MYSQLI->close();
      $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($out);
   }

   public function get_event($event_id)
   {
      $MYSQLI = get_db_connection();


      $result = $MYSQLI->query("SELECT events.id,events.name,type,venues.name AS venue_name,venues.lat,venues.lng,events.slug,events.logo,web,notes,category_id,venue_id,DATE_FORMAT(start_date,'%Y-%m-%d') as start_date ,DATE_FORMAT(end_date,'%Y-%m-%d') as end_date,published FROM events,venues WHERE events.venue_id = venues.id AND events.id = '$event_id'") or die($MYSQLI->error);

      if ($rows = $result->fetch_assoc())
      {
         // Select events tags
         $tags_ids = array();
         $tags = $MYSQLI->query("SELECT tags.id, tags.name, tags.slug FROM tags,events_tags WHERE tags.id = events_tags.tag_id AND events_tags.event_id = '$event_id'") or die($MYSQLI->error);
         while ($tag = $tags->fetch_assoc())
         {
            $tags_ids[] = $tag;
            //$tags_names .=$tag["name"] . ",";
         }
         // Add events tags to the primary result
         $rows["tags"] = ($tags_ids);
         $rows["notes"] = stripcslashes($rows["notes"]);
         //$rows["tags_ids"] = ($tags_ids);
         $MYSQLI->close();
         // $rows["logo"] = "/media/" . $rows["logo"];
         return json_encode($rows);
      }
      $rows["id"] = $event_id;
      return json_encode($rows);
   }

   public function get_event_by_slug($event_slug)
   {
      $MYSQLI = get_db_connection();
      if ($_REQUEST["eventSlug"])
      {
         $event_slug = $_REQUEST["eventSlug"];
      }

      $result = $MYSQLI->query("SELECT events.id,events.name,type,venues.name AS venue_name,venues.lat,venues.lng,events.slug,events.logo,web,notes,category_id,venue_id,DATE_FORMAT(start_date,'%Y-%m-%d') as start_date ,DATE_FORMAT(end_date,'%Y-%m-%d') as end_date, published FROM events,venues WHERE events.venue_id = venues.id AND events.slug = '$event_slug'") or die($MYSQLI->error);

      if ($rows = $result->fetch_assoc())
      {
         $tags_ids = array();
         $tags = $MYSQLI->query("SELECT tags.id, tags.name, tags.slug FROM tags,events_tags WHERE tags.id = events_tags.tag_id AND events_tags.event_id = '{$rows["id"]}'") or die($MYSQLI->error);
         while ($tag = $tags->fetch_assoc())
         {
            $tags_ids[] = $tag;
         }
         // Add events tags to the primary result
         $rows["tags"] = ($tags_ids);
         $MYSQLI->close();
         //$rows["logo"] = "/media/" . $rows["logo"];
         return json_encode($rows);
      }
   }

   public function add_tags_to_event($tags = null, $event_id = null)
   {
      $MYSQLI = get_db_connection();
      $tags = explode(",", $tags);
      foreach ($tags as $tag_name)
      {
         //$tag_slug = EWCore::t
         //$tag_id;
         $exist = $MYSQLI->query("SELECT * FROM tags WHERE name = '$tag_name'") or die($MYSQLI->error);
         if ($row = $exist->fetch_assoc())
         {
            $tag_id = $row["id"];
         }
         else
         {
            $created = date('Y-m-d H:i:s');
            $tag_slug = EWCore::to_slug($tag_name, "tags");
            $MYSQLI->query("INSERT INTO tags (name, slug, created, modified) VALUES ('$tag_name', '$tag_slug' , '$created', '$created')") or die($MYSQLI->error);
            $tag_id = $MYSQLI->insert_id;
         }
         $MYSQLI->query("INSERT INTO events_tags (event_id,tag_id) VALUES ('$event_id', '$tag_id')") or die($MYSQLI->error);
      }
   }

   public function add_event($user_id = null, $category_id = null
   , $venue_id = null
   , $repeat_parent = null
   , $name = null, $type = null, $logo = null, $short_url = null, $start_date = null, $end_date = null, $notes = null, $web = null, $tags = null, $published = null, $promoted = null)
   {
      $MYSQLI = get_db_connection();
      if (!$user_id)
         $user_id = $_SESSION["EW.USER_ID"];
      if (!$category_id)
         $category_id = $MYSQLI->real_escape_string($_REQUEST['category_id']);
      if (!$venue_id)
         $venue_id = $MYSQLI->real_escape_string($_REQUEST['venue_id']);
      if (!$repeat_parent)
         $repeat_parent = $MYSQLI->real_escape_string($_REQUEST['repeat_parent']);
      if (!$name)
         $name = $MYSQLI->real_escape_string($_REQUEST['name']);
      if (!$type)
         $type = $MYSQLI->real_escape_string($_REQUEST['type']);
      if (!$logo)
         $logo = $MYSQLI->real_escape_string($_REQUEST['logo']);
      if (!$short_url)
         $short_url = $MYSQLI->real_escape_string($_REQUEST['shorturl']);
      if (!$start_date)
         $start_date = $MYSQLI->real_escape_string($_REQUEST['start_date']);
      if (!$end_date)
         $end_date = $MYSQLI->real_escape_string($_REQUEST['end_date']);
      if (!$notes)
         $notes = $MYSQLI->real_escape_string($_REQUEST['notes']);
      if (!$web)
         $web = $MYSQLI->real_escape_string($_REQUEST['web']);
      if (!$published)
         $published = $MYSQLI->real_escape_string($_REQUEST['published']);
      if (!$promoted)
         $promoted = $MYSQLI->real_escape_string($_REQUEST['promoted']);
      if (!$tags)
         $tags = $MYSQLI->real_escape_string($_REQUEST['tags']);

      if (!$end_date)
         $end_date = $start_date;

      $stm = $MYSQLI->prepare("INSERT INTO events (user_id, 
       category_id, 
       venue_id, repeat_parent, name, slug, type, logo, shorturl, start_date, end_date, notes, web, published, promoted, created, modified)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)") or die($MYSQLI->error);
      $stm->bind_param("sssssssssssssssss", $user_id, $category_id, $venue_id, $repeat_parent, $name, EWCore::to_slug($name, "events"), $type, $logo, $short_url, $start_date, $end_date, $notes, $web, $published, $promoted, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $event_id = $stm->insert_id;
         $this->add_tags_to_event($tags, $event_id);
         $res = array("status" => "success", "id" => $event_id, "message" => "The event $name has been added successfully", "error_message" => $MYSQLI->error);
      }
      else
      {
         $res = array("status" => "unsuccess", "message" => "The event $name has been NOT added", "error_message" => $MYSQLI->error);
      }
      /* if ($result["id"])
        {
        $content_id = $result["id"];

        // Call plugins actions
        $actions = EWCore::read_actions_registry("ew-article-action-add");
        try
        {
        foreach ($actions as $id => $data)
        {
        if (method_exists($data["class"], $data["function"]))
        {
        $function_result = call_user_func(array($data["class"], $data["function"]), $content_id);
        if ($function_result != true)
        {
        $message.=$function_result . "<br/>";
        }
        }
        }
        } catch (Exception $e)
        {

        }
        $res = array("status" => "success", "categoryId" => $content_id, "title" => $title, "error_message" => $message);
        // End of plugins actions call
        } */

      return json_encode($res);
   }

   public function update_event($event_id = null, $category_id = null
   , $venue_id = null
   , $repeat_parent = null
   , $name = null, $type = null, $logo = null, $short_url = null, $start_date = null, $end_date = null, $notes = null, $web = null, $tags = null, $published = null, $promoted = null)
   {
      $MYSQLI = get_db_connection();
      if (!$event_id)
         $event_id = $MYSQLI->real_escape_string($_REQUEST['id']);
      if (!$category_id)
         $category_id = $MYSQLI->real_escape_string($_REQUEST['category_id']);
      if (!$venue_id)
         $venue_id = $MYSQLI->real_escape_string($_REQUEST['venue_id']);
      if (!$repeat_parent)
         $repeat_parent = $MYSQLI->real_escape_string($_REQUEST['repeat_parent']);
      if (!$name)
         $name = $MYSQLI->real_escape_string($_REQUEST['name']);
      if (!$type)
         $type = $MYSQLI->real_escape_string($_REQUEST['type']);
      if (!$logo)
         $logo = $MYSQLI->real_escape_string($_REQUEST['logo']);
      if (!$short_url)
         $short_url = $MYSQLI->real_escape_string($_REQUEST['shorturl']);
      if (!$start_date)
         $start_date = $MYSQLI->real_escape_string($_REQUEST['start_date']);
      if (!$end_date)
         $end_date = $MYSQLI->real_escape_string($_REQUEST['end_date']);
      if (!$notes)
         $notes = $MYSQLI->real_escape_string($_REQUEST['notes']);
      if (!$web)
         $web = $MYSQLI->real_escape_string($_REQUEST['web']);
      if (!$tags)
         $tags = $MYSQLI->real_escape_string($_REQUEST['tags']);
      if (!$published)
         $published = $MYSQLI->real_escape_string($_REQUEST['published']);
      if (!$promoted)
         $promoted = $MYSQLI->real_escape_string($_REQUEST['promoted']);

      $stm = $MYSQLI->prepare("UPDATE events SET  
       category_id = ?, 
       venue_id = ?,
       repeat_parent = ?,
       name = ?,
       slug = ?,
       type = ?,
       logo = ?,
       shorturl = ?,
       start_date = ?,
       end_date = ?, 
       notes = ?,
       web = ?,
       published = ?,
       promoted = ?,
       modified = ? 
       WHERE id = ?") or die($MYSQLI->error);
      $stm->bind_param("ssssssssssssssss", $category_id, $venue_id, $repeat_parent, $name, EWCore::to_slug($name, "events"), $type, $logo, $short_url, $start_date, $end_date, $notes, $web, $published, $promoted, date('Y-m-d H:i:s'), $event_id) or die($MYSQLI->error);
      if ($stm->execute())
      {
         $MYSQLI->query("DELETE FROM events_tags WHERE event_id = '$event_id'") or die($MYSQLI->error);
         $this->add_tags_to_event($tags, $event_id);
         $res = array("status" => "success", "message" => "The event $name has been updated successfully", "error_message" => $MYSQLI->error);
      }
      else
      {
         $res = array("status" => "unsuccess", "message" => "The event $name has been NOT updated", "error_message" => $MYSQLI->error);
      }

      return json_encode($res);
   }

   public function feeder_event_sub_menus()
   {
      $city = "all-cities";
      if ($GLOBALS["page_parameters"][0] && gettype($GLOBALS["page_parameters"][0]) == "string")
      {
         $city = $GLOBALS["page_parameters"][0];
      }
      $items = array();
      $items[] = array("title" => "All", "link" => "events-list/", "id" => 0);
      $items[] = array("title" => "Clubs & Discos", "link" => "events-list/1/$city", "id" => 1);
      $items[] = array("title" => "Restaurant", "link" => "events-list/2/$city", "id" => 2);
      $items[] = array("title" => "Shops", "link" => "events-list/3/$city", "id" => 3);
      $items[] = array("title" => "Cultural", "link" => "events-list/4/$city", "id" => 4);

      return json_encode($items);
   }

   public function feeder_location_sub_menus()
   {
      $items = array();
      $MYSQLI = get_db_connection();
      $result = $MYSQLI->query("SELECT cities.slug, cities.name FROM cities,venues,events WHERE cities.id = venues.city_id 
      AND venues.id = events.venue_id GROUP BY cities.id ORDER BY events.start_date DESC LIMIT 10") or die($MYSQLI->error);

      $items[] = array("title" => "All Cities", "link" => "events-list/all-cities");
      while ($rows = $result->fetch_assoc())
      {
         $items[] = array("title" => $rows["name"], "link" => "events-list/{$rows["slug"]}");
      }

      return json_encode($items);
   }

   public function login_register_form()
   {
      echo $widget_style_class;
      ob_start();
      include 'login-register-form.php';
      $html = ob_get_clean();
      return json_encode(array("html" => $html));
   }

   public function feeder_event_info()
   {
      $event_id = $_REQUEST["_parameters"];
      if (is_numeric($event_id))
         $event_info = json_decode($this->get_event($event_id), true);
      else
         $event_info = json_decode($this->get_event_by_slug($event_id), true);

      $type = "All";
      switch ($event_info["type"])
      {
         case "1":
            $type = "Clubs & Discos";
            break;
         case "2":
            $type = "Restaurant";
            break;
         case "3":
            $type = "tr{Shops}";
            break;
         case "4":
            $type = "Cultural";
            break;
      }
      \admin\WidgetsManagement::set_html_title($event_info["name"]);
      \admin\WidgetsManagement::set_html_keywords("{$event_info["name"]}., {$event_info["venue_name"]}, $type");
      ob_start();
      echo "<div class='widget-header event-info'><h1>{$event_info["name"]}</h1></div>";
      ?>
      <div class="widget-content event-info">
         <p class="date">
            <?php echo "From: {$event_info["start_date"]} to {$event_info["end_date"]}"; ?>
         </p>
         <p class="address">
            <?php echo $event_info["venue_name"]; ?>
         </p>
         <p class="notes">
            <?php echo nl2br(stripcslashes($event_info["notes"])); ?>
         </p>
         <?php
         if ($event_info["web"])
            echo "<p class='website'><a href='http://{$event_info["web"]}' target='_blank'>{$event_info["web"]}</a></p>"
            ?>
         <ul class="tags">
            <?php
            //print_r($event_info);
            foreach ($event_info["tags"] as $tag)
            {
               echo "<li><a href='./events-with-tag/{$tag["slug"]}'>{$tag["name"]}</a></li>";
            }
            ?>
         </ul>

         <div class="map" id="map-canvas" style="display:block;width:auto;height:400px;"></div>
      </div>
      <script>
         var script = document.createElement('script');
         script.id = "google-map-api";
         script.type = 'text/javascript';
         script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' + 'callback=initMap';
         document.body.appendChild(script);
         function initMap()
         {
            var marker = new google.maps.Marker();
            var mapOptions = {
               zoom: 15,
               streetViewControl: false,
               center: new google.maps.LatLng(<?php echo $event_info["lat"] . "," . $event_info["lng"] ?>),
               mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map(document.getElementById('map-canvas'),
                    mapOptions);
            marker.setPosition(new google.maps.LatLng(<?php echo $event_info["lat"] . "," . $event_info["lng"] ?>));
            marker.setMap(map);
         }

      </script>
      <?php
      $html = ob_get_clean();
      return json_encode(array("html" => $html));
   }

   public function feeder_latest_events($limit)
   {
      $MYSQLI = get_db_connection();
      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(start_date,'%e-%m-%Y') as date FROM events ORDER BY start_date DESC LIMIT 5") or die($MYSQLI->error);
      $feed = array();
      while ($rows = $result->fetch_assoc())
      {
         ob_start();
         ?>
         <a href="#">
            <h3>
               <?php echo $rows["date"] ?>
            </h3>
            <p>
               <?php echo $rows["name"] ?>
            </p>
         </a>
         <?php
         $html = ob_get_clean();
         $feed[] = array("html" => $html);
      }
      return json_encode($feed);
   }

   public function event_search_form()
   {
      if ($GLOBALS["page_parameters"][0])
         $type = $GLOBALS["page_parameters"][0];
      if ($GLOBALS["page_parameters"][1] && $GLOBALS["page_parameters"][1] != "all-countries")
         $c_country = $GLOBALS["page_parameters"][1];
      if ($GLOBALS["page_parameters"][2] && $GLOBALS["page_parameters"][2] != "all-cities")
         $c_city = $GLOBALS["page_parameters"][2];
      if ($GLOBALS["page_parameters"][3] && $GLOBALS["page_parameters"][3] != "all-categories")
         $c_category = $GLOBALS["page_parameters"][3];
      if (!$type)
         $type = 0;
      ob_start();
      ?>
      <div class="row">
         <form class="box-purple">

            <select class="col-xs-3" id="country_id" name="country_id" >
               <option value="all-countries">Country</option>
               <?php
               $countries = new Countries();
               $cl = json_decode($countries->getcountries_list(), true);
               $cl = $cl["result"];
               foreach ($cl as $country)
               {
                  if ($country["slug"] === $c_country)
                  {
                     echo "<option value='{$country["slug"]}' selected>{$country["name"]}</option>";
                  }
                  else
                  {
                     echo "<option value='{$country["slug"]}' >{$country["name"]}</option>";
                  }
               }
               ?>
            </select>

            <select class="col-xs-3" id="city_id" name="city_id" >
               <option value="all-cities">City</option>
               <?php
               $countries = new Cities();
               $cl = json_decode($countries->getcities_by_country_slug($c_country), true);
               $cl = $cl["result"];
               foreach ($cl as $city)
               {
                  if ($city["slug"] === $c_city)
                  {
                     echo "<option value='{$city["slug"]}' selected>{$city["name"]}</option>";
                  }
                  else
                  {
                     echo "<option value='{$city["slug"]}' >{$city["name"]}</option>";
                  }
               }
               ?>
            </select>

            <select class="col-xs-3" id="category_id" name="category_id" >
               <option value="all-categories">Category</option>
               <?php
               $categories = new Categories();
               $cl = json_decode($categories->get_categories_list(), true);
               $cl = $cl["result"];
               foreach ($cl as $category)
               {
                  if ($category["slug"] == $c_category)
                     echo "<option value='{$category["slug"]}' selected>{$category["name"]}</option>";
                  else
                     echo "<option value='{$category["slug"]}' >{$category["name"]}</option>";
               }
               ?>
            </select>
            <div class="col-xs-3">
               <button class="btn btn-submit" style="width:100%;" type="button" id="event-search-btn">
                  Search
               </button>
            </div>
         </form>
      </div>
      <script>
         $("#country_id").on("change", function () {

            if ($("#country_id").val() === "0")
            {
               $("#city_id").html("<option value='0'>City</option><option value='0' disabled>Select a Country</option>");
               $("#city_id").selectpicker("refresh");
               return;
            }
            $("#city_id").html("<option value='0'>Loading Cities...</option>");
            $.post("<?php echo EW_ROOT_URL ?>app-culturenight/Cities/get_cities_by_country_slug", {
               countrySlug: $("#country_id").val()
            },
            function (data) {
               $("#city_id").html("<option value='all-cities'>All Cities</option>");
               $.each(data.result, function (key, item) {
                  $("#city_id").append("<option value='" + item.slug + "'>" + item.name + "</option>");
               });
               $("#city_id").selectpicker("refresh");
            }, "json");
         });
         $("#event-search-btn").on("click", function () {
            window.location = "<?php echo EW_ROOT_URL . "events-list/" . $type ?>/" + $("#country_id").val() + "/" + $("#city_id").val() + "/" + $("#category_id").val();
         });
      </script>
      <?php
      $html = ob_get_clean();
      return json_encode(array("html" => $html));
   }

   public function event_with_tag($token = 0, $limit)
   {
      $MYSQLI = get_db_connection();
      //echo "asfasfasfasfasf";
      $tag = '%';
      $city = '%';
      if ($GLOBALS["page_parameters"][0])
         $tag = $GLOBALS["page_parameters"][0];
      if ($GLOBALS["page_parameters"][1] && $GLOBALS["page_parameters"][2] != "all-cities")
         $city = $GLOBALS["page_parameters"][1];

      $token = $MYSQLI->real_escape_string($_REQUEST["events-with-tag-token"]);
      $size = $MYSQLI->real_escape_string($_REQUEST["size"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$limit)
      {
         $limit = 99999999999999;
      }

      $token = $token * $limit;
      $result = $MYSQLI->query("SELECT events.id, events.slug, events.logo,start_date,events.name AS event_name,venues.name AS venue_name, SUBSTRING(notes,1,250) as event_note, venues.address,DATE_FORMAT(start_date,'%e-%m-%Y') as date,DATE_FORMAT(start_date,'%M') as month_name,DATE_FORMAT(start_date,'%e') as day_name 
      FROM cities,venues,events,categories,events_tags,tags
      WHERE cities.id = venues.city_id 
      AND venues.id = events.venue_id 
      AND events.category_id = categories.id 
      AND events.id = events_tags.event_id
      AND events_tags.tag_id = tags.id
      AND tags.slug = '$tag'
       AND cities.slug LIKE '$city' 
            ORDER BY start_date 
            DESC 
            LIMIT $token,$limit") or die($MYSQLI->error);
      //echo $type . " " . $city;

      $items = array();
      while ($rows = $result->fetch_assoc())
      {
         $html = $this->create_event_item($rows);
         $items[] = array("html" => $html, "title" => $rows["name"], "date" => $rows["date"], "link" => "events-list/$type/$country/$city/$category/{$rows["date_y_m_d"]}?currentDate={$rows["date_y_m_d"]}");
         //$num_rows = $rows["COUNT(*)"];
      }
      $ns = $MYSQLI->query("SELECT events.id
      FROM cities,venues,events,categories,events_tags,tags
      WHERE cities.id = venues.city_id 
      AND venues.id = events.venue_id 
      AND events.category_id = categories.id 
      AND events.id = events_tags.event_id
      AND events_tags.tag_id = tags.id
      AND tags.slug = '$tag'
       AND cities.slug LIKE '$city'") or die($MYSQLI->error);

      /* if ($rows = $num_rows->fetch_assoc())
        {
        $num_rows = $rows["COUNT(*)"];
        } */
      $num_rows = $ns->num_rows;
      $feed = array("num_rows" => $num_rows, "items" => $items);
      return json_encode($feed);
   }

   public function event_items_list($token = 0, $limit)
   {
      $MYSQLI = get_db_connection();
      $token = $MYSQLI->real_escape_string($_REQUEST["token"]);
      $size = $MYSQLI->real_escape_string($_REQUEST["size"]);
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$limit)
      {
         $limit = 99999999999999;
      }
      $type = '%';
      $country = "%";
      $city = '%';
      $category = '%';
      if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $GLOBALS["page_parameters"][0]))
      {
         $date = $GLOBALS["page_parameters"][0];
      }
      else if ($GLOBALS["page_parameters"][0] && $GLOBALS["page_parameters"][0] != "all-type")
      {
         $type = $GLOBALS["page_parameters"][0];
      }
      if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $GLOBALS["page_parameters"][1]))
      {
         $date = $GLOBALS["page_parameters"][1];
      }
      else if ($GLOBALS["page_parameters"][1] && $GLOBALS["page_parameters"][1] != "all-countries")
      {
         $country = $GLOBALS["page_parameters"][1];
      }
      if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $GLOBALS["page_parameters"][2]))
      {
         $date = $GLOBALS["page_parameters"][2];
      }
      else if ($GLOBALS["page_parameters"][2] && $GLOBALS["page_parameters"][2] != "all-cities")
      {
         $city = $GLOBALS["page_parameters"][2];
      }
      if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $GLOBALS["page_parameters"][3]))
      {
         $date = $GLOBALS["page_parameters"][3];
      }
      else if ($GLOBALS["page_parameters"][3] && $GLOBALS["page_parameters"][3] != "all-categories")
      {
         $category = $GLOBALS["page_parameters"][3];
      }
      if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $GLOBALS["page_parameters"][4]))
      {
         $date = $GLOBALS["page_parameters"][4];
      }
      // Default date if no date has been entered
      $start_date = date('Y-m-d');
      $date_query_string = "AND events.start_date >= '$start_date'";

      if ($_REQUEST["mes"] && $_REQUEST["ano"])
      {
         $mes = ($_REQUEST["mes"] < 10) ? '0' . $_REQUEST["mes"] : $_REQUEST["mes"];
         $date_query_string = "AND events.start_date LIKE '{$_REQUEST["ano"]}-$mes-%'";
      }
      if ($date)
      {
         $date_query_string = "AND events.start_date LIKE '$date%'";
      }

      //echo $date_query_string;
      //$token = $token * $limit;
      $result = $MYSQLI->query("SELECT events.id, events.slug, events.logo,start_date,events.name AS event_name,venues.name AS venue_name, SUBSTRING(notes,1,250) as event_note, venues.address,DATE_FORMAT(start_date,'%d-%m-%Y') as date , DATE_FORMAT(start_date,'%Y-%m-%d') as date_y_m_d,DATE_FORMAT(start_date,'%M') as month_name,DATE_FORMAT(start_date,'%e') as day_name 
      FROM cities,venues,events,categories 
      WHERE cities.id = venues.city_id 
      AND venues.id = events.venue_id 
      AND events.category_id = categories.id 
      AND events.type LIKE '$type' 
        AND cities.slug LIKE '$city' 
          AND categories.slug LIKE '$category'  
            $date_query_string
            ORDER BY start_date 
            ASC 
            LIMIT $token,$limit") or die($MYSQLI->error);

      $ns = $MYSQLI->query("SELECT events.id 
      FROM cities,venues,events,categories 
      WHERE cities.id = venues.city_id 
      AND venues.id = events.venue_id 
      AND events.category_id = categories.id 
      AND events.type LIKE '$type' 
        AND cities.slug LIKE '$city' 
          AND categories.slug LIKE '$category'  
            $date_query_string") or die($MYSQLI->error);

      $type = ($type == "%") ? "all-type" : $type;
      $country = ($country == "%") ? "all-countries" : $country;
      $city = ($city == "%") ? "all-cities" : $city;
      $category = ($category == "%") ? "all-categories" : $category;
      $items = array();
      while ($rows = $result->fetch_assoc())
      {
         $html = $this->create_event_item($rows);

         $items[] = array("html" => $html, "title" => $rows["name"], "date" => $rows["date"], "link" => "events-list/$type/$country/$city/$category/{$rows["date_y_m_d"]}?currentDate={$rows["date_y_m_d"]}");
         //$num_rows = $rows["COUNT(*)"];
      }


      /* if ($rows = $num_rows->fetch_assoc())
        {
        $num_rows = $rows["COUNT(*)"];
        } */
      $num_rows = $ns->num_rows;
      $feed = array("num_rows" => $num_rows, "items" => $items);
      return json_encode($feed);
   }

   private function create_event_item($event_info)
   {
      ob_start();
      ?>    
      <div class="content">
         <div class="seprator">
            <p class="address">
               <?php echo $event_info["venue_name"] ?>
            </p>
            <div class="date">
               <?php echo $event_info["day_name"] ?>
               <?php echo substr($event_info["month_name"], 0, 3) ?>
            </div>
         </div>
         <h3>
            <?php echo $event_info["event_name"] ?>
         </h3>  
         <?php
         if (($event_info["logo"]))
            echo "<img src='" . EW_ROOT_URL . "res/images/{$event_info["logo"]}' alt='{$event_info["event_name"]}'>";
         //else
         //echo "<img style='width:auto;' src='" . EW_ROOT_URL . "media/logo.png' alt='{$event_info["event_name"]}'>"
         ?>
         <div class="seprator">
            <p class="description">
               <?php echo nl2br(stripcslashes($event_info["event_note"])) ?>
            </p>
         </div>
         <div>
            <a class="btn btn-see-event" href="<?php echo ("event-info/{$event_info["slug"]}") ?>">See Event</a>
         </div>
      </div>
      <?php
      return ob_get_clean();
   }

   public function get_title()
   {
      return "Events";
   }

   public function get_description()
   {
      return "Manage events";
   }

//put your code here
}
