<?php

class ImageTextItem extends \ew\LayoutMason {

  public function __construct() {
    $this->template = 'mybit';
    $this->title = 'Image, Description';
  }

  public function get_html($data) {
    ?>
    <img src="<?= $data['content_fields']['front-image']['src'] ?>">
    <div class="description">
      <h2>Title</h2>
      <p>The description goes here!</p>
    </div>
    <?php
  }

}

return new ImageTextItem();
