<?php
namespace AKlump\LoftTesting\Simpletest;

class PhpUnitTestProxy extends TestProxy {

  protected $proxyExec = '/usr/local/bin/phpunit';

  public function proxyRun() {
    foreach ($this->proxyGetPaths() as $dir) {
      $this->pass('PhpUnit tests running from: ' . $dir);

      $cmd = $this->proxyGetExec() . ' ' . $dir;
      $handle = popen("$cmd 2>&1", 'r');
      $output = stream_get_contents($handle);
      pclose($handle);

      if (preg_match('/^Time.*/m', $output, $matches)) {
        $this->pass($matches[0]);
      }
      $success = FALSE;    
      if (preg_match('/^OK.*/m', $output, $matches)) {
        $success = TRUE;
        $output = $matches[0];
      }
      elseif (preg_match('/^FAILURES!/m', $output)) {
        preg_match('/^Tests:.*/m', $output, $matches);
        $output = $matches[0];
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