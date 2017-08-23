<?php

namespace Drupal\flag_line;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for querying Run entities.
 *
 * @ingroup flag_line
 */
interface RunInterface extends ContentEntityInterface, EntityOwnerInterface {

  /**
   * Constants that reflect the status of train services.
   */
  const TRAINS_NOT_YET_RUN = 'FRESH';
  const TRAINS_RUNNING = 'RUNNING';
  const TRAINS_STOPPED = 'STOPPED';

  /**
   * Constants that reflect the status of the stations.
   */
  const STATIONS_NOT_YET_OPENED = 'UNOPENED';
  const STATIONS_OPEN = 'OPEN';
  const STATIONS_CLOSED = 'CLOSED';

  /**
   * Returns the update period over which events are defined.
   *
   * @return int
   *   The time in seconds.
   */
  public function getUpdatePeriod() : int;

  /**
   * Returns the state of the train service.
   *
   * @return string
   *   One of the TRAINS constants listed above.
   */
  public function getTrainStatus() : string;

  /**
   * Returns the number of stations.
   *
   * @return int
   *   Get the number of stations.
   */
  public function getNumStations() : int;

  /**
   * Returns the state of the stations.
   *
   * @return string
   *   One of the STATIONS constants listed above.
   */
  public function getStationsStatus() : string;

  /**
   * Returns the number of passengers generate in the update period.
   *
   * @return int
   *   The number of passengers.
   */
  public function getNumPassengers() : int;

  /**
   * Set the train status to be one of the TRAIN constants.
   *
   * See above. If the status is invalid no change is made.
   *
   * @param string $status
   *   One a the predefined constants.
   *
   * @return static
   *   The object itself for chaining.
   */
  public function setTrainStatus(string $status);

  /**
   * Set the train status to be one of the STATIONS constants.
   *
   * See above. If the status is invalid no change is made.
   *
   * @param string $status
   *   One a the predefined constants.
   *
   * @return static
   *   The object itself for chaining.
   */
  public function setStationsStatus(string $status);

}
