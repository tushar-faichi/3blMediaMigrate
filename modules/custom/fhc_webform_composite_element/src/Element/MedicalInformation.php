<?php

namespace Drupal\fhc_webform_composite_element\Element;

use Drupal\webform\Element;


/**
 * Provides a webform element for an medical information element.
 *
 * @FormElement("medical_information")
 */
class MedicalInformation extends WebformCompositeBase {
  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements() {
    $elements = [];
    $elements['medication'] = [
      '#type' => 'textfield',
      '#title' => t('Medication'),
    ];
    $elements['dosage'] = [
      '#type' => 'textfield',
      '#title' => t('Dosage'),
    ];
    $elements['route'] = [
      '#type' => 'textfield',
      '#title' => t('Route'),
    ];
    $elements['frequency'] = [
      '#type' => 'textfield',
      '#title' => t('Frequency'),
    ];
    return $elements;
  }

}
