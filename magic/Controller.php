<?php

class Controller {
  private $twig;

  public function __construct() {
    $this->loadTwig();
    $this->route();
  }

//  Actions

  public function indexAction() {
    return $this->render('index', array());
  }

//  Helpers

  /**
   * Load and create Twig object.
   */
  private function loadTwig() {
    include_once('twig/lib/Twig/Autoloader.php');
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('templates');
    $this->twig = new Twig_Environment($loader, array(
      'cache' => 'cache',
      'debug' => TRUE,
    ));
    $this->twig->addExtension(new Twig_Extension_Core());
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
    return $this->twig->render('actions/' . $template . '.html.twig', $args);
  }

  /**
   * Clear templates cache.
   */
  protected function clearCache() {
    $this->twig->clearCacheFiles();
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
    list($args) = explode('/', $_SERVER['PATH_INFO']);
    if (isset($args[$n]) && !empty($args[$n])) {
      return $args[$n];
    }

    return '';
  }

  private function route() {
    $action = $this->arg(0);
    if ( empty($action) ) {
      $action = 'index';
    }

    $actionName = $action . 'Action';
    if ( method_exists($this, $actionName) ) {
      echo $this->$actionName();
    }
  }

}
