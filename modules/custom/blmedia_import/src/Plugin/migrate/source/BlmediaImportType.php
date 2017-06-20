<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

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
      ->distinct();
      // ->groupBy('a.magazine');
      
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