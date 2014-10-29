<?php
namespace AKlump\LoftTesting\ExportData;
use AKlump\LoftDataGrids\Exporter;
use AKlump\LoftDataGrids\ExporterInterface;

/**
 * Class JSONExporter
 */
class TaxonomyTermsExporter extends Exporter implements ExporterInterface {
  protected $extension = '.php';

  public function getInfo() {
    $info = parent::getInfo();
    $info = array(
      'name' => 'Drupal 7 Taxonomy Terms',
      'shortname' => 'D7 Terms', 
      'description' => 'Export data in php format for creating taxonomy terms in Drupal 7.',
    ) + $info;

    return $info;
  }

  public function compile($page_id = NULL) {
    $pages = $this->getData()->get();
    if ($page_id && array_key_exists($page_id, $pages)) {
      $pages = array($pages[$page_id]);
    }

    $this->output   = array();
    
    $this->output[] = "- Add this first snippet to your test's setUp method.";
    $this->output[] = "require_once 'taxonomy_terms.php';";
    $this->output[] = '';
    $this->output[] = "Save the following contents into taxonomy_terms.php in the same folder as your test";
    $this->output[] = '<?php';
    foreach ($pages as $page) {
      foreach ($page as $row) {
        $vocab = array_shift($row);
        $term = $row['name'];
        $this->output[] = <<<EOD
// Import new term: {$term}
if (\$vocab = taxonomy_vocabulary_machine_name_load('{$vocab}')) {
  \$term = (object) array(
    'vid' => \$vocab->vid,
EOD;
        foreach ($row as $key => $value) {
          $this->output[] = "    '$key' => '$value',";
        }
        $this->output[] = <<<EOD
  );
  taxonomy_term_save(\$term);
}

EOD;
      }
    }
    $this->output = implode("\n", $this->output);
  }
}