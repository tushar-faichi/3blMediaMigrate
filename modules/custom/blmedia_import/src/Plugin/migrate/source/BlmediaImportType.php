<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;



/**
 * Provides a 'BlmediaImportType' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_type"
 * )
 */
class BlmediaImportType extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    
    return $this->select('article', 'a')
      ->fields('a', array('type'))
      ->isNotNull('a.type')
      ->distinct();
  }
  
  public function prepareRow(Row $row) {
    //  Convert Category name for term.
    $type_name = ucfirst($row->getSourceProperty('type'));
    trim($type_name, '\'');
    trim($type_name, ' ');
    
    $row->setSourceProperty('type', $type_name);
    // Check if term already exist.
    $term = \Drupal::entityTypeManager()
    ->getStorage('taxonomy_term')
    ->loadByProperties(['name' => $type_name]);
    
    // Skip duplicate terms.
    if ($term) {
      $row->setSourceProperty('type', FALSE);
    }
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('type' => $this->t("This is  the term of Type"));
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'type' => array(
        'type' => 'string',
        'alias' => 'a',
      ),
    );
  }
}