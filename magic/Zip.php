<?php

class Zip {
  private $_zip;
  private $_tmpFolder;

  public function __construct() {
    $this->_zip = new ZipArchive();
  }

  public function getTmpFolder() {
    return $this->_tmpFolder;
  }

  /**
   * Handle uploading of zip file.
   *
   * @param string $name
   *  Name of item in $_FILE
   *
   * @param string $folderPath
   *  Absolute path to the tmp folder.
   *
   * @return bool
   *  Returns true when all ok, false otherwise
   */
  public function handleZipUpload($name = 'zip', $folderPath = 'tmp') {
    if (!isset($_FILES[ $name ])) {
      return FALSE;
    }

    $zip =& $_FILES[ $name ];

    if (count($zip['name']) > 1 ||
      reset($zip['type']) != 'application/zip' ||
      empty($zip['tmp_name'])
    ) {
      return FALSE;
    }

    if ( ($this->_tmpFolder = $this->generateTmpFolder($folderPath)) === FALSE) {
      return FALSE;
    }

    echo $folderPath;

    return $this->extract(reset($zip['tmp_name']), $this->_tmpFolder);
  }

  /**
   * Extract zip archive.
   *
   * @param $from
   *  Path to the archive
   * @param $to
   *  Extract into the given folder
   *
   * @return bool
   *  TRUE if all is ok, FALSE otherwise
   */
  protected function extract($from, $to) {
    $status = FALSE;
    if ($this->_zip->open($from) === TRUE &&
        file_exists($to) &&
        is_writable($to)
    ) {
      $status = $this->_zip->extractTo($to);
      $this->_zip->close();
    }

    return $status;
  }

  /**
   * Generate temporary folder.
   *
   * @param string $folder
   *  Folder name
   *
   * @return string
   *  Folder name/timestamp.
   *  Or if folder name will be empty then the unix timestamp will returned
   */
  private function generateTmpFolder($folder = '') {
    if (!empty($folder)) {
      $folder .= '/';
    }

    $folder .= time();

    if (!file_exists($folder) && !mkdir($folder)) {
      return FALSE;
    }

    return $folder;
  }

  /**
   * Recursively remove folder.
   *
   * @param $dir
   */
  private function rrmdir($dir) {
    foreach (glob($dir . '/*') as $file) {
      if ( is_dir($file) ) {
        $this->rrmdir($file);
      }
      else {
        unlink($file);
      }
    }

    rmdir($dir);
  }

  private function forceDownloadFile($filePath) {
    $fileUrl = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $filePath;

    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: Binary');
    header('Content-disposition: attachment; filename="' . $filePath . '"');
    readfile($fileUrl);
  }
}
