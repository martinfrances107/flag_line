<?php

/**
 * @file
 * Contains Drupal\flag_line\Entity\Station.
 */

namespace Drupal\flag_line\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Station entities.
 */
class StationViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['station']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Station'),
      'help' => $this->t('The Station ID.'),
    );

    return $data;
  }

}
