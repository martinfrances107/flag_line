<?php

namespace Drupal\flag_line;

use Drupal\Core\Queue\QueueInterface;

/**
 * Contains simple platform accessor methods.
 *
 * @package Drupal\flag_line
 */
class Platform implements PlatformInterface {

  /**
   * Human readable identifier for the platform.
   *
   * @var string
   */
  private $name;

  /**
   * A list of passengers waiting at the platform.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  private $queue;

  /**
   * The station identifier.
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
   *   The station identifier.
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
  public function getQueue() : QueueInterface {
    return $this->queue;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() : string {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getStationId() : int {
    return $this->stationId;
  }

}
