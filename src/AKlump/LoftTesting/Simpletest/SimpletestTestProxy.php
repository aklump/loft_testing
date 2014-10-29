<?php
namespace AKlump\LoftTesting\Simpletest;

class SimpletestTestProxy extends TestProxy {
  
  protected $proxyExec = '/usr/bin/php';

  public function proxyRun() {
    foreach ($this->proxyGetPaths() as $all_tests_file) {
      $this->pass('Simpletest calling: ' . $all_tests_file);

      $cmd = $this->proxyGetExec() . ' ' . $all_tests_file;
      $handle = popen("$cmd 2>&1", 'r');
      $output = stream_get_contents($handle);
      pclose($handle);

      $success = FALSE;    
      if (preg_match('/^Test cases run.*/m', $output, $matches)) {
        $output = $matches[0];
        $success = (bool) strpos($output, 'Failures: 0');
      }
      
      $this->assertTrue($success, $output);

      //Check to see if we get that pesky Mamp error
      //http://www.princexml.com/forum/topic/1292/prince-on-os-x-mamp
      //http://jonathonhill.net/2012-06-22/cannot-run-a-binary-executable-from-php-and-mamp/
      if (!$success
        && (strstr($output, 'dyld: Symbol not found')
          || strstr($output, 'MAMP/Library/lib'))) {
        $this->error('If you\'re using MAMP, this link may help you: <a onclick="window.open(this.href); return false;" href="http://www.princexml.com/forum/topic/1292/prince-on-os-x-mamp">http://www.princexml.com/forum/topic/1292/prince-on-os-x-mamp</a>');
      }
    }
  }
}