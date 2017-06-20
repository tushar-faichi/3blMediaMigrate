<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'BlmediaImportFiles' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_file"
 * )
 */
class BlmediaImportFiles extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('article', 'a')
      ->fields('a', array('image'))
      ->isNotNull('a.image')
      ->distinct()
      ->isNotNull('a.image')
      ->condition('a.image', ' ', '!=')
      ->condition('a.image', 'NULL', '!=');
      // ->groupBy('a.magazine');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('image' => $this->t("This is  the image file"),
        'file_source' => $this->t("This is  the source image file"),
        'file_destination' => $this->t("This is  the destination for image file")
    );
    return $fields;
  }
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    
  	$local_path = '/home/faichi/Downloads/ep_articles_images/';
  	$destination_base_uri = 'public://';
  	$filepath = $row->getSourceProperty('image');

  	$row->setSourceProperty('file_source', $local_path . $filepath);
  	$row->setSourceProperty('file_destination', $destination_base_uri . $filepath);

  	$file_name = basename($filepath);
  	$row->setSourceProperty('image', $file_name);
  	return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'image' => array(
        'type' => 'string',
        'alias' => 'a',
      ),
    );
  }
}