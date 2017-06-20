<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

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
    ->distinct();
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('keyword' => $this->t("This is  the term of Keyword"));
    return $fields;
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