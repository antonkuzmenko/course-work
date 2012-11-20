<?php

define('ROOT_ADR', $_SERVER['SERVER_NAME']);
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');

class Controller {
  private $_twig;

  public function __construct() {
    session_start();

    $this->loadTwig();
    $this->route();
  }

//  Actions

  public function indexAction() {
    return $this->render('index');
  }

  public function noneAction() {
    return 'There is no page with this address';
  }

  public function uploadAction() {
    $tmpFolder = '';

    include_once('magic/Zip.php');
    include_once('magic/Image.php');

    $image = new Image();
    $zip = new Zip();

    if (! $zip->handleZipUpload('field-images', ROOT_DIR . 'tmp') ) {
      return FALSE;
    }

    $tmpFolder = $zip->getTmpFolder();

    $images = scandir($tmpFolder);
    $images = array_filter($images, $this->filterImages($tmpFolder));

    $imageFolder = $tmpFolder . '/to_archive';

    if (!mkdir($imageFolder, 0777)) {
      return FALSE;
    }

    // all magic with image manipulation done in this cycle

    foreach ($images as $img) {
      $image->load($tmpFolder . '/' . $img);

      foreach ($_GET as $f => $v) {
        $f = $this->underscoreToCamel($f);
        echo $f . '<br>';
        if ( method_exists($image, $f) && !empty($v) ) {
          call_user_func(array($image, $f), $v);
        }
      }

      $image->save($imageFolder . '/' . $img);
    }

    $zipName = $tmpFolder . '/antonkuzmenko.net.zip';

    if ( $zip->compress($imageFolder, $zipName) ) {
      $this->forceDownloadFile($zipName);
    }

    $this->rrmdir($tmpFolder);

    return '';
  }

//  Helpers

  /**
   * Load and create Twig object.
   */
  private function loadTwig() {
    include_once('twig/lib/Twig/Autoloader.php');
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('templates');

    $this->_twig = new Twig_Environment($loader, array(
      'cache' => 'cache',
      'debug' => TRUE,
    ));

    $this->_twig->addExtension(new Twig_Extension_Core());
  }

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
  private function render($template, $args = array()) {
    return $this->_twig->render('actions/' . $template . '.html.twig', $args);
  }

  private function route() {
    $action = $this->arg();

    if (empty($action)) {
      $action = 'index';
    }

    $actionName = $action . 'Action';

    if ( method_exists($this, $actionName) ) {
      echo $this->$actionName();
      return;
    }

    echo $this->noneAction();
  }

  /**
   * Clear templates cache.
   */
  private function clearCache() {
    $this->_twig->clearCacheFiles();
  }

  /**
   * Get url argument.
   *
   * @param int $n
   *  Position of the url argument
   *
   * @return string
   *  argument or empty string
   */
  private function arg($n = 0) {
    $args = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));

    if (isset($args[$n]) && !empty($args[$n])) {
      return $args[$n];
    }

    return '';
  }

  /**
   * Filter images.
   *
   * @param $dirPath
   *
   * @return callable
   *  Returns true if file is image
   */
  private function filterImages($dirPath) {
    return function($arg) use($dirPath) {
      if ($arg == '.' || $arg == '..') {
        return FALSE;
      }

      $path = $dirPath . '/' . $arg;

      if ( is_dir($path) ) {
        return FALSE;
      }

      $mime = getFileMime($path);

      if ( !in_array($mime, array('jpeg', 'png', 'gif')) ) {
        return FALSE;
      }

      return TRUE;
    };
  }

  /**
   * @param $filePath
   *  Path to file
   *
   * @param string $fileName
   *  New file name
   */
  private function forceDownloadFile($filePath, $fileName = 'antonkuzmenko.net.zip') {
    $fileUrl = str_replace(ROOT_DIR, 'http://' . ROOT_ADR . '/', $filePath);

    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: Binary');
    header("Content-disposition: attachment; filename='{$fileName}'");

    readfile($fileUrl);
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

  protected function underscoreToCamel($str) {
    $ar = explode('-', $str);
    $newStr = '';

    foreach ($ar as $v) {
      $newStr .= ucfirst($v);
    }

    return lcfirst($newStr);
  }
}

function getFileMime($pathToFile) {
  if (!file_exists($pathToFile)) {
    return FALSE;
  }

  $fInfo = finfo_open(FILEINFO_MIME_TYPE);
  $mimeType = finfo_file($fInfo, $pathToFile);
  finfo_close($fInfo);

  list(, $type) = explode('/', $mimeType);

  return $type;
}
