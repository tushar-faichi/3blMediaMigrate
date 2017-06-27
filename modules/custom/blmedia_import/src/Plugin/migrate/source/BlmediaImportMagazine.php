<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;



/**
 * Provides a 'BlmediaImportMagazine' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_magazine"
 * )
 */
class BlmediaImportMagazine extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('article', 'a')
      ->fields('a', array('magazine'))
      ->isNotNull('a.magazine')
      ->distinct();
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('magazine' => $this->t("This is  the type of Magazine"));
    return $fields;
  }

  public function prepareRow(Row $row) {
    //  Convert Category name for term.
    $type_name = ucfirst($row->getSourceProperty('magazine'));
    trim($type_name, '\'');
    trim($type_name, ' ');
    
    $row->setSourceProperty('magazine', $type_name);
    // Check if term already exist.
    $term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $type_name]);
    
    // Skip duplicate terms.
    if ($term) {
      $row->setSourceProperty('magazine', FALSE);
    }
    return parent::prepareRow($row);
  }
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'magazine' => array(
        'type' => 'string',
        'alias' => 'a',
      ),
    );
  }
}