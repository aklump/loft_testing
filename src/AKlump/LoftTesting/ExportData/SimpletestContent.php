<?php
namespace AKlump\LoftTesting\ExportData;

/**
 * Represents the base class for exporting content from Drupal to PHP code
 */
abstract class SimpletestContent implements SimpletestContentInterface {

  protected $data = array(
    'settingsFilepath' => '',
    'database' => array(), 
  );

  abstract public function export();
  abstract public function show();
  
  public function setSettingsFilepath($settingsFilepath) {
    $settingsFilepath = (string) $settingsFilepath;
    
    // See if we have a readable file
    if (!is_readable($settingsFilepath)) {
      return FALSE;
    }
    $this->data['settingsFilepath'] = $settingsFilepath;
    
    // Load the db settings
    require $settingsFilepath;
    if (!isset($databases['default']['default'])) {
      return FALSE;
    }

    $this->data['database'] = $databases['default']['default'];
  
    return TRUE;
  }
  
  public function getSettingsFilepath() {
    return $this->data['settingsFilepath'];
  }

  /**
   * Perform a mysql query using the settings file.
   *
   * @param  string $sql
   *
   * @return string||Resource
   *   If a string is returned, it means an error.
   */
  protected function query($sql) {
    $db = $this->data['database'];
    $link = mysql_connect($db['host'], $db['username'], $db['password']);
    if (!$link) {
      return 'Could not connect: ' . mysql_error();
    }
    mysql_select_db($db['database']);
    $query = mysql_query($sql, $link);
    mysql_close($link);

    return $query;
  }  
}