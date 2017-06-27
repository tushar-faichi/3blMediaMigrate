<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;


/**
 * Provides a 'BlmediaImportKeyword' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_keywords"
 * )
 */
class BlmediaImportKeyword extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('keywords', 'k')
    ->fields('k', array('keyword'))
    ->isNotNull('k.keyword')
    ->distinct();
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('keyword' => $this->t("This is  the term of Keyword"));
    return $fields;
  }
  
  public function prepareRow(Row $row) {
    //  Convert Category name for term.
    $keyword_name = ucfirst($row->getSourceProperty('keyword'));    
    trim($type_name, '\'');
    trim($type_name, ' ');
    
    $row->setSourceProperty('keyword', $keyword_name);
    // Check if term already exist.
    $term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $keyword_name]);
    
    // Skip duplicate terms.
    if ($term) {
      $row->setSourceProperty('keyword', FALSE);
    }
    
    return parent::prepareRow($row);
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'keyword' => array(
        'type' => 'string',
        'alias' => 'k',
      )
    );
  }
}