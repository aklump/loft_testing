<?php
/**
 * @file
 * Builds a cheatsheet for drupal web test case
 *
 * @ingroup loft_testing
 * @{
 */
namespace AKlump\LoftDataGrids;
require_once dirname(__FILE__) . '/../vendor/autoload.php';
use AKlump\LoftTesting\Simpletest\DrupalWebTestCase;

// @todo This is not working because I need to bootstrap drupal...
$cheatsheet = new ExportData();
DrupalWebTestCase::buildCheatSheet($cheatsheet);

$out = new FlatTextExporter($cheatsheet);
print $out->export();

