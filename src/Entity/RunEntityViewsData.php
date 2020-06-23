<?php

namespace Drupal\flag_line\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Run Entity entities.
 */
class RunEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
