<?php

namespace Drupal\fhc_webform_composite_element\Plugin\WebformElement;


use Drupal\Core\Form\FormState;
use Drupal\webform\Plugin\WebformElement;
use Drupal\webform\Element\WebformCompositeBase;
use Drupal\fhc_webform_composite_element\Element\MedicalInformation as WebformMedicalInformationElement;



/**
 * Provides an 'medical_information' element.
 *
 * @WebformElement(
 *   id = "medical_information",
 *   label = @Translation("Medical information"),
 *   description = @Translation("Provides a form element to collect medical information (Medical, dosage, route, frequency)."),
 *   category = @Translation("Composite elements"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 */
class MedicalInformation extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  protected function getCompositeElements() {
    return WebformMedicalInformationElement::getCompositeElements();
  }

  /**
   * {@inheritdoc}
   */
  protected function getInitializedCompositeElement(array &$element) {
    $form_state = new FormState();
    $form_completed = [];
    return WebformMedicalInformationElement::processWebformComposite($element, $form_state, $form_completed);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, array $value) {
    return $this->formatTextItemValue($element, $value);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, array $value) {
    $lines = [];
    if (!empty($value['medication'])) {
      $lines['medication'] = $value['medication'];
    }
    if (!empty($value['dosage'])) {
      $lines['dosage'] = $value['dosage'];
    }
    if (!empty($value['route'])) {
      $lines['route'] = $value['route'];
    }
    if (!empty($value['frequency'])) {
      $lines['frequency'] = $value['frequency'];
    }
    return $lines;
  }

}
