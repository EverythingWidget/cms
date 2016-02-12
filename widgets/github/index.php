<?php
\webroot\WidgetsManagement::add_html_script([
    "id" => "github-bjs",
    "src" => "https://buttons.github.io/buttons.js"
]);
$user = $widget_parameters["user"];
$repo = $widget_parameters["repo"];

$user_repo = "$user/$repo";
if (!isset($repo) || !isset($user)) {
  return;
}

if ($widget_parameters["star"]) {
  ?>
  <iframe src="https://ghbtns.com/github-btn.html?user=<?= $user ?>&repo=<?= $repo ?>&type=star&count=true&size=large"
          frameborder="0"
          scrolling="0"
          width="180px" 
          height="30px">
  </iframe>
  <?php
}

if ($widget_parameters["watch"]) {
  ?>
<iframe src="https://ghbtns.com/github-btn.html?user=<?= $user ?>&repo=<?= $repo ?>&type=watch&count=true&size=large&v=2" 
        frameborder="0" 
        scrolling="0" 
        width="180px" 
        height="30px">  
</iframe>
  <?php
}

if ($widget_parameters["follow"]) {
  ?>
<iframe src="https://ghbtns.com/github-btn.html?user=<?= $user ?>&type=follow&count=true&size=large" 
        frameborder="0" 
        scrolling="0" 
        width="180px" 
        height="30px">  
</iframe>
  <?php
}