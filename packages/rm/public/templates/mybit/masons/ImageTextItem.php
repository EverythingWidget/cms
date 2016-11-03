<?php

namespace mybit;

class ImageTextItem extends \ew\LayoutMason {

  public function __construct() {
    $this->template = 'mybit';
    $this->title = 'Image, Description';
    $this->set_data_kays([
        'banner',
        'title',
        'description'
    ]);
  }

  public function get_html($data) {
    $contents = $data['description']['content'];

    if (is_string($contents)) {
      $contents = [$contents];
    }

    $tags = is_array($data['description']['tag']) ? $data['description']['tag'] : [$data['description']['tag']];

    if (is_array($contents)) {
      $description = join('', array_map(function($item, $tag) {
                return "<$tag>$item</$tag>";
              }, $contents, $tags));
    }
    ob_start();
    ?>
    <img class="banner" src="<?= $data['banner']['src'] ?>">
    <div class="description">
      <h2 class="title"><?= $data['title']['content'] ?></h2>
    <?= $description ?>
    </div>
    <?php
    return ob_get_clean();
  }

}
