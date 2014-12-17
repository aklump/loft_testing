<?php
namespace AKlump\LoftTesting\Simpletest;

class DrupalWebTestCase extends \DrupalWebTestCase {

  protected $loftDrupalWebTestCaseData = array(
    'groupedTests' => array(),
    'skipping' => array(), 
    'subtestGroup' => 'Other',
  );

  public function setUp($modules = array()) {

    // List out the tests that are being skipped.
    $class_methods = get_class_methods($this);
    foreach ($class_methods as $method) {
      if (strtolower(substr($method, 0, 5)) == '_test') {
        $this->skipSubtests($method);
      }
    }

    if (!is_array($modules)) {
      $modules = func_get_args();
    }
    parent::setUp($modules);
  }

  /**
   * Return the test group method that invoked another method.
   *
   * @return string
   */
  protected function getCallingTestMethod() {
    $e = new \Exception();
    $trace = $e->getTrace();

    return $trace[2]['function'];
  }

  /**
   * Register a group to skip all it's subtests.
   *
   * This will only work if you have used $this->doSubtests() as the body
   * of your test method.
   *
   * @param  string $group
   *
   * @return  $this
   */
  protected function skipSubtests($group = NULL) {
    if ($group === NULL) {
      $group = $this->getCallingTestMethod();
    }
    if (empty($this->loftDrupalWebTestCaseData['skipping'][$group])) {
      $this->loftDrupalWebTestCaseData['skipping'][$group] = $group;
      $this->error("Test group ($group) is currently being skipped.", $group);
    }    

    return $this;
  }

  /**
   * Run a group of tests by group name if they are not currently skipped.
   *
   * A subtest is any function that starts with "sub$group", e.g.,
   *
   * @code
   * public function test1() {
   *   // This function is called automatically by simpletest and a new
   *   // Drupal environment is created.
   *   $this->doSubtest(__FUNCTION__);
   * }
   *
   * public function subtest1DoSomeKindOfTest() {
   *   ...
   * }
   *
   * public function subtest1DoAnotherTest() {
   *   ...
   * }
   * @endcode
   *
   * @param  string $group The name of the testgroup method
   *
   * @return  $this
   */
  protected function doSubtests($group = NULL) {
    if ($group === NULL) {
      $group = $this->getCallingTestMethod();
    }
    if (in_array($group, $this->loftDrupalWebTestCaseData['skipping'])) {
      
    }
    else {
      //$this->assert(TRUE, "Running test group: $group");
      $this->setSubtestGroup($group);
      foreach($this->getSubtests($group) as $subtest) {
        $this->assert(TRUE, "Running subtest: $subtest", $group);
        $this->{$subtest}();
      }        
    }

    return $this;
  }

  /**
   * Return an array of subtests for a given group.
   *
   * @return array
   */  
  protected function getSubtests($group) {
    static $testGroups = array();
    if (empty($testGroups)) {
      $methods = get_class_methods($this);

      // Register all tests first
      foreach ($methods as $method) {
        if (preg_match('/^test.+/', $method)) {
          $testGroups[$method] = array();
        }
      }

      // Now register subtests
      foreach (array_keys($testGroups) as $test) {
        foreach ($methods as $method) {
          $regex = preg_quote("sub$test");
          if (preg_match("/^$regex.+/", $method)) {
            $testGroups[$test][] = $method;
            //$this->assert(TRUE, "Subtest found: $method");
          }
        }
      }
    }

    return isset($testGroups[$group]) ? $testGroups[$group] : array();
  }

  /**
   * Builds a cheatsheet into an ExportData object
   *
   * @param  ExportData $data
   */
  public static function buildCheatsheet(ExportData $data) {
    $methods = get_class_methods($this);
    sort($methods);
    foreach ($methods as $method) {
      $data->add($method);
    }
  }

  /**
   * Set the subtestGroup.
   *
   * @param string $subtestGroup
   *
   * @return $this
   */  
  public function setSubtestGroup($subtestGroup) {
    $this->loftDrupalWebTestCaseData['subtestGroup'] = (string) $subtestGroup;
  
    return $this;
  }
  
  /**
   * Return the subtestGroup.
   *
   * @return string
   */
  public function getSubtestGroup() {
    return $this->loftDrupalWebTestCaseData['subtestGroup'];
  }

  // These are PhpUnit aliases so I don't have to remember two systems
  public function assertFalse($value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    parent::assertFalse($value, $message, $group);
  }

  public function assertTrue($value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    parent::assertTrue($value, $message, $group);
  }

  public function assertEquals($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    $this->assertEqual($control, $value, $message, $group);
  }

  public function assertNotEquals($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    $this->assertNotEqual($control, $value, $message, $group);
  }  

  public function assertEmpty($value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    $this->assertTrue(empty($value), $message, $group);
  }

  public function assertNotEmpty($value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    $this->assertTrue(!empty($value), $message, $group);
  }

  public function assertSame($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    $this->assertIdentical($control, $value, $message, $group);
  }

  public function assertNotSame($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    $this->assertNotIdentical($control, $value, $message, $group);
  }

  public function assertInstanceOf($classname, $object, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $name = get_class($object);
      $message =  "Assert that Object ($name) is an instance of class \"$classname\"";
    }
    $this->assertTrue(is_a($object, $classname), $message, $group);
  }

  public function assertCount($control, $countable, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $actual = count($countable);
      $message = "Assert that actual size $actual matches expected size $control.";
    }
    $this->assertSame($control, count($countable), $message, $group);
  }

  public function assertArrayHasKey($key, $array, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that an array has the key $key.";
    }    
    $this->assertTrue(array_key_exists($key, $array), $message, $group);
  }

  public function assertArrayNotHasKey($key, $array, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that an array does not have the key $key.";
    }
    $this->assertFalse(array_key_exists($key, $array), $message, $group);
  }

  public function assertObjectHasAttribute($attribute, $object, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that an object has the attribute $attribute.";
    }    
    $this->assertTrue(property_exists($object, $attribute), $message, $group);
  }

  public function assertObjectNotHasAttribute($attribute, $object, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that an object does not have the attribute $attribute.";
    }
    $this->assertFalse(property_exists($object, $attribute), $message, $group);
  }

  public function assertGreaterThan($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that $value is greater than $control.";
    }
    $this->assertTrue($value > $control, $message, $group);
  }  

  public function assertGreaterThanOrEqual($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that $value is greater than or equal to $control.";
    }
    $this->assertTrue($value >= $control, $message, $group);
  }  

  public function assertLessThan($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that $value is less than $control.";
    }
    $this->assertTrue($value < $control, $message, $group);
  }  

  public function assertLessThanOrEqual($control, $value, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that $value is less than or equal to $control.";
    }
    $this->assertTrue($value <= $control, $message, $group);
  }

  public function assertContains($value, $array, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that $array contains $value.";
    }
    $test = FALSE;
    if (is_array($array)) {
      $test = in_array($value, $array);
    }
    elseif (is_object($array)) {
      $test = property_exists($array, $value);
    }
    $this->assertTrue($test, $message, $group);
  }

  /**
   * Deprecated
   */
  protected function skipSubtestGroup($group) {
    return $this->skipSubtests();
  }
}