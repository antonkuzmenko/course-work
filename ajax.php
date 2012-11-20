<?php

include_once('magic/Image.php');

if (isset($_GET['blur'])) {
  $image = new Image();
  $image->load('img/preview.jpg');

  foreach ($_GET as $f => $v) {
    $f = underscoreToCamel($f);
    if ( method_exists($image, $f) && !empty($v) ) {
      call_user_func(array($image, $f), $v);
    }
  }

  $imgPath = 'img/' . time() . '.jpg';

  $image->save($imgPath);

  echo '/' . $imgPath;
}

function underscoreToCamel($str) {
  $ar = explode('-', $str);
  $newStr = '';

  foreach ($ar as $v) {
    $newStr .= ucfirst($v);
  }

  return lcfirst($newStr);
}