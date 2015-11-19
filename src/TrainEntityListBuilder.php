<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainEntityListBuilder.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Train entity entities.
 *
 * @ingroup flag_line
 */
class TrainEntityListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Train entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\flag_line\Entity\TrainEntity */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $this->getLabel($entity),
      new Url(
        'entity.train_entity.edit_form', array(
          'train_entity' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
