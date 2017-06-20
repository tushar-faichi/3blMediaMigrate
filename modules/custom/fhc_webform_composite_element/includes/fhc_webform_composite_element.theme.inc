<?php

/**
 * Prepares variables for address composite element templates.
 *
 * Default template: fhc_webform_composite_element-composite-medical_information.html.twig.  //webform-element-address.html.twig
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #options, #description, #required,
 *     #attributes.
 */
function template_preprocess_fhc_webform_composite_element_composite_medical_information(array &$variables) {
  $element = $variables['element'];
  foreach (Element::children($element) as $key) {
    if ($element[$key]['#access']) {
      $variables['content'][$key] = $element[$key];
    }
  }
  $variables['flexbox'] = (isset($element['#flexbox'])) ? $element['#flexbox'] : FALSE;
}