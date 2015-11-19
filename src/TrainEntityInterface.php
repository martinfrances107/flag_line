<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainEntityInterface.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Train entity entities.
 *
 * @ingroup flag_line
 */
interface TrainEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Returns the service name.
   *
   * @return string
   *   A Human readable name of the train.
   */
  public function getServiceName();

  /**
   * Adds a passenger to the train.
   *
   * @param \Drupal\flag_line\PassengerInterface $passenger
   *   The passenger boarding the train.
   */
  public function addPassenger(PassengerInterface $passenger);

  /**
   * Return a list of passengers alighting from the train at the station.
   *
   * @param int $station_id
   *   The station identifer.
   *
   * @return \Drupal\flag_line\PassengerInterface[]
   *   The list of passenegers.
   */
  public function removePassengers($station_id);

}
