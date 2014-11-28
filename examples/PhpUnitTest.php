<?php
/**
 * @file
 * PHPUnit tests for the my_module module.
 */

/**
 * @var DRUPAL_ROOT
 *
 * If the root cannot be automatically detected (you will know this due to a
 * fatal error when you try to run the test), then you will have to do the
 * extra step of defining the root in each test file.
 */
// define('DRUPAL_ROOT', '/Library/Projects/website/public_html');

require_once dirname(__FILE__) . '/../../../loft_testing/includes/bootstrap.inc';

class LoftGtmTest extends \AKlump\LoftTesting\PhpUnit\TestCase {

  // ... place your tests here.  

  public function setUp() {
    // By default all bootstrap modules:
    // https://api.drupal.org/api/drupal/includes!module.inc/function/system_list/7
    // are loaded automatically: to have access to the functions in other
    // modules you need to send the module names through parent::setUp()...
    parent::setUp(array('my_module'));
  }
}
