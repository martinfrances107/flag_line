<?php

namespace Drupal\flag_line\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Run entities.
 */
class RunViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['run']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Run'),
      'help' => $this->t('The Run ID.'),
    );

    return $data;
  }

}
