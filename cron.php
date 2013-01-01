<?php

$images = glob('/home/antonkuz/antonkuzmenko.net/www/img/*');
foreach ($images as $img) {
  if (strpos($img, 'preview') === FALSE) {
    unlink($img);
  }
}
