<?php
session_start();

if (!isset($_SESSION['login'])) {
  include "Login.php";
  return;
}
?> 
<!DOCTYPE html>
<html>
  <head>
    <title>
      EW Admin
    </title>
    <?php include 'header.php'; ?>
  </head>
  <body id="base-pane" class="container <?= EWCore::get_language_dir($_REQUEST["_language"]); ?>" >
    <div id="app-content" >

      <div id="navigation-menu" class="navigation-menu">
        <div id="apps-menu" class="apps-menu" >
          <span id="app-title" class="apps-menu-title"></span>
          <ul class="apps-menu-list">
            <li v-for="app in apps">
              <a class="apps-menu-link" v-bind:data-app="app.id" v-bind:class="{ 'selected' : currentApp === app.id}">
                <span>{{app.title}}</span>
              </a>
            </li>
          </ul>
        </div>
        <div id="sections-menu" class="sections-menu">
          <system-list id="sections-menu-list" class="sections-menu-list" action="a">
            <div class="sections-menu-item">
              <a class="sections-menu-item-link" href="{{id}}" >{{title}}</a>
            </div>
          </system-list>
        </div>
      </div>

      <div id="app-main-actions"></div>

      <system-float-menu id="main-float-menu" class="system-float-menu">
        <div class="float-menu-indicator" indicator></div>
        <div class="float-menu-actions" actions>
          <button type="button"
                  class="btn btn-primary"
                  v-if="!action.hide"
                  v-for="action in actions" v-on:click="callActivity(action)">
            {{ action.title }}
          </button>
        </div>
      </system-float-menu>

      <div id="app-bar" class="app-bar" v-bind:class="styleClass">
        <div class="white-area">
          <div id="sections-menu-title" class="app-bar-title" v-bind:class="{ 'inline-loader' : isLoading }">
            {{ sectionsMenuTitle }}
          </div>

          <div class="app-bar-middle-section">
            <system-field class="field action-item search-field">
              <label>Search</label>
              <input class="text-field" type="text" />
            </system-field>
          </div>

          <div class="action-center">
            <?php
            if ($_SESSION['login']) {
              echo '<a class="ExitBtn action-item" href="api/admin/users-management/logout?url=' . EW_DIR_URL . 'html/admin/" ></a>';
            }
            ?>


          </div>  
        </div>
        <div id="tabs-menu" class="tabs-bar" >
          <ul class="nav nav-pills nav-black-text">
            <li v-for="tab in subSections" v-bind:class="{'active': tab.id === currentState}">
              <a v-bind:href="tab.state" rel="subsection" v-on:click="goTo(tab, $event)">{{ tab.title }}</a>
            </li>
          </ul>
        </div>
      </div>

      <div id="main-content" class="col-xs-12 in" 
           v-show="show" 
           v-bind:class="styleClass" 
           transition="in"></div>
    </div>

    <div id="notifications-panel"></div>   

    <?php include 'footer.php'; ?>      
  </body>
</html>
