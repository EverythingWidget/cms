<?php
session_start();
$user_id = $_REQUEST['id'];

$data = $user_id ? EWCore::call_api('admin/api/users-management/users', ['id' => $user_id]) : [];

$tabs = EWCore::read_registry('ew/ui/forms/user/tabs');
?>
<form id="user-form"  action="#" method="POST" onsubmit="return false;">
  <div class="header-pane tabs-bar thin">
    <h1 id='form-title'>
      tr{New User}
    </h1>

    <ul class="nav nav-pills">
      <?php
      $active = 'active';
      foreach ($tabs as $id => $tab) {
        echo "<li class='$active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab['title'] . "}</a></li>";
        $active = '';
      }
      ?>
    </ul>
  </div>
  <div class="block-row form-content  tabs-bar">
    <div class="tab-content">
      <?php
      $active = 'active';
      foreach ($tabs as $id => $tab) {
        echo "<div class='tab-pane $active' id='{$id}'>" . EWCore::get_view($tab['template_url'], [
            'user_id' => $user_id,
            'data'    => $data['data']
        ]) . "</div>";
        $active = '';
      }
      ?>
    </div>
  </div>
  <div class="block-row footer-pane actions-bar action-bar-items">
  </div>
</form>
