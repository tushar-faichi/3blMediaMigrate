<?php

namespace Drupal\url_redirect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Html;

class DeleteRedirect extends ConfigFormBase {
  public function getFormId() {
    return 'url_redirect_delete_form';
  }
  public function getEditableConfigNames() {
    return [
      'url_redirect.settings',
    ];

  }
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {

    $delete_path = \Drupal::request()->query->get('path');
    $path_data = url_redirect_path_edit($delete_path);
    if ($path_data) {
      $form['output'] = array(
        '#markup' => $this->t("Are you sure you want to delete") . " <strong> " . $delete_path . '</strong> ' . $this->t('redirect?') . ' <br><br>',
      );
      $form['delete'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
      );
      $form['no'] = array(
        '#type' => 'submit',
        '#value' => $this->t('No'),
      );
      return $form;
    }
    else {
      drupal_set_message($this->t('Path specified is not correct for deletion.'), 'error');
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($values['op']->getUntranslatedString() == 'No') {
      url_redirect_redirect(Url::fromRoute('url_redirect.list_redirects')->toString());
    }
    if ($values['op']->getUntranslatedString() == 'Delete') {
     $delete_path = \Drupal::request()->query->get('path');
     if($delete_path != "<front>") {
        $delete_path = Html::escape($delete_path);
     }
      $delete=\Drupal::database();
      $delete->delete('url_redirect')
        ->condition('path', $delete_path)
        ->execute();

      drupal_set_message($this->t("The path '@path' is deleted.", array('@path' => $delete_path)));
      url_redirect_redirect(Url::fromRoute('url_redirect.list_redirects')->toString());
    }
  }
}
