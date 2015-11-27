<?php

/**
 * @file
 * Contains Drupal\flag_line\Train.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\PlatformInterface;
use Drupal\Core\Queue\QueueInterface;

/**
 * An implementation of the TrainInterface.
 *
 * Contains simple platform accessor methods.
 *
 * @package Drupal\flag_line
 */
class Platform implements PlatformInterface {

  /**
   * Human readable identifer for the platform.
   *
   * @var string
   */
  private $name;

  /**
   * A list of passengers waiting at the platform.
   *
   * @param \Drupal\Core\Queue\QueueInterface $queue
   */
  private $queue;

  /**
   * The station identifer.
   *
   * @var int
   */
  private $stationId;

  /**
   * Constructor.
   *
   * @param string $name
   *   The name of the platform.
   * @param int $station_id
   *   The station_id.
   * @param Drupal\Core\Queue\QueueInterface $queue
   *   A queue factory.
   */
  public function __construct($name, $station_id, QueueInterface $queue) {
    $this->name = $name;
    $this->stationId = $station_id;
    $this->queue = $queue;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueue() {
    return $this->queue;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getStationId() {
    return $this->stationId;
  }

}
