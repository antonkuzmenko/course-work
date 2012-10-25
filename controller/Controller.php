<?php

class Controller {
  private $twig;

  public function __construct() {
    include_once('twig/lib/Twig/Autoloader.php');
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('templates');
    $this->twig = new Twig_Environment($loader, array(
      'cache' => 'twig/cache',
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
  public function render($template, $args = array()) {
    return $this->twig->render($template . '.html.twig', $args);
  }

  public function init() {
    echo $this->render('index', array());
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
}

