<input type="hidden" id="permission" name="permission">
<div class="col-lg-offset-2 col-lg-8 col-xs-12">
  <h3 class="form-title">All Permissions</h3>
</div>
<div class="col-lg-offset-2 col-lg-8 col-xs-12 content" id="all-permissions"  >
  <ul class="list permissions tree" data-toggle="buttons">
    <?php
    $permissions_titles = \EWCore::read_permissions_titles();
    if (isset($permissions_titles)) {
      foreach ($permissions_titles as $app_name => $sections) {
        ?>
        <li>
          <label data-value="<?= $app_name ?>">
            <i class="icon circle"></i>
            <h3 class="icon-header">
              <?= $sections['appTitle']; ?>
            </h3>
          </label>
          <ul class="row">
            <?php
            foreach ($sections['section'] as $section_name => $sections_permissions) {
              ?>
              <li >
                <label data-value="<?= "$app_name.$section_name" ?>">
                  <i class="icon circle"></i><h3 class="icon-header">
                    <?= $sections_permissions["sectionTitle"]; ?>
                  </h3>
                </label>
                <ul>
                  <?php
                  foreach ($sections_permissions['permission'] as $permission_name => $permission_info) {
                    ?>
                    <li class="permission-item">
                      <label  data-value="<?php echo "$app_name.$section_name.$permission_name" ?>">
                        <i class="icon circle"></i>
                        <h3 class="icon-header"><?= $permission_info['title'] ?></h3>
                        <p class="icon-header"><?= $permission_info['description'] ?></p>
                      </label>
                    </li>
                    <?php
                  }
                  ?>
                </ul>
              </li>
              <?php
            }
            ?>
          </ul>
        </li>
        <?php
      }
    }
    ?>
  </ul>
</div>