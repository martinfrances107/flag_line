<?php

/**
 * @file
 * Contains run_entity.page.inc.
 *
 * Page callback for Run Entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Run Entity templates.
 *
 * Default template: run_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_run_entity(array &$variables) {
  // Fetch RunEntity Entity Object.
  $run_entity = $variables['elements']['#run_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
