<?php

/**
 * @file
 * Contains Drupal\flag_line\Entity\Passenger.
 */

namespace Drupal\flag_line\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Passenger entities.
 */
class PassengerViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['passenger']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Passenger'),
      'help' => $this->t('The Passenger ID.'),
    );

    return $data;
  }

}
