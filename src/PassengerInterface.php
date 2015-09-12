<?php

/**
 * @file
 * Contains Drupal\flag_line\PassengerInterface.
 */

namespace Drupal\flag_line;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Passenger entities.
 *
 * @ingroup flag_line
 */
interface PassengerInterface extends ContentEntityInterface, EntityOwnerInterface {

  /**
   * Returns the station at which the passenger enters the line.
   *
   * @return int
   *   The station identifer.
   */
  public function getSrc();

  /**
   * Returns the station at which the passenger leaves.
   *
   * @return int
   *   The station identifer.
   */
  public function getDst();

  /**
   * Has the passenger got off a train?
   *
   * @return bool
   *   TRUE if a passenger has left a train.
   */
  public function hasAlighted();

  /**
   * Has a passenger ever boarded a train?
   *
   * @return bool
   *   TRUE if a passenger has every got on a train.
   */
  public function hasBoarded();

  /**
   * Indicates the direction of travel on the line.
   *
   * @return bool
   *   TRUE if the passenger is moving up the line, FALSE otherwise.
   */
  public function isMovingUpwards();

  /**
   * Record the event of a passenger boarding a train.
   *
   * @return static
   *   The object itself for chaining.
   */
  public function setBoarded();

  /**
   * Records the event of a passenger leaving a train.
   *
   * @return static
   *   The object itself for chaining.
   */
  public function setAlighted();

}
