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

// We'll assume that if DRUPAL_ROOT is defined we have already bootstrapped
// drupal, and can just abort now.
if (defined('DRUPAL_ROOT')) {
  return;
}

/**
 * Recursively search upwards to locate drupal root.
 *
 * @return [type] [description]
 */
function _loft_testing_define_root() {
  $root = getcwd();
  if (file_exists($root . '/includes/bootstrap.inc')) {
    define('DRUPAL_ROOT', $root);
    return;  
  }

  if (defined('DRUPAL_ROOT')) {
    return;
  }

  if (chdir('../') && getcwd() !== '/') {
    return _loft_testing_define_root();
  }

  return;
}

_loft_testing_define_root();
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_VARIABLES);

