<?php
/**
 * @file
 * Minimum Drupal bootstrap for function loading.
 *
 * This file needs to be included in each phpunit test case file that wishes
 * to utilitize the Drupal functions.  You will add something like this, but
 * the exact path will be dependent upon your modules relationship to this
 * module in the drupal file system.
 *
 * @code
 *   require_once dirname(__FILE__) . '/../../../loft_testing/includes/bootstrap.php';
 * @endcode
 * 
 *
 * @ingroup loft_testing
 * @{
 */

/**
 * Recursively search upwards to locate drupal root.
 */
function _loft_testing_define_root() {
  if (defined('DRUPAL_ROOT')) {
    return;
  }

  $root = realpath(getcwd());
  if (file_exists($root . '/includes/bootstrap.inc')) {
    define('DRUPAL_ROOT', $root);
  }
  elseif (chdir('../') && getcwd() !== '/') {
    return _loft_testing_define_root();
  }
}

_loft_testing_define_root();
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';

// If we haven't bootstrapped Drupal yet, do so, minimally.
if (drupal_get_bootstrap_phase() === -1) {
  drupal_bootstrap(DRUPAL_BOOTSTRAP_VARIABLES);
  require_once DRUPAL_ROOT . '/includes/common.inc';
  require_once DRUPAL_ROOT . '/includes/path.inc';
}

require_once dirname(__FILE__) . '../../vendor/autoload.php';