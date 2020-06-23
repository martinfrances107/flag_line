<?php

namespace Drupal\flag_line\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Run Entity entities.
 *
 * @ingroup flag_line
 */
interface RunEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

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

  /**
   * Gets the Run Entity name.
   *
   * @return string
   *   Name of the Run Entity.
   */
  public function getName();

  /**
   * Sets the Run Entity name.
   *
   * @param string $name
   *   The Run Entity name.
   *
   * @return \Drupal\flag_line\Entity\RunEntityInterface
   *   The called Run Entity entity.
   */
  public function setName($name);

  /**
   * Gets the Run Entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Run Entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Run Entity creation timestamp.
   *
   * @param int $timestamp
   *   The Run Entity creation timestamp.
   *
   * @return \Drupal\flag_line\Entity\RunEntityInterface
   *   The called Run Entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Run Entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Run Entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\flag_line\Entity\RunEntityInterface
   *   The called Run Entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Run Entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Run Entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\flag_line\Entity\RunEntityInterface
   *   The called Run Entity entity.
   */
  public function setRevisionUserId($uid);

}
