<?php

class Controller {

  /**
   * Render template.
   *
   * @param $path
   *  Template name
   * @param array $args
   *  Template arguments
   *
   * @return bool
   *  Returns true if template exists
   */
  public function render($path, $args = array()) {

    if ( file_exists($path) ) {
      extract($args, EXTR_OVERWRITE);
      file_get_contents($path);
      return TRUE;
    }

    return FALSE;
  }
}

