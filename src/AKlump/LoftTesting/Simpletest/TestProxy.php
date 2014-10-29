<?php
namespace AKlump\LoftTesting\Simpletest;

abstract class TestProxy extends \DrupalUnitTestCase implements TestProxyInterface {

  protected $proxyExec;
  protected $proxyPaths = array();

  abstract public function proxyRun();
  
  public function proxySetExec($proxy) {
    $this->proxyExec = (string) $proxy;
  
    return $this;
  }
  
  public function proxyGetExec() {
    return $this->proxyExec;
  }

  public function proxySetPaths($proxyPaths) {
    $this->proxyPaths = array();
    foreach($proxyPaths as $proxyPath) {
      $this->addPhpUnitPath($proxyPath);
    }
  
    return $this;
  }
  
  public function proxyAddPath($proxyPath) {
    $this->proxyPaths[] = (string) $proxyPath;
  
    return $this;
  }
  
  public function proxyGetPaths() {
    return $this->proxyPaths;
  }
}