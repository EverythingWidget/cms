<?php

namespace admin;

use App;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin
 *
 * @author Eeliya
 */
class Admin extends App
{

   protected $name = "EW Admin";
   protected $description = "EverythingWidget administration panel";
   protected $version = "0.8";
   protected $type = "core_app";

   public function init()
   {
      
   }

   public function index()
   {
      $compId = $_REQUEST['compId'];
      $secId = $_REQUEST['_section_name'];
      $className = $_REQUEST['className'];
      $cmd = $_REQUEST['cmd'];
      $compPage = null;
      $pageTitle = 'Administration';
$view_data = [];
      $sectionTitle = '';
      if (!$compId)
      {
         $compId = "AppsManagement";
      }
      //print_r($_REQUEST);
      if (class_exists("admin\\" . $compId))
      {
         $ccc = "admin\\" . $compId;
         $sc = new $ccc($ccc, $_REQUEST);

         // Load current component content
         $compPage = \EWCore::process_command("admin", $compId, null);
         $temp = json_decode($compPage, true);
         // If the statusCode is not 200 then show the error
         if ($temp["statusCode"] && $temp["statusCode"] != 200)
         {
            //http_response_code(200);
            header('Content-Type: text/html');
            $compPage = "<div class='box box-error'><h2>{$temp["statusCode"]}</h2>{$temp["message"]}</div>";
         }
         $pageTitle = "tr{{$sc->get_title()}}";
      }

      $this->load_view('index.php', compact(['compPage', 'pageTitle','compId']));
   }

}
