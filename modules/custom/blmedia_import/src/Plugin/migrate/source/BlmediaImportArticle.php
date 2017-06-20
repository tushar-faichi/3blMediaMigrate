<?php

namespace Drupal\blmedia_import\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\process\Explode;

/**
 * Provides a 'BlmediaImportArticle' migrate source.
 *
 * @MigrateSource(
 *  id = "3bl_ep_article_read"
 * )
 */
class BlmediaImportArticle extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {    
    $query = $this->select('article', 'a')
      ->fields('a',  array('articleID',
        'issueDate',
        'created',
        'updated',
        'title',
        'content',
        'link1',
        'link2',
        'image',
        'magazine',
        'type',
        'email1',
        'keyword1',
        'keyword2',
        'keyword3',
        'keyword4',
        'email2'));
      
      //$query->condition("a.articleID", "9704", "=");
      
      $query->addExpression("concat(keyword1,'|',keyword2,'|',keyword3,'|',keyword4)", "keyword");
      $query->addExpression("concat(link1,'|',link2)", "link");
      $query->addExpression("concat(email1,'|',email2)", "email");
      
    //$query->leftjoin('keywords', 'k', 'k.articleID = a.articleID');
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
      'link' => $this->t("External Link1"),
      'image' => $this->t("Image"),
      'magazine' => $this->t("Magazine term"),
      'type' => $this->t("Type term"),
      'keyword' => $this->t("Keywords term"),
      'email1' => $this->t("Keywords term"),
      'email' => $this->t("Contacts email-1 address for article"));

    return $fields;
  }

  /**
  * {@inheritdoc}
  */
  public function prepareRow(Row $row) {
    
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
    
    
    //  Check & Update 'issueDate' as per D8.
    $issueDateSource = $row->getSourceProperty('issueDate');
    if ($issueDateSource && '0000-00-00' != $issueDateSource) {
      $issueDateFormatted = date("Y-m-d\TH:i:s", strtotime($issueDateSource));
      $row->setSourceProperty('issueDate', $issueDateFormatted);
    }
    else {
      $row->setSourceProperty('issueDate', date("Y-m-d\TH:i:s"));  // Set NULL/EMPTY
    }

    $createdDateSource = $row->getSourceProperty('created');
    if ($createdDateSource && '0000-00-00' != $createdDateSource) {
      $createdDateFormatted = date("Y-m-d\TH:i:s", strtotime($createdDateSource));
      $row->setSourceProperty('created', $createdDateFormatted);
    }
    else {
      $row->setSourceProperty('created', date("Y-m-d\TH:i:s"));  // Set NULL/EMPTY
    }

    $updatedDateSource = $row->getSourceProperty('updated');
    if ($updatedDateSource && '0000-00-00' != $updatedDateSource) {
      $updatedDateFormatted = date("Y-m-d\TH:i:s", strtotime($updatedDateSource));
      $row->setSourceProperty('updated', $updatedDateFormatted);
    }
    else {
      $row->setSourceProperty('updated', date("Y-m-d\TH:i:s"));  // Set NULL/EMPTY
    }
    
    
    /**
     * Remove NULL refrence for keywords.
     * @var array $keywords_array
     */    
    $keywords_array = explode("|", $row->getSourceProperty('keyword'));
    foreach($keywords_array as $keyword_index => $keyword_value) {
      trim($keyword_value);
      if (!$keyword_value || 'NULL' == $keyword_value) {
        unset($keywords_array[$keyword_index]);
      }
    }
    
    $keywords = implode("|", $keywords_array);
    $row->setSourceProperty('keyword', $keywords);
    
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array('articleID' => array('type' => 'integer', 'alias' => 'a'));
  }
}

