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
    if (file_exists($from) &&
        $this->_zip->open($from) === TRUE &&
        file_exists($to) &&
        is_writable($to)
    ) {
      $status = $this->_zip->extractTo($to);
      $this->_zip->close();
    }

    return $status;
  }

  /**
   * Compress files into zip archive
   *
   * @param $from
   *  Compress files from given folder
   * @param $to
   *  Save archive into the given folder
   *
   * @return bool
   *  If archive successfully created the returns true, false otherwise
   */
  public function compress($from, $to) {
    if ( $this->_zip->open($to, ZIPARCHIVE::CREATE) !== TRUE) {
      return FALSE;
    }

    $files = scandir($from);

    // add file if it is not a folder
    foreach ($files as $file) {
      $filePath = $from . '/' . $file;

      if ( file_exists($filePath) && !is_dir($filePath) ) {
        $this->_zip->addFile($filePath, $file);
      }
    }

    $this->_zip->close();

    return file_exists($to);
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
}
