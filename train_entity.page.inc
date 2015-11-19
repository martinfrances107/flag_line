<?php

/**
 * @file
 * Contains train_entity.page.inc.
 *
 * Page callback for Train entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Train entity templates.
 *
 * Default template: train_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_train_entity(array &$variables) {
  // Fetch TrainEntity Entity Object.
  $train_entity = $variables['elements']['#train_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
