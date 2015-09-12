<?php

/**
 * @file
 * Contains run.page.inc.
 *
 * Page callback for Run entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Run templates.
 *
 * Default template: run.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_run(array &$variables) {
  // Fetch Run Entity Object.
  $run = $variables['elements']['#run'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
