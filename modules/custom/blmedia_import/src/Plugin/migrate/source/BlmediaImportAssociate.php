<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;



/**
 * Provides a 'BlmediaImportAssociate' migrate source.
 *
 * @MigrateSource(
 *  id = "blmedia_import_associate"
 * )
 */
class BlmediaImportAssociate extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('bestpractice', 'bp')
      ->fields('bp', array('associate'))
      ->isNotNull('bp.associate')
      ->condition('associate', '', '!=')
      ->distinct();
  }
  
  public function prepareRow(Row $row) {
    
    /**
     * Remove NULL refrence for keywords.
     * @var array $associate_names_array
     */
    
    $associate_names_array = explode(",", $row->getSourceProperty('associate'));
    foreach($associate_names_array as $associate_index => $associate_value) {
      trim($associate_value);
      if (!$associate_value|| 'NULL' == $associate_value) {
        unset($associate_names_array[$associate_index]);
      }
      else{
        $associate_names_array[$associate_index] = ucfirst($associate_value);
      }
    }
    
    $associates = implode("|", $associate_names_array);
    $row->setSourceProperty('associate', $associates);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array('associate' => $this->t("This is  the term of Associate"));
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'associate' => array(
        'type' => 'string',
        'alias' => 'bp',
      ),
    );
  }
}