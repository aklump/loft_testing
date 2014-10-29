<?php
namespace AKlump\LoftTesting\ExportData;

interface SimpletestContentInterface {
  
  /**
   * Return the exported php code representing the Drupal content
   *
   * @return string
   */
  public function export();

  /**
   * Show information about how the export is compiled, e.g., sql statement
   *
   * @return string
   */
  public function show();

  /**
   * Set the settingsFilepath.
   *
   * @param string $settingsFilepath
   *
   * @return bool
   *   If the database creds cannot be read from the filepath, false is returned.
   */
  public function setSettingsFilepath($settingsFilepath);
  
  /**
   * Return the settingsFilepath.
   *
   * @return string
   */
  public function getSettingsFilepath();
}