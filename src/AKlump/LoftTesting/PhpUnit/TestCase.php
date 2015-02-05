<?php
/**
 * @file
 * Defines a new TestCase class.
 *
 * @ingroup test
 * @{
 */
namespace AKlump\LoftTesting\PhpUnit;
use \AKlump\LoftLib\Code\Exposer;

/**
 * Represents a TestCase object class.
 */
class TestCase extends \PHPUnit_Framework_TestCase  {

  public function setUp() {

    // Include modules base files needed for this test. This could have been
    // passed in as either a single array argument or a variable number of
    // string arguments.
    $modules = func_get_args();
    if (isset($modules[0]) && is_array($modules[0])) {
      $modules = $modules[0];
    }
    if ($modules) {
      foreach ($modules as $module) {
        \module_load_include('module', $module, $module);

        if (function_exists("{$module}_boot")) {
          call_user_func("{$module}_boot");
        }
      }
      $this->setup = TRUE;
    }  
  }

  public function testNoWarning() {
    // Sticking this here so we don't get warning:
    // No tests found in class "AKlump\LoftTesting\PhpUnit\TestCase".
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
