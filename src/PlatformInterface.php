<?php

namespace Drupal\flag_line;

use Drupal\Core\Queue\QueueInterface;

/**
 * Defines platform accessor methods.
 *
 * @package Drupal\flag_line
 */
interface PlatformInterface {

  /**
   * Returns the queue associated with the platform.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   The Queue.
   */
  public function getQueue() : QueueInterface;

  /**
   * Returns the name of the platform.
   *
   * @return string
   *   The platform name.
   */
  public function getName() : string;

  /**
   * Returns the station id associated with the platform.
   *
   * @return int
   *   The station identifier.
   */
  public function getStationId() : int;

}
