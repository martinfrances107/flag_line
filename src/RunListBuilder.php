<?php

namespace Drupal\flag_line;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Run entities.
 *
 * @ingroup flag_line
 */
class RunListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Run ID');
    $header['name'] = $this->t('Name');
    $header['train_status'] = $this->t('Trains');
    $header['station_status'] = $this->t('Stations');
    $header['num_stations'] = $this->t('Number of stations');
    $header['update_period'] = $this->t('Update Period(s)');
    $header['num_passengers'] = $this->t('Number of passengers');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\flag_line\Entity\Run */
    $row['id'] = $entity->id();
    $row['name'] = \Drupal::l(
      $this->getLabel($entity),
      new Url(
        'entity.run.edit_form', array(
          'run' => $entity->id(),
        )
      )
    );
    $row['train_status'] = $this->t($entity->getTrainStatus());
    $row['station_status'] = $this->t($entity->getStationsStatus());
    $row['num_sttaions'] = $entity->getNumStations();
    $row['update_period'] = $entity->getUpdatePeriod();
    $row['num_passengers'] = $entity->getNumPassengers();
    return $row + parent::buildRow($entity);
  }

}
