<?php

namespace Drupal\flag_line;

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
  public function getQueue();

  /**
   * Returns the name of the platform.
   *
   * @return string
   *   The platform name.
   */
  public function getName();

  /**
   * Returns the station id associated with the platform.
   *
   * @return int
   *   The station identifer.
   */
  public function getStationId();

}
