<?php
/**
 * @file
 * Controller for exporting taxonomy terms for use by a simpletest
 *
 * Call this script from the command line with the path to the drupal settings file.
 *
 * @ingroup loft_testing
 * @{
 */
namespace AKlump\LoftTesting\ExportData;
require_once dirname(__FILE__) . '/../vendor/autoload.php';

array_shift($argv);
$settings_path = array_shift($argv);

if (empty($settings_path)) {
  print "Please call this script with the path to the settings.php file";
  exit;
}

$obj = new TaxonomyTerms();
if (!$obj->setSettingsFilepath($settings_path)) {
  print "$settings_path cannot be read, or is corrupt.";
  exit;
}

$obj->setVocabs($argv);
if (($php = $obj->export())) {
  print $php;
}
else {
  print($obj->show());
}
