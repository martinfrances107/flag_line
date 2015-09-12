<?php

/**
 * @file
 * Contains Drupal\flag_line\PassengerListBuilder.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;
use Drupal\Core\Routing\LinkGeneratorTrait;

/**
 * Defines a class to build a listing of Passenger entities.
 *
 * @ingroup flag_line
 */
class PassengerListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Passenger ID');
    $header['src'] = $this->t('Starting Station');
    $header['dst'] = $this->t('Destination');
    $header['boarded'] = $this->t('Boarded');
    $header['alighted'] = $this->t('Alighted');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\flag_line\Entity\Passenger */
    $row['id'] = $this->l(
      $entity->id(), new Url(
      'entity.passenger.edit_form', ['passenger' => $entity->id()]
      )
    );

    $row['src'] = $entity->getSrc();
    $row['dst'] = $entity->getDst();
    $row['boarded'] = $entity->hasBoarded() ? $this->t('YES') : $this->t('NO');
    $row['alighted'] = $entity->hasAlighted() ? $this->t('YES') : $this->t('NO');
    return $row + parent::buildRow($entity);
  }

}
