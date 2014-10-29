<?php
namespace AKlump\LoftTesting\ExportData;
use AKlump\LoftDataGrids\ExportData;
use AKlump\LoftTesting\ExportData\TaxonomyTermsExporter as Exporter;

/**
 * Represents a taxonomy terms exporter.
 */
class TaxonomyTerms extends SimpletestContent implements SimpletestContentInterface {

  protected $data = array(
    'vocabs' => array(),
  );

  /**
   * Set the vocabs array.
   *
   * @param array $vocabs
   *
   * @return $this
   */
  public function setVocabs($vocabs) {
    $this->data['vocabs'] = array();
    foreach($vocabs as $vocab) {
      $this->addVocab($vocab);
    }
  
    return $this;
  }
 
   /**
   * Adds a single vocab.
   *
   * @param string $vocab
   *
   * @return $this
   */ 
  public function addVocab($vocab) {
    $this->data['vocabs'][] = (string) $vocab;
  
    return $this;
  }

  /**
   * Return the vocabs array.
   *
   * @return array
   */  
  public function getVocabs() {
    return $this->data['vocabs'];
  }

  protected function getSqlStatement() {
    $sql = array();
    $sql[] = "SELECT v.machine_name as vocab, t.name, t.description, t.format, t.weight FROM taxonomy_term_data t JOIN taxonomy_vocabulary v USING (vid)";
    if (($vocabs = $this->getVocabs())) {
      $sql[] = "WHERE v.machine_name IN ('" . implode("','", $vocabs) . "')";
    }
    $sql[] = "ORDER BY t.weight;";

    return implode(" ", $sql);
  }

  public function show() {
    return $this->getSqlStatement();
  }

  public function export() {
    $query = $this->query($this->getSqlStatement());
    if (is_string($query)) {
      return $query;
    }

    $data = new ExportData;
    while($row = mysql_fetch_assoc($query)) {
      foreach ($row as $key => $value) {
        $data->add($key, $value);
      }
      $data->next();
    }

    $exporter = new Exporter($data);

    return $exporter->export();
  }
}