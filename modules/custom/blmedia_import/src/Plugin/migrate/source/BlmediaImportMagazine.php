<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

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
      ->distinct();
      // ->groupBy('a.magazine');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('magazine' => $this->t("This is  the type of Magazine"));
    return $fields;
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