<?php

define('ROOT_ADR', $_SERVER['SERVER_NAME']);
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');

class Controller {
  private $_twig;
  private $_zip;

  public function __construct() {
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
    include_once('magic/Zip.php');

    $this->_zip = new Zip();
    $this->_zip->handleZipUpload('field-images', ROOT_DIR . 'tmp');
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

  /**
   * Clear templates cache.
   */
  protected function clearCache() {
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
  protected function arg($n = 0) {
    $args = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));

    if (isset($args[$n]) && !empty($args[$n])) {
      return $args[$n];
    }

    return '';
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

}
