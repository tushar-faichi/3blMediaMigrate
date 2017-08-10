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
    
    /**
     *  Build query which wil select all required column from source DB.
     *  Each column will get mapped to drupal fields.
     *  
     * @var \Drupal\Core\Database\Query\SelectInterface $query
     */
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
        'location',
        'category',
        'email2'));
      $query->condition("a.live", "1", "=");
      $query->addExpression("concat(keyword1,'|',keyword2,'|',keyword3,'|',keyword4)", "keyword"); // Handle multiple value field.
      $query->addExpression("concat(link1,'|',link2)", "link");
      $query->addExpression("concat(email1,'|',email2)", "email");
      
      // Do not excute query, just retrun query object.
    return $query;
  }

  /**
   * 
   * Nothing for migration process here, except  listing fields info  at drush and Migrate UI.
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
      'link1' => $this->t("External Link1"),
      'image' => $this->t("Image"),
      'magazine' => $this->t("Magazine term"),
      'type' => $this->t("Type term"),
      'keyword' => $this->t("Keywords term"),
      'category' => $this->t("Category term"),
      'location' => $this->t("Location term"),
      'email1' => $this->t("Keywords term"),
      'email' => $this->t("Contacts email-1 address for article"));
    return $fields;
  }

  /**
  * {@inheritdoc}
  */
  public function prepareRow(Row $row) {
    
    
    // First letter for ARTICLE TITLE need to be capital.
    $row->setSourceProperty('title', ucfirst($row->getSourceProperty('title')));
    
    /**
     * Handle NULL images. Check if image exist OR valid path is provided.
     * Set to empty  if not valid image details.
     * 
     * @var file $image
     */
    $image = $row->getSourceProperty('image');    
    $wrong_file_flag = count(explode('/', $image));
    if ($wrong_file_flag > 1) {
      $row->setSourceProperty('image', '');  // Set NULL/EMPTY
    }
    else if (NULL == $image || 'NULL' == $image || 'null' == $image || ' ' == $image || null == $image || is_null($image) ) {
      $row->setSourceProperty('image', '');  // Set NULL/EMPTY
    }
    
    // Field  Links, check if empty. 
    if (NULL == $row->getSourceProperty('link1')){
      $row->setSourceProperty('link1', '');
    }
    
    //Field Category, Check if empty. 
    if (NULL == $row->getSourceProperty('category')){
      $row->setSourceProperty('category', FALSE);
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
      else {
        $keywords_array[$keyword_index] = ucfirst($keyword_value);
      }
    }
    
    $keywords = implode("|", $keywords_array);
    $row->setSourceProperty('keyword', $keywords);
    
    
    // Extra work for email.    
    $email_array = explode("|", $row->getSourceProperty('email'));
    foreach($email_array as $email_index => $email_value) {
      trim($email_value);
      if (!$email_value || 'NULL' == $email_value) {
        unset($email_array[$email_index]);
      }
    }
    $emails = implode("|", $email_array);
    $row->setSourceProperty('email', $emails);
    
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array('articleID' => array('type' => 'integer', 'alias' => 'a'));
  }
}

