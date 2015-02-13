<?php
namespace AKlump\LoftTesting\Simpletest;
use \AKlump\LoftLib\Code\Exposer;

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

    static::setUpBeforeClass();
  }

  /**
   * Sets the global $user object to an account.
   *
   * When the test code you're running is expecting to user global $user,
   * it usually doesn't want to use $GLOBALS['user'] (the admin running the
   * tests), so this module allows you to mock the global user for the duration
   * of one subtest.
   *
   * @param  obj $account
   */
  public function userMakeGlobal(\stdClass $account) {
    $this->userRestore();
    if (isset($account->uid)) {
      global $user;
      $this->userMakeGlobal = $user;
      $user = $account;
      $this->pass('Global user set to uid = ' . $user->uid);
    }
    else {
      $this->fail('Could not set global user; missing uid.');
    }
  }

  /**
   * Restores the global user after calling userMakeGlobal.
   *
   * THIS IS AUTOMATICALLY CALLED AT THE END OF EACH SUBTEST AND BEFORE
   * EACH CALL OF userMakeGlobal().
   */
  public function userRestore() {
    global $user;
    if (isset($this->userMakeGlobal)) {
      $user = $this->userMakeGlobal;
      unset($this->userMakeGlobal);
      $this->pass('Global user restored to uid = ' . $user->uid);
    }
  }

  public function tearDown() {
    static::tearDownAfterClass();
  }

  public static function setUpBeforeClass() {
    // May be extended if desired for PhpUnit syntax.
  }
  
  public static function tearDownAfterClass() {
    // May be extended if desired for PhpUnit syntax.
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
    $group = ltrim($group, '_');
    if (empty($this->loftDrupalWebTestCaseData['skipping'][$group])) {
      $this->loftDrupalWebTestCaseData['skipping'][$group] = $group;
      $this->error("Test group \"$group\" is currently being skipped.", $group);
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
      $this->setSubtestGroup($group);
      foreach($this->getSubtests($group) as $subtest) {
        $this->pass("Running subtest: $subtest", $group);
        
        $method = "setUpSub{$group}";
        if (method_exists($this, $method)) {
          $this->{$method}();
        }

        $this->{$subtest}();

        $method = "tearDownSub{$group}";
        if (method_exists($this, $method)) {
          $this->{$method}();
        }
        // Final cleanup
        $this->userRestore();
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
          if (preg_match("/^$regex.*/", $method)) {
            $testGroups[$test][] = $method;
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

  /**
   * Dumps a variable to the output as a warning.
   *
   * @param  mixed $var
   */
  public function dump($var) {
    parent::error(print_r($var, TRUE));
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

  public function assertArrayContains($value, $array, $message = '', $group = NULL) {
    $group = $group ? $group : $this->getSubtestGroup();
    if (empty($message)) {
      $message = "Assert that an array contains $value.";
    }    
    $this->assertTrue(array_search($value, $array) !== FALSE, $message, $group);
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
   * Asserts that $actual is of type $expected.
   *
   * @param  string $expected
   *   One of:
   *   - boolean || bool
   *   - integer || int
   *   - float
   *   - string
   *   - array
   *   - object
   *   - null
   * @param  mixed $actual
   * @param  string $message
   *   Optional.
   * @param  string $group
   *   Optional.
   */
  public function assertInternalType($expected, $actual, $message = '', $group = NULL) {
    $group   = $group ? $group : $this->getSubtestGroup();
    
    // We can can convert $actual to a string we will...
    $temp    = $actual;
    if (@settype($temp, 'string') === FALSE) {
      $temp = 'subject';
    }

    $message = $message ? (string) $message : "Failed asserting that \"$temp\" is of type \"$expected\".";
    $test = FALSE;
    switch ($expected) {
      case 'bool':
      case 'boolean':
        $test = is_bool($actual);
        break;

      case 'float':
        $test = is_float($actual);
        break;

      case 'int':
      case 'integar':
        $test = is_int($actual);
        break;
        
      case 'object':
        $test = is_object($actual);
        break;
        
      case 'array':
        $test = is_array($actual);
        break;

      case 'string':
        $test = is_string($actual);
        break;

      case 'null':
        $test = is_null($actual);
        break;
    }
    
    if (!$test) {
      $this->assertTrue($test, $message, $group);
    } 
  }
  
  /**
   * Deprecated
   */
  protected function skipSubtestGroup($group) {
    return $this->skipSubtests();
  }

  /**
   * Grants access to protected and private elements of a class.
   *
   * @code
   *   // Call a protected method
   *   $this->access($obj)->protectedMethod($arg, $arg2);
   *   
   *   // Read a protected property.
   *   $this->access($obj)->protectedProperty;
   *
   *   // Set a protected proptery.
   *   $this->access($obj)->protectedProperty = 5;
   * @endcode
   *
   * @param  object $obj
   *
   * @return \AKlump\LoftLib\Code\Exposer
   */
  public function access($obj) {
    return new Exposer($obj);
  }  
}