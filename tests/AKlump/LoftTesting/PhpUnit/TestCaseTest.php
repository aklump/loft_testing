<?php
/**
 * @file
 * PHPUnit tests for the {{ my_module_name }} module.
 */
require_once dirname(__FILE__) . '/../../../../vendor/autoload.php';

class TestCaseTest extends \AKlump\LoftTesting\PhpUnit\TestCase {

  public function testInvokeProptectedMethod() {
    $obj = new TestSubject;
    $this->assertSame(46, $obj->getAge());
    $this->assertSame(56, $this->access($obj)->getRealAge());
    $this->assertSame(46, $this->access($obj)->getAge());    
  }  

  public function testSetProtectedProperty() {
    $obj = new TestSubject;
    $this->access($obj)->age = 31;
    $this->assertSame(21, $obj->getAge());
  }

  public function testGetProtectedProperty() {
    $obj = new TestSubject;
    $this->assertSame(56, $this->access($obj)->age);
    $this->assertSame(46, $obj->getAge());
  }
}

/**
 * Class used in TestCaseTest
 */
class TestSubject {
  
  protected $age = 56;

  public function getAge() {
    return $this->age - 10;
  }

  protected function getRealAge() {
    return $this->age;
  }
}