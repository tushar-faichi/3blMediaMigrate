<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'BlmediaImportCategory' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_category"
 * )
 */
class BlmediaImportCategory extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('topics', 't')
    ->fields('t', array('name', 'topicID'))
    ->isNotNull('t.name')
    ->orderBy('t.name')
    ->distinct();
    
    
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('name' => $this->t("This is  the term of Category"),
        'TopicID' => $this->t("Topic ID for mapping from Article.")
    );
    return $fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    //  Convert Category name for term.    
    $category_name = ucfirst($row->getSourceProperty('name'));
    $category_name = str_replace('\'', '', $category_name);
    $category_name = trim($category_name, ' ');
    
    $row->setSourceProperty('name', $category_name);
    // Check if term already exist. 
    $term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $category_name]);
    
    // Skip duplicate terms. 
    if ($term) {
      $row->setSourceProperty('name', FALSE);
    }
    return parent::prepareRow($row);
  }
  
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'topicID' => array(
        'type' => 'integer',
        'alias' => 't',
      )
    );
  }
}