<?php

/**
 * @file
 * Contains Drupal\flag_line\StationManager.
 */

namespace Drupal\flag_line;

use Drupal\flag_line\Entity\Passenger;
use Drupal\flag_line\StationManagerInterface;
use Drupal\Core\Queue\QueueFactory;
use Psr\Log\LoggerInterface;

/**
 * An instance of the station manager interface.
 *
 * @package Drupal\flag_line
 */
class StationManager implements StationManagerInterface {

  /**
   * A list of platforms to visit in assending order.
   *
   * @var \Drupal\flag_line\PlatformInterface[]|null
   */
  protected $platformsUp = NULL;

  /**
   * A list of platforms to visit in descending order.
   *
   * @var \Drupal\flag_line\PlatformInterface[]|null
   */
  protected $platformsDown = NULL;

  /**
   * The number of stations on the line.
   *
   * @var int
   */
  protected $numStations;

  /**
   * A factory for generating platform queues.
   *
   * @var \Drupal\Core\Queue\QueueFactory $queueFactory.
   */
  protected $queueFactory;

  /**
   * Keeps records of passenger movements etc.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructor.
   *
   * @param Drupal\Core\Queue\QueueFactory $queue_factory
   *   A queue factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(QueueFactory $queue_factory, LoggerInterface $logger) {
    $this->queueFactory = $queue_factory;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumStations() {
    return $this->numStations;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlatforms($upwards) {
    // Return a cached list where possible.
    if ($upwards) {
      if (is_null($this->platformsUp)) {
        $this->platformsUp = $this->generatePlatforms(TRUE);
      }
      return $this->platformsUp;
    }
    else {
      if (is_null($this->platformsDown)) {
        $this->platformsDown = $this->generatePlatforms(FALSE);
      }
      return $this->platformsDown;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getStationNames($upwards = TRUE) {
    if ($upwards) {
      $range = range(0, $this->getNumStations() - 1, 1);
    }
    else {
      $range = range($this->getNumStations() - 1, 0, -1);
    }

    return $range;
  }

  /**
   * {@inheritdoc}
   */
  public function populateStationsAtRandom($num_passengers, $run_id) {
    for ($i = 0; $i < $num_passengers; $i++) {
      $passenger = $this->generatePassengerAtRandom($run_id);
      $this->addPassengerToPlatform($passenger);
    }
    $this->logger->debug("Run:$run_id: $num_passengers passengers arrive at platforms.");
  }

  /**
   * {@inheritdoc}
   */
  public function setNumStations($num_stations) {
    $this->numStations = $num_stations;
    return $this;
  }

  /**
   * Generate passenger.
   *
   * Generate a random ticket and passenger type.
   *
   * @param int $run_id
   *   The run identifier.
   *
   * @return \Drupal\flag_line\Entity\Passenger
   *   A new passenger.
   */
  private function generatePassengerAtRandom($run_id) {
    // Generate ticket information.
    $max = $this->getNumStations() - 1;
    $src = rand(1, $max);
    $dst = rand(1, $max);

    $passenger = Passenger::create([
        'src' => $src,
        'dst' => $dst,
        'upwards' => ($src <= $dst),
        'run_id' => $run_id,
    ]);
    $passenger->save();

    return $passenger;
  }

  /**
   * Place passenger in the appropiate platform queue.
   *
   * If the src and dst are equal the passenger will still be placed in a
   * platform queue it is up to the train to unload correctly.
   *
   * @param \Drupal\flag_line\PassengerInterface $passenger
   *   An instance of a passenger.
   */
  private function addPassengerToPlatform(PassengerInterface $passenger) {
    // Must check platforms exists before putting passenger into the system.
    $upwards = $passenger->isMovingUpwards();
    if ($upwards && is_null($this->platformsUp)) {
      $this->platformsUp = $this->generatePlatforms(TRUE);
    }
    if (!$upwards && is_null($this->platformsDown)) {
      $this->platformsDown = $this->generatePlatforms(FALSE);
    }

    // Identify the platform.
    $name = $this->getPlatformName($passenger->getSrc(), $upwards);
    $platform = $this->queueFactory->get($name, TRUE);

    // Add passenger.
    $platform->createItem($passenger);
  }

  /**
   * Returns a platform name for a given station and direction of travel.
   *
   * This method is intended to be the sole line specfic mapping.
   * It is used by both trains and passegers to identify the approiate
   * platform for a given direction of travel.
   *
   * @param int $station_id
   *   The station identifier.
   * @param bool $upwards
   *   Is the direction of travel up the line?
   *
   * @return string
   *   The pltform name.
   */
  private function getPlatformName($station_id, $upwards) {
    if ($upwards) {
      $name = "S:$station_id-P:A";
    }
    else {
      $name = "S:$station_id-P:B";
    }

    return $name;
  }

  /**
   * Generate platforms queues for a given direction of travel.
   *
   * @param bool $upwards
   *   Is the direciton of travel up the line?
   *
   * @return \Drupal\flag_line\PlatformInterface[]
   *   An ordered list of platforms who order is based on the direction of
   *   travel.
   */
  private function generatePlatforms($upwards) {
    $platforms = [];
    $stations = $this->getStationNames($upwards);
    foreach ($stations as $station) {
      $platforms[] = $this->generatePlatform($station, $upwards);
    }

    return $platforms;
  }

  /**
   * Generate a new platform for a given direction of travel.
   *
   * @param int $station_id
   *   The station identifer.
   * @param bool $upwards
   *   Is the direction of travel up the line?
   */
  private function generatePlatform($station_id, $upwards) {
    $platform_name = $this->getPlatformName($station_id, $upwards);
    $queue = $this->queueFactory->get($platform_name);
    $queue->createQueue();
    return new Platform($platform_name, $station_id, $queue);
  }

}
