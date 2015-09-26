<?php

/**
 * @file
 * Contains Drupal\flag_line\TrainInterface.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\PassengerInterface;

/**
 * Provides an train interface.
 *
 * @ingroup flag_line
 */
interface TrainInterface {

  /**
   * Adds a passenger to the train.
   *
   * @param \Drupal\flag_line\PassengerInterface $passenger
   *   The passenger boarding the train.
   */
  public function addPassenger(PassengerInterface $passenger);

  /**
   * Returns the service name.
   *
   * @return string
   *   A Human readable name of the train.
   */
  public function getServiceName();

  /**
   * Returns the number of passengers on the train.
   *
   * @return int
   *   The number on the train.
   */
  public function getNumPassengers();

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

  /**
   * Sets the name of the train service.
   *
   * @param string $name
   *   The name of the service.
   */
  public function setServiceName($name);

}
