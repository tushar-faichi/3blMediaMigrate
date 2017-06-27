<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'BlmediaImportLocation' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_location"
 * )
 */
class BlmediaImportLocation extends SqlBase {

  
  private $DefaultLocationCodes = array(
      "1" => "UK & NI Ireland",
      "2" => "Europe",
      "3" => "North America",
      "4" => "South America",
      "5" => "Middle East",
      "6" => "Africa",
      "7" => "Asia",
      "8" => "Australasia",
      "9" => "Global");
  
  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('article', 'a')
    ->fields('a', array('location'))
    ->isNotNull('a.location')
    ->distinct();
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('location' => $this->t("This is  the term of Location"),
        'location_name' => $this->t("LocationID for term  reference import mapping.")
    );
    return $fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    //  Convert Category name for term.    
    $location_name = $row->getSourceProperty('location');
    
    $row->setSourceProperty('location_name', $this->sourceLocationIDMapping($location_name));
    
    return parent::prepareRow($row);
  }
  
  // Mapping location for countries.
  public function sourceLocationIDMapping($locationID) {
    if(isset($this->DefaultLocationCodes[$locationID])) {
      return $this->DefaultLocationCodes[$locationID];
    }
    return FALSE;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'location' => array(
        'type' => 'integer',
        'alias' => 'a',
      )
    );
  }
}