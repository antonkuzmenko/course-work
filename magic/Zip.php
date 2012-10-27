<?php

class Zip {
  private $_zip;
  private $_tmpFolder;

  public function __construct() {
    $this->_zip = new ZipArchive();
    $this->_tmpFolder = $this->genTmpFolder('tmp');

    if (isset($_FILES['zip']) && $_FILES['zip']['type'] == 'application/zip' && !empty($_FILES['zip']['tmp_name'])) {
      $this->_zipPath = $_FILES['zip']['tmp_name'];
    }
  }

  /**
   * Extract into the given folder.
   *
   * @param $from
   *  Folder with the archive
   * @param $to
   *  Extract into the given folder
   *
   * @return bool
   *  TRUE if all is ok, FALSE otherwise
   */
  protected function extract($from, $to) {
    if ($this->_zip->open($from) === TRUE) {
      $this->_zip->extractTo($to);
      $this->_zip->close();

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Generate temporary folder.
   *
   * @param string $folder
   *  Folder name
   *
   * @return string
   *  Folder name + current time
   */
  private function genTmpFolder($folder = '') {
    if (!empty($folder)) {
      $folder .= '/';
    }

    return $folder . date_format(new DateTime(), 'Y-m-d_H-i-s');
  }

  /**
   * Recursively remove folder.
   *
   * @param $dir
   */
  private function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
      if(is_dir($file))
        $this->rrmdir($file);
      else
        unlink($file);
    }
    rmdir($dir);
  }

}
