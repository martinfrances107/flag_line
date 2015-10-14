<?php

/**
 * @file
 * Contains Drupal\flag_line\Controller\Handbook.
 */

namespace Drupal\flag_line\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class Handbook.
 *
 * @package Drupal\flag_line\Controller
 */
class Handbook extends ControllerBase {
  /**
   * Index.
   *
   * @return array
   *   The render array linking to the main page.
   */
  public function index() {
    return [
      'handbook' => [
        '#theme' => 'flag_line_handbook',
      ],
    ];
  }

}
