<?php

/**
 * @file
 * Contains Drupal\flag_line\Entity\TrainEntity.
 */

namespace Drupal\flag_line\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Train entity entities.
 */
class TrainEntityViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['train_entity']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Train entity'),
      'help' => $this->t('The Train entity ID.'),
    );

    return $data;
  }

}
