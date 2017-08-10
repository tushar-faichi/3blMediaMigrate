<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\process\Explode;

/**
 * Provides a 'BlmediaImportBestPractice' migrate source.
 *
 * @MigrateSource(
 *  id = "3bl_ep_bestpractice_read"
 * )
 */
class BlmediaImportBestPractice extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('bestpractice', 'b')
      ->fields('b',  array('articleID',
        'issueDate',
        'created',
        'updated',
        'title',
        'content',        
        'image',
        'noCSR',
        'pdf',
        'comment',  
        'introduction',
        'associate',  
        'magazine'));

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    
    $fields = array(
      'articleID' => $this->t("Source Article ID"),
      'issueDate' => $this->t("Article issue date(Month & Year)"),
      'created' => $this->t("The date YYYY-MM-DD H:i:s time that the Article was added"),
      'updated' => $this->t("The date YYYY-MM-DD H:i:s time that the Article was last updated"),
      'title' => $this->t("Title of Article"),
      'content' => $this->t("HTML format content"),      
      'image' => $this->t("Image"),
      'pdf' => $this->t("PDF files"),
      'magazine' => $this->t("Magazine term"));
    
    return $fields;
  }

  /**
  * {@inheritdoc}
  */
  public function prepareRow(Row $row) {
    
    
    // First letter for ARTICLE TITLE need to be capital.
    $row->setSourceProperty('title', ucfirst($row->getSourceProperty('title')));
    
    /**
     * Handle NULL images.
     * @var Ambiguous $image
     */
    
    $image = $row->getSourceProperty('image');    
    $wrong_file_flag = count(explode('/', $image));
    if ($wrong_file_flag > 1) {
      $row->setSourceProperty('image', '');  // Set NULL/EMPTY
    }
    else if (NULL == $image || 'NULL' == $image || 'null' == $image || ' ' == $image || null == $image || is_null($image) ) {
      $row->setSourceProperty('image', '');  // Set NULL/EMPTY
    }
    
    
    $image = $row->getSourceProperty('pdf');
    $wrong_file_flag = count(explode('/', $image));
    if ($wrong_file_flag > 1) {
      $row->setSourceProperty('pdf', '');  // Set NULL/EMPTY
    }
    else if (NULL == $image || 'NULL' == $image || 'null' == $image || ' ' == $image || null == $image || is_null($image) ) {
      $row->setSourceProperty('pdf', '');  // Set NULL/EMPTY
    }
    
    //  Check & Update 'issueDate' as per D8.
    $issueDateSource = $row->getSourceProperty('issueDate');
    if ($issueDateSource && '0000-00-00' != $issueDateSource) {
      $issueDateFormatted = date("Y-m-d\TH:i:s", strtotime($issueDateSource));
      $row->setSourceProperty('issueDate', $issueDateFormatted);
    }
    else {
      $row->setSourceProperty('issueDate', date("Y-m-d\TH:i:s"));  // Set NULL/EMPTY
    }

    
    // Node created and updated date.
    $createdDateSource = $row->getSourceProperty('created');
    if ($createdDateSource && '0000-00-00' != $createdDateSource) {
      $createdDateFormatted = date("Y-m-d\TH:i:s", strtotime($createdDateSource));
      $createdDateFormatted = strtotime($createdDateSource);
      $row->setSourceProperty('created', $createdDateFormatted);
    }
    else {
      $row->setSourceProperty('created', strtotime("now"));  // Set NULL/EMPTY
    }

    $updatedDateSource = $row->getSourceProperty('updated');
    if ($updatedDateSource && '0000-00-00' != $updatedDateSource) {
      $updatedDateFormatted = date("Y-m-d\TH:i:s", strtotime($updatedDateSource));
      $updatedDateFormatted = strtotime($updatedDateSource);
      $row->setSourceProperty('updated', $updatedDateFormatted);
    }
    else {
      $row->setSourceProperty('updated', strtotime("now"));  // Set NULL/EMPTY
    }
    
    
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
  public function getIds() {
    return array('articleID' => array('type' => 'integer', 'alias' => 'b'));
  }
}

