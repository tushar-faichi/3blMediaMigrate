<?php

namespace Drupal\url_redirect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

class AddRedirect extends ConfigFormBase {
  public function getFormId() {
    return 'url_redirect_settings_form';
  }
  public function getEditableConfigNames() {
    return [
      'url_redirect.settings',
    ];

  }
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {

    $url = Url::fromRoute('url_redirect.list_redirects');
    $internal_link = Link::fromTextAndUrl($this->t('Url Redirect List'), $url)->toString();
    $form['goto_list'] = array(
      '#markup' => $internal_link,
    );
    $form['url'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Url Redirect'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );
    $form['url']['path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#attributes' => array(
        'placeholder' => $this->t('Enter Path'),
      ),
      '#required' => TRUE,
      '#description' => $this->t('This can be an internal Drupal path such as node/add, node/* Enter <front> to link to the front page.'),
    );
    $form['url']['redirect_path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Redirect Path'),
      '#attributes' => array(
        'placeholder' => $this->t('Enter Redirect Path'),
      ),
      '#required' => TRUE,
      '#description' => $this->t('This redirect path can be internal Drupal path such as node/add Enter <front> to link to the front page.'),
    );

    $form['url']['checked_for'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Select Redirect path for'),
      '#options' => array(
        'Role' => $this->t('Role'),
        'User' => $this->t('User')
      ),
      '#required' => TRUE,
    );

    $form['url']['url_roles'] = array(
      '#type' => 'container',
      '#states' => array(
        'visible' => array(
          ':input[name="checked_for"]' => array('value' => 'Role'),
        ),
      ),
    );

    $user_roles = user_role_names();
    $form['url']['url_roles']['roles'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Roles'),
      '#options' => $user_roles,
      '#multiple' => TRUE,
    );

    $form['url']['url_user'] = array(
      '#type' => 'container',
      '#states' => array(
        'visible' => array(
          ':input[name="checked_for"]' => array('value' => 'User'),
        ),
      ),
    );
    $users = url_redirect_user_fetch();
    $form['url']['url_user']['user'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Users.'),
      '#options' => $users,
      '#multiple' => TRUE,
    );
    $form['url']['message'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Display Message for Redirect'),
      '#required' => TRUE,
      '#description' => $this->t('Show a message for redirect path.'),
      '#options' => array(
        'Yes' => $this->t('Yes'),
        'No' => $this->t('No')
      ),
    );
    $form['url']['status'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#options' => array(
        0 => $this->t('Disabled'),
        1 => $this->t('Enabled'),
      ),
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    );
    $form['reset'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
    );
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($values['op']->render() == 'Reset') {
      url_redirect_redirect(Url::fromRoute('url_redirect.add_redirect')->toString());
    }
    if ($values['op']->render() == 'Submit') {

      $path = $values['path'];
      $path_check = url_redirect_path_check($path);
      if (!\Drupal::service('path.validator')->isValid($path_check)) {
        $form_state->setErrorByName('path', $this->t("The path '@link_path' already used for redirect.", array('@link_path' => $path)));
      }

      $redirect_path = $values['redirect_path'];
      if (!\Drupal::service('path.validator')->isValid($redirect_path)) {
        $form_state->setErrorByName('redirect_path', $this->t("The redirect path '@link_path' is either invalid or you do not have access to it.", array('@link_path' => $redirect_path)));
      }

      $checked_for = $values['checked_for'];
      // Get Checked for User / Role.
      if ($checked_for == 'User') {
        $user_values = $values['user'];
        if (!$user_values) {
          $form_state->setErrorByName('user', $this->t("Select atleast one user for which you want to apply this redirect."));
        }
      }

      if ($checked_for == 'Role') {
        $roles_values = $values['roles'];
        if (!$roles_values) {
          $form_state->setErrorByName('roles', $this->t("Select atleast one role for which you want to apply this redirect."));
        }
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $path = $values['path'];
    $redirect_path = $values['redirect_path'];
    $checked_for = $values['checked_for'];
    $status = $values['status'];
    $message = $values['message'];

    // Get Checked for User / Role.
    if ($checked_for == 'User') {
      $user_values = $values['user'];
      if ($user_values) {
        $users_values = json_encode($user_values);
        $role_values = '';
      }
    }

    if ($checked_for == 'Role') {
      $roles_values = $values['roles'];
      if ($roles_values) {
        $role_values = json_encode($roles_values);
        $users_values = '';
      }
    }

    // Inserting the data in the url_redirect table.
       $query = \Drupal::database();
       $query->insert('url_redirect')->fields(array(
        'path' => $path,
        'roles' => $role_values,
        'users' => $users_values,
        'redirect_path' => $redirect_path,
        'status' => $status,
        'message' => $message,
        'check_for' => $checked_for,
      ))
      ->execute();

    // Redirect to listing page.
    url_redirect_redirect(Url::fromRoute('url_redirect.list_redirects')->toString());
  }
}
