<?php
namespace AKlump\LoftTesting\Simpletest;

interface TestProxyInterface {
  
  public function proxyRun();
  
  /**
   * Set the absolute filepath to the phpunit executable
   *
   * @param  string $proxy
   *
   * @return $this
   */
  public function proxySetExec($proxy);
  
  public function proxyGetExec();

  /**
   * Set the proxyPaths array.
   *
   * @param array $proxyPaths
   *
   * @return $this
   */
  public function proxySetPaths($proxyPaths);
  
  /**
   * Adds a single proxyPath.
   *
   * E.g., the dir for a phpunit test, or an all_tests.php for simpletests, etc.
   *
   * @param string $proxyPath
   *
   * @return $this
   */
  public function proxyAddPath($proxyPath);
  
  /**
   * Return the proxyPaths array.
   *
   * @return array
   */
  public function proxyGetPaths();
}